@extends('resellerPanel.layouts.master')
@section('title', 'Ticket #' . $ticket->id)

@section('css')
<style>
    .tk-msg { border-radius: 10px; padding: 12px 14px; margin-bottom: 12px; max-width: 80%; }
    .tk-reseller { background: #e8f5e9; margin-left: auto; }
    .tk-admin { background: #f1f5f9; margin-right: auto; }
    .tk-sender { font-size: 12px; font-weight: 700; margin-bottom: 4px; }
    .tk-time { font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .tk-body { white-space: pre-line; font-size: 14px; }
</style>
@endsection

@section('content')
    @php [$stN, $stC] = $ticket->statusBadge(); @endphp
    <div class="row align-items-center mb-2">
        <div class="col">
            <div class="page-title-box">
                <h4 class="page-title">Ticket #{{ $ticket->id }}
                    <span class="badge bg-{{ $stC }} align-middle ms-1">{{ $stN }}</span>
                </h4>
            </div>
        </div>
        <div class="col-auto">
            <a href="{{ route('reseller.tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-1">{{ $ticket->subject }}</h5>
                    <p class="text-muted small mb-3">
                        @if($ticket->order)
                            <i class="mdi mdi-package-variant"></i>
                            Order: <a href="{{ route('reseller.orders.show', $ticket->order->id) }}">#{{ $ticket->order->invoice_id }}</a> &nbsp;|&nbsp;
                        @endif
                        Created: {{ $ticket->created_at->format('d M Y, h:i A') }}
                    </p>

    {{-- conversation --}}
                    <div class="mb-3" id="tk-thread" data-after="{{ $ticket->messages->max('id') ?? 0 }}">
                        @foreach($ticket->messages as $msg)
                            <div class="tk-msg {{ $msg->sender === 'reseller' ? 'tk-reseller' : 'tk-admin' }}">
                                <div class="tk-sender">
                                    {{ $msg->sender === 'reseller' ? 'You' : 'Admin / Support' }}
                                </div>
                                <div class="tk-body">{{ $msg->message }}</div>
                                <div class="tk-time">{{ $msg->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- reply --}}
                    @if($ticket->status === 'closed')
                        <div class="alert alert-secondary mb-0">Ei ticket close kora hoyeche.</div>
                    @else
                        <form action="{{ route('reseller.tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label">Reply</label>
                                <textarea name="message" rows="3" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                @error('message')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <button class="btn btn-success btn-sm"><i class="mdi mdi-send"></i> Send Reply</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
(function () {
    const thread = document.getElementById('tk-thread');
    if (!thread) return;
    const url = "{{ route('reseller.tickets.messages', $ticket->id) }}";
    const statusBadge = document.querySelector('.page-title .badge');

    function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    function poll() {
        const after = thread.dataset.after || 0;
        fetch(url + '?after=' + after, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                (data.messages || []).forEach(m => {
                    const mine = m.sender === 'reseller';
                    const div = document.createElement('div');
                    div.className = 'tk-msg ' + (mine ? 'tk-reseller' : 'tk-admin');
                    div.innerHTML = '<div class="tk-sender">' + (mine ? 'You' : 'Admin / Support') + '</div>' +
                        '<div class="tk-body">' + esc(m.message) + '</div>' +
                        '<div class="tk-time">' + m.time + '</div>';
                    thread.appendChild(div);
                    thread.dataset.after = m.id;
                    div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            })
            .catch(() => {});
    }

    // proti 10 second-e notun message check (refresh chhara)
    setInterval(poll, 10000);
})();
</script>
@endsection
