{{-- Admin ▸ Content ▸ FAQs ▸ add / edit one question. See CmsAdminController::faqStore()/faqUpdate(). --}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', $faq->exists ? 'Edit FAQ' : 'Add FAQ')
@section('main-content')
@include('admin.cms.partials.styles')

<div class="cms-bar">
    <a class="cms-btn" href="{{ route('admin.cms.faqs') }}"><i class="fa fa-arrow-left"></i> All FAQs</a>
</div>

<form method="POST" action="{{ $faq->exists ? route('admin.cms.faqs.update', $faq) : route('admin.cms.faqs.store') }}" class="cms-form">
    @csrf
    <div class="cms-field">
        <label for="faq_q">Question</label>
        <input type="text" id="faq_q" name="question" value="{{ old('question', $faq->question) }}" maxlength="300" required placeholder="e.g. How much does it cost for a membership?">
        @error('question')<div class="cms-err">{{ $message }}</div>@enderror
    </div>

    <div class="cms-field">
        <label>Answer</label>
        @include('admin.cms.partials.editor', ['name' => 'answer', 'value' => old('answer', $faq->answer), 'height' => 260])
        @error('answer')<div class="cms-err">{{ $message }}</div>@enderror
    </div>

    <div class="cms-savebar">
        <label class="cms-check"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->exists ? $faq->is_active : true) ? 'checked' : '' }}> Show this question on the FAQ page</label>
        <button type="submit" class="cms-btn cms-btn--gold" style="height:44px;"><i class="fa fa-check"></i> {{ $faq->exists ? 'Save FAQ' : 'Add FAQ' }}</button>
    </div>
</form>
@endsection
