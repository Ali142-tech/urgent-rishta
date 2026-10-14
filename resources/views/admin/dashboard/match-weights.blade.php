{{--
    Admin-configurable weight per compatibility factor — see
    Profile::compatibilityWith() and MatchWeightSetting::current(). Every
    factor defaults to 1 (equal weight), reproducing the old flat-average
    score exactly until an admin changes something here.
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'AI Match Weights')
@section('main-content')
<style>
    .ur-weights-form { max-width: 640px; }
    .ur-weights-form h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 22px; margin: 0 0 8px; }
    .ur-weights-form p.ur-weights-note { color: #6B7570; font-size: 13.5px; margin: 0 0 22px; }
    .ur-weights-form .row { margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .ur-weights-form label { font-size: 14px; font-weight: 600; color: #1C2321; }
    .ur-weights-form input[type=number] { border: 1px solid #E7E2D6; border-radius: 8px; height: 40px; width: 100px; padding: 0 12px; text-align: right; }
    .ur-weights-form .ur-submit-btn { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 28px; border-radius: 999px; background: #C9974D; color: #fff !important; font-size: 14px; font-weight: 700; border: none; margin-top: 10px; }
    .ur-weights-form .ur-submit-btn:hover { background: #B07C3D; }
</style>

<div class="ur-weights-form">
    <h1>AI Match Weights</h1>
    <p class="ur-weights-note">Higher weight = more influence on the overall AI Match % shown to members. Weights don't need to sum to 100 — they're normalized automatically. Setting a factor to 0 removes it from the score entirely.</p>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.match-weights.update') }}">
        @csrf
        @php
            $labels = [
                'age_weight' => 'Age', 'location_weight' => 'Location', 'religion_weight' => 'Religion',
                'caste_weight' => 'Caste', 'marital_status_weight' => 'Marital Status', 'education_weight' => 'Education',
                'mother_tongue_weight' => 'Mother Tongue', 'children_weight' => 'Children',
            ];
            $weightsByField = [
                'age_weight' => $weights['Age'], 'location_weight' => $weights['Location'], 'religion_weight' => $weights['Religion'],
                'caste_weight' => $weights['Caste'], 'marital_status_weight' => $weights['Marital Status'], 'education_weight' => $weights['Education'],
                'mother_tongue_weight' => $weights['Mother Tongue'], 'children_weight' => $weights['Children'],
            ];
        @endphp
        @foreach($labels as $field => $label)
        <div class="row">
            <label for="{{ $field }}">{{ $label }}</label>
            <input type="number" min="0" max="100" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $weightsByField[$field]) }}">
        </div>
        @endforeach

        <button type="submit" class="ur-submit-btn"><i class="fa fa-check"></i> Save Weights</button>
    </form>
</div>
@endsection
