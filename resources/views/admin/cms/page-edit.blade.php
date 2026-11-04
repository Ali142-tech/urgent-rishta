{{-- Admin ▸ Content ▸ Pages ▸ create or edit a page. See CmsAdminController::pageStore()/pageUpdate(). --}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', $page->exists ? 'Edit page' : 'Add page')
@section('main-content')
@include('admin.cms.partials.styles')

<div class="cms-bar">
    <a class="cms-btn" href="{{ route('admin.cms') }}"><i class="fa fa-arrow-left"></i> All pages</a>
    @if($page->exists)<a class="cms-btn" href="{{ $page->publicUrl() }}" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> View page on the site</a>@endif
</div>

<form method="POST" action="{{ $page->exists ? route('admin.cms.pages.update', $page) : route('admin.cms.pages.store') }}" class="cms-form">
    @csrf
    <div class="cms-field">
        <label for="cms_title">Page title</label>
        <input type="text" id="cms_title" name="title" value="{{ old('title', $page->title) }}" maxlength="150" required placeholder="e.g. Refund Policy">
        @error('title')<div class="cms-err">{{ $message }}</div>@enderror
    </div>

    <div class="cms-field">
        <label for="cms_slug">Page address</label>
        @if($page->is_system)
            <div style="font-size:14px; color:#4B5651;">{{ $page->publicUrl() }} <span style="color:#8A938E;">(fixed for this page)</span></div>
        @else
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="color:#6B7570; font-size:14px; white-space:nowrap;">{{ url('p') }}/</span>
                <input type="text" id="cms_slug" name="slug" value="{{ old('slug', $page->slug) }}" maxlength="80" placeholder="refund-policy" style="flex:1;">
            </div>
            <div style="font-size:12px; color:#8A938E; margin-top:5px;">Lower-case letters, numbers and hyphens. Leave empty to make it from the title.</div>
            @error('slug')<div class="cms-err">{{ $message }}</div>@enderror
        @endif
    </div>

    <div class="cms-field">
        <label>Page text</label>
        @include('admin.cms.partials.editor', ['name' => 'body', 'value' => old('body', $page->body), 'height' => 520])
        @error('body')<div class="cms-err">{{ $message }}</div>@enderror
    </div>

    <div class="cms-field" style="display:flex; gap:28px; flex-wrap:wrap;">
        <label class="cms-check"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->exists ? $page->is_active : true) ? 'checked' : '' }}> Active &mdash; visitors can open this page</label>
        @unless($page->is_system)
        <label class="cms-check"><input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', $page->show_in_footer) ? 'checked' : '' }}> Show a link in the site footer</label>
        @endunless
    </div>

    <div class="cms-savebar">
        <span style="font-size:12.5px; color:#6B7570;">Saved changes appear on the site straight away.@if($page->updated_at) Last saved {{ $page->updated_at->diffForHumans() }}.@endif</span>
        <button type="submit" class="cms-btn cms-btn--gold" style="height:44px;"><i class="fa fa-check"></i> {{ $page->exists ? 'Save page' : 'Create page' }}</button>
    </div>
</form>
@endsection
