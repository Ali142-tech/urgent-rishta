{{--
    Direct-entry "Add Proposal" form for team members — a single flat form,
    no OTP/session wizard (unlike auth/register.blade.php's self-registration
    flow), since a team member is entering a client's proposal on their
    behalf. Submits to TeamController::store(), which stamps `added_by` and
    creates a login-less "shell" record (client confirmed: these proposals
    are never meant to log in themselves).

    2026-09: client sends a fixed WhatsApp template to prospective clients
    to fill in and send back — this form now mirrors that template's own
    section order/labels, and a "Paste client details" box at the top can
    parse that whole block and auto-fill the fields below (still fully
    editable before saving — the parser is a convenience, not a black box).
    "Your Requirements" only has the 5 fields the client's template lists
    (Age Limit/Height/City/Caste/Qualification) — this deliberately drops
    Preferred Country/Marital Status/Education from the old form, which
    used to feed AI Matches' hard filtering; new proposals added here only
    get AI-matched by age range now (client's explicit call).
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Add Proposal')
@section('main-content')
<style>
    .ur-team-form { max-width: 820px; }
    .ur-team-form h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 18px; }
    .ur-team-form h2 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 16px; margin: 26px 0 4px; display: flex; align-items: center; gap: 8px; }
    .ur-team-form h2 small { font-weight: 400; font-size: 12.5px; color: #6B7570; }
    .ur-team-form .row { margin-bottom: 14px; }
    .ur-team-form label { font-size: 13px; font-weight: 600; color: #1C2321; margin-bottom: 4px; display: block; }
    .ur-team-form label .ur-opt { font-weight: 400; font-size: 11px; color: #9AA5A0; }
    .ur-team-form .form-control, .ur-team-form select { border: 1px solid #E7E2D6; border-radius: 8px; height: 42px; padding: 0 12px; width: 100%; }
    .ur-team-form textarea.form-control { height: auto; padding: 10px 12px; }
    .ur-team-form .ur-submit-btn { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 28px; border-radius: 999px; background: #C9974D; color: #fff !important; font-size: 14px; font-weight: 700; border: none; }
    .ur-team-form .ur-submit-btn:hover { background: #B07C3D; }

    .ur-paste-box { background: #FCF6EA; border: 1px solid #E9D2A0; border-radius: 12px; padding: 18px; margin-bottom: 24px; }
    .ur-paste-box h3 { font-size: 14px; font-weight: 700; color: #123A2E; margin: 0 0 6px; display: flex; align-items: center; gap: 8px; }
    .ur-paste-box p { font-size: 12.5px; color: #6B7570; margin: 0 0 10px; }
    .ur-paste-box textarea { width: 100%; border: 1px solid #E9D2A0; border-radius: 8px; padding: 10px 12px; font-size: 12.5px; font-family: monospace; height: 130px; }
    .ur-paste-box__actions { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
    .ur-paste-box__fill-btn { background: #123A2E; color: #fff; border: none; border-radius: 999px; padding: 9px 18px; font-size: 12.5px; font-weight: 700; cursor: pointer; }
    .ur-paste-box__fill-btn:hover { background: #0F2E24; }
    .ur-paste-box__status { font-size: 12px; color: #2E7D5B; font-weight: 600; }

    .ur-photo-slots { display: flex; gap: 16px; flex-wrap: wrap; }
    .ur-photo-slot { width: 140px; }
    .ur-photo-slot label { font-size: 12px; }
    .ur-photo-slot__drop { border: 1.5px dashed #E7E2D6; border-radius: 10px; height: 140px; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 11.5px; color: #9AA5A0; background: #F6F4EF; overflow: hidden; position: relative; }
    .ur-photo-slot__drop img { width: 100%; height: 100%; object-fit: cover; }
    .ur-photo-slot input[type=file] { margin-top: 8px; font-size: 11px; }
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

    <div class="ur-paste-box">
        <h3><i class="fa fa-whatsapp" style="color:#C9974D;"></i> Step 1 — Send this template to the client</h3>
        <p>Copy this and send it to the prospective client so they can fill it in and send it back to you.</p>
        <textarea id="template_box" readonly>{{ $sendTemplate }}</textarea>
        <div class="ur-paste-box__actions">
            <button type="button" class="ur-paste-box__fill-btn" onclick="urCopyTemplate(event)"><i class="fa fa-copy"></i> Copy template</button>
        </div>
    </div>

    <div class="ur-paste-box">
        <h3><i class="fa fa-clipboard" style="color:#C9974D;"></i> Step 2 — Paste their reply here</h3>
        <p>Paste the filled-in template the client sent back — this reads it and fills in the fields below automatically. Review everything before saving; nothing is submitted until you click "Add Proposal".</p>
        <textarea id="paste_box" placeholder="PERSONAL INFORMATION&#10;Gender: ...&#10;Age: ...&#10;Marital Status: ...&#10;..."></textarea>
        <div class="ur-paste-box__actions">
            <button type="button" class="ur-paste-box__fill-btn" id="paste_fill_btn"><i class="fa fa-magic"></i> Fill fields from this text</button>
            <span class="ur-paste-box__status" id="paste_status"></span>
        </div>
    </div>

    <form method="POST" action="{{ route('team.proposals.store') }}" enctype="multipart/form-data" id="af_form">
        @csrf

        <h2>1. Personal Information</h2>
        <div class="row">
            <div class="col-md-6">
                <label>First Name <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}">
            </div>
            <div class="col-md-6">
                <label>Last Name <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="" disabled selected>Select</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-5">
                <label>Date of Birth</label>
                <div style="display:flex; gap:6px;">
                    <input type="text" class="form-control" name="day" placeholder="DD" maxlength="2" value="{{ old('day') }}" required>
                    <input type="text" class="form-control" name="month" placeholder="MM" maxlength="2" value="{{ old('month') }}" required>
                    <input type="text" class="form-control" name="year" placeholder="YYYY" maxlength="4" value="{{ old('year') }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <label>Height</label>
                <input type="text" class="form-control" name="height" value="{{ old('height') }}" placeholder="e.g. 5'8">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Marital Status</label>
                <select name="marital_status" class="form-control">
                    <option value="">Select</option>
                    @foreach($maritalstatuses as $maritalstatus)
                        <option value="{{ $maritalstatus->dataid }}" {{ old('marital_status') == $maritalstatus->dataid ? 'selected' : '' }}>{{ $maritalstatus->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Email <span class="ur-opt">Optional</span></label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Contact Number <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="contact_mobile_number" value="{{ old('contact_mobile_number') }}">
            </div>
            <div class="col-md-6">
                <label>Profile For <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="profile_for" value="{{ old('profile_for') }}" placeholder="Self / Son / Daughter / etc.">
            </div>
        </div>

        <h2>2. Education Details</h2>
        <div class="row">
            <div class="col-md-6">
                <label>Qualification</label>
                <select name="education" class="form-control">
                    <option value="">Select</option>
                    @foreach($education as $degree)
                        <option value="{{ $degree->dataid }}" {{ old('education') == $degree->dataid ? 'selected' : '' }}>{{ $degree->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>College / University <span class="ur-opt">Optional</span></label>
                <textarea class="form-control" name="college_university" rows="2" placeholder="e.g. Government Medical College, Punjab — USMLE Step 1 & 2 clear">{{ old('college_university') }}</textarea>
            </div>
        </div>

        <h2>3. Occupation Detail</h2>
        <div class="row">
            <div class="col-md-6">
                <label>Job / Business</label>
                <input type="text" class="form-control" name="profession" value="{{ old('profession') }}" placeholder="e.g. Doctor">
            </div>
            <div class="col-md-6">
                <label>Income <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="income" value="{{ old('income') }}">
            </div>
        </div>

        <h2>4. Religion Details</h2>
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
                <label>Sect <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="sect" value="{{ old('sect') }}" placeholder="e.g. Sunni">
            </div>
            <div class="col-md-4">
                <label>Caste <span class="ur-opt">Optional</span></label>
                <select name="caste" class="form-control">
                    <option value="">Select</option>
                    @foreach($caste as $cst)
                        <option value="{{ $cst->dataid }}" {{ old('caste') == $cst->dataid ? 'selected' : '' }}>{{ $cst->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <h2>5. Residence Details</h2>
        <div class="row">
            <div class="col-md-4">
                <label>Home Own / On Rent <span class="ur-opt">Optional</span></label>
                <select name="family_residence" class="form-control">
                    <option value="">Select</option>
                    <option {{ old('family_residence') == 'Rent' ? 'selected' : '' }}>Rent</option>
                    <option {{ old('family_residence') == 'Own' ? 'selected' : '' }}>Own</option>
                    <option {{ old('family_residence') == 'Do not know' ? 'selected' : '' }}>Do not know</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Size <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="residence_size" value="{{ old('residence_size') }}" placeholder="e.g. 10 Marla">
            </div>
            <div class="col-md-4">
                <label>City</label>
                <input type="text" class="form-control" name="city" value="{{ old('city') }}" placeholder="Home city">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Address <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="address" value="{{ old('address') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Nationality <span class="ur-opt">Optional</span></label>
                <select name="con_of_citizenship" class="form-control">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dataid }}" {{ old('con_of_citizenship') == $country->dataid ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Current City <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="current_city" value="{{ old('current_city') }}" placeholder="e.g. Maryland, USA — if different from home city">
            </div>
        </div>

        <h2>6. Family Details</h2>
        <div class="row">
            <div class="col-md-6">
                <label>Father's Occupation <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="father_occupation" value="{{ old('father_occupation') }}" placeholder="e.g. Deceased (Business Owner)">
            </div>
            <div class="col-md-6">
                <label>Mother's Occupation <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="mother_occupation" value="{{ old('mother_occupation') }}" placeholder="e.g. House Wife">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Brothers <span class="ur-opt">Optional</span></label>
                <textarea class="form-control" name="siblings_brothers" rows="2" placeholder="e.g. 1, Business Owner (cars import business in Uganda)">{{ old('siblings_brothers') }}</textarea>
            </div>
            <div class="col-md-6">
                <label>Sisters <span class="ur-opt">Optional</span></label>
                <textarea class="form-control" name="siblings_sisters" rows="2" placeholder="e.g. 3 (all Married) — 1 Dentist in Australia...">{{ old('siblings_sisters') }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Married <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="siblings_married_note" value="{{ old('siblings_married_note') }}">
            </div>
        </div>

        <h2>7. Photos <small>up to 2 images</small></h2>
        <div class="row">
            <div class="ur-photo-slots">
                <div class="ur-photo-slot">
                    <div class="ur-photo-slot__drop" id="photo_preview_1"><span>No image selected</span></div>
                    <label>Photo 1 <span class="ur-opt">Optional</span></label>
                    <input type="file" name="image1" accept="image/*" onchange="urPreviewProposalPhoto(this, 'photo_preview_1')">
                </div>
                <div class="ur-photo-slot">
                    <div class="ur-photo-slot__drop" id="photo_preview_2"><span>No image selected</span></div>
                    <label>Photo 2 <span class="ur-opt">Optional</span></label>
                    <input type="file" name="image2" accept="image/*" onchange="urPreviewProposalPhoto(this, 'photo_preview_2')">
                </div>
            </div>
        </div>

        <h2>8. Your Requirements <small>— used to find AI Matches for this proposal</small></h2>
        <div class="row">
            <div class="col-md-6">
                <label>Age Limit <span class="ur-opt">Optional</span></label>
                <div style="display:flex; gap:6px; align-items:center;">
                    <input type="number" class="form-control" name="pref_age_min" placeholder="Min" min="18" max="99" value="{{ old('pref_age_min') }}">
                    <span>&ndash;</span>
                    <input type="number" class="form-control" name="pref_age_max" placeholder="Max" min="18" max="99" value="{{ old('pref_age_max') }}">
                </div>
            </div>
            <div class="col-md-6">
                <label>Height <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="pref_height" value="{{ old('pref_height') }}" placeholder="e.g. Same or close to similar height">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>City <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="pref_city" value="{{ old('pref_city') }}" placeholder="e.g. Preferred city Lahore">
            </div>
            <div class="col-md-6">
                <label>Caste <span class="ur-opt">Optional</span></label>
                <input type="text" class="form-control" name="pref_caste_note" value="{{ old('pref_caste_note') }}" placeholder="e.g. Any caste but not Mughal">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Qualification <span class="ur-opt">Optional</span></label>
                <textarea class="form-control" name="pref_qualification_note" rows="2" placeholder="e.g. Seeking a well-educated individual (preferably a doctor)...">{{ old('pref_qualification_note') }}</textarea>
            </div>
        </div>

        <h2>Client Detail</h2>
        <div class="row">
            <div class="col-md-12">
                <textarea class="form-control" name="profile_description" rows="6" placeholder="Paste or type any extra detail about this client that doesn't fit the fields above...">{{ old('profile_description') }}</textarea>
            </div>
        </div>

        <button type="submit" class="ur-submit-btn"><i class="fa fa-plus"></i> Add Proposal</button>
    </form>
</div>

<script>
function urPreviewProposalPhoto(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) { preview.innerHTML = '<img src="' + e.target.result + '">'; };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<span>No image selected</span>';
    }
}

function urCopyTemplate(event) {
    event.preventDefault();
    var text = document.getElementById('template_box').value;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
            showAlert('success', 'Template copied — paste it into WhatsApp.', 2000);
        }).catch(function () {
            showAlert('danger', 'Could not copy the template.', 2000);
        });
    }
    return false;
}

(function () {
    var form = document.getElementById('af_form');

    // ---------- helpers ----------
    function norm(s) {
        return (s || '').toString().toLowerCase().replace(/['".]/g, '').replace(/\s+/g, ' ').trim();
    }
    function setVal(name, value) {
        var el = form.querySelector('[name="' + name + '"]');
        if (el && value !== undefined && value !== null && String(value).trim() !== '') {
            el.value = value;
        }
    }
    function appendVal(name, value) {
        var el = form.querySelector('[name="' + name + '"]');
        if (el && value) {
            el.value = el.value ? (el.value + (el.tagName === 'TEXTAREA' ? '\n' : ' — ') + value) : value;
        }
    }
    function selectFuzzy(name, text) {
        var el = form.querySelector('select[name="' + name + '"]');
        if (!el || !text) return false;
        var target = norm(text);
        var options = Array.from(el.options);
        var match = options.find(function (o) { return norm(o.textContent) === target; })
            || options.find(function (o) { return target.indexOf(norm(o.textContent)) !== -1 && norm(o.textContent).length > 2; })
            || options.find(function (o) { return norm(o.textContent).indexOf(target) !== -1 && target.length > 2; });
        if (match) { el.value = match.value; return true; }
        return false;
    }
    function fillAgeFromYears(value) {
        var m = String(value).match(/(\d{1,3})/);
        if (!m) return;
        var age = parseInt(m[1], 10);
        if (age < 1 || age > 110) return;
        var dob = new Date();
        dob.setFullYear(dob.getFullYear() - age);
        setVal('day', String(dob.getDate()).padStart(2, '0'));
        setVal('month', String(dob.getMonth() + 1).padStart(2, '0'));
        setVal('year', String(dob.getFullYear()));
    }
    function fillAgeLimit(value) {
        var nums = String(value).match(/\d{1,3}/g);
        if (!nums) return;
        if (nums.length >= 2) {
            setVal('pref_age_min', nums[0]);
            setVal('pref_age_max', nums[1]);
        } else {
            var n = parseInt(nums[0], 10);
            var isApprox = /around|approx|~|about/i.test(value);
            setVal('pref_age_min', isApprox ? Math.max(18, n - 2) : n);
            setVal('pref_age_max', isApprox ? Math.min(99, n + 2) : n);
        }
    }
    function fillName(value) {
        var parts = String(value).trim().split(/\s+/).filter(Boolean);
        // Drop a leading title like "Dr." / "Mr." / "Engr." — folded into
        // first name instead of a separate field, to avoid a new column.
        var title = '';
        if (parts.length && /^(dr|mr|mrs|ms|miss|engr|prof|syed|hafiz)\.?$/i.test(parts[0])) {
            title = parts.shift() + ' ';
        }
        if (!parts.length) return;
        setVal('first_name', title + parts[0]);
        if (parts.length > 1) setVal('last_name', parts.slice(1).join(' '));
    }

    // ---------- section-aware label maps ----------
    var SECTIONS = [
        { test: /personal\s*information/i, fields: {
            'gender': function (v) { setVal('gender', /female/i.test(v) ? 'female' : 'male'); },
            'name': fillName,
            'age': fillAgeFromYears,
            'marital status': function (v) { selectFuzzy('marital_status', v); },
            'height': function (v) { setVal('height', v); },
        }},
        { test: /education\s*details?/i, fields: {
            'qualification': function (v) {
                if (!selectFuzzy('education', v)) appendVal('college_university', v);
            },
            'college': function (v) { appendVal('college_university', v); },
            'college/university': function (v) { appendVal('college_university', v); },
            'university': function (v) { appendVal('college_university', v); },
        }},
        { test: /occupation\s*detail?s?/i, fields: {
            'job/business': function (v) { setVal('profession', v); },
            'job': function (v) { setVal('profession', v); },
            'business': function (v) { setVal('profession', v); },
            'income': function (v) { setVal('income', v); },
        }},
        { test: /religion\s*details?/i, fields: {
            'religion': function (v) { selectFuzzy('religion', v); },
            'sect': function (v) { setVal('sect', v); },
            'cast': function (v) { selectFuzzy('caste', v); },
            'caste': function (v) { selectFuzzy('caste', v); },
        }},
        { test: /resid[ae]nce\s*details?/i, fields: {
            'home own/on rent': function (v) { setVal('family_residence', /own/i.test(v) ? 'Own' : (/rent/i.test(v) ? 'Rent' : 'Do not know')); },
            'home own/rent': function (v) { setVal('family_residence', /own/i.test(v) ? 'Own' : (/rent/i.test(v) ? 'Rent' : 'Do not know')); },
            'size': function (v) { setVal('residence_size', v); },
            'city': function (v) { setVal('city', v); },
            'address': function (v) { setVal('address', v); },
            'adress': function (v) { setVal('address', v); },
            'nationality': function (v) { selectFuzzy('con_of_citizenship', v.replace(/i$/i, '').replace(/ian$/i, '')) || selectFuzzy('con_of_citizenship', v); },
            'current city': function (v) { setVal('current_city', v); },
        }},
        { test: /family\s*details?/i, fields: {
            "father's occupation": function (v) { setVal('father_occupation', v); },
            'fathers occupation': function (v) { setVal('father_occupation', v); },
            "mother's occupation": function (v) { setVal('mother_occupation', v); },
            'mothers occupation': function (v) { setVal('mother_occupation', v); },
            'brothers': function (v) { setVal('siblings_brothers', v); },
            'sisters': function (v) { setVal('siblings_sisters', v); },
            'married': function (v) { setVal('siblings_married_note', v); },
        }},
        { test: /your\s*requirements?/i, fields: {
            'age limit': fillAgeLimit,
            'height': function (v) { setVal('pref_height', v); },
            'city': function (v) { setVal('pref_city', v); },
            'caste': function (v) { setVal('pref_caste_note', v); },
            'qualification': function (v) { setVal('pref_qualification_note', v); },
        }},
    ];

    function parseAndFill(rawText) {
        var lines = rawText.split(/\r?\n/);
        var currentSection = null;
        var filledCount = 0;

        lines.forEach(function (rawLine) {
            // Strip leading emoji/number bullets (1️⃣, 2️⃣, numbers, symbols) before matching.
            var line = rawLine.replace(/^[^a-zA-Z]*/, '').trim();
            if (!line) return;

            var matchedSection = SECTIONS.find(function (s) { return s.test.test(line); });
            if (matchedSection) {
                currentSection = matchedSection;
                return;
            }
            if (!currentSection) return;

            var colonIndex = line.indexOf(':');
            if (colonIndex === -1) return;
            var label = norm(line.substring(0, colonIndex));
            var value = line.substring(colonIndex + 1).trim();
            if (!value) return;

            var handler = currentSection.fields[label];
            if (handler) {
                handler(value);
                filledCount++;
            }
        });

        return filledCount;
    }

    document.getElementById('paste_fill_btn').addEventListener('click', function () {
        var text = document.getElementById('paste_box').value;
        if (!text.trim()) return;
        var count = parseAndFill(text);
        document.getElementById('paste_status').textContent = count > 0
            ? ('Filled ' + count + ' field' + (count === 1 ? '' : 's') + ' — please review before saving.')
            : 'Could not recognize any fields in that text — please fill the form manually.';
    });
})();
</script>
@endsection
