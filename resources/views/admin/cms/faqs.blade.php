{{-- Admin ▸ Content ▸ FAQs — list with add / edit / delete / hide / reorder. See CmsAdminController. --}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'FAQs')
@section('main-content')
@include('admin.cms.partials.styles')

<div class="cms-bar">
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="cms-btn" href="{{ route('admin.cms') }}"><i class="fa fa-arrow-left"></i> All content</a>
        <a class="cms-btn" href="{{ url('faqs') }}" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> View FAQ page</a>
    </div>
    <a class="cms-btn cms-btn--gold" href="{{ route('admin.cms.faqs.create') }}"><i class="fa fa-plus"></i> Add FAQ</a>
</div>

<div class="cms-table-wrap">
    <table class="cms-table">
        <thead><tr><th style="width:52px;">#</th><th>Question</th><th style="width:110px;">On the site</th><th style="width:330px;">Actions</th></tr></thead>
        <tbody>
            @forelse($faqs as $i => $faq)
            <tr>
                <td class="cms-num">{{ $i + 1 }}</td>
                <td class="cms-qa"><b>{{ $faq->question }}</b><small>{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 110) }}</small></td>
                <td><span class="cms-pill {{ $faq->is_active ? '' : 'cms-pill--off' }}">{{ $faq->is_active ? 'Shown' : 'Hidden' }}</span></td>
                <td>
                    <div class="cms-row-actions">
                        <form method="POST" action="{{ route('admin.cms.faqs.move', [$faq, 'up']) }}">@csrf<button class="cms-mini cms-mini--icon" title="Move up" {{ $loop->first ? 'disabled' : '' }}><i class="fa fa-arrow-up"></i></button></form>
                        <form method="POST" action="{{ route('admin.cms.faqs.move', [$faq, 'down']) }}">@csrf<button class="cms-mini cms-mini--icon" title="Move down" {{ $loop->last ? 'disabled' : '' }}><i class="fa fa-arrow-down"></i></button></form>
                        <a class="cms-mini" href="{{ route('admin.cms.faqs.edit', $faq) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.cms.faqs.toggle', $faq) }}">@csrf<button class="cms-mini">{{ $faq->is_active ? 'Hide' : 'Show' }}</button></form>
                        <form method="POST" action="{{ route('admin.cms.faqs.delete', $faq) }}" class="js-confirm" data-title="Delete this FAQ?" data-text="&quot;{{ e(\Illuminate\Support\Str::limit($faq->question, 90)) }}&quot; will be removed from the FAQ page. You cannot undo this. Use Hide instead if you may need it again." data-button="Yes, delete FAQ">@csrf<button class="cms-mini cms-mini--danger">Delete</button></form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:30px; text-align:center; color:#6B7570;">No FAQs yet. Click <b>Add FAQ</b> to write the first one.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@include('admin.cms.partials.confirm')
@endsection
