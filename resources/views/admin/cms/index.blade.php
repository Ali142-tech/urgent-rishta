{{-- Admin ▸ Content — every page an admin can write (system pages + new ones), with add / edit / hide / delete, and the FAQ summary. See CmsAdminController. --}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Pages')
@section('main-content')
@include('admin.cms.partials.styles')

<div class="cms-note">Write pages for the site here — add as many as you need, edit them any time, and hide a page without deleting it. Saved changes go live straight away.</div>

<div class="cms-bar">
    <div class="cms-faqbox">
        <span class="cms-card__icon" style="margin:0;"><i class="fa fa-question-circle"></i></span>
        <div>
            <b>FAQs</b>
            <small>{{ $faqVisible }} shown &middot; {{ $faqTotal }} in total{{ $faqUpdated ? ' · last change ' . \Carbon\Carbon::parse($faqUpdated)->diffForHumans() : '' }}</small>
        </div>
        <a class="cms-btn cms-btn--dark" href="{{ route('admin.cms.faqs') }}">Manage FAQs</a>
    </div>
    <a class="cms-btn cms-btn--gold" href="{{ route('admin.cms.pages.create') }}"><i class="fa fa-plus"></i> Add page</a>
</div>

<div class="cms-table-wrap">
    <table class="cms-table cms-table--pages">
        <thead><tr><th>Page</th><th style="width:170px;">Address</th><th style="width:100px;">Status</th><th style="width:190px;">Last edited</th><th style="width:290px;">Actions</th></tr></thead>
        <tbody>
            @forelse($pages as $page)
            <tr>
                <td class="cms-pg-title">
                    <div class="cms-title">
                        <b>{{ $page->title }}</b>
                        @if($page->is_system)<span class="cms-pill cms-pill--tag">System</span>@endif
                        @if($page->show_in_footer)<span class="cms-pill cms-pill--tag">In footer</span>@endif
                    </div>
                </td>
                <td class="cms-pg-addr" data-label="Address" style="font-size:12.5px; color:#4B5651;">/{{ ltrim(str_replace(url('/'), '', $page->publicUrl()), '/') }}</td>
                <td class="cms-pg-status"><span class="cms-pill {{ $page->is_active ? '' : 'cms-pill--off' }}">{{ $page->is_active ? 'Active' : 'Hidden' }}</span></td>
                <td class="cms-pg-edited" data-label="Last edited" style="font-size:12.5px; color:#6B7570;">{{ $page->updated_at ? $page->updated_at->diffForHumans() : '—' }}@if($page->editor)<small>by {{ \Illuminate\Support\Str::limit(trim($page->editor->first_name . ' ' . $page->editor->last_name), 22) }}</small>@endif</td>
                <td class="cms-pg-act">
                    <div class="cms-row-actions">
                        <a class="cms-mini" href="{{ route('admin.cms.pages.edit', $page) }}">Edit</a>
                        <a class="cms-mini" href="{{ $page->publicUrl() }}" target="_blank" rel="noopener">View</a>
                        <form method="POST" action="{{ route('admin.cms.pages.toggle', $page) }}">@csrf<button class="cms-mini">{{ $page->is_active ? 'Hide' : 'Show' }}</button></form>
                        @unless($page->is_system)
                        <form method="POST" action="{{ route('admin.cms.pages.delete', $page) }}" class="js-confirm" data-title="Delete &quot;{{ e($page->title) }}&quot;?" data-text="This page and its address {{ $page->publicUrl() }} will be removed from the site. You cannot undo this. Use Hide instead if you may need it again." data-button="Yes, delete page">@csrf<button class="cms-mini cms-mini--danger">Delete</button></form>
                        @endunless
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:30px; text-align:center; color:#6B7570;">No pages yet. Click <b>Add page</b> to write the first one.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<style>
    .cms-faqbox { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 12px 16px; }
    .cms-faqbox b { display: block; color: #123A2E; font-size: 15px; }
    .cms-faqbox small { display: block; color: #6B7570; font-size: 12.5px; margin-top: 2px; }
    .cms-faqbox .cms-card__icon { width: 40px; height: 40px; font-size: 18px; }
    /* Phones: stack the FAQ box, and show each page as its own card instead of a 5-column sideways table */
    @media (max-width: 767.98px) {
        .cms-note { font-size: 13px; padding: 12px 14px; }
        .cms-bar > .cms-faqbox { width: 100%; flex-wrap: wrap; box-sizing: border-box; }
        .cms-faqbox > div { flex: 1 1 0; min-width: 0; }
        .cms-faqbox .cms-btn { width: 100%; justify-content: center; }
        .cms-table-wrap:has(.cms-table--pages) { background: none; border: 0; border-radius: 0; overflow: visible; }
        .cms-table--pages, .cms-table--pages tbody { display: block; width: 100%; min-width: 0; }
        .cms-table--pages thead { display: none; }
        .cms-table--pages tbody tr { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: start; column-gap: 10px; background: #FBF8F1; border: 1px solid #E3D9C3; border-left: 4px solid #C9974D; border-radius: 12px; padding: 12px 14px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(15,46,36,.06); }
        .cms-table--pages tbody tr:hover td { background: transparent; }
        .cms-table--pages tbody td { display: block; border: 0 !important; padding: 0 !important; width: auto !important; min-width: 0; word-break: break-word; }
        .cms-table--pages td.cms-pg-title { grid-column: 1; grid-row: 1; }
        .cms-table--pages td.cms-pg-title b { font-family: 'Playfair Display', Georgia, serif; font-size: 16px; line-height: 1.3; flex: 1 1 100%; }
        .cms-table--pages td.cms-pg-status { grid-column: 2; grid-row: 1; }
        .cms-table--pages td.cms-pg-addr, .cms-table--pages td.cms-pg-edited { grid-column: 1 / -1; display: flex !important; justify-content: space-between; gap: 10px; padding: 7px 0 !important; border-top: 1px dashed #DCCFB4 !important; }
        .cms-table--pages td.cms-pg-addr { grid-row: 2; margin-top: 10px; }
        .cms-table--pages td.cms-pg-edited { grid-row: 3; text-align: right; }
        .cms-table--pages td[data-label]::before { content: attr(data-label); color: #6B7570; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; flex-shrink: 0; }
        .cms-table--pages td.cms-pg-edited small { display: inline; margin-left: 4px; }
        .cms-table--pages td.cms-pg-act { grid-column: 1 / -1; grid-row: 4; padding-top: 10px !important; border-top: 1px dashed #DCCFB4 !important; }
        .cms-table--pages .cms-row-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(70px, 1fr)); gap: 6px; white-space: normal; }
        .cms-table--pages .cms-row-actions form { display: block; }
        .cms-table--pages .cms-row-actions .cms-mini { width: 100%; min-width: 0; height: 38px; padding: 0 6px !important; }
        .cms-table--pages td[colspan] { display: block; grid-column: 1 / -1; text-align: center; }
    }
</style>
@include('admin.cms.partials.confirm')
@endsection
