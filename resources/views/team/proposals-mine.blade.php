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
    .ur-mc-head h1 { font-family: 'Manrope', system-ui, sans-serif; color: #123A2E; font-weight: 800; font-size: 26px; line-height: 1.2 !important; margin: 0 0 6px; }
    .ur-mc-head p { color: #4B5651; font-size: 14.5px; line-height: 1.5 !important; margin: 0; }
    .ur-mc-add-btn { display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 22px; border-radius: 14px; background: #123A2E; color: #fff !important; font-size: 14px; font-weight: 800; line-height: 1 !important; text-decoration: none; white-space: nowrap; box-shadow: 0 8px 20px rgba(15,46,36,.18); }
    .ur-mc-add-btn:hover { background: #0F2E24; }

    .ur-mc-panel { background: #fff; border: 1px solid #E7E2D6; border-radius: 22px; padding: 18px 18px 14px; }
    .ur-mc-toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
    .ur-mc-search { position: relative; flex: 1 1 260px; max-width: 420px; }
    .ur-mc-search i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9AA5A0; font-size: 13px; }
    .ur-mc-search input { width: 100%; height: 46px; border: 1px solid #E7E2D6; border-radius: 14px; padding: 0 14px 0 40px; font-size: 14px; background: #fff; }
    .ur-mc-toolbar select { height: 46px; border: 1px solid #E7E2D6; border-radius: 14px; padding: 0 14px; font-size: 14px; background: #fff; flex: 0 0 auto; }
    .ur-mc-count { margin-left: auto; background: #F3F0E8; color: #6B7570; font-size: 12px; font-weight: 800; line-height: 1 !important; padding: 8px 14px; border-radius: 999px; white-space: nowrap; }

    .ur-mc-table-wrap { overflow-x: auto; overflow-y: visible; }
    .ur-mc-table { width: 100%; border-collapse: collapse; min-width: 760px; }
    .ur-mc-table th { text-align: left; font-size: 10.5px; font-weight: 800; letter-spacing: .06em; line-height: 1.2 !important; text-transform: uppercase; color: #8A958F; padding: 0 12px 10px; border-bottom: 1px solid #E7E2D6; white-space: nowrap; }
    .ur-mc-table td { padding: 12px; height: auto !important; line-height: 1.3 !important; border-bottom: 1px solid #F0ECE0; font-size: 14px; color: #1C2321; vertical-align: middle; }
    .ur-mc-table tr:last-child td { border-bottom: none; }

    .ur-mc-profile { display: flex; align-items: center; gap: 12px; }
    .ur-mc-profile__photo { display: block; width: 40px !important; height: 40px !important; max-width: none; border-radius: 10px; object-fit: cover; flex: 0 0 40px; background: #F3F0E8; margin: 0 !important; padding: 0 !important; }
    .ur-mc-profile__text { display: block; }
    .ur-mc-profile__id { display: block; font-weight: 800; color: #123A2E; font-size: 14px; line-height: 1.25 !important; margin: 0 !important; white-space: nowrap; }
    .ur-mc-profile__meta { display: block; font-size: 11.5px; line-height: 1.3 !important; margin: 2px 0 0 !important; color: #6B7570; white-space: nowrap; }

    .ur-mc-status { display: inline-block; font-size: 11px; font-weight: 800; line-height: 1.2 !important; padding: 5px 12px; border-radius: 999px; white-space: nowrap; }
    .ur-mc-status--active { background: #E7F3EC; color: #205C3F; }
    .ur-mc-status--on_hold { background: #FCF0DE; color: #8A5A15; }
    .ur-mc-status--matched, .ur-mc-status--engaged { background: #E4EEF7; color: #2C5F8A; }
    .ur-mc-status--married { background: #FBF0DA; color: #8A6218; }
    .ur-mc-status--closed { background: #F0ECE0; color: #6B7570; }

    .ur-mc-matches { font-weight: 800; color: #123A2E; }
    .ur-mc-actions { display: flex; align-items: center; gap: 8px; white-space: nowrap; }
    .ur-mc-icon-btn { width: 34px; height: 34px; border-radius: 10px; border: 1px solid #E7E2D6; background: #fff; color: #123A2E; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .ur-mc-icon-btn:hover { background: #F6F4EF; }
    .ur-mc-menu { min-width: 190px; padding: 6px; border: 1px solid #E7E2D6; box-shadow: 0 16px 36px rgba(15,46,36,.14); border-radius: 12px; }
    .ur-mc-menu a, .ur-mc-menu button { display: flex; align-items: center; gap: 9px; width: 100%; padding: 8px 10px; border-radius: 8px; font-size: 12.5px; color: #1C2321; background: transparent; border: 0; text-align: left; text-decoration: none; }
    .ur-mc-menu a:hover, .ur-mc-menu button:hover { background: #F6F4EF; text-decoration: none; color: #1C2321; }
    .ur-mc-menu i { width: 14px; text-align: center; color: #9AA5A0; }


    /* new-theme.css has a global `table * { padding: 10px !important }` that
       pads every row/div/span inside any table — reset it for this table. */
    .ur-mc-table * { padding: 0 !important; }
    .ur-mc-table th { padding: 0 10px 10px !important; }
    .ur-mc-table td { padding: 15px 10px !important; }
    .ur-mc-table .ur-mc-status { padding: 5px 12px !important; }
    .ur-mc-table .ur-mc-icon-btn { padding: 0 !important; }
    .ur-mc-table .ur-mc-menu { padding: 6px !important; }
    .ur-mc-table .ur-mc-menu a { padding: 8px 10px !important; }

    .ur-mc-empty { text-align: center; color: #6B7570; padding: 40px 10px; }

    .ur-mc-pagination { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-top: 18px; padding-top: 16px; border-top: 1px dashed #E7E2D6; }
    .ur-mc-pagination__note { font-size: 12.5px; color: #6B7570; }
    .ur-mc-pagination .pagination { margin: 0; gap: 6px; flex-wrap: wrap; }
    .ur-mc-pagination .pagination .page-link, .ur-mc-pagination .pagination .page-item > span { border-radius: 10px !important; min-width: 36px; text-align: center; }
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
        <div class="ur-mc-table-wrap" style="height:auto !important; max-height:none !important; overflow-y:visible !important;">
            <table class="ur-mc-table" style="height:auto !important;">
                <thead>
                    <tr style="height:auto !important;">
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
                        <tr style="height:auto !important;">
                            <td style="padding:15px 10px !important; height:auto !important; line-height:1.3 !important;">
                                <div class="ur-mc-profile">
                                    <img class="ur-mc-profile__photo" src="{{ $member->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($member->gender) }}';">
                                    <div class="ur-mc-profile__text">
                                        <span class="ur-mc-profile__id">{{ $member->dataid }}</span>
                                        <span class="ur-mc-profile__meta">{{ collect([ucfirst($member->gender), $age, $member->lbl_marital_status ?? null])->filter()->implode(' • ') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:15px 10px !important; height:auto !important; line-height:1.3 !important;">{{ $member->lbl_city ?: ($member->lbl_con_of_residence ?: '—') }}</td>
                            <td style="padding:15px 10px !important; height:auto !important; line-height:1.3 !important;">{{ $member->profession ?: '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($member->created_at)->format('d M Y') }}</td>
                            <td style="padding:15px 10px !important; height:auto !important; line-height:1.3 !important;"><span class="ur-mc-status ur-mc-status--{{ $member->profile_status ?: 'active' }}">{{ $statusOptions[$member->profile_status] ?? 'Active' }}</span></td>
                            <td class="ur-mc-matches" style="padding:15px 10px !important; height:auto !important; line-height:1.3 !important;">{{ $member->matchesCount ?? '—' }}</td>
                            <td style="padding:15px 10px !important; height:auto !important; line-height:1.3 !important;">
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
                                            <a href="#" onclick="return deleteProposal('{{ $member->dataid }}');" style="color:#B5674A;"><i class="fa fa-trash"></i> Delete proposal</a>
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
