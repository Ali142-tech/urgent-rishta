<?php

namespace App\Http\Controllers;

use App\CmsPage;
use App\Faq;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Admin ▸ Content: the pages an admin writes himself — any number of pages (the original Terms & Conditions and Privacy
 * Policy plus new ones, each can be hidden) and the FAQ list. Admin only (same guard as AdminController). Everything an
 * admin types is sanitised (HtmlSanitizer) before it is saved, so nothing but simple formatting reaches visitors.
 */
class CmsAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        return view('admin.cms.index', [
            'pages' => CmsPage::with('editor:id,first_name,last_name')->orderByDesc('is_system')->orderBy('sort_order')->orderBy('title')->get(),
            'faqTotal' => Faq::count(), 'faqVisible' => Faq::where('is_active', true)->count(), 'faqUpdated' => Faq::max('updated_at'),
        ]);
    }

    // ---------------------------------------------------------------- pages

    public function pageCreate()
    {
        return view('admin.cms.page-edit', ['page' => new CmsPage(['is_active' => true])]);
    }

    public function pageStore(Request $request)
    {
        $page = new CmsPage(['sort_order' => (int) CmsPage::max('sort_order') + 10]);
        $this->savePage($request, $page);
        Session::flash('message', 'success|Page created. It is live at ' . $page->publicUrl());

        return redirect()->route('admin.cms');
    }

    public function pageEdit(CmsPage $page)
    {
        return view('admin.cms.page-edit', ['page' => $page]);
    }

    public function pageUpdate(Request $request, CmsPage $page)
    {
        $this->savePage($request, $page);
        Session::flash('message', 'success|Page saved.' . ($page->is_active ? ' It is live on the site now.' : ' It is hidden, so visitors cannot see it.'));

        return redirect()->route('admin.cms.pages.edit', $page);
    }

    /** Hides a page from the site (visitors get "not found") or shows it again — the text is kept. */
    public function pageToggle(CmsPage $page)
    {
        $page->update(['is_active' => !$page->is_active, 'updated_by' => Auth::id()]);
        Session::flash('message', 'success|' . $page->title . ($page->is_active ? ' is visible on the site.' : ' is now hidden from the site.'));

        return redirect()->route('admin.cms');
    }

    public function pageDestroy(CmsPage $page)
    {
        abort_if($page->is_system, 403, 'The Terms & Conditions and Privacy Policy pages cannot be deleted — hide them instead.');
        $page->delete();
        Session::flash('message', 'success|Page deleted.');

        return redirect()->route('admin.cms');
    }

    private function savePage(Request $request, CmsPage $page): void
    {
        $rules = [
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:200000',
        ];
        // a system page keeps its address; any other page's address (slug) must be unique and URL-safe
        if (!$page->is_system) {
            $rules['slug'] = ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::unique('cms_pages', 'slug')->ignore($page->id)];
        }
        $data = $request->validate($rules, [
            'slug.regex' => 'The address can only use lower-case letters, numbers and single hyphens (for example refund-policy).',
            'slug.unique' => 'Another page already uses this address — choose a different one.',
            'title.required' => 'Please enter the page title.',
            'body.required' => 'Please write the page text.',
        ]);

        $page->title = trim($data['title']);
        $page->body = HtmlSanitizer::clean($data['body']);
        if (!$page->is_system) {
            $page->slug = !empty($data['slug']) ? $data['slug'] : $this->uniqueSlug(Str::slug($page->title) ?: 'page', $page->id);
        }
        $page->is_active = $request->boolean('is_active');
        $page->show_in_footer = $request->boolean('show_in_footer');
        $page->updated_by = Auth::id();
        $page->save();
        Log::info('Admin ' . Auth::user()->dataid . ' saved CMS page ' . $page->slug);
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $slug = $base;
        for ($i = 2; CmsPage::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists(); $i++) {
            $slug = $base . '-' . $i;
        }

        return $slug;
    }

    // ---------------------------------------------------------------- FAQs

    public function faqs()
    {
        return view('admin.cms.faqs', ['faqs' => Faq::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function faqCreate()
    {
        return view('admin.cms.faq-edit', ['faq' => new Faq(['is_active' => true])]);
    }

    public function faqStore(Request $request)
    {
        $faq = new Faq(['sort_order' => (int) Faq::max('sort_order') + 10]);
        $this->saveFaq($request, $faq);
        Session::flash('message', 'success|FAQ added. It is live on the FAQ page now.');

        return redirect()->route('admin.cms.faqs');
    }

    public function faqEdit(Faq $faq)
    {
        return view('admin.cms.faq-edit', ['faq' => $faq]);
    }

    public function faqUpdate(Request $request, Faq $faq)
    {
        $this->saveFaq($request, $faq);
        Session::flash('message', 'success|FAQ saved.');

        return redirect()->route('admin.cms.faqs');
    }

    public function faqDestroy(Faq $faq)
    {
        $faq->delete();
        Session::flash('message', 'success|FAQ deleted.');

        return redirect()->route('admin.cms.faqs');
    }

    /** Shows or hides one question on the public page without deleting it. */
    public function faqToggle(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);

        return redirect()->route('admin.cms.faqs');
    }

    /** Moves a question one place up or down by swapping its position with its neighbour's. */
    public function faqMove(Faq $faq, string $direction)
    {
        abort_unless(in_array($direction, ['up', 'down'], true), 404);

        $list = Faq::orderBy('sort_order')->orderBy('id')->get()->values();
        $i = $list->search(fn ($f) => $f->id === $faq->id);
        $j = $direction === 'up' ? $i - 1 : $i + 1;
        if ($i !== false && isset($list[$j])) {
            $order = $list->pluck('id')->all();
            [$order[$i], $order[$j]] = [$order[$j], $order[$i]];
            foreach ($order as $position => $id) {
                Faq::where('id', $id)->update(['sort_order' => ($position + 1) * 10]);   // renumbered, so duplicates never tangle the order
            }
        }

        return redirect()->route('admin.cms.faqs');
    }

    private function saveFaq(Request $request, Faq $faq): void
    {
        $data = $request->validate(['question' => 'required|string|max:300', 'answer' => 'required|string|max:50000']);

        $faq->question = trim(strip_tags($data['question']));
        $faq->answer = HtmlSanitizer::clean($data['answer']);
        $faq->is_active = $request->boolean('is_active');
        $faq->save();
    }
}
