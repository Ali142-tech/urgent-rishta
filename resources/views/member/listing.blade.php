@extends('layouts.dashboard')
@section('dashboard-title', $type=="interests" ? 'My Interests' : (strtoupper(substr($type, 0, 1)).substr($type, 1).(substr($type, strlen($type)-1)=="e"?"d":"ed"))." Members")
@section('main-content')
<?php use App\User; ?>
<style>
    @media (max-width: 576px) {
        .listing-image {
            height: 330px !important;
        }
    }
</style>
<section class="page-title page-title--style-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h2 class="heading heading-3 strong-400 mb-0">
                @if ($type=="interests")
                    Interests
                @else
                    {{ (strtoupper(substr($type, 0, 1)).substr($type, 1).(substr($type, strlen($type)-1)=="e"?"d":"ed"))." Members" }}
                @endif
            </div>
        </div>
    </div>
</section>
<section class="slice sct-color-1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="block-wrapper" id="result">
                    @if($type=="interests")
                        @yield('interest-data')
                    @else
                        @yield('filtered-data')
                    @endif
                </div>
                <!-- {-{- $profiles->links() -}-} -->
                <script type="text/javascript">
                    $(document).ready(function() {
                        //$('#datatable').DataTable();
                    });
                </script>
            </div>
        </div>
    </div>
</section>
<style>
    /* xs */
    .size-sm {
        display: none;
    }

    .size-sm-btn {
        display: block;
    }

    /* sm */
    @media (min-width: 768px) {
        .size-sm {
            display: none;
        }

        .size-sm-btn {
            display: block;
        }
    }

    /* md */
    @media (min-width: 992px) {
        .size-sm {
            display: block;
        }

        .size-sm-btn {
            display: none;
        }
    }

    /* lg */
    @media (min-width: 1200px) {
        .size-sm {
            display: block;
        }

        .size-sm-btn {
            display: none;
        }
    }
</style>
@endsection
