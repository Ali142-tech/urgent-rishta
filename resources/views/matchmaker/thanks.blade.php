@extends('layouts.master')

@section('main-content')
<style>
    .mk-thanks { font-family: 'Manrope', system-ui, sans-serif; background: #FBF7EF; padding: 80px 20px; text-align: center; }
    .mk-thanks__icon { width: 64px; height: 64px; border-radius: 50%; background: #123A2E; color: #C9974D; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 20px; }
    .mk-thanks h1 { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 28px; color: #123A2E; margin: 0 0 10px; }
    .mk-thanks p { font-size: 14.5px; color: #5B6560; max-width: 460px; margin: 0 auto 24px; line-height: 1.6; }
    .mk-thanks a { display: inline-block; background: #C9974D; color: #1C2321; font-weight: 700; font-size: 14px; padding: 13px 28px; border-radius: 999px; text-decoration: none; }
</style>
<div class="mk-thanks">
    <div class="mk-thanks__icon"><i class="fa fa-check"></i></div>
    <h1>Application Received</h1>
    <p>Thank you for applying to become a partner with Urgent Rishta. Our team will review your application and be in touch soon. You can already log in to your account below.</p>
    <a href="{{ url('/') }}">Back to Home</a>
</div>
@endsection
