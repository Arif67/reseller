<!-- ===== Services ===== -->
<section class="section" id="services">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">আমাদের সার্ভিস</span>
            <h2>একই প্ল্যাটফর্মে সব সুবিধা</h2>
            <p>রিসেলিং থেকে শুরু করে হোলসেল, কাস্টম প্রিন্টিং, ফ্রিল্যান্সিং — সব আয়ের উৎস এক জায়গায়।</p>
        </div>
        <div class="grid grid-4">
            @php
                $services = [
                    ['fa-arrows-rotate', 'রিসেলিং ও ড্রপশিপিং'],
                    ['fa-warehouse', 'হোলসেল প্রোডাক্ট'],
                    ['fa-print', 'কাস্টম প্রিন্টিং'],
                    ['fa-handshake', 'সাপ্লায়ার / ভেন্ডরশিপ'],
                    ['fa-users', 'লিডারশিপ ইনকাম'],
                    ['fa-laptop-code', 'ফ্রিল্যান্সিং মার্কেটপ্লেস'],
                    ['fa-list-check', 'মাইক্রো জবস'],
                    ['fa-mobile-screen', 'মোবাইল রিচার্জ'],
                    ['fa-bullhorn', 'ডিজিটাল মার্কেটিং'],
                    ['fa-rocket', 'বুস্টিং সার্ভিস'],
                    ['fa-file-invoice-dollar', 'বিল পেমেন্ট'],
                    ['fa-globe', 'ই-কমার্স ওয়েবসাইট'],
                ];
            @endphp
            @foreach ($services as $s)
                <div class="card">
                    <div class="ic"><i class="fa-solid {{ $s[0] }}"></i></div>
                    <h3 style="font-size:16px;margin-bottom:0">{{ $s[1] }}</h3>
                </div>
            @endforeach
        </div>
    </div>
</section>
