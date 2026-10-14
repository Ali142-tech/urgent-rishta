@extends('layouts.admin.master')
@section('dashboard-title', 'Dashboard Overview')
@section('admin-content')

<div class="ur-ov-header">
    <div>
        <h2>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, Admin</h2>
        <p>{{ now()->format('l, j F') }} &bull; here's how the platform looks today</p>
    </div>
</div>

<div class="ur-ov-stats">
    <div class="ur-ov-stat">
        <span class="ur-ov-stat__label">Total Members</span>
        <span class="ur-ov-stat__value">{{ number_format($totalMembers) }}</span>
        @if($totalMembersGrowthPct !== null)
            <span class="ur-ov-stat__delta {{ $totalMembersGrowthPct >= 0 ? 'is-up' : 'is-down' }}">{{ $totalMembersGrowthPct >= 0 ? '+' : '' }}{{ $totalMembersGrowthPct }}% this month</span>
        @endif
    </div>
    <div class="ur-ov-stat">
        <span class="ur-ov-stat__label">Pending Verification</span>
        <span class="ur-ov-stat__value">{{ number_format($pendingVerification) }}</span>
        <a href="{{ url('admin/photo-verification') }}" class="ur-ov-stat__delta is-warning">Needs review</a>
    </div>
    <div class="ur-ov-stat">
        <span class="ur-ov-stat__label">Active Memberships</span>
        <span class="ur-ov-stat__value">{{ number_format($activeMemberships) }}</span>
        @if($activeGrowthPct !== null)
            <span class="ur-ov-stat__delta {{ $activeGrowthPct >= 0 ? 'is-up' : 'is-down' }}">{{ $activeGrowthPct >= 0 ? '+' : '' }}{{ $activeGrowthPct }}% this month</span>
        @endif
    </div>
    <div class="ur-ov-stat">
        <span class="ur-ov-stat__label">Appointments Today</span>
        <span class="ur-ov-stat__value">{{ number_format($appointmentsToday) }}</span>
        @if($appointmentsUnconfirmedToday > 0)
            <span class="ur-ov-stat__delta is-warning">{{ $appointmentsUnconfirmedToday }} unconfirmed</span>
        @endif
    </div>
    <div class="ur-ov-stat ur-ov-stat--accent">
        <span class="ur-ov-stat__label">Revenue ({{ now()->format('M') }})</span>
        @if($revenueThisMonth->isEmpty())
            <span class="ur-ov-stat__value">&mdash;</span>
            <span class="ur-ov-stat__delta">No paid transactions yet this month</span>
        @else
            <span class="ur-ov-stat__value">
                @foreach($revenueThisMonth as $currency => $amount)
                    {{ $currency }} {{ number_format($amount, 0) }}@if(!$loop->last) + @endif
                @endforeach
            </span>
            <span class="ur-ov-stat__delta">
                @foreach($revenueThisMonth as $currency => $amount)
                    @php $prev = $revenueLastMonth[$currency] ?? 0; @endphp
                    @if($prev > 0)
                        {{ $currency }} {{ $amount >= $prev ? '+' : '' }}{{ round((($amount - $prev) / $prev) * 100) }}% vs last month
                    @endif
                @endforeach
            </span>
        @endif
    </div>
</div>

<div class="ur-ov-row">
    <div class="ur-profile-preview__panel">
        <h3><span class="ur-dot"></span> Membership Growth <span class="ur-hint" style="margin-left:auto;">Last 6 months</span></h3>
        <div class="ur-ov-chart-wrap">
            <canvas id="membershipGrowthChart"></canvas>
        </div>
    </div>

    <div class="ur-profile-preview__panel">
        <h3><span class="ur-dot"></span> Members by Package</h3>
        <p class="ur-hint" style="margin:-8px 0 12px;">Matchmaking tier</p>
        <div class="ur-ov-progress">
            @foreach($packageDistribution as $tier)
                <div class="ur-ov-progress__row">
                    <span class="ur-ov-progress__label">{{ $tier['name'] }}</span>
                    <span class="ur-ov-progress__pct">{{ $tier['pct'] }}%</span>
                </div>
                <div class="ur-ov-progress__bar">
                    <div class="ur-ov-progress__fill ur-ov-progress__fill--{{ $tier['slug'] }}" style="width: {{ $tier['pct'] }}%;"></div>
                </div>
            @endforeach
        </div>
        <p class="ur-hint" style="margin:16px 0 12px; border-top:1px solid var(--ur-dash-border); padding-top:14px;">Online subscription plan</p>
        <div class="ur-ov-progress">
            @foreach($onlinePackageDistribution as $tier)
                <div class="ur-ov-progress__row">
                    <span class="ur-ov-progress__label">{{ $tier['name'] }}</span>
                    <span class="ur-ov-progress__pct">{{ $tier['pct'] }}%</span>
                </div>
                <div class="ur-ov-progress__bar">
                    <div class="ur-ov-progress__fill ur-ov-progress__fill--online" style="width: {{ $tier['pct'] }}%;"></div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="ur-ov-row">
    <div class="ur-profile-preview__panel">
        <h3><span class="ur-dot"></span> Recent Activity</h3>
        @if($recentActivity->isEmpty())
            <div class="ur-admin-empty"><i class="fa fa-clock-o"></i> No recent activity.</div>
        @else
            <ul class="ur-ov-activity">
                @foreach($recentActivity as $item)
                    <li class="ur-ov-activity__item">
                        <span class="ur-ov-activity__dot ur-ov-activity__dot--{{ $item['type'] }}"></span>
                        <div>
                            <div class="ur-ov-activity__text">{{ $item['text'] }}</div>
                            <div class="ur-ov-activity__time">{{ \Carbon\Carbon::parse($item['at'])->diffForHumans() }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="ur-profile-preview__panel">
        <h3><span class="ur-dot"></span> Pending Approvals</h3>
        @if($pendingApprovals->isEmpty())
            <div class="ur-admin-empty"><i class="fa fa-check"></i> Nothing pending.</div>
        @else
            <ul class="ur-ov-approvals">
                @foreach($pendingApprovals as $member)
                    <li class="ur-ov-approval__item">
                        <span class="ur-ov-approval__avatar">{{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}</span>
                        <div class="ur-ov-approval__body">
                            <div class="ur-ov-approval__name">{{ $member->first_name }} {{ $member->last_name }}</div>
                            <div class="ur-ov-approval__sub">Photo review</div>
                        </div>
                        <a href="{{ url('admin/photo-verification') }}?search={{ urlencode($member->dataid) }}" class="ur-btn ur-btn--dark">Review</a>
                    </li>
                @endforeach
            </ul>
            <a href="{{ url('admin/photo-verification') }}" class="ur-ov-view-all">View all pending &rarr;</a>
        @endif
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script type="text/javascript">
    (function () {
        var ctx = document.getElementById('membershipGrowthChart');
        if (!ctx || !window.Chart) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json(array_column($membershipGrowth, 'label')),
                datasets: [{
                    label: 'New members',
                    data: @json(array_column($membershipGrowth, 'count')),
                    borderColor: '#123A2E',
                    backgroundColor: 'rgba(18,58,46,0.08)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#C9974D',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    })();
</script>
@endsection
