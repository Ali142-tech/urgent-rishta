@extends('layouts.master')

@section('main-content')

<!--<script type="text/javascript">-->
<!--    window.onscroll = function() {-->
<!--        scrollFunction();-->
<!--    };-->
<!--    var header = document.getElementById("myHeader");-->
<!--    var sticky = header.offsetTop;-->

<!--    function scrollFunction() {-->
<!--        if (window.pageYOffset > sticky) {-->
<!--            header.classList.remove("sticky-header");-->
<!--        } else {-->
<!--            header.classList.remove("sticky-header");-->
<!--        }-->
<!--    }-->
<!--</script>-->

<script type="text/javascript">
    $(document).ready(function() {
        $('.set_langs').on('click', function() {
            var lang_url = $(this).data('href');
            $.ajax({
                url: lang_url,
                success: function(result) {
                    location.reload();
                }
            });
        });
    });
</script>
<section class="slice sct-color-1">
    <div class="container">
        <div class="section-title section-title--style-1 text-center mb-4">
            <h3 class="section-title-inner heading-1 strong-300 text-normal">
                Frequently Asked Questions </h3>
            <span class="section-title-delimiter clearfix d-none"></span>
        </div>

        <span class="clearfix"></span>

        <div class="paragraph paragraph-sm c-gray-light strong-300 pb-5">
            <div class="row">
                <div class="col-md-12">
                    <!-- Collapse - Style 4 -->
                    <div class="accordion accordion--style-4" id="collapseFive">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a class="accordion-toggle " data-toggle="collapse" data-parent="#collapseFive" href="#collapseFive-1" aria-hidden="false" aria-expanded="true">
                                        <span class="collapse-heading-icon">
                                            <i class="fa">1</i>
                                        </span>
                                        Where are most of your members from?</a>
                                </h4>
                            </div>
                            <div id="collapseFive-1" class="panel-collapse collapse show" style="border: 1px solid #e0eded">
                                <div class="card-body">
                                    <p>While most of our members are from Pakistan, we have members from all over the world.</p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseFive" href="#collapseFive-2" aria-expanded="false">
                                        <span class="collapse-heading-icon">
                                            <i class="fa">2</i>
                                        </span>
                                        I have never used a website so, I am hesitant.</a>
                                </h4>
                            </div>
                            <div id="collapseFive-2" class="panel-collapse collapse " style="border: 1px solid #e0eded">
                                <div class="card-body">
                                    <p>We don't blame you. Most of these matchmaking websites are not good at all. They use a "dating website" template for Muslims looking to get married, and it just doesn't work. Urgent Rishta is the alternative to the other websites, and many people that have used it to find their other half are now married. Urgent Rishta was built differently and designed specifically for Pakistanis looking to get married. The price is very affordable, and the website is private, which means only members can view who is using Urgent Rishta. In fact, the website is so private, that you can view only profiles from the opposite gender. So, if you and a friend are both using the same site, you would never see each other. Now that's private!</p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseFive" href="#collapseFive-3" aria-expanded="false">
                                        <span class="collapse-heading-icon">
                                            <i class="fa">3</i>
                                        </span>
                                        Do you have a free trial so that I can test it out before I subscribe?</a>
                                </h4>
                            </div>
                            <div id="collapseFive-3" class="panel-collapse collapse " style="border: 1px solid #e0eded">
                                <div class="card-body">
                                    <p>Some websites claim to give you a free subscription but the only thing "free" they give you is the ability to create a profile. What they don't tell you is that you can’t communicate with anyone until you pay their ridiculous fee, which can be between $29 and $44 a month. What makes it worse is that their website is public, which means that anyone and everyone can view your profile. They're basically using your public profile to pull in new members. Many people choose Urgent Rishta because they don't want their friends, coworkers and relatives to know they are on a matchmaking site. And since the site is private, their friends, coworkers and relatives will never know. Allowing a "free trial" would remove that sense of privacy, since it would allow anyone to view who is using the site. We believe that the only people who should know you're looking to get married are people who are also looking to get married.</p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseFive" href="#collapseFive-4" aria-expanded="false">
                                        <span class="collapse-heading-icon">
                                            <i class="fa">4</i>
                                        </span>
                                        How much does it cost for a membership?</a>
                                </h4>
                            </div>
                            <div id="collapseFive-4" class="panel-collapse collapse " style="border: 1px solid #e0eded">
                                <div class="card-body">
                                    <p>Finding your other half on a matchmaking website takes time and patience. It isn't an overnight process. Your ideal match may not be on the site today, but they may join weeks or months from now. This is one reason why we made Urgent Rishta affordable. You can check <a href="https://urgentrishta.co/packages">Our Packages</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseFive" href="#collapseFive-5" aria-expanded="false">
                                        <span class="collapse-heading-icon">
                                            <i class="fa">5</i>
                                        </span>
                                        Does Urgent Rishta really work?</a>
                                </h4>
                            </div>
                            <div id="collapseFive-5" class="panel-collapse collapse " style="border: 1px solid #e0eded">
                                <div class="card-body">
                                    <p>Yes, it does. At Urgent Rishta, we do our best to bring together like-minded Muslim brothers and sisters to marry. We do this by providing a matchmaking experience that is unique, easy, and as stress-free as possible. We also know that seeking a marriage partner online is a new and sometimes scary experience for some, so at Urgent Rishta we focus on privacy of our members. We keep your profile information private so that you do not have to worry that everyone and the world will know that you are looking to marry. We are also mindful of your money. We know that many online Muslim marriage sites charge very high fees just to use their service. At Urgent Rishta, our membership rate is one of the lowest — if not the lowest — out there and we do not try to nickel and dime you. Once you are a registered member with Urgent Rishta, you have full access to the site and there are no hidden fees to worry about, ever! We also like to keep things halal so we do our best to provide our members with an opportunity to meet a potential partner in a manner that is upright. We even welcome your parents to our websites and to be involved in your decision-making process. Urgent Rishta actually has an option that allows parents to sign up on behalf of their sons or daughters. Most of our parents have been married for decades and we believe they have valuable experience that will help their kids make the right decision. In a nutshell, Urgent Rishta really does work! With many success stories, it is no coincidence that thousands of people like Urgent Rishta on Facebook. Sign up today and give it a chance.</p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
