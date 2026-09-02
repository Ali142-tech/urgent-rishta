@extends('layouts.admin.master')
@section('dashboard-title', 'Admin Dashboard')
@section('admin-content')
<div class="ur-admin-header">
    <p class="ur-admin-back"><a href="{{ url('admin/profiles') }}"><i class="fa fa-arrow-left"></i> Back to Profiles</a></p>
    <h2>Change Package &mdash; {{ $member->first_name }} {{ $member->last_name }} <span class="ur-admin-header__muted">({{ $member->dataid }})</span></h2>
</div>

<div class="ur-admin-panel" style="max-width: 480px;">
    <form id="package_form">
        @csrf
        <div class="form-group">
            <label for="package">Package</label>
            <select name="package" id="package" class="form-control" required>
                <option value="">Choose one</option>
                @foreach($packages as $package)
                    <option value="{{ $package->dataid }}" {{ $member->package == $package->dataid ? 'selected' : '' }}>{{ $package->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="ur-admin-btn ur-admin-btn--gold mt-2">
            <i class="fa fa-save"></i> Update Package
        </button>
    </form>
</div>

<script type="text/javascript">
    $('#package_form').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type=submit]');
        var oldHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-refresh fa-spin"></i> Saving...');

        $.ajax({
            url: "{{ url('admin/profile/updatepackage/'.$member->dataid) }}",
            type: 'post',
            data: $(this).serialize(),
            success: function(result) {
                btn.prop('disabled', false).html(oldHtml);
                var message = result.message;
                if (result.code == '200') {
                    showAlert('success', message, 2000);
                    setTimeout(function() {
                        window.location = "{{ url('admin/profiles') }}";
                    }, 1200);
                } else {
                    showAlert('danger', message, 5000);
                }
            },
            error: function() {
                btn.prop('disabled', false).html(oldHtml);
                showAlert('danger', 'Something went wrong. Please try again.', 5000);
            }
        });
    });
</script>
@endsection
