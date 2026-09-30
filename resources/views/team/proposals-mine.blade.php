{{--
    "My Clients" (client mockup, Sep 2026) — this team member's own added
    proposals only (added_by = auth()->id()), as a searchable/filterable
    table instead of the shared card grid (team.partials.proposal-grid +
    member-card.blade.php), which admin/team-proposals.blade.php still
    uses unchanged. See TeamController::myProposals().
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'My Clients')
@section('main-content')
<style>
    .ur-mc-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; }
    .ur-mc-head h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 4px; }
    .ur-mc-head p { color: #6B7570; font-size: 13.5px; margin: 0; }
    .ur-mc-add-btn { display: inline-flex; align-items: center; gap: 7px; height: 44px; padding: 0 20px; border-radius: 999px; background: #123A2E; color: #fff !important; font-size: 13.5px; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .ur-mc-add-btn:hover { background: #0F2E24; }

    .ur-mc-panel { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 18px; }
    .ur-mc-toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
    .ur-mc-search { position: relative; flex: 1 1 260px; }
    .ur-mc-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9AA5A0; font-size: 13px; }
    .ur-mc-search input { width: 100%; height: 42px; border: 1px solid #E7E2D6; border-radius: 999px; padding: 0 14px 0 38px; font-size: 13px; }
    .ur-mc-toolbar select { height: 42px; border: 1px solid #E7E2D6; border-radius: 999px; padding: 0 14px; font-size: 13px; background: #fff; flex: 0 0 auto; }
    .ur-mc-count { margin-left: auto; background: #F6F4EF; color: #6B7570; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 999px; white-space: nowrap; }

    .ur-mc-table-wrap { overflow-x: auto; }
    .ur-mc-table { width: 100%; border-collapse: collapse; min-width: 760px; }
    .ur-mc-table th { text-align: left; font-size: 10.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #9AA5A0; padding: 0 12px 10px; border-bottom: 1px solid #E7E2D6; white-space: nowrap; }
    .ur-mc-table td { padding: 14px 12px; border-bottom: 1px solid #F0ECE0; font-size: 13px; color: #1C2321; vertical-align: middle; }
    .ur-mc-table tr:last-child td { border-bottom: none; }

    .ur-mc-profile { display: flex; align-items: center; gap: 10px; }
    .ur-mc-profile__photo { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; flex-shrink: 0; background: #F6F4EF; }
    .ur-mc-profile__initials { width: 40px; height: 40px; border-radius: 10px; background: #123A2E; color: #fff; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ur-mc-profile__id { font-weight: 700; color: #123A2E; white-space: nowrap; }
    .ur-mc-profile__meta { font-size: 11.5px; color: #6B7570; white-space: nowrap; }

    .ur-mc-status { display: inline-block; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px; white-space: nowrap; }
    .ur-mc-status--active { background: #E7F3EC; color: #205C3F; }
    .ur-mc-status--on_hold { background: #FCF0DE; color: #8A5A15; }
    .ur-mc-status--matched, .ur-mc-status--engaged { background: #E4EEF7; color: #2C5F8A; }
    .ur-mc-status--married { background: #FBF0DA; color: #8A6218; }
    .ur-mc-status--closed { background: #F0ECE0; color: #6B7570; }

    .ur-mc-actions { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
    .ur-mc-icon-btn { width: 34px; height: 34px; border-radius: 8px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
    .ur-mc-icon-btn:hover { background: #F6F4EF; }
    .ur-mc-menu { min-width: 190px; padding: 6px; border: 1px solid #E7E2D6; box-shadow: 0 16px 36px rgba(15,46,36,.14); border-radius: 10px; }
    .ur-mc-menu a, .ur-mc-menu button { display: flex; align-items: center; gap: 9px; width: 100%; padding: 8px 10px; border-radius: 7px; font-size: 12.5px; color: #1C2321; background: transparent; border: 0; text-align: left; text-decoration: none; }
    .ur-mc-menu a:hover, .ur-mc-menu button:hover { background: #F6F4EF; text-decoration: none; color: #1C2321; }
    .ur-mc-menu i { width: 14px; text-align: center; color: #9AA5A0; }

    .ur-mc-empty { text-align: center; color: #6B7570; padding: 40px 10px; }

    .ur-mc-pagination { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-top: 18px; padding-top: 16px; border-top: 1px dashed #E7E2D6; }
    .ur-mc-pagination__note { font-size: 12.5px; color: #6B7570; }
    .ur-mc-pagination .pagination { margin: 0; gap: 6px; flex-wrap: wrap; }
    .ur-mc-pagination .pagination .page-link, .ur-mc-pagination .pagination .page-item > span { border-radius: 999px !important; min-width: 36px; text-align: center; }
    .ur-mc-pagination .pagination > .active .page-link { background-color: #123A2E !important; border-color: #123A2E !important; color: #fff !important; }
</style>

@php
    $statusOptions = [
        'active' => 'Active', 'on_hold' => 'On Hold', 'matched' => 'Matched',
        'engaged' => 'Engaged', 'married' => 'Married', 'closed' => 'Closed',
    ];
    $ageOf = fn ($p) => !empty($p->birthday) ? date_diff(date_create($p->birthday), date_create('now'))->y : null;
@endphp

<div class="ur-mc-head">
    <div>
        <h1>My clients</h1>
        <p>Manage profiles imported or added by your matchmaker account.</p>
    </div>
    <a href="{{ route('team.proposals.create') }}" class="ur-mc-add-btn"><i class="fa fa-plus"></i> Add profile</a>
</div>

<div class="ur-mc-panel">
    <form method="GET" action="{{ route('team.proposals.mine') }}" class="ur-mc-toolbar">
        <div class="ur-mc-search">
            <i class="fa fa-search"></i>
            <input type="text" name="search" placeholder="Search profile ID, city or profession" value="{{ request('search') }}">
        </div>
        <select name="status" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach($statusOptions as $val => $label)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" style="display:none;"></button>
        <span class="ur-mc-count">{{ number_format($resultCount) }} profile{{ $resultCount == 1 ? '' : 's' }}</span>
    </form>

    @if(empty($members) || sizeof($members) === 0)
        <div class="ur-mc-empty">
            @if(request()->filled('search') || request()->filled('status'))
                No clients match these filters.
            @else
                You haven't added any proposals yet — use "Add profile" above to add the first one.
            @endif
        </div>
    @else
        <div class="ur-mc-table-wrap">
            <table class="ur-mc-table">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Location</th>
                        <th>Profession</th>
                        <th>Added</th>
                        <th>Status</th>
                        <th>Matches</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        @php $age = $ageOf($member); @endphp
                        <tr>
                            <td>
                                <div class="ur-mc-profile">
                                    @if(!empty($member->displaypic) || !empty($member->images))
                                        <img class="ur-mc-profile__photo" src="{{ $member->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
                                    @else
                                        <div class="ur-mc-profile__initials">{{ strtoupper(substr($member->gender ?: 'm', 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="ur-mc-profile__id">{{ $member->dataid }}</div>
                                        <div class="ur-mc-profile__meta">
                                            {{ ucfirst($member->gender) }}
                                            @if($age) &bull; {{ $age }} @endif
                                            @if(!empty($member->lbl_marital_status)) &bull; {{ $member->lbl_marital_status }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $member->lbl_city ?: ($member->lbl_con_of_residence ?: '—') }}</td>
                            <td>{{ $member->profession ?: '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($member->created_at)->format('d M Y') }}</td>
                            <td><span class="ur-mc-status ur-mc-status--{{ $member->profile_status ?: 'active' }}">{{ $statusOptions[$member->profile_status] ?? 'Active' }}</span></td>
                            <td>{{ $member->matchesCount ?? '—' }}</td>
                            <td>
                                <div class="ur-mc-actions">
                                    <button type="button" class="ur-mc-icon-btn" title="View file" onclick="openClientFile('{{ $member->dataid }}')"><i class="fa fa-eye"></i></button>
                                    <div class="dropdown">
                                        <button type="button" class="ur-mc-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More"><i class="fa fa-ellipsis-h"></i></button>
                                        <div class="dropdown-menu dropdown-menu-right ur-mc-menu">
                                            <a href="{{ route('team.proposals.edit', $member->dataid) }}"><i class="fa fa-pencil"></i> Edit profile</a>
                                            <a href="{{ route('team.proposals.photos', $member->dataid) }}"><i class="fa fa-camera"></i> Manage photos</a>
                                            @if($member->matchesCount !== null)
                                                <a href="{{ route('team.matches.show', $member->dataid) }}"><i class="fa fa-magic"></i> AI Match</a>
                                            @endif
                                            <a href="{{ route('team.proposals.share.whatsapp', $member->dataid) }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> Share via WhatsApp</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ur-mc-pagination">
            <div class="ur-mc-pagination__note">
                Showing {{ (($currentPage - 1) * 15) + 1 }}&ndash;{{ min($currentPage * 15, $resultCount) }} of {{ number_format($resultCount) }} profiles
            </div>
            @if($numPages > 1)
            <ul class="pagination">
                @if($currentPage != 1)
                <li class="page-item"><a href="{{ route('team.proposals.mine', array_merge(request()->except('page'), ['page' => $currentPage - 1])) }}" class="page-link">&lsaquo;</a></li>
                @endif
                @for ($i = ($currentPage - 3 > 1 ? $currentPage - 3 : 1); $i <= ($currentPage + 4 <= $numPages ? $currentPage + 4 : $numPages); $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}"><a href="{{ route('team.proposals.mine', array_merge(request()->except('page'), ['page' => $i])) }}" class="page-link">{{ $i }}</a></li>
                @endfor
                @if($currentPage < $numPages)
                <li class="page-item"><a href="{{ route('team.proposals.mine', array_merge(request()->except('page'), ['page' => $currentPage + 1])) }}" class="page-link">&rsaquo;</a></li>
                @endif
            </ul>
            @endif
        </div>
    @endif
</div>
@endsection
