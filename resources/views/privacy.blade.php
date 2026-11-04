{{-- Privacy Policy — the text is written in Admin ▸ Content ▸ Privacy Policy (cms_pages, slug "privacy"). --}}
@extends('layouts.master')

@section('main-content')
<style>
    .cms-body { font-size: 15px; line-height: 1.75; color: #3C4642; }
    .cms-body h1, .cms-body h2, .cms-body h3, .cms-body h4 { color: #123A2E; margin: 22px 0 10px; }
    .cms-body p { margin: 0 0 14px; }
    .cms-body ul, .cms-body ol { padding-left: 24px; margin: 0 0 14px; }
    .cms-body li { margin-bottom: 6px; }
    .cms-body a { color: #123A2E; text-decoration: underline; }
    .cms-body blockquote { border-left: 3px solid #C9974D; margin: 0 0 14px; padding: 4px 0 4px 16px; color: #5B6560; }
</style>
<section class="slice sct-color-1">
    <div class="container">
        <div class="section-title section-title--style-1 text-center mb-4">
            <h3 class="section-title-inner heading-1 strong-300 text-normal">
                {{ $page->title ?? 'Privacy Policy' }} </h3>
            <span class="section-title-delimiter clearfix d-none"></span>
        </div>

        <span class="clearfix"></span>

        <div class="paragraph paragraph-sm c-gray-light strong-300 pb-5 cms-body">
            {!! $page->body ?? '' !!}
        </div>
    </div>
</section>
@endsection
