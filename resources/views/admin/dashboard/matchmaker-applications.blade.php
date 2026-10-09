{{--
    Pending "Become a Partner" applications — see
    MatchmakerApplicationController::store() (public signup) and
    AdminController::approveMatchmakerApplication()/rejectMatchmakerApplication().
    Approving sets is_team_member=1 (same flag "Team Members" already uses).
--}}
@extends('layouts.admin.dashboard')
@section('dashboard-title', 'Matchmaker Applications')
@section('main-content')
<style>
    .ur-mka-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px; }
    .ur-mka-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 20px; }
    .ur-mka-card__head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .ur-mka-card__photo { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; background: #F6F4EF; border: 1px solid #E7E2D6; }
    .ur-mka-card__name { font-family: 'Playfair Display', serif; font-weight: 700; color: #123A2E; font-size: 15.5px; }
    .ur-mka-card__meta { font-size: 12px; color: #6B7570; }
    .ur-mka-card__field { font-size: 12.5px; margin-bottom: 8px; }
    .ur-mka-card__field span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #9AA5A0; }
    .ur-mka-card__actions { display: flex; gap: 8px; margin-top: 14px; }
    .ur-mka-btn { flex: 1; text-align: center; border-radius: 999px; padding: 9px 0; font-size: 12.5px; font-weight: 700; border: none; cursor: pointer; }
    .ur-mka-btn--approve { background: #123A2E; color: #fff; }
    .ur-mka-btn--reject { background: #fff; color: #B5674A; border: 1px solid #E6968C; }
    .ur-mka-empty { background: #fff; border: 1px solid #E7E2D6; border-radius: 14px; padding: 30px; text-align: center; color: #6B7570; }
</style>

<h1 style="font-family:'Playfair Display', serif; color:#123A2E; font-weight:700; font-size:24px; margin:0 0 18px;">Matchmaker Applications</h1>

@if($applications->isEmpty())
<div class="ur-mka-empty">No pending applications right now.</div>
@else
<div class="ur-mka-grid" id="mka_grid">
    @foreach($applications as $app)
    <div class="ur-mka-card" id="mka_card_{{ $app->dataid }}">
        <div class="ur-mka-card__head">
            <img class="ur-mka-card__photo" src="{{ $app->getProfileImage(true) }}" alt="" onerror="this.onerror=null;this.src='{{ \App\Profile::defaultImage($app->gender) }}';">
            <div>
                <div class="ur-mka-card__name">{{ $app->first_name }} {{ $app->last_name }}</div>
                <div class="ur-mka-card__meta">Applied {{ $app->created_at->diffForHumans() }}</div>
            </div>
        </div>
        <div class="ur-mka-card__field"><span>Email</span>{{ $app->email }}</div>
        <div class="ur-mka-card__field"><span>Contact</span>{{ $app->contact_mobile_number }}</div>
        @if(!empty($app->city))<div class="ur-mka-card__field"><span>City</span>{{ $app->city }}</div>@endif
        @if(!empty($app->experience))<div class="ur-mka-card__field"><span>Experience</span>{{ $app->experience }}</div>@endif
        @if(!empty($app->about_me))<div class="ur-mka-card__field"><span>About</span>{{ $app->about_me }}</div>@endif

        <div class="ur-mka-card__actions">
            <button type="button" class="ur-mka-btn ur-mka-btn--approve" onclick="mkaReview('{{ $app->dataid }}', 'approve', this)">Approve</button>
            <button type="button" class="ur-mka-btn ur-mka-btn--reject" onclick="mkaReview('{{ $app->dataid }}', 'reject', this)">Reject</button>
        </div>
    </div>
    @endforeach
</div>
@endif

<script>
function mkaReview(dataid, action, elem) {
    var url = action === 'approve'
        ? "{{ url('admin/matchmaker-applications') }}/" + dataid + "/approve"
        : "{{ url('admin/matchmaker-applications') }}/" + dataid + "/reject";

    elem.disabled = true;
    $.ajax({
        type: 'post',
        url: url,
        data: { '_token': '{{ csrf_token() }}' },
        success: function (result) {
            if (result.code == '200') {
                showAlert('success', result.message, 3000);
                document.getElementById('mka_card_' + dataid).remove();
            } else {
                elem.disabled = false;
                showAlert('danger', result.message, 5000);
            }
        },
        error: function () {
            elem.disabled = false;
            showAlert('danger', 'Something went wrong. Please try again.', 5000);
        }
    });
}
</script>
@endsection
