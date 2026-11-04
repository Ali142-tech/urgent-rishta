@extends('layouts.dashboard')
@section('dashboard-title', 'Partner Preferences')
@push('styles')
<link rel="stylesheet" href="/css/ur-preferences.css?v={{ filemtime(public_path('css/ur-preferences.css')) }}">
@endpush
@section('main-content')

<div class="ur-prefs">
    <div class="ur-prefs__card">
        <h2 class="ur-prefs__title">Partner Preferences</h2>
        <p class="ur-prefs__sub">Tell us what you're looking for in a match — this isn't shown on your own profile, it's used to help find and filter better matches for you.</p>

        <form id="preferences_form" role="form" method="post" action="{{ route('member.preferences.update') }}">
            @csrf

            <div class="ur-prefs__section-label">Basic Preferences</div>
            <div class="ur-prefs__row">
                <div class="ur-prefs__field">
                    <label for="age_min">Age From</label>
                    <input type="number" min="18" max="99" class="form-control" id="age_min" name="age_min" value="{{ $preference->age_min ?? '' }}">
                </div>
                <div class="ur-prefs__field">
                    <label for="age_max">Age To</label>
                    <input type="number" min="18" max="99" class="form-control" id="age_max" name="age_max" value="{{ $preference->age_max ?? '' }}">
                </div>
            </div>
            <div class="ur-prefs__row">
                <div class="ur-prefs__field">
                    <label for="height">Height</label>
                    <input type="text" class="form-control" id="height" name="height" placeholder="e.g. 5'4&quot; and above" value="{{ $preference->height ?? '' }}">
                </div>
                <div class="ur-prefs__field">
                    <label for="weight">Weight</label>
                    <input type="text" class="form-control" id="weight" name="weight" value="{{ $preference->weight ?? '' }}">
                </div>
            </div>
            <div class="ur-prefs__row">
                <div class="ur-prefs__field">
                    <label for="marital_status">Marital Status</label>
                    <select class="form-control selectpicker" id="marital_status" name="marital_status" data-placeholder="Choose a marital status" data-hide-disabled="true">
                        <option value="">Any</option>
                        @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ ($preference->marital_status ?? '') == $maritalstatus->dataid ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ur-prefs__field">
                    <label for="with_children">With Children Acceptable?</label>
                    <select class="form-control selectpicker" id="with_children" name="with_children" data-placeholder="Choose an option" data-hide-disabled="true">
                        <option value="">Any</option>
                        <option value="Yes" {{ ($preference->with_children ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ ($preference->with_children ?? '') == 'No' ? 'selected' : '' }}>No</option>
                        <option value="Does not matter" {{ ($preference->with_children ?? '') == 'Does not matter' ? 'selected' : '' }}>Does not matter</option>
                    </select>
                </div>
            </div>

            <div class="ur-prefs__section-label">Location</div>
            <div class="ur-prefs__row">
                <div class="ur-prefs__field">
                    <label for="country_id">Country Of Residence</label>
                    <select class="form-control selectpicker" id="country_id" name="country_id" data-placeholder="Choose a country" data-hide-disabled="true" onchange="javascript:loadSelect('{{ url('states') }}', this.value, $('#state_id'), '{{ $preference->state_id ?? '' }}'); loadSelect('{{ url('cities') }}', this.value+'/1', $('#city_id'), '{{ $preference->city_id ?? '' }}');">
                        <option value="">Any</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ ($preference->country_id ?? '') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ur-prefs__field">
                    <label for="state_id">State</label>
                    <select class="form-control selectpicker" id="state_id" name="state_id" data-placeholder="Choose a state" data-hide-disabled="true">
                        <option value="">{{ ($preference->country_id ?? '') ? 'Any' : 'Choose a country first' }}</option>
                    </select>
                </div>
            </div>
            <div class="ur-prefs__row ur-prefs__row--single">
                <div class="ur-prefs__field">
                    <label for="city_id">City</label>
                    <select class="form-control selectpicker" id="city_id" name="city_id" data-placeholder="Choose a city" data-hide-disabled="true">
                        <option value="">{{ ($preference->country_id ?? '') ? 'Any' : 'Choose a country first' }}</option>
                    </select>
                </div>
            </div>

            <div class="ur-prefs__section-label">Religion &amp; Background</div>
            <div class="ur-prefs__row">
                <div class="ur-prefs__field">
                    <label for="religion_id">Religion</label>
                    <select class="form-control selectpicker" id="religion_id" name="religion_id" data-placeholder="Choose a religion" data-hide-disabled="true">
                        <option value="">Any</option>
                        @foreach($religions as $religion)
                        <option value="{{ $religion->dataid }}" {{ ($preference->religion_id ?? '') == $religion->dataid ? 'selected' : '' }}>{{ $religion->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ur-prefs__field">
                    <label for="caste_id">Caste</label>
                    <select class="form-control selectpicker" id="caste_id" name="caste_id" data-placeholder="Choose a caste" data-hide-disabled="true">
                        <option value="">Any</option>
                        @foreach($caste as $cst)
                        <option value="{{ $cst->dataid }}" {{ ($preference->caste_id ?? '') == $cst->dataid ? 'selected' : '' }}>{{ $cst->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="ur-prefs__row">
                <div class="ur-prefs__field">
                    <label for="sect">Sect</label>
                    <input type="text" class="form-control" id="sect" name="sect" value="{{ $preference->sect ?? '' }}">
                </div>
                <div class="ur-prefs__field">
                    <label for="mother_tongue_id">Mother Tongue</label>
                    <select class="form-control selectpicker" id="mother_tongue_id" name="mother_tongue_id" data-placeholder="Choose a mother tongue" data-hide-disabled="true">
                        <option value="">Any</option>
                        @foreach($mothertongues as $mothertongue)
                        <option value="{{ $mothertongue->dataid }}" {{ ($preference->mother_tongue_id ?? '') == $mothertongue->dataid ? 'selected' : '' }}>{{ $mothertongue->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="ur-prefs__row ur-prefs__row--single">
                <div class="ur-prefs__field">
                    <label for="preferred_languages">Language(s) Spoken</label>
                    <select class="form-control selectpicker" id="preferred_languages" name="preferred_languages[]" multiple data-placeholder="Choose one or more languages">
                        @php $selectedLanguages = !empty($preference->languages) ? explode(',', $preference->languages) : []; @endphp
                        @foreach($mothertongues as $lang)
                        <option value="{{ $lang->dataid }}" {{ in_array($lang->dataid, $selectedLanguages) ? 'selected' : '' }}>{{ $lang->name }}</option>
                        @endforeach
                    </select>
                    <div class="ur-prefs__hint">Separate from Mother Tongue above — pick every language you'd be comfortable communicating in.</div>
                </div>
            </div>

            <div class="ur-prefs__section-label">Education, Career &amp; More</div>
            <div class="ur-prefs__row">
                <div class="ur-prefs__field">
                    <label for="education_id">Minimum Education</label>
                    <select class="form-control selectpicker" id="education_id" name="education_id" data-placeholder="Choose an education level" data-hide-disabled="true">
                        <option value="">Any</option>
                        @foreach($education as $degree)
                        <option value="{{ $degree->dataid }}" {{ ($preference->education_id ?? '') == $degree->dataid ? 'selected' : '' }}>{{ $degree->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ur-prefs__field">
                    <label for="profession">Profession / Occupation</label>
                    <input type="text" class="form-control" id="profession" name="profession" value="{{ $preference->profession ?? '' }}">
                </div>
            </div>
            <div class="ur-prefs__row ur-prefs__row--single">
                <div class="ur-prefs__field">
                    <label for="preferred_country_id">Preferred Country (if different from residence)</label>
                    <select class="form-control selectpicker" id="preferred_country_id" name="preferred_country_id" data-placeholder="Choose a country" data-hide-disabled="true">
                        <option value="">Any</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ ($preference->preferred_country_id ?? '') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="ur-prefs__row ur-prefs__row--single">
                <div class="ur-prefs__field">
                    <label for="general_requirement">Any Other Requirements / Preferences</label>
                    <textarea class="form-control" id="general_requirement" name="general_requirement" rows="3">{{ $preference->general_requirement ?? '' }}</textarea>
                </div>
            </div>

            <button type="submit" class="ur-prefs__submit">Save Preferences</button>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        @if(!empty($preference) && !empty($preference->country_id))
        loadSelect('{{ url('states') }}', '{{ $preference->country_id }}', $('#state_id'), '{{ $preference->state_id ?? '' }}');
        loadSelect('{{ url('cities') }}', '{{ $preference->country_id }}/1', $('#city_id'), '{{ $preference->city_id ?? '' }}');
        @endif
    });
</script>
@endsection
