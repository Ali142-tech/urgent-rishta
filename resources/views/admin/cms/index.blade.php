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
    <table class="cms-table">
        <thead><tr><th>Page</th><th style="width:170px;">Address</th><th style="width:100px;">Status</th><th style="width:190px;">Last edited</th><th style="width:290px;">Actions</th></tr></thead>
        <tbody>
            @forelse($pages as $page)
            <tr>
                <td>
                    <div class="cms-title">
                        <b>{{ $page->title }}</b>
                        @if($page->is_system)<span class="cms-pill cms-pill--tag">System</span>@endif
                        @if($page->show_in_footer)<span class="cms-pill cms-pill--tag">In footer</span>@endif
                    </div>
                </td>
                <td style="font-size:12.5px; color:#4B5651;">/{{ ltrim(str_replace(url('/'), '', $page->publicUrl()), '/') }}</td>
                <td><span class="cms-pill {{ $page->is_active ? '' : 'cms-pill--off' }}">{{ $page->is_active ? 'Active' : 'Hidden' }}</span></td>
                <td style="font-size:12.5px; color:#6B7570;">{{ $page->updated_at ? $page->updated_at->diffForHumans() : '—' }}@if($page->editor)<small>by {{ \Illuminate\Support\Str::limit(trim($page->editor->first_name . ' ' . $page->editor->last_name), 22) }}</small>@endif</td>
                <td>
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
</style>
@include('admin.cms.partials.confirm')
@endsection
