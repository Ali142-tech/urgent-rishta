@extends('layouts.master')

@section('main-content')
<style>
    .mobile-input-group { display: flex; }
    .mobile-input-group .mobile-country-prepend { flex: 0 0 auto; width: 120px; max-width: 120px; }
    .mobile-input-group .mobile-country-prepend select,
    .mobile-input-group .mobile-country-prepend .form-control,
    .mobile-input-group .mobile-country-prepend .bootstrap-select,
    .mobile-input-group .mobile-country-prepend .dropdown-toggle { width: 100% !important; max-width: 120px !important; min-width: 0 !important; }
    .mobile-input-group .mobile-number-input { flex: 1 1 auto; min-width: 0; }
</style>
<section class="slice-lg has-bg-cover bg-size-cover" style="background-image: url(images/registration-banner.jpg); background-position: bottom bottom;">
    <span class="mask mask-dark--style-2"></span>
    <div class="container">
        <div class="row cols-xs-space align-items-center text-center text-md-left">
            <div class="col-lg-6 col-md-10 ml-auto mr-auto">
                <div class="form-card form-card--style-2 z-depth-3-top">
                    <div class="form-body">
                        <div class="text-center px-2">
                            <h4 class="heading heading-4 strong-400 mb-4 font_light">Create Your Account</h4>
                            @if(!empty($googleOAuth))
                                <p class="font_light" style="color:#E91E63;font-size:13px;">Signed in with Google — complete your profile below.</p>
                            @endif
                        </div>
                        <form class="form-default mt-4" id="register_form" action="{{route('register')}}" method="post" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">First Name</label><br />
                                        <input type="text" class="form-control form-control-sm" name="first_name" required="required" autofocus value="{{ old('first_name', $googleOAuth['first_name'] ?? '') }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">Last Name</label><br />
                                        <input type="text" class="form-control form-control-sm" name="last_name" required="required" value="{{ old('last_name', $googleOAuth['last_name'] ?? '') }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">Gender</label><br />
                                        <select name="gender" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a gender" data-hide-disabled="true">
                                            <option value="" disabled>Choose one</option>
                                            <option value="male" @if (old('gender') == 'male') selected="selected" @endif>Male</option>
                                            <option value="female" @if (old('gender') == 'female') selected="selected" @endif>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">Email</label><br />
                                        <input type="email" class="form-control form-control-sm" name="email" id="email" required="required" value="{{ old('email', $googleOAuth['email'] ?? '') }}" @if(!empty($googleOAuth['email'])) readonly @endif />
                                        <small id="email_alert" class="lv-alert"></small>
                                        @if(!empty($googleOAuth['email']))
                                            <small class="form-text font_light">Email from Google (locked).</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">WhatsApp/Mobile Number</label><br />
                                        <input id="mobile_full" type="tel" class="form-control form-control-sm" required="required" value="{{ old('mobile_full') }}" />
                                        <input type="hidden" name="mobile_country_code" value="{{ old('mobile_country_code', '92') }}">
                                        <input type="hidden" name="mobile" value="{{ old('mobile') }}">
                                        <small id="mobile-help" class="form-text font_light">
                                            Select your country and enter a valid WhatsApp number. It will be saved with the correct country code (e.g. +92 3331234567 → 923331234567).
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="height" class="control-label font_light">Height</label>
                                        <input type="text" class="form-control form-control-sm" name="height" value="{{ old('height') }}" required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label font_light">Date Of Birth</label><br />
                                        <select name="day" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a day for date of birth" data-hide-disabled="true">
                                            <option value="" disabled>Choose one</option>
                                            @for ($i = 1; $i <= 31; $i++)
                                              <option @if (old('day') == ($i < 10 ? "0".$i : $i)) selected="selected" @endif>{{$i<10?"0".$i:$i}}</option>
                                            @endfor
                                        </select>

                                        <select name="month" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a month for date of birth" data-hide-disabled="true">
                                            <option value="" disabled>Choose one</option>
                                            <option value="01" @if (old('month') == '01') selected="selected" @endif>January</option>
                                            <option value="02" @if (old('month') == '02') selected="selected" @endif>February</option>
                                            <option value="03" @if (old('month') == '03') selected="selected" @endif>March</option>
                                            <option value="04" @if (old('month') == '04') selected="selected" @endif>April</option>
                                            <option value="05" @if (old('month') == '05') selected="selected" @endif>May</option>
                                            <option value="06" @if (old('month') == '06') selected="selected" @endif>June</option>
                                            <option value="07" @if (old('month') == '07') selected="selected" @endif>July</option>
                                            <option value="08" @if (old('month') == '08') selected="selected" @endif>August</option>
                                            <option value="09" @if (old('month') == '09') selected="selected" @endif>September</option>
                                            <option value="10" @if (old('month') == '10') selected="selected" @endif>October</option>
                                            <option value="11" @if (old('month') == '11') selected="selected" @endif>November</option>
                                            <option value="12" @if (old('month') == '12') selected="selected" @endif>December</option>
                                        </select>

                                        <select name="year" style="display: inline; width: auto" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a year for date of birth" data-hide-disabled="true">
                                            <option value="" disabled>Choose one</option>
                                            @for ($i = 2002; $i >= 1927; $i--)
                                            <option @if (old('year') == $i) selected="selected" @endif>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country" class="control-label font_light">Country</label>
                                        <select name="country" onchange="javascript:loadSelect('{{url('cities')}}', this.value+'/1', $('#city'), null);" class="form-control form-control-sm selectpicker" data-placeholder="Choose a country" data-hide-disabled="true" required="required">
                                            <option value="">Choose one</option>
                                            @foreach($countries as $country)
                                            <option value="{{$country->dataid}}" @if (old('country') == $country->dataid) selected="selected" @endif>{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city" class="control-label font_light">City</label>
                                        <select id="city" name="city" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a city" data-hide-disabled="true" required="required">
                                            <option value="">Choose a country first</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">Religion</label><br />
                                        <select name="religion" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a religion" data-hide-disabled="true">
                                            <option value="" disabled>Choose one</option>
                                            @foreach($religions as $religion)
                                            <option value="{{$religion->dataid}}" @if (old('religion') == $religion->dataid) selected="selected" @endif>{{$religion->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="caste" class="control-label font_light">Caste</label>
                                        <select class="form-control form-control-sm selectpicker present_caste_edit" name="caste" required="required">
                                            <option value="">Choose one</option>
                                            @foreach($caste as $cst)
                                            <option value="{{$cst->dataid}}" @if (old('caste') == $cst->dataid) selected="selected" @endif>{{$cst->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sect" class="control-label font_light">Sect</label>
                                        <input type="text" class="form-control form-control-sm" name="sect" value="{{ old('sect') }}" required="required">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">On Behalf</label><br />
                                        <select name="on_behalf" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a on behalf value" data-hide-disabled="true">
                                            <option value="" disabled>Choose one</option>
                                            <option @if (old('on_behalf') == 'Self') selected="selected" @endif>Self</option>
                                            <option @if (old('on_behalf') == 'Son') selected="selected" @endif>Son</option>
                                            <option @if (old('on_behalf') == 'Daughter') selected="selected" @endif>Daughter</option>
                                            <option @if (old('on_behalf') == 'Brother') selected="selected" @endif>Brother</option>
                                            <option @if (old('on_behalf') == 'Sister') selected="selected" @endif>Sister</option>
                                            <option @if (old('on_behalf') == 'Relative') selected="selected" @endif>Relative</option>
                                            <option @if (old('on_behalf') == 'Friend') selected="selected" @endif>Friend</option>
                                        </select> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">Mother Tongue</label><br />
                                        <select name="mother_tongue" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" required="required" data-placeholder="Choose a mother tongue" data-hide-disabled="true">
                                            <option value="" disabled>Choose one</option>
                                            @foreach($mothertongues as $mothertongue)
                                            <option value="{{$mothertongue->dataid}}" @if (old('mother_tongue') == $mothertongue->dataid) selected="selected" @endif>{{$mothertongue->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="marital_status" class="control-label font_light">Marital Status</label>
                                        <select name="marital_status" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a marital status" data-hide-disabled="true" required="required">
                                            <option value="">Choose one</option>
                                            @foreach($maritalstatuses as $maritalstatus)
                                            <option value="{{$maritalstatus->dataid}}" @if (old('marital_status') == $maritalstatus->dataid) selected="selected" @endif>{{$maritalstatus->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="education" class="control-label font_light">Education</label>
                                        <select name="education" onChange="(this.value,this)" class="form-control form-control-sm selectpicker" data-placeholder="Choose a degree" data-hide-disabled="true" required="required">
                                            <option value="">Choose one</option>
                                            @foreach($education as $degree)
                                            <option value="{{$degree->dataid}}" @if (old('education') == $degree->dataid) selected="selected" @endif>{{$degree->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profession" class="control-label font_light">Profession</label>
                                        <input type="text" class="form-control form-control-sm" name="profession" value="{{ old('profession') }}" required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">Password</label><br />
                                        <input type="password" class="form-control form-control-sm" name="password" id="password" required="required" minlength="8" value="{{ old('password') }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font_light">Confirm Password </label><br />
                                        <input type="password" class="form-control form-control-sm" name="confirm_password" id="password1" required="required" minlength="8" value="{{ old('confirm_password') }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mt-1 col-12">
                                    <small class="c-gray-light">By Clicking CREATE ACCOUNT You Agree To Our <a href="{{url('tandc')}}" class="c-gray-light" target="_blank"><u>Terms And Conditions</u></a></small>
                                    <div class="mt-2" style="color: #ccc !important">
                                    </div>
                                    <style>
                                        p {
                                            margin: 0px;
                                            font-size: 12px;
                                            color: red;
                                        }
                                    </style>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" id="btnSubmit" class="btn btn-styled btn-sm btn-base-1 z-depth-2-bottom mt-2" style="width: 100%">CREATE ACCOUNT</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- International Telephone Input for full country code list -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var form = $("#register_form");
        var button = $("#btnSubmit");

        form.on('submit', function(e) {
            button.prop('disabled', true);
            var parent = button.parent();
            parent.html("<i class='fa fa-refresh fa-spin'></i> Processing..");
            checkEmail($("#email"));
            checkPassword();
        });

        $("#email").on('blur', function() {
            checkEmail($(this));
        });

        $("#password1").on('input', function() {
            checkPassword();
        });

        // Initialize intl-tel-input for full country code list
        var input = document.querySelector("#mobile_full");
        if (input && window.intlTelInput) {
            var iti = window.intlTelInput(input, {
                initialCountry: "pk",
                separateDialCode: true,
                nationalMode: true
            });

            form.on('submit', function() {
                var countryData = iti.getSelectedCountryData();
                var dialCode = countryData.dialCode || '';
                var nationalNumber = input.value.replace(/\D+/g, '');

                $('input[name="mobile_country_code"]').val(dialCode);
                $('input[name="mobile"]').val(nationalNumber);
            });
        }
    });

    function checkPassword() {
        var pass = $("#password");
        var pass1 = $("#password1");
        if (pass.val() != pass1.val())
            pass1[0].setCustomValidity("Passwords do not match");
        else pass1[0].setCustomValidity('');
    }

    function checkEmail(input) {
        var email = input.val();
        $.ajax({
            type: "POST",
            url: "/eiu",
            cache: false,
            data: {
                'e': email
            },
            success: function(response) {
                var emailAlert = $("#email_alert");
                if (response.data == 1) {
                    emailAlert.removeClass("alert-success");
                    emailAlert.addClass("alert-danger");
                    emailAlert.html("Email is already in use. Please use a different email address.");
                    input.val('');
                    input.focus();
                } else {
                    emailAlert.removeClass("alert-danger");
                    emailAlert.addClass("alert-success");
                    emailAlert.html("Email is available for use.");
                }
            },
            fail: function(error) {
                errorAlert(error);
                input.val('');
            }
        });
    }
</script>
@endsection
