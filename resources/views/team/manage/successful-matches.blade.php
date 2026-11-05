{{--
    Team Management → Successful Matches (admin's own team account only, see TeamAdminController).
    Every match a team member has marked successful (TeamController::markSuccessfulMatch()), with the
    collaboration revenue share (§15) editable here, case by case. Moved here from the Admin dashboard.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Successful Matches')
@section('main-content')
<style>
    .ur-sm-title { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 24px; margin: 0 0 18px; }
    .ur-sm-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; }
    .ur-sm-table th, .ur-sm-table td { padding: 12px 14px !important; text-align: left; border-bottom: 1px solid #E7E2D6; font-size: 13.5px; vertical-align: middle; }
    .ur-sm-table th { background: #F6F4EF; color: #123A2E; font-weight: 700; }
    .ur-sm-table input[type=number] { width: 70px; border: 1px solid #E7E2D6; border-radius: 6px; height: 32px; padding: 0 8px; }
    .ur-sm-save { background: #C9974D; color: #fff; border: none; border-radius: 6px; height: 32px; padding: 0 12px; font-size: 12px; cursor: pointer; }
    .ur-sm-wrap { overflow-x: auto; }
    .ur-sm-form { display: flex; gap: 6px; align-items: center; }
    .ur-sm-heart { display: none; }

    /* Phones: each successful match is a card (the two proposals on top, partners + date as labelled rows, share editor at the bottom) */
    @media (max-width: 767.98px) {
        .ur-sm-title { font-size: 22px; margin-bottom: 14px; }
        .ur-sm-wrap { overflow: visible; }
        .ur-sm-table, .ur-sm-table tbody { display: block; width: 100%; background: none; border-radius: 0; }
        .ur-sm-table thead { display: none; }
        .ur-sm-table tbody tr { display: grid; grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr); align-items: center; column-gap: 8px; background: #FBF8F1; border: 1px solid #E3D9C3; border-left: 4px solid #C9974D; border-radius: 12px; padding: 14px !important; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(15,46,36,.06); }
        .ur-sm-table tbody td { display: block; border: 0 !important; padding: 0 !important; min-width: 0; word-break: break-word; }
        .ur-sm-table td.ur-sm-c-prop { grid-row: 1; text-align: center; font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 17px; color: #123A2E; background: #fff; border: 1px solid #E3D9C3 !important; border-radius: 10px; padding: 10px 6px !important; }
        .ur-sm-table td.ur-sm-c-prop:nth-child(1) { grid-column: 1; }
        .ur-sm-table td.ur-sm-c-prop:nth-child(2) { grid-column: 3; }
        .ur-sm-table tbody tr::before { content: "\f004"; font-family: FontAwesome; grid-column: 2; grid-row: 1; width: 30px; height: 30px; border-radius: 50%; background: #123A2E; color: #E2B66E; display: flex; align-items: center; justify-content: center; font-size: 13px; }
        .ur-sm-table td.ur-sm-c-row { grid-column: 1 / -1; display: flex !important; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 8px 0 !important; border-top: 1px dashed #DCCFB4 !important; font-size: 13px; text-align: right; }
        .ur-sm-table td.ur-sm-c-row:nth-of-type(3) { margin-top: 12px; }
        .ur-sm-table td.ur-sm-c-row::before { content: attr(data-label); color: #6B7570; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; flex-shrink: 0; text-align: left; }
        .ur-sm-table td.ur-sm-c-share { grid-column: 1 / -1; padding-top: 10px !important; border-top: 1px dashed #DCCFB4 !important; }
        .ur-sm-share-label { display: block; color: #6B7570; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 8px; }
        .ur-sm-form { display: grid; grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr); gap: 8px; }
        .ur-sm-form input[type=number] { width: 100%; height: 42px; border-radius: 10px; box-sizing: border-box; text-align: center; font-size: 15px; background: #fff; }
        .ur-sm-save { grid-column: 1 / -1; height: 42px; border-radius: 999px; background: #123A2E; font-size: 13.5px; font-weight: 700; }
        .ur-sm-empty-row td { text-align: center; padding: 24px 0 !important; color: #6B7570; }
    }
    @media (min-width: 768px) { .ur-sm-share-label { display: none; } }
</style>

<h1 class="ur-sm-title">Successful Matches</h1>

<div class="ur-sm-wrap">
<table class="ur-sm-table">
    <thead>
        <tr>
            <th>Proposal</th>
            <th>Counterpart</th>
            <th>Partner A</th>
            <th>Partner B</th>
            <th>Matched On</th>
            <th>Share A / B</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($matches as $match)
        <tr id="sm_row_{{ $match->id }}">
            <td class="ur-sm-c-prop">{{ $match->proposal->dataid ?? '—' }}</td>
            <td class="ur-sm-c-prop">{{ $match->counterpartProposal->dataid ?? '—' }}</td>
            <td class="ur-sm-c-row" data-label="Partner A">{{ optional($match->partnerA)->first_name }} {{ optional($match->partnerA)->last_name }}</td>
            <td class="ur-sm-c-row" data-label="Partner B">{{ optional($match->partnerB)->first_name }} {{ optional($match->partnerB)->last_name }}</td>
            <td class="ur-sm-c-row" data-label="Matched on">{{ \Carbon\Carbon::parse($match->matched_at)->format('d/m/Y') }}</td>
            <td class="ur-sm-c-share">
                <span class="ur-sm-share-label">Share A / B (%)</span>
                <form onsubmit="return saveShare(event, {{ $match->id }});" class="ur-sm-form">
                    <input type="number" min="0" max="100" name="share_partner_a" value="{{ $match->share_partner_a }}">
                    <span>/</span>
                    <input type="number" min="0" max="100" name="share_partner_b" value="{{ $match->share_partner_b }}">
                    <button type="submit" class="ur-sm-save">Save</button>
                </form>
            </td>
        </tr>
        @empty
        <tr class="ur-sm-empty-row"><td colspan="7">No successful matches recorded yet.</td></tr>
        @endforelse
    </tbody>
</table>
</div>

<div style="margin-top:20px;">{{ $matches->links() }}</div>

<script>
    function saveShare(e, id) {
        e.preventDefault();
        var form = e.target;
        $.ajax({
            type: 'post',
            url: '{{ url('team/manage/successful-matches') }}' + '/' + id + '/share',
            data: {
                '_token': '{{ csrf_token() }}',
                'share_partner_a': form.share_partner_a.value,
                'share_partner_b': form.share_partner_b.value
            },
            success: function(result) {
                showAlert(result.code == '200' ? 'success' : 'danger', result.message, 3000);
            },
            error: function() {
                showAlert('danger', 'Could not save. Please try again.', 5000);
            }
        });
        return false;
    }
</script>
@endsection
