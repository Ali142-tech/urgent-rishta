{{--
    Edit an existing proposal — same flat form as team/proposal-create.blade.php,
    pre-filled, submitting to TeamController::update(). Only reachable by the
    proposal's own team member or an admin (see TeamController::edit()).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Edit Proposal')
@section('main-content')
<style>
    .ur-team-form { max-width: 760px; }
    .ur-team-form h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 18px; }
    .ur-team-form .row { margin-bottom: 14px; }
    .ur-team-form label { font-size: 13px; font-weight: 600; color: #1C2321; margin-bottom: 4px; display: block; }
    .ur-team-form .form-control, .ur-team-form select { border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; width: 100%; }
    .ur-team-form .ur-submit-btn { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 28px; border-radius: 999px; background: #C9974D; color: #fff !important; font-size: 14px; font-weight: 700; border: none; }
    .ur-team-form .ur-submit-btn:hover { background: #B07C3D; }
</style>

<div class="ur-team-form">
    <h1>Edit Proposal — {{ $proposal->dataid }}</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $dob = \Carbon\Carbon::parse($proposal->birthday);
    @endphp

    <form method="POST" action="{{ route('team.proposals.update', $proposal->dataid) }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>First Name</label>
                <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $proposal->first_name) }}" required>
            </div>
            <div class="col-md-6">
                <label>Last Name</label>
                <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $proposal->last_name) }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="" disabled>Select</option>
                    <option value="male" {{ old('gender', $proposal->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $proposal->gender) == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Date of Birth</label>
                <div style="display:flex; gap:6px;">
                    <input type="text" class="form-control" name="day" placeholder="DD" maxlength="2" value="{{ old('day', $dob->format('d')) }}" required>
                    <input type="text" class="form-control" name="month" placeholder="MM" maxlength="2" value="{{ old('month', $dob->format('m')) }}" required>
                    <input type="text" class="form-control" name="year" placeholder="YYYY" maxlength="4" value="{{ old('year', $dob->format('Y')) }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <label>Height</label>
                <input type="text" class="form-control" name="height" value="{{ old('height', $proposal->height) }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email', $proposal->email) }}" required>
            </div>
            <div class="col-md-6">
                <label>Contact Number</label>
                <input type="text" class="form-control" name="contact_mobile_number" value="{{ old('contact_mobile_number', $proposal->contact_mobile_number) }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Country</label>
                <select name="country" class="form-control">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ old('country', $proposal->con_of_residence) == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>State</label>
                <input type="text" class="form-control" name="state" value="{{ old('state', $proposal->state) }}">
            </div>
            <div class="col-md-4">
                <label>City</label>
                <input type="text" class="form-control" name="city" value="{{ old('city', $proposal->city) }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Religion</label>
                <select name="religion" class="form-control">
                    <option value="">Select</option>
                    @foreach($religions as $religion)
                        <option value="{{ $religion->dataid }}" {{ old('religion', $proposal->religion) == $religion->dataid ? 'selected' : '' }}>{{ $religion->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Caste</label>
                <select name="caste" class="form-control">
                    <option value="">Select</option>
                    @foreach($caste as $cst)
                        <option value="{{ $cst->dataid }}" {{ old('caste', $proposal->caste) == $cst->dataid ? 'selected' : '' }}>{{ $cst->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Sect</label>
                <input type="text" class="form-control" name="sect" value="{{ old('sect', $proposal->sect) }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Marital Status</label>
                <select name="marital_status" class="form-control">
                    <option value="">Select</option>
                    @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ old('marital_status', $proposal->marital_status) == $maritalstatus->dataid ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Mother Tongue</label>
                <select name="mother_tongue" class="form-control">
                    <option value="">Select</option>
                    @foreach($mothertongues as $mothertongue)
                        <option value="{{ $mothertongue->dataid }}" {{ old('mother_tongue', $proposal->mother_tongue) == $mothertongue->dataid ? 'selected' : '' }}>{{ $mothertongue->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Profile For</label>
                <input type="text" class="form-control" name="profile_for" value="{{ old('profile_for', $proposal->profile_for) }}" placeholder="Self / Son / Daughter / etc.">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Education</label>
                <select name="education" class="form-control">
                    <option value="">Select</option>
                    @foreach($education as $degree)
                        <option value="{{ $degree->dataid }}" {{ old('education', $proposal->education) == $degree->dataid ? 'selected' : '' }}>{{ $degree->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Profession</label>
                <input type="text" class="form-control" name="profession" value="{{ old('profession', $proposal->profession) }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Citizenship Country</label>
                <select name="con_of_citizenship" class="form-control">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ old('con_of_citizenship', $proposal->con_of_citizenship) == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Visa / Residence Status</label>
                <input type="text" class="form-control" name="immigration_status" value="{{ old('immigration_status', $proposal->immigration_status) }}" placeholder="e.g. Citizen, Work Visa, Green Card">
            </div>
            <div class="col-md-4">
                <label>Income</label>
                <input type="text" class="form-control" name="income" value="{{ old('income', $proposal->income) }}" placeholder="Optional">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Family Residence</label>
                <select name="family_residence" class="form-control">
                    <option value="">Select</option>
                    <option {{ old('family_residence', $proposal->family_residence) == 'Rent' ? 'selected' : '' }}>Rent</option>
                    <option {{ old('family_residence', $proposal->family_residence) == 'Own' ? 'selected' : '' }}>Own</option>
                    <option {{ old('family_residence', $proposal->family_residence) == 'Do not know' ? 'selected' : '' }}>Do not know</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Family Values</label>
                <select name="family_values" class="form-control">
                    <option value="">Select</option>
                    <option value="Traditional" {{ old('family_values', $proposal->family_values) == 'Traditional' ? 'selected' : '' }}>Traditional</option>
                    <option value="Moderate" {{ old('family_values', $proposal->family_values) == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                    <option value="Liberal" {{ old('family_values', $proposal->family_values) == 'Liberal' ? 'selected' : '' }}>Liberal</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Property / Financial Status</label>
                <input type="text" class="form-control" name="property_financial_status" value="{{ old('property_financial_status', $proposal->property_financial_status) }}" placeholder="Optional">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Profile Description</label>
                <textarea class="form-control" name="profile_description" rows="4" style="height:auto;">{{ old('profile_description', $proposal->profile_description) }}</textarea>
            </div>
        </div>

        <h1 style="font-size:18px; margin-top:24px;">Partner Requirements <small style="font-weight:400; font-size:13px; color:#6B7570;">— used to find AI Matches for this proposal</small></h1>
        <div class="row">
            <div class="col-md-4">
                <label>Preferred Age</label>
                <div style="display:flex; gap:6px;">
                    <input type="number" class="form-control" name="pref_age_min" placeholder="Min" min="18" max="99" value="{{ old('pref_age_min', optional($preference)->age_min) }}">
                    <input type="number" class="form-control" name="pref_age_max" placeholder="Max" min="18" max="99" value="{{ old('pref_age_max', optional($preference)->age_max) }}">
                </div>
            </div>
            <div class="col-md-4">
                <label>Preferred Country</label>
                <select name="pref_country" class="form-control">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ old('pref_country', optional($preference)->country_id) == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Preferred Marital Status</label>
                <select name="pref_marital_status" class="form-control">
                    <option value="">Select</option>
                    @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ old('pref_marital_status', optional($preference)->marital_status) == $maritalstatus->dataid ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Preferred Education</label>
                <select name="pref_education" class="form-control">
                    <option value="">Select</option>
                    @foreach($education as $degree)
                        <option value="{{ $degree->dataid }}" {{ old('pref_education', optional($preference)->education_id) == $degree->dataid ? 'selected' : '' }}>{{ $degree->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Preferred Profession</label>
                <input type="text" class="form-control" name="pref_profession" value="{{ old('pref_profession', optional($preference)->profession) }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Partner Requirements</label>
                <textarea class="form-control" name="partner_requirements" rows="3" style="height:auto;" placeholder="Any other requirements for a suitable match...">{{ old('partner_requirements', optional($preference)->general_requirement) }}</textarea>
            </div>
        </div>

        <button type="submit" class="ur-submit-btn"><i class="fa fa-check"></i> Save Changes</button>
        <a href="{{ route('team.proposals.photos', $proposal->dataid) }}" class="ur-submit-btn" style="background:#123A2E;"><i class="fa fa-camera"></i> Manage Photos</a>
    </form>
</div>
@endsection
