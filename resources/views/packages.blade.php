@extends('layouts.master')
@section('main-content')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<?php use App\User; ?>
<style>
    .ss-banner {
        padding: 160px 0px 200px !important;
        border-bottom: 1px solid #f2f2f2;
        color: rgb(255, 255, 255) !important;
        background: linear-gradient(to right, rgb(137, 33, 107), rgb(218, 68, 83)) !important;
        overflow: hidden;
    }
    .ss-banner::before, .ss-banner::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        transition: all 0.5s ease;
        animation: zoomout 5s infinite linear both;
    }
    .ss-banner::before {
        left: -120px;
        top: -75px;
        width: 350px;
        height: 350px;
        background: rgb(158, 42, 101);
    }
     .ss-banner::after {
        right: -120px;
        bottom: -75px;
        width: 350px;
        height: 350px;
        background: rgb(218, 68, 83);
    }
    .ss-banner span.pri {
        margin: 0px auto;
        text-transform: uppercase;
        font-weight: 400;
        letter-spacing: 1px;
        color: #fff;
    }
    .ss-banner .heading{
        font-family:"Playfair Display", serif !important;
        color:white !important;
        margin-bottom:10px !important;
    }
    .ss-banner p {
        width: 100%;
        font-weight: 300;
        font-size: 16px;
        color: #f3f3f3;
    }
    .ss-banner span.nocre {
        margin: 0px auto;
        background: rgb(219, 33, 76);
        font-size: 12px;
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 25px;
        color: #fff;
        width: auto;
    }
    .ss-container .feature--boxed-border.active:after{
        display:none;
    }
    .navbar-light .navbar-nav .nav-link{
        color:white !important;
    }
    .ss-container{
        margin-top:-125px;
    }
    .ss-package_bg {
        color: rgb(0, 0, 0);
        background: rgb(255, 255, 255);
        padding: 25px 30px;
        float: left;
        width: 100%;
        border: 2px solid rgb(253 37 109);
        border-radius: 35px;
        box-shadow: rgba(51, 51, 51, 0.05) 0px 1px 12px 0px;
        text-align: center;
    }
    .col-black{
        color:black !important;
    }
    .package_items {
        color: #818a91 !important;
    }
    .ss-container .c-base-1 {
        color: #E91E63 !important;
    }
    .ss-container .feature--bg-2 *:not(.btn):not(.alert):not(.form-control):not(.heading):not(a), .feature-inverse *:not(.btn):not(.alert):not(.form-control):not(.heading):not(a) {
        color: unset !important;
    }
    .bank-details .para{
        position: relative; 
    }
     .copy-text{

    position: absolute;
    right: 4px;
    top: -20px;
    font-size: 10px;
    background: #e91628;
    color: wheat;
    padding: 0 4px;
    border-radius: 7px;

    }
    .ss-package_bg.ss-online {
    min-height: 427px;
    margin-bottom: 20px;
}


    .special-image { position: absolute; width: 66px; height: auto; top: -20px; right: -20px; }

    /* Separate sections: Online vs Admin packages */
    .package-section-online {
        margin-bottom: 3rem;
        padding-bottom: 2.5rem;
        /* border-bottom: 2px solid rgba(233, 30, 99, 0.2); */
    }
    .package-section-admin {
        padding-top: 0.5rem;
    }
    .package-section-title {
        margin-bottom: 0.5rem;
    }
    .package-section-desc {
        font-size: 15px;
        color: #6c757d;
        max-width: 640px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 1.5rem;
    }
    .ss-package_bg.ss-online {
        border-color: rgb(33, 150, 243);
        background: linear-gradient(to bottom, #fff 0%, #f8fbff 100%);
    }
    .ss-package_bg.ss-admin {
        border-color: rgb(253, 37, 109);
        background: linear-gradient(to bottom, #fff 0%, #fff8fb 100%);
    }

    .package-active-badge {
        display: inline-block;
        background: #2196F3;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        margin-bottom: 6px;
    }
    .package-expiry-text {
        font-size: 13px;
        color: #2e7d32;
        font-weight: 500;
        margin-bottom: 8px;
    }

    /* Package tabs */
    .package-tabs {
        display: flex;
        justify-content: center;
        gap: 0;
        margin-bottom: 2rem; 
        /* flex-wrap: wrap; */
    }
    

    
     
    
    .package-tab-panel {
        display: none;
    }
    .package-tab-panel.active {
        display: block;
    }



    .ss-package_bg 
        { 
            display: flex; 
            justify-content: center;
        }

        .icon-block--style-1-v5 {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.buttonWrapper{
    display:flex;
    gap:40px;
}

/* Common Button Style */
.customBtn {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 17px 40px;
    border-radius: 14px;
    font-size: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
    outline: none;
}
button:focus{
    outline: none;
    border: none;
}
/* White Button */
.customBtn.btnLight{
    background:#f5f5f5;
    color:#c73c61;
}

.customBtn.btnlight i{
    color:#2b8dbf;
    font-size:26px;
}

/* Pink Gradient Button */
.btnlight.active{
    background:linear-gradient(135deg,#d83b6a,#b91d4f);
    color:#fff;
}
/* .customBtn.btnGradient{
    background:linear-gradient(135deg,#d83b6a,#b91d4f);
    color:#fff;
} */

.customBtn.btnlight.active i{
    color:#fff;
    font-size:24px;
}

/* Hover Effect */
.customBtn:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 25px rgba(0,0,0,0.2);
}

    @keyframes zoomout {
        0% {
            transform: scale(1);
        }
    
        50% {
            transform: scale(2);
            opacity: 0.7;
        }
    
        100% {
            transform: scale(1);
        }
    }
    @media screen and (min-width: 767px) {
        .col-sm-6 {
            flex: 0 0 33% !important;
            max-width: 33% !important;
        }
        .ss-1, .ss-2, .ss-3  {
            margin-top: -70px;
        }

        .ss-2 .block-content {
            margin-top: 75px;
        }
    }
    @media (max-width: 767px) {
        .package-tabs .tab-btn {
            padding: 12px 24px;
            font-size: 13px;
            width: 100%;
            justify-content: center;
        }
        .package-tabs { 
        margin-bottom: 1rem; 
        flex-wrap: wrap;
    }
    .buttonWrapper {
    display: flex;
    gap: 25px;
    margin-bottom: 29px !important;
}


    }




    
      .online-service .container {
        max-width: 1000px;
        margin: 0 auto;
      }
      .online-service .card {
        background: #fff;
        border-radius: 16px;  
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 0 1px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.06); 
      }
      .online-service .card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        padding: 1.5rem 1.75rem;
      }
      .online-service .card-header h5 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .online-service .search-wrap {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
      }
      .online-service .search-box {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.65rem 1rem;
        transition:
          border-color 0.2s,
          box-shadow 0.2s;
      }
      .online-service .search-box:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
      }
      .online-service .search-box svg {
        flex-shrink: 0;
        color: #64748b;
      }
      .online-service .search-box input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 0.95rem;
        font-family: inherit;
        color: #0f172a;
      }
      .online-service .search-box input::placeholder {
        color: #94a3b8;
      }
      .online-service .contentWrap {
        padding: 1.75rem 1.75rem 1.5rem;
        color: #334155;
        font-size: 0.9375rem;
        line-height: 1.7;
        text-align: left !important;
      }
      .online-service .contentWrap .lead {
        color: #475569;
        margin-bottom: 1rem;
      }
      .online-service .contentWrap h6 {
        color: #0f172a;
        font-size: 1rem;
        font-weight: 600;
        margin: 1.25rem 0 0.5rem;
      }
      .online-service .contentWrap ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
        list-style: none;
      }
      .online-service .contentWrap ul li {
        position: relative;
        padding-left: 1.25rem !important;
        /* margin-bottom: 0.5rem !important; */
        padding-top: 0 !important;
    text-align: left !important;
    color: #0000009c !important;
      }
      .online-service .contentWrap ul li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3b82f6;
      }
      .online-service .contentWrap .note {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 0.875rem 1rem;
        margin: 1rem 0;
        border-radius: 0 8px 8px 0;
        font-size: 0.9rem;
        color: #92400e;
      }
      .online-service .contentWrap .highlights {
        margin-top: 1rem;
      }
      .online-service .read-more-wrap {
        margin-top: 1.25rem;
        text-align: center;
      }
      .online-service .read-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: black !important;
        background: #00000008;
        border: none;
        cursor: pointer;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-family: inherit;
        transition:
          background 0.2s,
          color 0.2s;
      }
      .online-service .read-more-btn:hover {
        background: #eff6ff;
        color: #2563eb;
      }
      .online-service .read-more-btn .chevron {
        transition: transform 0.25s ease;
      }
      .online-service .content-full {
        display: none;
      }
      .online-service .content-full.is-open {
        display: block;
      }
      .online-service p.lead.content-preview{
        color: black !important;
      }
      .online-service .content-preview {
        display: block;
      }
      .online-service .content-preview.is-hidden {
        display: block;
      }
      .online-service .card + .card {
        margin-top: 2rem;
      }
      .online-service .personalized-card .card-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
      }
      .online-service .personalized-card .contentWrap li::before {
        background: #7c3aed;
      }
      .online-service .personalized-card .read-more-btn {
        color: #7c3aed;
      }
      .online-service .personalized-card .read-more-btn:hover {
        background: #f5f3ff;
        color: #6d28d9;
      }
      .online-service .personalized-card .search-box:focus-within {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
      }
      .online-service .contentWrap .quote {
        font-style: italic;
        color: #64748b;
        margin: 0.75rem 0;
        padding-left: 1rem;
        border-left: 3px solid #e2e8f0;
      }
      .online-service .contentWrap .payment-box {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin: 1rem 0;
        font-size: 0.9rem;
        color: #334155;
      }
      .online-service .contentWrap .payment-box strong {
        color: #0f172a;
      }
      .online-service .contentWrap .check {
        color: #059669;
        margin-right: 0.35rem;
      }
      p.content-preview,
      .contentWrap p {
    color: #0000009c !important;
}
.paymantBox-details {
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    gap: 20px;
    max-width: 100%;
    justify-content: space-between;
}
.paymantBox-details .payment-box {
    max-width: 100%;
    width: 48%;
}

