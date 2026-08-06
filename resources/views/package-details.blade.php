@extends('layouts.master')

@section('main-content')
<style>
/* General Styling */
body {
    font-family: 'Roboto', sans-serif;
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'Playfair Display', serif;
}

/* Card Styles */
.card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background-color: #d71a44;
    color: white;
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    padding: 15px;
    border-radius: 15px 15px 0 0;
}

.card-body ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.card-body ul li {
    font-size: 16px;
    padding: 10px 15px;
    border-bottom: 1px solid #f1f1f1;
    color: #555;
}

.card-body ul li strong {
    color: #d71a44;
}
.card-body strong {
    color: #d71a44;
}
.list-group-item:last-child {
    border-bottom: none;
}

.btn-lg {
    font-size: 18px;
    padding: 12px 25px;
    border-radius: 30px;
}

.bg-secondary {
    background-color: #6c757d !important;
    color: white;
}

.text-white {
    color: #fff !important;
}

/* Banner Styling */
.ss-banner {
    background: linear-gradient(to right, #d71a44, #e94c5b);
    color: white;
    text-align: center;
    padding: 100px 0;
}

.ss-banner h1 {
    font-size: 40px;
    font-weight: bold;
}

.ss-banner p {
    font-size: 18px;
    margin-top: 10px;
}

/* Scrollable Terms & Conditions Section */
.scrollable-section {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 15px;
    border: 1px solid #f1f1f1;
    border-radius: 15px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
    background: #ffffff;
}
</style>
<style>
    .ss-banner {
        padding: 183px 0px 50px !important;
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
        .ss-2 {
            margin-top: -70px;
        }
        .ss-2 .height {
            height: 96%;
        }
        .ss-2 .block-content {
            margin-top: 75px;
        }
    }

</style>
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 25;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex; /* Use flex to center modal */
    align-items: center; /* Center content vertically */
    justify-content: center; /* Center content horizontally */
}

.modal-content {
    background-color: #fefefe;
    margin: 20px;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 500px; /* Set a max width for larger screens */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow */
    position: relative; /* Position relative for close button */
}
.modal-content h2 {
    font-family: "Playfair Display", serif !important;
    font-size: 28px;
    color: black;
}
.modal-content h3 {
    font-family: "Playfair Display", serif !important;
    font-size: 24px;
    color: black;
}
.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute; /* Position the close button */
    right: 15px;
    top: 15px;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.bank-details p {
    margin: 5px 0; /* Margin for spacing */
}

.customer-note {
    margin: 15px 0; /* Margin for spacing around the note */
    background-color: #f8d7da; /* Light red background */
    padding: 10px; /* Padding around the note */
    border-radius: 4px; /* Rounded corners */
}
button.copy-button {
    background: transparent;
    border: 0;
    position: absolute;
    right: 10px;
}
button.copy-button:hover {
    color:pink;
}
button.copy-button svg{
    width:16px;
    height:16px;
}
@media (max-width: 768px) {
    .modal-content {
        width: 95%; /* Adjust width for smaller screens */
    }

    .close {
        font-size: 24px; /* Smaller close button */
    }
}
</style>


<section class="page-title page-title--style-1 ss-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">

                <h1 class="heading heading-1 strong-700 mb-0">Package Details<br>{{ $package->name }} Package</h1>
            </div>
        </div>
    </div>
</section>

