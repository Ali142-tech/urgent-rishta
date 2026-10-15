<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Admin-managed content: the Terms & Conditions and Privacy Policy pages (cms_pages) and the FAQ list (faqs). The text that is
 * on the site today is copied in once, from the existing pages, so nothing disappears when the pages start reading from here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('title', 150);
            $table->longText('body')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 300);
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $this->importExistingContent();
    }

    private function importExistingContent(): void
    {
        $now = now();
        // The text as it was on the old static pages (kept in database/seed-content, so the import works whether or not the page files have been replaced yet).
        $view = fn (string $name) => is_file(database_path("seed-content/$name.blade.php")) ? file_get_contents(database_path("seed-content/$name.blade.php")) : '';

        // the text block of the page: from the "paragraph" div up to the closing tag of that same div
        $inner = function (string $html): string {
            $open = '<div class="paragraph paragraph-sm c-gray-light strong-300 pb-5">';
            $start = strpos($html, $open);
            if ($start === false) {
                return '';
            }
            $start += strlen($open);
            $end = strpos($html, '</section>', $start);          // </div> (paragraph) </div> (container) </section>
            $chunk = substr($html, $start, $end - $start);
            $chunk = substr($chunk, 0, strrpos($chunk, '</div>'));   // container
            $chunk = substr($chunk, 0, strrpos($chunk, '</div>'));   // paragraph

            return trim($chunk);
        };

        $privacy = trim(preg_replace('#^\s*<p></p>\s*<div></div>#', '', $inner($view('privacy'))));
        $privacy = trim(preg_replace('#<p></p>\s*$#', '', $privacy));
        $terms = $inner($view('tandc'));
        // the old page typed "1.", "2." ... into each point; as an admin-editable numbered list the numbers come from the list itself
        if (preg_match_all('#<li[^>]*>(.*?)</li>#s', $terms, $points)) {
            $terms = '<ol>' . implode('', array_map(fn ($li) => '<li>' . preg_replace('/^\s*\d+\.\s*/', '', trim($li)) . '</li>', $points[1])) . '</ol>';
        }

        foreach ([['privacy', 'Privacy Policy', $privacy], ['terms', 'Terms & Conditions', $terms]] as [$slug, $title, $body]) {
            DB::table('cms_pages')->insert(['slug' => $slug, 'title' => $title, 'body' => $body, 'created_at' => $now, 'updated_at' => $now]);
        }

        if (preg_match_all('#<a class="accordion-toggle[^>]*>.*?</span>\s*(.*?)</a>.*?<div class="card-body">\s*(.*?)\s*</div>\s*</div>\s*</div>#s', $view('faqs'), $m, PREG_SET_ORDER)) {
            foreach ($m as $i => $row) {
                DB::table('faqs')->insert([
                    'question' => trim(strip_tags($row[1])), 'answer' => trim($row[2]), 'sort_order' => ($i + 1) * 10,
                    'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('cms_pages');
    }
};
