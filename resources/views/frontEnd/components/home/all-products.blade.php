@push('css')
<style>
.ap-section {
    padding: 24px 0 32px;
    background: #EFF0F5;
}
.ap-section .custom-container {
    max-width: 1520px;
    margin: 0 auto;
    padding: 0 15px;
}
.ap-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.ap-title {
    font-size: 18px;
    font-weight: 800;
    color: #222;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ap-title::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 20px;
    background: #F85606;
    border-radius: 3px;
}
.ap-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 8px;
}
.ap-loader {
    display: none;
    justify-content: center;
    padding: 20px 0 8px;
    gap: 6px;
    align-items: center;
    font-size: 13px;
    color: #888;
}
.ap-loader.active { display: flex; }
.ap-spinner {
    width: 20px; height: 20px;
    border: 2px solid #eee;
    border-top-color: #F85606;
    border-radius: 50%;
    animation: ap-spin .7s linear infinite;
}
@keyframes ap-spin { to { transform: rotate(360deg); } }
.ap-end-msg {
    display: none;
    text-align: center;
    padding: 14px 0 4px;
    font-size: 12px;
    color: #bbb;
}
.ap-end-msg.active { display: block; }

@media (max-width: 1199.98px) { .ap-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
@media (max-width: 991.98px)  { .ap-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
@media (max-width: 767.98px)  { .ap-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; } }
@media (max-width: 479.98px)  { .ap-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 5px; } }
</style>
@endpush

<section class="ap-section">
    <div class="custom-container">

        <div class="ap-header">
            <h2 class="ap-title">All Products</h2>
        </div>

        <div class="ap-grid" id="apGrid">
            {{-- first page loaded via JS on mount --}}
        </div>

        <div class="ap-loader" id="apLoader">
            <div class="ap-spinner"></div> Loading…
        </div>

        <div class="ap-end-msg" id="apEnd">— No more products —</div>

    </div>
</section>

@push('script')
<script>
(function () {
    var grid    = document.getElementById('apGrid');
    var loader  = document.getElementById('apLoader');
    var endMsg  = document.getElementById('apEnd');
    var page    = 1;
    var loading = false;
    var done    = false;
    var url     = @json(route('home.all_products'));

    function load() {
        if (loading || done) return;
        loading = true;
        loader.classList.add('active');

        fetch(url + '?page=' + page)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                grid.insertAdjacentHTML('beforeend', data.html);
                page++;
                if (!data.has_more) {
                    done = true;
                    endMsg.classList.add('active');
                }
            })
            .catch(function () {})
            .finally(function () {
                loading = false;
                loader.classList.remove('active');
            });
    }

    // load first page immediately
    load();

    // infinite scroll trigger
    window.addEventListener('scroll', function () {
        var section = document.querySelector('.ap-section');
        if (!section) return;
        var bottom = section.getBoundingClientRect().bottom;
        if (bottom - window.innerHeight < 300) {
            load();
        }
    }, { passive: true });
})();
</script>
@endpush
