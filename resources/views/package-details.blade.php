@extends('layouts.master')

@section('main-content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Manrope:wght@400;500;600;700;800&display=swap');

    body.page-package-details #main-content { background: #FBF7EF; }

    .pd-page {
        --pd-green: #123A2E;
        --pd-green-deep: #0F2E24;
        --pd-gold: #C9974D;
        --pd-cream: #FBF7EF;
        --pd-sand: #EFE7D6;
        --pd-line: #F0EADD;
        --pd-terracotta: #B5674A;
        --pd-text: #5B6560;
        --pd-ink: #1C2321;
        --pd-ink-2: #33403A;
        --pd-cream-text: #EFE3C8;
        --pd-cream-text-2: #D7E4DC;
        font-family: 'Manrope', system-ui, sans-serif;
        background: var(--pd-cream);
        color: var(--pd-ink);
    }
    .pd-page * { box-sizing: border-box; }
    .pd-page a { text-decoration: none; }

    .pd-breadcrumb {
        padding: 20px 56px 0;
        font-size: 12.5px;
        color: var(--pd-text);
    }
    .pd-breadcrumb a { color: var(--pd-text); }
    .pd-breadcrumb a:hover { color: var(--pd-green); }
    .pd-breadcrumb span { color: var(--pd-green); font-weight: 700; }

    .pd-summary {
        padding: 32px 56px 64px;
        display: grid;
        grid-template-columns: 0.85fr 1.15fr;
        gap: 48px;
        align-items: start;
    }
    .pd-sticky { position: sticky; top: 90px; }
    .pd-img-wrap {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(18,58,46,0.12);
        margin-bottom: 20px;
        background: linear-gradient(160deg, var(--pd-green) 0%, #1F5C46 100%);
        height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pd-badge {
        width: 128px;
        height: 128px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.24);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: var(--pd-gold);
    }
    .pd-fee-card {
        background: var(--pd-green);
        border-radius: 16px;
        padding: 24px;
        color: #fff;
    }
    .pd-fee-eyebrow {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--pd-gold);
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .pd-fee-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.12);
        gap: 12px;
    }
    .pd-fee-row:last-child { border-bottom: none; }
    .pd-fee-row span:first-child { font-size: 13px; color: var(--pd-cream-text-2); }
    .pd-fee-row span:last-child { font-weight: 700; text-align: right; }

    .pd-eyebrow {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--pd-terracotta);
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .pd-h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(28px, 3.4vw, 38px);
        font-weight: 600;
        margin: 0 0 18px;
        color: var(--pd-ink);
    }
    .pd-lead {
        font-size: 15px;
        line-height: 1.8;
        color: var(--pd-text);
        margin: 0 0 24px;
        max-width: 640px;
    }
    .pd-cta-row { display: flex; gap: 14px; margin-bottom: 36px; flex-wrap: wrap; }
    .pd-btn-gold, .pd-btn-green {
        font-weight: 700;
        font-size: 14px;
        padding: 14px 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        font-family: 'Manrope', system-ui, sans-serif;
    }
    .pd-btn-gold { background: var(--pd-gold); color: var(--pd-green); }
    .pd-btn-green { background: #25D366; color: #fff; }
    .pd-muted-note { font-size: 13.5px; color: var(--pd-text); }

    .pd-section-label {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--pd-terracotta);
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .pd-timeline { display: flex; flex-direction: column; gap: 0; position: relative; margin-bottom: 36px; }
    .pd-timeline::before {
        content: "";
        position: absolute;
        left: 15px;
        top: 8px;
        bottom: 8px;
        width: 1.5px;
        background: var(--pd-line);
    }
    .pd-step { display: flex; gap: 16px; padding: 10px 0; position: relative; }
    .pd-step-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--pd-green);
        color: var(--pd-cream-text);
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        flex-shrink: 0;
    }
    .pd-step.pd-step--final .pd-step-num { background: var(--pd-gold); color: var(--pd-green); }
    .pd-step-text { font-size: 13.5px; line-height: 1.7; color: var(--pd-ink-2); padding-top: 5px; }
    .pd-step.pd-step--final .pd-step-text { font-weight: 700; }
    .pd-step-text a { color: var(--pd-green); font-weight: 700; }

    .pd-terms-card {
        background: #fff;
        border-radius: 14px;
        padding: 8px 20px;
        box-shadow: 0 2px 14px rgba(18,58,46,0.06);
        max-height: 280px;
        overflow-y: auto;
        margin-bottom: 8px;
    }
    /* The site's global reset (new-theme.css) sets ol/ul/li { list-style: none } —
       override it here so the clauses actually show their numbers. */
    .pd-terms-card ol { margin: 0; padding: 12px 0 12px 20px; list-style: decimal !important; }
    .pd-terms-card li {
        font-size: 13px;
        line-height: 2.1;
        color: #41504A;
        list-style: decimal !important;
        display: list-item !important;
    }
    .pd-terms-card li::marker { color: var(--pd-terracotta); font-weight: 700; }

    .pd-fine-print { font-size: 12.5px; color: var(--pd-text); line-height: 1.6; margin-bottom: 24px; }

    .pd-agreement {
        background: var(--pd-sand);
        border-radius: 14px;
        padding: 22px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
        margin-bottom: 24px;
    }
    .pd-agreement input[type="checkbox"] {
        width: 20px;
        height: 20px;
        border-radius: 5px;
        border: 2px solid var(--pd-green);
        flex-shrink: 0;
        margin: 2px 0 0;
        accent-color: var(--pd-green);
        cursor: pointer;
    }
    .pd-agreement label { font-size: 13px; line-height: 1.7; color: #41504A; cursor: pointer; }
    .pd-agreement strong { color: var(--pd-green); }

    .pd-btn-confirm {
        background: var(--pd-green);
        color: var(--pd-cream-text);
        font-weight: 700;
        font-size: 14px;
        padding: 14px 28px;
        border-radius: 10px;
        text-align: center;
        max-width: 280px;
        display: inline-block;
        border: none;
        cursor: pointer;
        font-family: 'Manrope', system-ui, sans-serif;
        transition: opacity .2s ease;
    }
    .pd-btn-confirm[disabled],
    .pd-btn-confirm.is-disabled {
        opacity: .45;
        pointer-events: none;
        cursor: not-allowed;
    }

    @media (max-width: 900px) {
        .pd-breadcrumb, .pd-summary { padding-left: 22px; padding-right: 22px; }
        .pd-summary { grid-template-columns: 1fr; }
        .pd-sticky { position: static; }
    }

    /* ============ Payment modal ============ */
    .pd-modal-overlay {
        display: none;
        position: fixed;
        z-index: 2000;
        inset: 0;
        background: rgba(15,46,36,0.55);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .pd-modal-overlay.is-open { display: flex; }
    .pd-modal-card {
        background: #fff;
        border-radius: 20px;
        padding: 32px;
        max-width: 460px;
        width: 100%;
        box-shadow: 0 30px 60px rgba(18,58,46,0.18);
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
    }
    .pd-modal-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; gap: 10px; }
    .pd-modal-title { font-family: 'Playfair Display', Georgia, serif; font-size: 22px; font-weight: 600; color: var(--pd-ink); }
    .pd-modal-sub { font-size: 13px; color: var(--pd-text); margin-top: 2px; }
    .pd-modal-close {
        width: 26px; height: 26px; border-radius: 50%; background: var(--pd-line); color: var(--pd-text);
        font-size: 16px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; flex-shrink: 0;
    }
    .pd-amount-row {
        background: var(--pd-green);
        color: #fff;
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 10px;
    }
    .pd-amount-row .lbl { font-size: 13px; color: var(--pd-cream-text-2); }
    .pd-amount-row .val { font-family: 'Playfair Display', Georgia, serif; font-size: 22px; color: var(--pd-gold); }
    .pd-bank-label {
        font-size: 11px; font-weight: 700; letter-spacing: .08em; color: var(--pd-terracotta);
        text-transform: uppercase; margin-bottom: 10px;
    }
    .pd-bank-rows { display: flex; flex-direction: column; margin-bottom: 18px; }
    .pd-bank-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--pd-line); gap: 12px; }
    .pd-bank-row:last-child { border-bottom: none; }
    .pd-bank-row .k { font-size: 12.5px; color: var(--pd-text); }
    .pd-bank-row .v { font-size: 13px; font-weight: 700; color: var(--pd-ink); display: flex; align-items: center; gap: 6px; text-align: right; }
    .pd-copy-btn { background: transparent; border: none; color: var(--pd-gold); cursor: pointer; padding: 0; display: inline-flex; }
    .pd-copy-btn svg { width: 14px; height: 14px; fill: currentColor; }
    .pd-copy-text { font-size: 11px; color: var(--pd-green); }
    .pd-modal-note { font-size: 12.5px; color: var(--pd-text); line-height: 1.6; margin-bottom: 18px; }
    .pd-modal-wa {
        background: #25D366; color: #fff; font-weight: 700; font-size: 13.5px; padding: 13px; border-radius: 10px;
        text-align: center; display: block;
    }

    /* Footer intentionally NOT overridden here — uses the single shared footer
       styling from layouts/master.blade.php, same as every other page on the site. */
