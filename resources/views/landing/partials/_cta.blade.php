<!-- ===== CTA band ===== -->
@php $ctaRegisterUrl = Route::has('reseller.register') ? route('reseller.register') : '#'; @endphp
<section class="section">
    <div class="container">
        <div class="cta-band">
            <h2>আজই আপনার ব্যবসা শুরু করুন</h2>
            <p>ফ্রি রেজিস্ট্রেশন করে যুক্ত হোন দেশের সর্ববৃহৎ রিসেলিং কমিউনিটিতে। পুঁজি লাগবে না, শুধু ইচ্ছাশক্তি।</p>
            <a href="{{ $ctaRegisterUrl }}" class="btn btn-light"><i class="fa-solid fa-user-plus"></i> ফ্রি
                রেজিস্ট্রেশন করুন</a>
        </div>
    </div>
</section>