@media (max-width: 567px) { 
    .paymantBox-details .payment-box {
        width: 100%;
    }
    .paymantBox-details {
        flex-direction: column; 
    width: 100%; 
    gap: 0;
    max-width: 100%; 
    margin-bottom: 0 !IMPORTANT;
}
}
.personalised-btn{
  pointer-events: none;
}




     
</style>


<section class="page-title page-title--style-1 ss-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <span class="pri">Pricing</span>
                <h1 class="heading heading-1 strong-700 mb-0">Get Started <br>Pick your Plan Now</h1>
                <p>Your Journey To Love Starts With The Perfect Package.</p>
                <span class="nocre">No credit card required</span>
            </div>
        </div>
    </div>
</section>
@php
    $standardPackages = $standardPackages ?? collect();
    $premiumPackages = $premiumPackages ?? collect();
    $userOnlinePackageDataid = $userOnlinePackageDataid ?? null;
    $userOnlineExpiresAtFormatted = $userOnlineExpiresAtFormatted ?? null;
    $userHasActiveOnlinePackage = $userHasActiveOnlinePackage ?? false;
    if ($standardPackages->isEmpty() && $premiumPackages->isEmpty()) {
        $standardPackages = collect($packages ?? []);
    }
@endphp
<section class="slice sct-color-1 pricing-plans pricing-plans--style-1 has-bg-cover bg-size-cover" style=" background-position: bottom bottom;">
    <div class="container ss-container mt-5">
        <span class="clearfix"></span>

        <div class="package-tabs buttonWrapper" role="tablist">
            <!-- <button type="button" class="tab-btn active customBtn btnlight" data-tab="online" role="tab" aria-selected="true">
            <i class="fa-solid fa-globe"></i>
            Online Services
            </button> -->
            <button type="button" class="tab-btn customBtn btnlight mt-2 personalised-btn" data-tab="premium" role="tab" aria-selected="true">
            <i class="fa-solid fa-gem"></i>
            Personalised Service
            </button>
        </div>

        <div id="tab-online" class="package-tab-panel " role="tabpanel">
        <div class="package-section-online">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-3">
                
