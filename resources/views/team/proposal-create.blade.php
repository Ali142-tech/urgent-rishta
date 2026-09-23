{{--
    Direct-entry "Add Proposal" form for team members — a single flat form,
    no OTP/session wizard (unlike auth/register.blade.php's self-registration
    flow), since a team member is entering a client's proposal on their
    behalf. Submits to TeamController::store(), which stamps `added_by` and
    creates a login-less "shell" record (client confirmed: these proposals
    are never meant to log in themselves).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Add Proposal')
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
    <h1>Add Proposal</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('team.proposals.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>First Name</label>
                <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
            </div>
            <div class="col-md-6">
                <label>Last Name</label>
                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="" disabled selected>Select</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Date of Birth</label>
                <div style="display:flex; gap:6px;">
                    <input type="text" class="form-control" name="day" placeholder="DD" maxlength="2" value="{{ old('day') }}" required>
                    <input type="text" class="form-control" name="month" placeholder="MM" maxlength="2" value="{{ old('month') }}" required>
                    <input type="text" class="form-control" name="year" placeholder="YYYY" maxlength="4" value="{{ old('year') }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <label>Height</label>
                <input type="text" class="form-control" name="height" value="{{ old('height') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6">
                <label>Contact Number</label>
                <input type="text" class="form-control" name="contact_mobile_number" value="{{ old('contact_mobile_number') }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Country</label>
                <select name="country" class="form-control">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ old('country') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>State</label>
                <input type="text" class="form-control" name="state" value="{{ old('state') }}">
            </div>
            <div class="col-md-4">
                <label>City</label>
                <input type="text" class="form-control" name="city" value="{{ old('city') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Religion</label>
                <select name="religion" class="form-control">
                    <option value="">Select</option>
                    @foreach($religions as $religion)
                        <option value="{{ $religion->dataid }}" {{ old('religion') == $religion->dataid ? 'selected' : '' }}>{{ $religion->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Caste</label>
                <select name="caste" class="form-control">
                    <option value="">Select</option>
                    @foreach($caste as $cst)
                        <option value="{{ $cst->dataid }}" {{ old('caste') == $cst->dataid ? 'selected' : '' }}>{{ $cst->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Sect</label>
                <input type="text" class="form-control" name="sect" value="{{ old('sect') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Marital Status</label>
                <select name="marital_status" class="form-control">
                    <option value="">Select</option>
                    @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ old('marital_status') == $maritalstatus->dataid ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Mother Tongue</label>
                <select name="mother_tongue" class="form-control">
                    <option value="">Select</option>
                    @foreach($mothertongues as $mothertongue)
                        <option value="{{ $mothertongue->dataid }}" {{ old('mother_tongue') == $mothertongue->dataid ? 'selected' : '' }}>{{ $mothertongue->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Profile For</label>
                <input type="text" class="form-control" name="profile_for" value="{{ old('profile_for') }}" placeholder="Self / Son / Daughter / etc.">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Education</label>
                <select name="education" class="form-control">
                    <option value="">Select</option>
                    @foreach($education as $degree)
                        <option value="{{ $degree->dataid }}" {{ old('education') == $degree->dataid ? 'selected' : '' }}>{{ $degree->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Profession</label>
                <input type="text" class="form-control" name="profession" value="{{ old('profession') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Citizenship Country</label>
                <select name="con_of_citizenship" class="form-control">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ old('con_of_citizenship') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Visa / Residence Status</label>
                <input type="text" class="form-control" name="immigration_status" value="{{ old('immigration_status') }}" placeholder="e.g. Citizen, Work Visa, Green Card">
            </div>
            <div class="col-md-4">
                <label>Income</label>
                <input type="text" class="form-control" name="income" value="{{ old('income') }}" placeholder="Optional">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Family Residence</label>
                <select name="family_residence" class="form-control">
                    <option value="">Select</option>
                    <option {{ old('family_residence') == 'Rent' ? 'selected' : '' }}>Rent</option>
                    <option {{ old('family_residence') == 'Own' ? 'selected' : '' }}>Own</option>
                    <option {{ old('family_residence') == 'Do not know' ? 'selected' : '' }}>Do not know</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Family Values</label>
                <select name="family_values" class="form-control">
                    <option value="">Select</option>
                    <option value="Traditional" {{ old('family_values') == 'Traditional' ? 'selected' : '' }}>Traditional</option>
                    <option value="Moderate" {{ old('family_values') == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                    <option value="Liberal" {{ old('family_values') == 'Liberal' ? 'selected' : '' }}>Liberal</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Property / Financial Status</label>
                <input type="text" class="form-control" name="property_financial_status" value="{{ old('property_financial_status') }}" placeholder="Optional">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Profile Description</label>
                <textarea class="form-control" name="profile_description" rows="4" style="height:auto;">{{ old('profile_description') }}</textarea>
            </div>
        </div>

        <h1 style="font-size:18px; margin-top:24px;">Partner Requirements <small style="font-weight:400; font-size:13px; color:#6B7570;">— used to find AI Matches for this proposal</small></h1>
        <div class="row">
            <div class="col-md-4">
                <label>Preferred Age</label>
                <div style="display:flex; gap:6px;">
                    <input type="number" class="form-control" name="pref_age_min" placeholder="Min" min="18" max="99" value="{{ old('pref_age_min') }}">
                    <input type="number" class="form-control" name="pref_age_max" placeholder="Max" min="18" max="99" value="{{ old('pref_age_max') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label>Preferred Country</label>
                <select name="pref_country" class="form-control">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ old('pref_country') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Preferred Marital Status</label>
                <select name="pref_marital_status" class="form-control">
                    <option value="">Select</option>
                    @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ old('pref_marital_status') == $maritalstatus->dataid ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
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
                        <option value="{{ $degree->dataid }}" {{ old('pref_education') == $degree->dataid ? 'selected' : '' }}>{{ $degree->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Preferred Profession</label>
                <input type="text" class="form-control" name="pref_profession" value="{{ old('pref_profession') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Partner Requirements</label>
                <textarea class="form-control" name="partner_requirements" rows="3" style="height:auto;" placeholder="Any other requirements for a suitable match...">{{ old('partner_requirements') }}</textarea>
            </div>
        </div>

        <button type="submit" class="ur-submit-btn"><i class="fa fa-plus"></i> Add Proposal</button>
    </form>
</div>
@endsection
