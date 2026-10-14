{{--
    Website Upgrade Brief §18 — admin-visible list of every match a team
    member has marked successful (see TeamController::markSuccessfulMatch()).
    Collaboration share (§15) is editable here, case by case.
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Successful Matches')
@section('main-content')
<style>
    .ur-sm-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; }
    .ur-sm-table th, .ur-sm-table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #E7E2D6; font-size: 13.5px; }
    .ur-sm-table th { background: #F6F4EF; color: #123A2E; font-weight: 700; }
    .ur-sm-table input[type=number] { width: 70px; border: 1px solid #E7E2D6; border-radius: 6px; height: 32px; padding: 0 8px; }
    .ur-sm-save { background: #C9974D; color: #fff; border: none; border-radius: 6px; height: 32px; padding: 0 12px; font-size: 12px; cursor: pointer; }
</style>

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
            <td>{{ $match->proposal->dataid ?? '—' }}</td>
            <td>{{ $match->counterpartProposal->dataid ?? '—' }}</td>
            <td>{{ optional($match->partnerA)->first_name }} {{ optional($match->partnerA)->last_name }}</td>
            <td>{{ optional($match->partnerB)->first_name }} {{ optional($match->partnerB)->last_name }}</td>
            <td>{{ \Carbon\Carbon::parse($match->matched_at)->format('d/m/Y') }}</td>
            <td>
                <form onsubmit="return saveShare(event, {{ $match->id }});" style="display:flex; gap:6px; align-items:center;">
                    <input type="number" min="0" max="100" name="share_partner_a" value="{{ $match->share_partner_a }}">
                    /
                    <input type="number" min="0" max="100" name="share_partner_b" value="{{ $match->share_partner_b }}">
                    <button type="submit" class="ur-sm-save">Save</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6">No successful matches recorded yet.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:20px;">{{ $matches->links() }}</div>

<script>
    function saveShare(e, id) {
        e.preventDefault();
        var form = e.target;
        $.ajax({
            type: 'post',
            url: '{{ url('admin/successful-matches') }}' + '/' + id + '/share',
            data: {
                '_token': '{{ csrf_token() }}',
                'share_partner_a': form.share_partner_a.value,
                'share_partner_b': form.share_partner_b.value
            },
            success: function(result) {
                showAlert(result.code == '200' ? 'success' : 'danger', result.message, 3000);
            }
        });
        return false;
    }
</script>
@endsection
