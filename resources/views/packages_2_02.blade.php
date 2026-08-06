@extends('layouts.master')
@section('main-content')
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
    .special-image { position: absolute; width: 66px; height: auto; top: -20px; right: -20px; }

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
<section class="slice sct-color-1 pricing-plans pricing-plans--style-1 has-bg-cover bg-size-cover" style=" background-position: bottom bottom;">
    <div class="container ss-container">
        <span class="clearfix"></span>
        <div class="row justify-content-center">
            @foreach ($packages as $package)
            @if($package->dataid!="99")
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 ss-{{$loop->iteration}} ss">
                <div class="feature feature--boxed-border feature--bg-2 active ss-package_bg mt-4 height">
                    <div class="icon-block--style-1-v5 text-center">
                        <div class="block-icon c-gray-dark">
                            <li style="list-style-type: none;">
                                <img src="/images/package_{{$package->dataid}}.png" class="img-sm" height="100">
                            </li>
                        </div>
                        <!--<div class="block-content">-->
                        <!--    <h3 class="col-black heading heading-5 strong-500"><strong>{{$package->name}}</strong></h3>-->
                        <!--    <h3 class="price-tag col-black" style="font-size: 20px;">Registration: {{explode('|', $package->description)[0]}}</h3>-->
                        <!--    <ul class="pl-0 pr-0 mt-0">-->
                        <!--        <li class="package_items">Success Fee: <span class="c-base-1"><b>{{explode('|', $package->description)[1]}}</b></span> <span class="c-gray-light">after successful match</span></li>-->
                        <!--        <li class="package_items"><span><b>{{explode('|', $package->description)[2]}}</b></span>@if (sizeof(explode('|', $package->description))==4) <span class="c-gray-light">{{explode('|', $package->description)[3]}}</span>@endif</li>-->
                        <!--    </ul>-->
                            
                        <!--     @if( $package->name == "Diamond" || $package->name == "Royal" )-->
                        <!--    <div class="special-image">-->
                        <!--        <img src="/images/special_{{$package->name}}.png" alt="{{$package->name}} Special Image" class="img-fluid">-->
                        <!--    </div>-->
                        <!--    @endif-->
                            

                           

                        <!--</div>-->
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
    </div>
</section>

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
<script type="text/javascript">
    @auth
    @if(empty(User::retrieveUserObject()->package))
    $(document).ready(function() {
        swalAlert("info", "Select a Package", "Review packages available and contact Usman at 0304-0227000 for package activation.", null);
    });
    @endif
    @endauth
</script>

@endsection
