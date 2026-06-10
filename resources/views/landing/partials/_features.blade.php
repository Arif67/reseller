<!-- ===== Features ===== -->
<section class="section section--soft">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">বিশেষ সুবিধা</span>
            <h2>যা আপনাকে এগিয়ে রাখবে</h2>
        </div>
        <div class="grid grid-3">
            @php
                $features = [
                    ['fa-truck-fast', 'দ্রুত ডেলিভারি', 'সেইম-ডে বুকিং, ৪৮–৭২ ঘণ্টায় কাস্টমারের হাতে প্রোডাক্ট পৌঁছে যায়।'],
                    ['fa-magnifying-glass', 'ইমেজ সার্চ', 'ছবি দিয়েই প্রোডাক্ট খুঁজে বের করুন, সময় বাঁচান।'],
                    ['fa-video', 'অরিজিনাল জুম ভিডিও', 'প্রতিটি প্রোডাক্টের আসল ভিডিও দিয়ে সহজে সেল করুন।'],
                    ['fa-money-bill-wave', 'ক্যাশ অন ডেলিভারি', 'কাস্টমার পণ্য হাতে পেয়ে টাকা দেবে — বিশ্বাস বাড়বে।'],
                    ['fa-star', 'রিভিউ ও রেটিং', 'ভেরিফায়েড রিভিউসহ প্রোডাক্ট, বিক্রি হয় সহজে।'],
                    ['fa-headset', '২৪/৭ সাপোর্ট', 'যেকোনো সমস্যায় কল সেন্টার ও কাস্টমার কেয়ার সবসময় পাশে।'],
                ];
            @endphp
            @foreach ($features as $f)
                <div class="card">
                    <div class="ic"><i class="fa-solid {{ $f[0] }}"></i></div>
                    <h3>{{ $f[1] }}</h3>
                    <p>{{ $f[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