<div class="online-service">
      <div class="container">
        <div class="card">
          <div class="contentWrap">
          <h2 class="heading heading-3 strong-600 col-black package-section-title text-left mb-4">Online Services: Take Control of Your Journey</h2>
            <p class=" content-preview" id="preview">
              Our Online Service is a self-managed platform designed for those
              who prefer to explore and connect at their own pace. This
              automated service empowers you to find your perfect match using
              our advanced search tools.
            </p>

            <div class="content-full" id="fullContent">
              <h6>How it Works:</h6>
              <ul>
                <li>
                  <strong>Self-Exploration:</strong> Upon account activation,
                  you gain full access to our extensive database. You can search
                  for profiles based on your specific preferences (Education,
                  Cast, City, etc.) by yourself.
                </li>
                <li>
                  <strong>Direct Interaction:</strong> You can send 'Interests'
                  to profiles that catch your eye. Once your interest is
                  accepted, you can initiate direct communication with the other
                  party.
                </li>
                <li>
                  <strong>Tiered Access:</strong> Your search and
                  interest-sending limits are defined by your chosen
                  Subscription Package.
                </li>
                <li>
                  <strong>Premium Profiles:</strong> Access to our most elite
                  and verified premium profiles is exclusively reserved for our
                  top-tier package holders.
                </li>
              </ul>

              <h6>Important Note:</h6>
              <div class="note">
                Please Note: This is a DIY (Do-It-Yourself) service. Our
                dedicated matchmaking team does not personally suggest or find
                matches for you in this plan. From profile signup to final
                contact, you have the freedom and responsibility to manage your
                own search journey.
              </div>

              <h6>Key Highlights for Your Website:</h6>
              <ul class="highlights">
                <li>
                  <strong>Full Privacy Control:</strong> Manage who sees your
                  profile.
                </li>
                <li>
                  <strong>Instant Connectivity:</strong> No middleman; connect
                  as soon as there is mutual interest.
                </li>
                <li>
                  <strong>Flexible Packages:</strong> Choose a plan that fits
                  your search frequency.
                </li>
              </ul>
            </div>

            <div class="read-more-wrap">
              <button
                type="button"
                class="read-more-btn"
                id="toggleBtn"
                aria-expanded="false"
              >
                <span class="btn-text">Read more</span>
                <svg
                  class="chevron"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>
            </div>
          </div>
        </div> 
      </div>
