{{--
    Manage photos for a proposal — see TeamController::photos()/uploadPhoto()/
    deletePhoto(). Only reachable by the proposal's own team member or admin.
--}}
@extends('layouts.team.dashboard')
@section('dashboard-title', 'Proposal Photos')
@section('main-content')
<style>
    .ur-photos-page { max-width: 760px; }
    .ur-photos-page h1 { font-family: 'Playfair Display', serif; color: #123A2E; font-weight: 700; font-size: 22px; margin: 0 0 18px; }
    .ur-photos-upload { background: #fff; border: 1px solid #E7E2D6; border-radius: 12px; padding: 16px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
    .ur-photos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 14px; }
    .ur-photos-grid__item { position: relative; border-radius: 10px; overflow: hidden; border: 1px solid #E7E2D6; }
    .ur-photos-grid__item img { width: 100%; height: 140px; object-fit: cover; display: block; }
    .ur-photos-grid__delete { position: absolute; top: 6px; right: 6px; background: rgba(181,103,74,.9); color: #fff; border: none; border-radius: 999px; width: 26px; height: 26px; }
    .ur-submit-btn { display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 22px; border-radius: 999px; background: #C9974D; color: #fff !important; font-size: 14px; font-weight: 700; border: none; }
    .ur-submit-btn:hover { background: #B07C3D; }
</style>

<div class="ur-photos-page">
    <h1>Photos — {{ $proposal->dataid }}</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form class="ur-photos-upload" method="POST" action="{{ route('team.proposals.photos.store', $proposal->dataid) }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required>
        <button type="submit" class="ur-submit-btn"><i class="fa fa-upload"></i> Upload</button>
    </form>

    <div class="ur-photos-grid">
        @foreach($images as $image)
        <div class="ur-photos-grid__item">
            <img src="{{ url(\App\Profile::MEMBER_IMAGES_PATH . '/thumbnail_' . $image->name) }}" alt="">
            <form method="POST" action="{{ route('team.proposals.photos.destroy', [$proposal->dataid, $image->id]) }}" onsubmit="return confirm('Delete this photo?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="ur-photos-grid__delete"><i class="fa fa-times"></i></button>
            </form>
        </div>
        @endforeach
    </div>
    @if($images->isEmpty())
    <p style="color:#6B7570;">No photos uploaded yet.</p>
    @endif
</div>
@endsection
