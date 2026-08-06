@extends('layouts.master')

@section('main-content')
<section class="slice sct-color-1">
    <div class="container">
        <div class="section-title section-title--style-1 text-center mb-4">
            <h3 class="section-title-inner heading-1 strong-300 text-normal">
                Contact Us </h3>
            <span class="section-title-delimiter clearfix d-none"></span>
        </div>

        <span class="clearfix"></span>
        <div class="fluid-paragraph fluid-paragraph-sm c-gray-light strong-300 text-center">
            Contact Us
        </div>

        <span class="space-xs-xl"></span>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Contact form -->
                <form class="form-default" role="form" method="POST" action="{{ url('contact-us') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group has-feedback">
                                <label for="" class="text-uppercase c-gray-light">Your Name</label>
                                <input type="text" name="name" class="form-control form-control-md" required="" value="">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                <label for="" class="text-uppercase c-gray-light">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-md" required="" value="">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group has-feedback">
                                <label for="" class="text-uppercase c-gray-light">Subject</label>
                                <input type="text" name="subject" class="form-control form-control-md" required="" value="">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group has-feedback">
                                <label for="" class="text-uppercase c-gray-light">Message <small class="text-danger sml_txt" style="text-transform: none;">(Max 300 Characters)</small></label>
                                <textarea name="message" class="form-control no-resize" rows="5" required="" maxlength="300"></textarea>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 col-12">
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-styled btn-base-1 mt-4">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-success').fadeOut('fast');
        }, 5000); // <-- time in milliseconds
    });
</script>
@endsection