</style>

@php
    $descriptionParts = explode('|', (string) $package->description);
    $meta = [];
    $decoded = json_decode((string) $package->description, true);
    if (is_array($decoded)) $meta = $decoded;
    $isOnlinePackage = !empty($meta) && isset($meta['price']);
    $planIcons = [
        'Platinum' => 'fa-shield',
        'Diamond' => 'fa-diamond',
        'Royal' => 'fa-star',
        'Imperial' => 'fa-trophy',
    ];
    $badgeIcon = $planIcons[trim($package->name)] ?? 'fa-certificate';
    $planFullNames = [
        'Royal' => 'Royal - Executive Matchmaking',
        'Imperial' => 'Imperial - Bespoke Private Matchmaking',
    ];
    $displayName = $planFullNames[trim($package->name)] ?? $package->name;
    $whatsappNumber = '447445723296';
    $waHref = 'https://api.whatsapp.com/send?phone='.$whatsappNumber.'&text='.rawurlencode('Hello, I am interested in the '.$package->name.' package.');
@endphp

<div class="pd-page">

    <div class="pd-breadcrumb">
        <a href="{{ url('packages') }}">Premium Plans</a> &nbsp;/&nbsp; <span>{{ $displayName }} Package</span>
    </div>

    <div class="pd-summary">
        <div class="pd-sticky">
            <div class="pd-img-wrap">
                <div class="pd-badge"><i class="fa {{ $badgeIcon }}" aria-hidden="true"></i></div>
            </div>
            <div class="pd-fee-card">
                @if($isOnlinePackage)
                    <div class="pd-fee-eyebrow">Online Package</div>
                    <div class="pd-fee-row"><span>Price</span><span>{{ $meta['currency'] ?? 'USD' }} {{ number_format((float)$meta['price'], 2) }}</span></div>
                    <div class="pd-fee-row"><span>Duration</span><span>{{ $meta['duration_label'] ?? 'N/A' }}</span></div>
                    <div class="pd-fee-row"><span>Access</span><span>Until expiry</span></div>
                @else
                    <div class="pd-fee-eyebrow">Personalized Service</div>
                    <div class="pd-fee-row"><span>Registration Fee</span><span  class="text-white">{{ $descriptionParts[0] ?? 'N/A' }}</span></div>
                    <div class="pd-fee-row"><span>Success Fee</span><span class="text-white">{{ $descriptionParts[1] ?? 'N/A' }}</span></div>
                @endif
            </div>
        </div>

        <div>
            <div class="pd-eyebrow">Package Details</div>
            <h1 class="pd-h1">{{ $displayName }} Package</h1>

            @if(!$isOnlinePackage)
            <p class="pd-lead">
                {{ $descriptionParts[2] ?? 'Tailored matchmaking curated around your preferences and lifestyle.' }}
                @if(isset($descriptionParts[3]))
                    <br><small>{{ $descriptionParts[3] }}</small>
                @endif
            </p>
            @endif

            <div class="pd-cta-row">
                @if($isOnlinePackage)
                    @auth
                        @if($userHasActiveOnlinePackage ?? false)
                            <div class="pd-muted-note">You have an active subscription. Subscribe again after {{ $userOnlineExpiresAtFormatted ?? '' }}.</div>
                        @else
                            <a class="pd-btn-gold" href="{{ route('packages.checkout', ['id' => $package->id]) }}">Subscribe Now</a>
                        @endif
                    @else
                        <a class="pd-btn-gold" href="{{ url('login') }}">Login to Subscribe</a>
                    @endauth
                @else
                    <button type="button" class="pd-btn-gold"
                        data-package-name="{{ $package->name }}"
                        data-package-price="{{ $descriptionParts[0] ?? '' }}"
                        onclick="openBankDetails(this.dataset.packageName, this.dataset.packagePrice)">
                        Subscribe Now
                    </button>
                @endif
                <a class="pd-btn-green" href="{{ $waHref }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i> Contact Us on WhatsApp</a>
            </div>

            <!-- OUR PROCESS -->
            <div class="pd-section-label">Our Process</div>
            <div class="pd-timeline">
                <div class="pd-step"><div class="pd-step-num">1</div><div class="pd-step-text">Our Bio Data form downloads to your system once you register.</div></div>
                <div class="pd-step"><div class="pd-step-num">2</div><div class="pd-step-text">Fill the Bio Data form and send it back with your photos to <a href="mailto:urgentrishta.co@gmail.com">urgentrishta.co@gmail.com</a>.</div></div>
                <div class="pd-step"><div class="pd-step-num">3</div><div class="pd-step-text">We receive your complete profile with your detailed requirements.</div></div>
                <div class="pd-step"><div class="pd-step-num">4</div><div class="pd-step-text">We verify your details.</div></div>
                <div class="pd-step"><div class="pd-step-num">5</div><div class="pd-step-text">We receive our registration fee.</div></div>
                <div class="pd-step"><div class="pd-step-num">6</div><div class="pd-step-text">Registration fee is payable via Debit Card, Net Banking, or Cash to our company account <strong>&quot;URGENT RISHTA&quot;</strong>.</div></div>
                <div class="pd-step pd-step--final"><div class="pd-step-num">7</div><div class="pd-step-text">We share profiles, arrange meetings and facilitate until your matchmaking is done.</div></div>
            </div>

            <!-- TERMS -->
            <div class="pd-section-label">Terms &amp; Conditions</div>
            <div class="pd-terms-card">
                <ol>
                    <li>We provide services according to the client's requirements and preferences.</li>
                    <li>The registration process involves collecting client information, conducting interviews, and verifying documents. We reserve the right to verify the accuracy of the information provided by clients.</li>
                    <li>Client will pay advance registration fees to start working &amp; searching for their spouses. This isn't part of the full fee which both parties are bound to settle in advance.</li>
                    <li>Additional fees may apply for special demands, such as a doctor or foreign national for widower or divorcee proposals.</li>
                    <li>The registration fee is non-refundable and non-transferable.</li>
                    <li>We will not share client information or photographs on social media but may share with other consultants to find a suitable match.</li>
                    <li>Only serious clients interested in each other's profiles will be entitled to meet each other.</li>
                    <li>If you directly access a proposal once met by us, it will be double charged and legal action may also be taken against you.</li>
                    <li>We will share proposals as per your requirements. You should reply ASAP.</li>
                    <li>If a client cancels the service, the advance fee will not be refunded.</li>
                    <li>Changes in client requirements may affect our charges.</li>
                    <li>Clients must provide identification documents and age proof.</li>
                    <li>We will share profiles and photographs of potential matches with clients and facilitate communication.</li>
                    <li>No work will begin until the advance fee is paid, and the agreement is signed.</li>
                    <li>There is no time limit to find your matching proposals.</li>
                    <li>If the match is not successful within 3 months, we will refund 50% of the advance fee. (See &amp; ask for our refund policy).</li>
                    <li>Clients are not allowed to share our terms of the agreement with anyone.</li>
                </ol>
            </div>
            <div class="pd-fine-print">If you agree to our terms and conditions, we welcome you to Urgent Rishta. Otherwise, we apologize for being unable to provide our services.</div>

            <!-- AGREEMENT -->
            <div class="pd-agreement">
                <input type="checkbox" id="pdAgree" onchange="document.getElementById('pdConfirmBtn').classList.toggle('is-disabled', !this.checked)">
                <label for="pdAgree">I certify that all the information I provide is accurate and I am responsible for any inaccuracies. I have read and understood the terms and conditions of <strong>Urgent Rishta</strong> and agree to abide by them, and to pay the full fee immediately after a match is confirmed.</label>
            </div>

            @if($isOnlinePackage)
                @auth
                    @if($userHasActiveOnlinePackage ?? false)
                        <div class="pd-muted-note">You have an active subscription. Subscribe again after {{ $userOnlineExpiresAtFormatted ?? '' }}.</div>
                    @else
                        <a id="pdConfirmBtn" class="pd-btn-confirm is-disabled" href="{{ route('packages.checkout', ['id' => $package->id]) }}">Subscribe Now</a>
                    @endif
                @else
                    <a id="pdConfirmBtn" class="pd-btn-confirm is-disabled" href="{{ url('login') }}">Login to Subscribe</a>
                @endauth
            @else
                <button type="button" id="pdConfirmBtn" class="pd-btn-confirm is-disabled"
                    data-package-name="{{ $package->name }}"
                    data-package-price="{{ $descriptionParts[0] ?? '' }}"
                    onclick="if(!this.classList.contains('is-disabled')) openBankDetails(this.dataset.packageName, this.dataset.packagePrice)">
                    Subscribe Now
                </button>
            @endif
        </div>
    </div>