</div>
            </div>
        </div>
        @if(!$standardPackages->isEmpty())
        <div class="row justify-content-center">
            @foreach ($standardPackages as $package)
            @if($package->dataid!="99")
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 ss-{{$loop->iteration}} ss mt-2">
                <div class="feature feature--boxed-border feature--bg-2 active ss-package_bg ss-online height">
                    <div class="icon-block--style-1-v5 text-center">
                        <div class="block-icon c-gray-dark">
                            <li style="list-style-type: none;">
                                @php
                                    $imgPath = '/images/package_'.$package->dataid.'.png';
                                    if (!file_exists(public_path($imgPath))) $imgPath = '/images/package_10.png';
                                    $meta = method_exists($package, 'meta') ? $package->meta() : [];
                                @endphp
                                <img src="{{ $imgPath }}" class="img-sm" height="100">
                            </li>
                        </div>
                        {{--
                            NOTE:
                            Do NOT use HTML comments to "disable" Blade expressions.
                            Blade still evaluates {{ ... }} inside <!-- ... --> which can crash the page.

                            Old offline package markup removed from here.
                        --}}
                        <div class="block-content mt-3">
                            @if($userHasActiveOnlinePackage && $userOnlinePackageDataid === $package->dataid)
                            <div class="package-active-badge">Current plan</div>
                            <div class="package-expiry-text">Expires: {{ $userOnlineExpiresAtFormatted }}</div>
                            @endif
                            <h3 class="col-black heading heading-5 strong-500 mb-2"><strong>{{ $package->name }}</strong></h3>
                            @if(!empty($meta) && isset($meta['price']))
                                <div class="price-tag col-black" style="font-size: 20px;">
                                    {{ $meta['currency'] ?? 'USD' }} {{ number_format((float)$meta['price'], 2) }}
                                    <span class="c-gray-light" style="font-size: 14px;">/ {{ $meta['duration_label'] ?? '' }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="py-2 text-center mb-2">
                                <a href="{{ url('package-details/'.$package->id) }}" class="btn btn-styled btn-sm btn-base-1 btn-outline btn-circle">
    View Package Details
</a>
                                @auth
                                    @if($userHasActiveOnlinePackage)
                                        <p class="package-expiry-text mt-2 mb-0">Subscribe again after {{ $userOnlineExpiresAtFormatted }}</p>
                                    @else
                                        <a href="{{ route('packages.checkout', ['id' => $package->id]) }}" class="btn btn-styled btn-sm btn-base-1 btn-circle mt-2">
                                            Buy Now
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ url('login') }}" class="btn btn-styled btn-sm btn-base-1 btn-circle mt-2">
                                        Login to Buy
                                    </a>
                                @endauth

                            </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @else
        <p class="text-center text-muted py-4">No online packages available at the moment.</p>
        @endif
        </div>
        </div>
        </div>

        <div id="tab-premium" class="package-tab-panel active" role="tabpanel">
        <div class="package-section-admin">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-3">
                
                <!-- <p class="package-section-desc">Assigned by admin (e.g. Platinum, Diamond, Royal, Sovereign Matchmaking). These define which Soul Mate categories you can search. Contact admin to get a package assigned.</p> -->

