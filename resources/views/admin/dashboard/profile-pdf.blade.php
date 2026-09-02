<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $member->name }} — {{ $member->dataid }}</title>
<style>
    @page { margin: 28px 34px; }
    body { font-family: 'DejaVu Sans', sans-serif; color: #1C2321; font-size: 11px; }

    .ur-pdf-header { width: 100%; border-bottom: 2px solid #123A2E; padding-bottom: 12px; margin-bottom: 16px; }
    .ur-pdf-header table { width: 100%; border-collapse: collapse; }
    .ur-pdf-header td { vertical-align: top; }
    .ur-pdf-photo { width: 90px; }
    .ur-pdf-photo img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #E7E2D6; }
    .ur-pdf-photo-placeholder {
        width: 80px; height: 80px; border: 1px dashed #C7C0AF; border-radius: 4px;
        text-align: center; color: #9AA5A0; font-size: 9px; padding-top: 32px;
    }
    .ur-pdf-name { font-size: 18px; font-weight: bold; color: #123A2E; margin: 0 0 3px; }
    .ur-pdf-id { font-size: 11px; color: #6B7570; margin-bottom: 6px; }
    .ur-pdf-badge {
        display: inline-block; font-size: 9px; font-weight: bold; text-transform: uppercase;
        padding: 3px 9px; border-radius: 10px; margin-right: 6px; color: #fff;
    }
    .ur-pdf-badge--active { background: #123A2E; }
    .ur-pdf-badge--pending { background: #C9974D; }
    .ur-pdf-badge--package { background: #6B7570; }
    .ur-pdf-meta { text-align: right; font-size: 9.5px; color: #9AA5A0; }

    .ur-pdf-section { margin-top: 16px; }
    .ur-pdf-section-title {
        font-size: 11.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
        color: #123A2E; border-bottom: 1px solid #E7E2D6; padding-bottom: 4px; margin-bottom: 8px;
    }
    table.ur-pdf-grid { width: 100%; border-collapse: collapse; }
    table.ur-pdf-grid td { padding: 4px 6px; vertical-align: top; width: 25%; }
    table.ur-pdf-grid td.label { color: #6B7570; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.3px; }
    table.ur-pdf-grid td.value { font-size: 11px; font-weight: bold; padding-bottom: 8px; }

    .ur-pdf-intro { font-size: 10.5px; line-height: 1.5; color: #333; background: #F6F4EF; padding: 10px 12px; border-radius: 4px; }

    .ur-pdf-footer { margin-top: 24px; padding-top: 8px; border-top: 1px solid #E7E2D6; font-size: 8.5px; color: #9AA5A0; text-align: center; }
</style>
</head>
<body>

<div class="ur-pdf-header">
    <table>
        <tr>
            <td class="ur-pdf-photo">
                @if($photo)
                    <img src="{{ $photo }}">
                @else
                    <div class="ur-pdf-photo-placeholder">No Photo</div>
                @endif
            </td>
            <td>
                <div class="ur-pdf-name">{{ $member->name }}</div>
                <div class="ur-pdf-id">Member ID: {{ $member->dataid }} &nbsp;|&nbsp; {{ ucfirst($member->gender) }}</div>
                <span class="ur-pdf-badge {{ $member->active ? 'ur-pdf-badge--active' : 'ur-pdf-badge--pending' }}">
                    {{ $member->active ? 'Active' : 'Pending' }}
                </span>
                @if(!empty($member->lbl_package))
                    <span class="ur-pdf-badge ur-pdf-badge--package">{{ $member->lbl_package }}</span>
                @endif
            </td>
            <td class="ur-pdf-meta">
                Generated {{ now()->format('d M Y, h:i A') }}<br>
                Urgent Rishta — Admin
            </td>
        </tr>
    </table>
</div>

@if(!empty($member->intro))
<div class="ur-pdf-section">
    <div class="ur-pdf-section-title">Introduction</div>
    <div class="ur-pdf-intro">{{ $member->intro }}</div>
</div>
@endif

<div class="ur-pdf-section">
    <div class="ur-pdf-section-title">Personal Details</div>
    <table class="ur-pdf-grid">
        <tr>
            <td><div class="label">Age</div><div class="value">{{ $member->birthday ? date_diff(date_create($member->birthday), date_create('now'))->y : 'N/A' }}</div></td>
            <td><div class="label">Height</div><div class="value">{{ $member->height ?: 'N/A' }}</div></td>
            <td><div class="label">Weight</div><div class="value">{{ $member->weight ?: 'N/A' }}</div></td>
            <td><div class="label">Marital Status</div><div class="value">{{ $member->lbl_marital_status ?: 'N/A' }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Religion</div><div class="value">{{ $member->lbl_religion ?: 'N/A' }}</div></td>
            <td><div class="label">Caste / Sect</div><div class="value">{{ $member->lbl_caste ?: 'N/A' }}</div></td>
            <td><div class="label">Mother Tongue</div><div class="value">{{ $member->lbl_mother_tongue ?: 'N/A' }}</div></td>
            <td><div class="label">Languages</div><div class="value">{{ $member->lbl_language ?: 'N/A' }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Education</div><div class="value">{{ $member->lbl_education ?: 'N/A' }}</div></td>
            <td><div class="label">Profession</div><div class="value">{{ $member->profession ?: 'N/A' }}</div></td>
            <td><div class="label">Company</div><div class="value">{{ $member->companyname ?: 'N/A' }}</div></td>
            <td><div class="label">Children</div><div class="value">{{ $member->children ?: 'N/A' }}</div></td>
        </tr>
    </table>
</div>

<div class="ur-pdf-section">
    <div class="ur-pdf-section-title">Location</div>
    <table class="ur-pdf-grid">
        <tr>
            <td><div class="label">City</div><div class="value">{{ $member->lbl_city ?: 'N/A' }}</div></td>
            <td><div class="label">Country of Residence</div><div class="value">{{ $member->lbl_con_of_residence ?: 'N/A' }}</div></td>
            <td><div class="label">Country of Birth</div><div class="value">{{ $member->lbl_con_of_birth ?: 'N/A' }}</div></td>
            <td><div class="label">Immigration Status</div><div class="value">{{ $member->immigration_status ?: 'N/A' }}</div></td>
        </tr>
    </table>
</div>

<div class="ur-pdf-section">
    <div class="ur-pdf-section-title">Contact</div>
    <table class="ur-pdf-grid">
        <tr>
            <td><div class="label">Email</div><div class="value">{{ $member->email ?: 'N/A' }}</div></td>
            <td><div class="label">Mobile</div><div class="value">{{ $member->contact_mobile_number ?: 'N/A' }}</div></td>
            <td><div class="label">Profile Created</div><div class="value">{{ $member->created_at ? \Carbon\Carbon::parse($member->created_at)->format('d M Y') : 'N/A' }}</div></td>
            <td><div class="label">Last Updated</div><div class="value">{{ $member->updated_at ? \Carbon\Carbon::parse($member->updated_at)->format('d M Y') : 'N/A' }}</div></td>
        </tr>
    </table>
</div>

@if($member->father || $member->mother || $member->siblings || $member->family_residence)
<div class="ur-pdf-section">
    <div class="ur-pdf-section-title">Family</div>
    <table class="ur-pdf-grid">
        <tr>
            <td><div class="label">Father</div><div class="value">{{ $member->father ?: 'N/A' }}</div></td>
            <td><div class="label">Father's Profession</div><div class="value">{{ $member->father_profession ?: 'N/A' }}</div></td>
            <td><div class="label">Mother</div><div class="value">{{ $member->mother ?: 'N/A' }}</div></td>
            <td><div class="label">Siblings</div><div class="value">{{ $member->siblings ?: 'N/A' }}</div></td>
        </tr>
        <tr>
            <td colspan="4"><div class="label">Family Residence</div><div class="value">{{ $member->family_residence ?: 'N/A' }}</div></td>
        </tr>
    </table>
</div>
@endif

@if($member->rage || $member->lbl_rreligion || $member->lbl_rcaste || $member->lbl_rmarital_status)
<div class="ur-pdf-section">
    <div class="ur-pdf-section-title">Partner Preferences</div>
    <table class="ur-pdf-grid">
        <tr>
            <td><div class="label">Age Range</div><div class="value">{{ $member->rage ?: 'N/A' }}</div></td>
            <td><div class="label">Height Range</div><div class="value">{{ $member->rheight ?: 'N/A' }}</div></td>
            <td><div class="label">Marital Status</div><div class="value">{{ $member->lbl_rmarital_status ?: 'N/A' }}</div></td>
            <td><div class="label">With Children</div><div class="value">{{ $member->rwith_children ?: 'N/A' }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Religion</div><div class="value">{{ $member->lbl_rreligion ?: 'N/A' }}</div></td>
            <td><div class="label">Caste / Sect</div><div class="value">{{ $member->lbl_rcaste ?: 'N/A' }}</div></td>
            <td><div class="label">Mother Tongue</div><div class="value">{{ $member->lbl_rmother_tongue ?: 'N/A' }}</div></td>
            <td><div class="label">Education</div><div class="value">{{ $member->lbl_reducation ?: 'N/A' }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Profession</div><div class="value">{{ $member->rprofession ?: 'N/A' }}</div></td>
            <td colspan="3"><div class="label">Preferred Location</div><div class="value">{{ $member->lbl_rcity ?: $member->lbl_rcon_of_residence ?: 'N/A' }}</div></td>
        </tr>
    </table>
</div>
@endif

<div class="ur-pdf-footer">
    This document was generated from the Urgent Rishta admin dashboard for internal review purposes only.
</div>

</body>
</html>