</div><!-- /.pd-page -->

<!-- Payment modal -->
<div id="bankDetailsModal" class="pd-modal-overlay">
    <div class="pd-modal-card">
        <div class="pd-modal-head">
            <div>
                <div class="pd-modal-title">Package Price</div>
                <div class="pd-modal-sub" id="modalTitle"></div>
            </div>
            <button type="button" class="pd-modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="pd-amount-row">
            <span class="lbl">Amount Due</span>
            <span class="val" id="packagePrice"></span>
        </div>
        <div class="pd-bank-label">Bank Details</div>
        <div class="pd-bank-rows">
            <div class="pd-bank-row"><span class="k">Account Title</span><span class="v">Urgent Rishta</span></div>
            <div class="pd-bank-row">
                <span class="k">Account Number</span>
                <span class="v">
                    <span id="accountNumber">2640343139629</span>
                    <button type="button" onclick="copyToClipboard('accountNumber', 'copyAccount')" class="pd-copy-btn" title="Copy Account Number">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M384 336l-192 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l140.1 0L400 115.9 400 320c0 8.8-7.2 16-16 16zM192 384l192 0c35.3 0 64-28.7 64-64l0-204.1c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1L192 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64L0 448c0 35.3 28.7 64 64 64l192 0c35.3 0 64-28.7 64-64l0-32-48 0 0 32c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l32 0 0-48-32 0z"/></svg>
                    </button>
                    <span id="copyAccount" class="pd-copy-text" style="display:none;">Copied</span>
                </span>
            </div>
            <div class="pd-bank-row"><span class="k">Bank Name</span><span class="v">United Bank Limited</span></div>
            <div class="pd-bank-row">
                <span class="k">IBAN</span>
                <span class="v">
                    <span id="iban">PK98UNIL0109000343139629</span>
                    <button type="button" onclick="copyToClipboard('iban', 'copyIban')" class="pd-copy-btn" title="Copy IBAN">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M384 336l-192 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l140.1 0L400 115.9 400 320c0 8.8-7.2 16-16 16zM192 384l192 0c35.3 0 64-28.7 64-64l0-204.1c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1L192 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64L0 448c0 35.3 28.7 64 64 64l192 0c35.3 0 64-28.7 64-64l0-32-48 0 0 32c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l32 0 0-48-32 0z"/></svg>
                    </button>
                    <span id="copyIban" class="pd-copy-text" style="display:none;">Copied</span>
                </span>
            </div>
        </div>
        <div class="pd-modal-note"><strong style="color:#B5674A;">Note:</strong> Please provide a screenshot of your payment on our WhatsApp after completing the transaction.</div>
        <a id="whatsappLink" href="#" class="pd-modal-wa" target="_blank" rel="noopener">Contact Us on WhatsApp</a>
    </div>
</div>

<script>
    function openBankDetails(packageName, packagePrice) {
        document.getElementById('modalTitle').innerText = packageName + ' — Registration Fee';
        document.getElementById('packagePrice').innerText = packagePrice;

        var whatsappMessage = encodeURIComponent('Hello, I would like to proceed with the purchase of the ' + packageName + ' package.');
        document.getElementById('whatsappLink').href = 'https://api.whatsapp.com/send?phone={{ $whatsappNumber }}&text=' + whatsappMessage;

        document.getElementById('bankDetailsModal').classList.add('is-open');
    }

    function closeModal() {
        document.getElementById('bankDetailsModal').classList.remove('is-open');
    }

    function copyToClipboard(elementId, copyTextId) {
        var textToCopy = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(textToCopy).then(function () {
            var copyTextElement = document.getElementById(copyTextId);
            copyTextElement.style.display = 'inline';
            setTimeout(function () {
                copyTextElement.style.display = 'none';
            }, 2000);
        }).catch(function (err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>

@endsection