<div class="online-service">
      <div class="container">
        <!-- Personalized (Confidential) Service -->
        <div class="card personalized-card">
          <div class="contentWrap">
          <h2 class="heading heading-3 strong-600 col-black package-section-title text-left mb-4">Personalized (Confidential) Service: Expert Matchmaking</h2>

            <p class=" content-preview" id="preview2">
              Our Personalized Service is a premium, high-touch experience
              designed for those who value privacy, accuracy, and expert
              guidance. Here, we don't just provide a platform; we provide a
              Dedicated Matchmaking Partner.
            </p>
            <div class="content-full" id="fullContent2">
              <h6>Our Exclusive Process</h6>
              <ul>
                <li>
                  <strong>Complete Confidentiality:</strong> We handle your
                  profile with the utmost discretion. Your information is never
                  made public and is only shared with potential matches after
                  your explicit approval.
                </li>
                <li>
                  <strong>Profile Assessment:</strong> Upon receiving your
                  details and requirements, our experts carefully review them.
                  We believe in transparency—if we feel we can successfully find
                  a match, we proceed; otherwise, we offer an immediate and
                  respectful apology to save your time.
                </li>
                <li>
                  <strong>Curated Matching:</strong> If a suitable match is
                  available in our exclusive database, we guide you through
                  every step, from introduction to family meetings.
                </li>
                <li>
                  <strong>Tailored Plans:</strong> Our personalized service
                  offers various premium plans, each designed to cater to your
                  specific demands, lifestyle, and preferences.
                </li>
              </ul>
              <h6>Why Choose This?</h6>
              <p>
                <strong>Maximum Results, Minimum Effort:</strong> This service
                is ideal for busy professionals and elite families. We do the
                manual searching, screening, and coordination so you can focus
                on making the right decision.
              </p>
              <h6>Private &amp; Targeted Search</h6>
              <div class="note">
                We understand that many families prefer a discreet approach
                without any public advertisement. You desire a match that aligns
                perfectly with your values and mindset. To honor this, we offer
                <strong>Special Executive Plans</strong> where our CEO
                personally oversees the search based on your criteria. Our
                expert team takes the time to deeply understand your specific
                demands, ensuring we find a match that truly fits your family's
                vision.
              </div>
              <p class="quote">
                Great matches are not based on status or education—they are
                built on understanding and compatibility.
              </p>
              <p>
                Our experienced consultants take the time to speak with you,
                understand your personality, and then share carefully selected
                profiles that truly match your preferences.
              </p>
              <h6>Consultation Services Available</h6>
              <p>
                Senior marriage consultants are available for both office
                appointments and scheduled call sessions.
              </p>
              <p class="note">
                📌 Please note: Calls are only accepted with prior booking.
              </p>
              <h6>How Our Service Works</h6>
              <ul>
                <li>
                  Our team can guide you through the entire process via an
                  online or physical appointment.
                </li>
                <li>
                  Once you book your appointment, our consultants will explain
                  how our service works and assist you step by step.
                </li>
                <li>
                  <span class="check">✅</span> After booking, our team will
                  contact you directly within 24 to 48 hours.
                </li>
              </ul>
              <p>
                Book your consultation today and take the first step toward
                finding the right match.
              </p>
              <h6>Video Session Booking</h6>
              <p>
                A video consultation session can be booked for
                <strong>Rs. 2000</strong>. If you are genuinely interested,
                please book a session to:
              </p>
              <ul>
                <li>View suitable profiles</li>
                <li>Communicate directly with compatible matches</li>
              </ul>
              <h6>Payment Details</h6>
              <div class="paymantBox-details">
              <div class="payment-box">
                <strong>Bank Transfer</strong><br />
                Account Title: Urgent Rishta<br />
                Bank: UBL<br />
                IBAN: PK98UNIL0109000343139629
              </div>
              <div class="payment-box">
                <strong>Easypaisa / JazzCash</strong><br />
                Mobile Number: 03040227000<br />
                Account Name: Usman Zaheer
              </div>
              </div>
            </div>
            <div class="read-more-wrap">
              <button
                type="button"
                class="read-more-btn"
                id="toggleBtn2"
                aria-expanded="false"
              >
                <span class="btn-text">Read more</span>
                <svg
                  class="chevron"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
                        </div>
            </div>
        </div>
        @if(!$premiumPackages->isEmpty())
        <div class="container ss-container mt-2 ">
        <div class="row justify-content-center">
            @foreach ($premiumPackages as $package)
            @if($package->dataid!="99")
            @php
                $imgPath = '/images/package_'.$package->dataid.'.png';
                if (!file_exists(public_path($imgPath))) $imgPath = '/images/package_10.png';
            @endphp
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 ss-{{$loop->iteration}} ss mt-2">
                <div class="feature feature--boxed-border feature--bg-2 active ss-package_bg ss-admin  height">
                    <div class="icon-block--style-1-v5 text-center">
                        <div class="block-icon c-gray-dark">
                            <li style="list-style-type: none;">
                                <img src="{{ $imgPath }}" class="img-sm" height="100">
                            </li>
                        </div>
                        <div class="block-content mt-3">
                            <h3 class="col-black heading heading-5 strong-500 mb-2"><strong>{{ $package->name }}</strong></h3>
                        </div>
                        <div class="py-2 text-center mb-2">
                            <a href="{{ url('package-details/'.$package->id) }}" class="btn btn-styled btn-sm btn-base-1 btn-outline btn-circle">
                                View Package Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @else
        <p class="text-center text-muted py-4">No premium packages available at the moment.</p>
        @endif
        </div>
        </div>
        </div>
        </div>
    </div>
