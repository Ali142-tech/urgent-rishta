{{-- FAQ page — the questions are written in Admin ▸ Content ▸ FAQs (faqs table); only those marked "shown" appear. --}}
@extends('layouts.master')

@section('main-content')
<section class="slice sct-color-1">
    <div class="container">
        <div class="section-title section-title--style-1 text-center mb-4">
            <h3 class="section-title-inner heading-1 strong-300 text-normal">
                Frequently Asked Questions </h3>
            <span class="section-title-delimiter clearfix d-none"></span>
        </div>

        <span class="clearfix"></span>

        <div class="paragraph paragraph-sm c-gray-light strong-300 pb-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="accordion accordion--style-4" id="collapseFive">
                        @forelse($faqs as $faq)
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a class="accordion-toggle {{ $loop->first ? '' : 'collapsed' }}" data-toggle="collapse" data-parent="#collapseFive" href="#collapseFive-{{ $faq->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                        <span class="collapse-heading-icon">
                                            <i class="fa">{{ $loop->iteration }}</i>
                                        </span>
                                        {{ $faq->question }}</a>
                                </h4>
                            </div>
                            <div id="collapseFive-{{ $faq->id }}" class="panel-collapse collapse {{ $loop->first ? 'show' : '' }}" style="border: 1px solid #e0eded">
                                <div class="card-body">
                                    {!! $faq->answer !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        @empty
                        <p class="text-center">No questions have been added yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
