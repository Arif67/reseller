<!-- ===== Testimonials ===== -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">সফলতার গল্প</span>
            <h2>আমাদের রিসেলাররা যা বলছেন</h2>
        </div>
        <div class="grid grid-3">
            @php
                $reviews = [
                    ['রাকিব হাসান', 'ঢাকা', 'বিনা পুঁজিতে শুরু করেছিলাম, এখন মাসে ৪০,০০০+ টাকা আয় করছি। পেমেন্ট সবসময় টাইমে পাই।'],
                    ['সাবরিনা আক্তার', 'চট্টগ্রাম', 'ঘরে বসেই ব্যবসা করছি ২ বছর ধরে। সাপোর্ট টিম খুবই হেল্পফুল, প্রোডাক্ট কোয়ালিটিও দারুণ।'],
                    ['মেহেদী হাসান', 'সিলেট', 'স্টুডেন্ট অবস্থায় শুরু করি, এখন টিম বানিয়ে মাসে লাখ টাকার বেশি আয় হচ্ছে।'],
                ];
            @endphp
            @foreach ($reviews as $r)
                <div class="quote">
                    <div class="stars">★★★★★</div>
                    <p>“{{ $r[2] }}”</p>
                    <div class="who">
                        <div class="av">{{ mb_substr($r[0], 0, 1) }}</div>
                        <div>
                            <b>{{ $r[0] }}</b>
                            <small>{{ $r[1] }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