</section>

<script>
(function() {
    var tabBtns = document.querySelectorAll('.package-tabs .tab-btn');
    var panels = document.querySelectorAll('.package-tab-panel');
    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.getAttribute('data-tab');
            tabBtns.forEach(function(b) { b.classList.remove('active'); b.setAttribute('aria-selected', 'false'); });
            panels.forEach(function(p) { p.classList.remove('active'); });
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            var panel = document.getElementById('tab-' + tab);
            if (panel) panel.classList.add('active');
        });
    });
})();
</script>
   
     <div class="home-tit" style="background: black; margin-bottom:0;">
                        <p></p>
                        <h2><span>Our Services</span></h2>

                    </div>
    
<div class="match-buttons">
    
  <a href="#" class="match-btn digital-match" onclick="openPopupDetail()">Digital Match (Online Services)</a>
  <a href="#" class="match-btn personal-match" onclick="openPopupOffline()">Personal Match (Offline Service)</a>
</div>

<!-- Popup Modal -->
<div id="popup-modal-detail" class="popup-overlay">
<span class="close-btn" onclick="closePopupDetail()">&times;</span>
  <div class="popup-content">
    <h2>Digital Match (Online Services)</h2>
    <p>With our online service, clients create their own profiles and choose a plan according to their preferences. Based on the selected plan, they will see potential matches. They can express interest in a match, and if the other party accepts, both will be able to connect.</p>
    
    <p><strong>Special Offer:</strong> We are currently offering a <strong>50% discount</strong> on our website!</p>

    <p>For the best results, we highly recommend uploading a profile picture, as it increases the chances of receiving better responses.</p>

    
    
    <a href="https://urgentrishta.co/packages" class="popup-btn">I am Interested</a>
  </div>