<section class="slice sct-color-1 py-5">
    <div class="container">
        <div class="row">
            <!-- Left Section -->
            <div class="col-md-8">
                <!-- Package Details Card -->
                <div class="card shadow-lg mb-4">
                    <div class="card-header">
                        {{ $package->name }} Package
                    </div>
                    <div class="card-body">
                        @php
                            $descriptionParts = explode('|', (string) $package->description);
                            $meta = [];
                            $decoded = json_decode((string) $package->description, true);
                            if (is_array($decoded)) $meta = $decoded;
                        @endphp
                        <ul class="list-group list-group-flush">
                            @if(!empty($meta) && isset($meta['price']))
                                <li class="list-group-item">
                                    <strong>Price:</strong> {{ $meta['currency'] ?? 'USD' }} {{ number_format((float)$meta['price'], 2) }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Duration:</strong> {{ $meta['duration_label'] ?? 'N/A' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Access:</strong> Online services active until expiry.
                                </li>
                            @else
                                <li class="list-group-item">
                                    <strong>Registration Fee:</strong> {{ $descriptionParts[0] ?? 'N/A' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Success Fee:</strong> {{ $descriptionParts[1] ?? 'N/A' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Additional Benefits:</strong> 
                                    {{ $descriptionParts[2] ?? 'N/A' }}
                                    @if (isset($descriptionParts[3]))
                                        <br><small>{{ $descriptionParts[3] }}</small>
                                    @endif
                                </li>
                            @endif
                        </ul>
                        <div class="py-2 text-left mb-2">
                            @if(!empty($meta) && isset($meta['price']))
                                @auth
                                    @if($userHasActiveOnlinePackage ?? false)
                                        <p class="text-muted mb-0" style="font-size: 14px;">You have an active subscription. Subscribe again after {{ $userOnlineExpiresAtFormatted ?? '' }}.</p>
                                    @else
                                        <a class="btn btn-styled btn-sm btn-base-1 btn-outline btn-circle" href="{{ route('packages.checkout', ['id' => $package->id]) }}">
                                            Subscribe Now
                                        </a>
                                    @endif
                                @else
                                    <a class="btn btn-styled btn-sm btn-base-1 btn-outline btn-circle" href="{{ url('login') }}">
                                        Login to Subscribe
                                    </a>
                                @endauth
                            @else
                                <button
                                    class="btn btn-styled btn-sm btn-base-1 btn-outline btn-circle"
                                    data-package-name="{{ $package->name }}"
                                    data-package-price="{{ $descriptionParts[0] ?? '' }}"
                                    onclick="openBankDetails(this.dataset.packageName, this.dataset.packagePrice)">
                                    Subscibe Now
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="col-md-4">
                <!-- Package Image -->
                <div class="card shadow-lg mb-3">
                    @php
                        $imgPath = '/images/package_'.$package->dataid.'.png';
                        if (!file_exists(public_path($imgPath))) $imgPath = '/images/package_10.png';
                    @endphp
                    <img 
                        src="{{ $imgPath }}"
                        sizes="(max-width: 768px) 100vw, 50vw" 
                        alt="{{ $package->name }}" 
                        class="card-img-top" 
                        style="object-fit: cover; width: 100%; height: auto;">
                </div>

                <!-- Call to Action -->
                <div class="text-center mt-4">
                    <a href="https://api.whatsapp.com/send?phone=447445723296&text=Hello, I am interested in the {{ $package->name }} package." 
                       class="btn btn-success btn-lg btn-block" target="_blank">
                        <i class="fa fa-whatsapp"></i> Contact Us on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="slice bg-light py-5">
    <div class="container">
        <!-- Our Process Section -->
        <div class="card shadow-lg mb-4">
            <div class="card-header">
                Our Process
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Step 1:</strong> Our Bio data form would be downloaded to your system once you Register.
                    </li>
                    <li class="list-group-item">
                        <strong>Step 2:</strong> Kindly fill the Bio data form and send it back to us along with your Photos on <a href="mailto:urgentrishta.co@gmail.com" style="color: #d71a44;">urgentrishta.co@gmail.com</a>
                    </li>
                    <li class="list-group-item">
                        <strong>Step 3:</strong> We receive your complete profile with your detailed Requirements.
                    </li>
                    <li class="list-group-item">
                        <strong>Step 4:</strong> We verify your details.
                    </li>
                    <li class="list-group-item">
                        <strong>Step 5:</strong> We receive our Registration fee.
                    </li>
                    <li class="list-group-item">
                        <strong>Step 6:</strong> Registration fee can be paid through Debit card / Net Banking / Cash to our Company's Account <strong>"URGENT RISHTA"</strong>.
                    </li>
                    <li class="list-group-item">
                        <strong>Step 7:</strong> We share profiles, arrange meetings, and facilitate until your matchmaking is done.
                    </li>
                </ul>
            </div>
        </div>

        <!-- Terms & Conditions Section -->
        <div class="card shadow-lg">
            <div class="card-header">
                Terms & Conditions
            </div>
            <div class="card-body scrollable-section">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">1. We provide services according to the client's requirements and preferences.</li>
                    <li class="list-group-item">2. The registration process involves collecting client information, conducting interviews, and verifying documents. We reserve the right to verify the accuracy of the information provided by clients.</li>
                    <li class="list-group-item">3. Client will pay advance registration fees to start working & searching for their spouses. This isn't part of the full fee which both parties are bound to settle in advance.</li>
                    <li class="list-group-item">4. Additional fees may apply for special demands, such as a doctor or foreign national for widower or divorcee proposals.</li>
                    <li class="list-group-item">5. The registration fee is non-refundable and non-transferable.</li>
                    <li class="list-group-item">6. We will not share client information or photographs on social media but may share with other consultants to find a suitable match.</li>
                    <li class="list-group-item">7. Only serious clients interested in each other's profiles will be entitled to meet each other.</li>
                    <li class="list-group-item">8. If you directly access a proposal once met by us, it will be double charged and legal action may also be taken against you.</li>
                    <li class="list-group-item">9. We will share proposals as per your requirements. You should reply ASAP.</li>
                    <li class="list-group-item">10. If a client cancels the service, the advance fee will not be refunded.</li>
                    <li class="list-group-item">11. Changes in client requirements may affect our charges.</li>
                    <li class="list-group-item">12. Clients must provide identification documents and age proof.</li>
                    <li class="list-group-item">13. We will share profiles and photographs of potential matches with clients and facilitate communication.</li>
                    <li class="list-group-item">14. No work will begin until the advance fee is paid, and the agreement is signed.</li>
                    <li class="list-group-item">15. There is no time limit to find your matching proposals.</li>
                    <li class="list-group-item">16. If the match is not successful within 3 months, we will refund 50% of the advance fee. (See & ask for our refund policy).</li>
                    <li class="list-group-item">17. Clients are not allowed to share our terms of the agreement with anyone.</li>
                </ul>
            </div>
        </div>

        <!-- Note Section -->
        <div class="card shadow-lg mt-4">
            <div class="card-body">
                <p class="mb-3" style="font-size: 16px; color: #555;">
                    <strong>Note:</strong> If you agree to our terms and conditions, we welcome you to Urgent Rishta. Otherwise, we apologize for being unable to provide our services.
                </p>
                <p style="font-size: 16px; color: #555;">
                    I certify that all the information provided is accurate, and I am responsible for any inaccuracies. I have read and understood the terms and conditions of <strong>Urgent Rishta</strong> and agree to abide by them. I promise to keep the agency updated during the matching process and pay the full fee immediately after the match is confirmed. If I fail to comply, the agency reserves the right to take legal action against me.
                </p>
            </div>
        </div>
    </div>
</section>



<!-- Popup Modal -->
<div id="bankDetailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle"></h2>
        <h3>Package Price: <span id="packagePrice" style="color: green;"></span></h3> <!-- Display package price here -->
        <h3>Bank Details:</h3>
        <div class="bank-details">
            <p><strong>Account Title:</strong> Urgent Rishta</p>
            <p class="para">
                <strong>Account Number: </strong> 
                <span id="accountNumber">2640343139629</span>
                <button onclick="copyToClipboard('accountNumber', 'copyAccount')" class="copy-button" title="Copy Account Number">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M384 336l-192 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l140.1 0L400 115.9 400 320c0 8.8-7.2 16-16 16zM192 384l192 0c35.3 0 64-28.7 64-64l0-204.1c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1L192 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64L0 448c0 35.3 28.7 64 64 64l192 0c35.3 0 64-28.7 64-64l0-32-48 0 0 32c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l32 0 0-48-32 0z"/></svg>
                </button>
                <span id="copyAccount" class="copy-text" style="display: none;">Copied</span>
            </p>
            <p>
                <strong>Bank Name:</strong> United Bank Limited
            </p>
            <p class="para">
                <strong>IBAN: </strong> 
                <span id="iban">PK98UNIL0109000343139629</span>
                <button onclick="copyToClipboard('iban', 'copyIban')" class="copy-button" title="Copy IBAN">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M384 336l-192 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l140.1 0L400 115.9 400 320c0 8.8-7.2 16-16 16zM192 384l192 0c35.3 0 64-28.7 64-64l0-204.1c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1L192 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64L0 448c0 35.3 28.7 64 64 64l192 0c35.3 0 64-28.7 64-64l0-32-48 0 0 32c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l32 0 0-48-32 0z"/></svg>
                </button>
                <span id="copyIban" class="copy-text" style="display: none;">Copied</span>
            </p>
            <!--<p><strong>SWIFT Code:</strong> ABPAPKKA</p>-->
        </div>

        <!-- Customer Note -->
        <div class="customer-note">
            <p style="color: red; font-weight: bold;">Note:</p>
            <p>Please provide a screenshot of your payment on our WhatsApp after completing the transaction.</p>
        </div>

        <div class="text-center mb-2">
            <a id="whatsappLink" href="#" class="btn btn-styled btn-sm btn-base-1 btn-outline btn-circle" target="_blank">
                Contact Us on WhatsApp
            </a>
        </div>
    </div>
</div>


<script>
    function openBankDetails(packageName, packagePrice) {
        document.getElementById('modalTitle').innerText = 'Bank Details for ' + packageName;
        document.getElementById('packagePrice').innerText = packagePrice; // Set the package price in the modal
        
        // Update WhatsApp link with the correct package name
        const whatsappMessage = encodeURIComponent('Hello, I would like to proceed with the purchase of the ' + packageName + ' package.');
        document.getElementById('whatsappLink').href = 'https://api.whatsapp.com/send?phone=447445723296&text=' + whatsappMessage;
    
        document.getElementById('bankDetailsModal').style.display = 'flex'; // Use flex for centering
    }
    
    function closeModal() {
        document.getElementById('bankDetailsModal').style.display = 'none';
    }
    
    function copyToClipboard(elementId, copyTextId) {
        const textToCopy = document.getElementById(elementId).innerText; // Get the text content of the specified element
        navigator.clipboard.writeText(textToCopy).then(() => {
            // Show "Copied" text next to the SVG icon
            const copyTextElement = document.getElementById(copyTextId);
            copyTextElement.style.display = 'inline'; // Show the "Copied" text
            setTimeout(() => {
                copyTextElement.style.display = 'none'; // Hide it after 2 seconds
            }, 2000);
        }).catch(err => {
            console.error('Could not copy text: ', err); // Log any errors
        });
    }
</script>


@endsection