</div>

<!-- Personal Match Popup -->
<div id="popup-modal-offline" class="popup-overlay">
  <div class="popup-content">
    <span class="close-btn" onclick="closePopupOffline()">&times;</span>
    <h2>Personal Match (Offline Service)</h2>
    <p>In our offline service, we provide four exclusive matchmaking services based on the client’s selected plan. Unlike online services, no discounts are available for this premium matchmaking experience.</p>

    <h3>1. Exclusive Access</h3>
    <p>Clients receive a unique login with a username and password to a private database where they can view detailed profiles (excluding photos). If they find a suitable match, they provide us with a code, and we will share the picture separately.</p>

    <h3>2. Personalized Weekly Matches</h3>
    <p>Our expert team curates and sends weekly match suggestions tailored to the client’s preferences.</p>

    <h3>3. Broadcast List</h3>
    <p>Clients are added to our exclusive broadcast list, where they receive new proposals daily.</p>

    <h3>4. Video Consultation</h3>
    <p>We arrange video sessions to present multiple matchmaking options, ensuring a smooth and transparent process.</p>

    <h2>Why Choose Us?</h2>
    <p>We never disappoint our clients! Unlike other services that show only a couple of proposals and disappear, we stay in touch with our clients and work continuously to find the perfect match based on their expectations.</p>

    <p><strong>This level of commitment and service is unmatched—you won’t find it anywhere else!</strong> So, get ready to enjoy a stress-free and professional matchmaking experience with us.</p>

    <a href="http://urgentrishta.wedlock204.com" class="popup-btn">I am Interested</a>
  </div>
</div>
<style>
.match-buttons {
  display: flex;
  justify-content: center;
  gap: 20px;
  padding-top: 20px;
  background: black;
}

a.match-btn.personal-match{
  background-color: transparent;
  color: #E91E63;
  border:1px solid #E91E63;
  padding: 12px 24px;
  text-decoration: none;
  font-size: 18px;
  border-radius: 6px;
  transition: 0.3s;
}
a.match-btn.personal-match:hover{
    background-color:  #E91E63;
  color: white;
  border:1px solid #E91E63;
}
a.match-btn.digital-match{
  background-color: #E91E63;
  color: #fff;
  padding: 12px 24px;
  text-decoration: none;
  font-size: 18px;
  border-radius: 6px;
  transition: 0.3s;
}
a.match-btn.digital-match:hover {
  background-color: transparent;
  color: #E91E63;
  border:1px solid #E91E63;
}

@media (max-width: 768px) {
  .match-buttons {
    flex-direction: column;
    align-items: center;
  }

  .match-btn {
    width: 80%;
    text-align: center;
  }
}

.popup-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  justify-content: center;
  align-items: center;
}

div#popup-modal-detail .popup-content, #popup-modal-offline .popup-content{
  background: #fff;
  padding: 20px;
  width: 80%;
  max-height:85vh;
  max-width: 600px;
  height: auto; 
  overflow-y: auto;
  border-radius: 8px;
  text-align: left;
  display: flex;
  flex-direction: column;
}
div#popup-modal-detail .popup-content h2, div#popup-modal-detail .popup-content h3, #popup-modal-offline .popup-content h2, #popup-modal-offline .popup-content h3{
    font-size: 20px;
}

div#popup-modal-detail .close-btn {
  position: absolute;
  top: 10px;
  right: 20px;
  font-size: 24px;
  cursor: pointer;
}

div#popup-modal-detail .popup-btn, #popup-modal-offline .popup-btn{
  display: inline-block;
  background-color: #D10000;
  color: #fff;
  padding: 10px 20px;
  margin-top: 15px;
  text-decoration: none;
  font-size: 16px;
  border-radius: 5px;
  transition: 0.3s;
  text-align: center;
}

div#popup-modal-detail .popup-btn:hover, #popup-modal-offline .popup-btn:hover {
  background-color: #E91E63;
}
.no-scroll {
  overflow: hidden;
  height: 100vh;
}
</style>

<script>
function openPopupDetail() {
  document.getElementById("popup-modal-detail").style.display = "flex";
  document.body.classList.add("no-scroll");
}

function closePopupDetail() {
  document.getElementById("popup-modal-detail").style.display = "none";
  document.body.classList.remove("no-scroll");
}

function openPopupOffline() {
  document.getElementById("popup-modal-offline").style.display = "flex";
  document.body.classList.add("no-scroll");
}

function closePopupOffline() {
  document.getElementById("popup-modal-offline").style.display = "none";
  document.body.classList.remove("no-scroll");
}
</script>

@auth
    @if(empty(User::retrieveUserObject()->online_package))
        <script type="text/javascript">
            $(document).ready(function() {
                swalAlert("info", "Select a Package", "Review packages available and contact Usman at 0304-0227000 for package activation.", null);
            });
        </script>
        
    @endif
@endauth




<script>
      (function () {
        function setupToggle(btnId, fullId, previewId) {
          var btn = document.getElementById(btnId);
          var full = document.getElementById(fullId);
          var preview = document.getElementById(previewId);
          if (!btn || !full || !preview) return;
          var btnText = btn.querySelector(".btn-text");
          var chevron = btn.querySelector(".chevron");
          btn.addEventListener("click", function () {
            var isOpen = full.classList.toggle("is-open");
            preview.classList.toggle("is-hidden", isOpen);
            btn.setAttribute("aria-expanded", isOpen);
            btnText.textContent = isOpen ? "Show less" : "Read more";
            chevron.style.transform = isOpen ? "rotate(180deg)" : "rotate(0)";
          });
        }
        setupToggle("toggleBtn", "fullContent", "preview");
        setupToggle("toggleBtn2", "fullContent2", "preview2");
      })();
    </script>
@endsection
