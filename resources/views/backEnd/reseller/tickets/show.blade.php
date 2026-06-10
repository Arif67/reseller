@extends('backEnd.layouts.master')
@section('title','Ticket #' . $ticket->id)

@section('css')
<style>
    .tk-msg { border-radius: 10px; padding: 12px 14px; margin-bottom: 12px; max-width: 80%; }
    .tk-admin { background: #e8f5e9; margin-left: auto; }
    .tk-reseller { background: #f1f5f9; margin-right: auto; }
    .tk-sender { font-size: 12px; font-weight: 700; margin-bottom: 4px; }
    .tk-time { font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .tk-body { white-space: pre-line; font-size: 14px; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @php [$stN, $stC] = $ticket->statusBadge(); @endphp
    <div class="row align-items-center">
        <div class="col">
            <div class="page-title-box">
                <h4 class="page-title">Ticket #{{ $ticket->id }}
                    <span class="badge bg-{{ $stC }} align-middle ms-1">{{ $stN }}</span>
                </h4>
            </div>
        </div>
        <div class="col-auto pt-2"><a href="{{ route('admin.reseller_tickets.index') }}" class="btn btn-light btn-sm">← All Tickets</a></div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-1">{{ $ticket->subject }}</h5>
                    <p class="text-muted small mb-3">
                        @if($ticket->order)
                            Order: <strong>#{{ $ticket->order->invoice_id }}</strong> &nbsp;|&nbsp;
                        @endif
                        Created: {{ $ticket->created_at->format('d M Y, h:i A') }}
                    </p>

                    <div class="mb-3" id="tk-thread" data-after="{{ $ticket->messages->max('id') ?? 0 }}">
                        @foreach($ticket->messages as $msg)
                            <div class="tk-msg {{ $msg->sender === 'admin' ? 'tk-admin' : 'tk-reseller' }}">
                                <div class="tk-sender">{{ $msg->sender === 'admin' ? 'Admin / Support' : ($ticket->reseller->name ?? 'Reseller') }}</div>
                                <div class="tk-body">{{ $msg->message }}</div>
                                <div class="tk-time">{{ $msg->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if($ticket->status === 'closed')
                        <div class="alert alert-secondary">Ei ticket close kora hoyeche. Reopen korle reply kora jabe.</div>
                    @else
                        <form action="{{ route('admin.reseller_tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label">Reply</label>
                                <textarea name="message" rows="3" class="form-control" required>{{ old('message') }}</textarea>
                            </div>
                            <button class="btn btn-success btn-sm"><i class="fe-send"></i> Send Reply</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title mb-3">Reseller</h5>
                    <p class="mb-1"><strong>{{ $ticket->reseller->name ?? '—' }}</strong></p>
                    <p class="mb-1 text-muted">{{ $ticket->reseller->phone ?? '' }}</p>
                    <p class="mb-3 text-muted">{{ $ticket->reseller->email ?? '' }}</p>

                    @if($ticket->order)
                        <a href="{{ route('admin.order.workspace', ['invoice_id' => $ticket->order->invoice_id, 'tab' => 'invoice']) }}"
                            class="btn btn-outline-primary btn-sm w-100 mb-3">
                            <i class="fe-eye"></i> Order #{{ $ticket->order->invoice_id }} dekhun
                        </a>
                    @endif

                    <h6 class="text-muted">Status change</h6>
                    <form action="{{ route('admin.reseller_tickets.status', $ticket->id) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-control form-control-sm">
                            @foreach(['open','answered','closed'] as $s)
                                <option value="{{ $s }}" @selected($ticket->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-primary">Set</button>
                    </form>
                </div>
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
    const url = "{{ route('admin.reseller_tickets.messages', $ticket->id) }}";
    const resellerName = @json($ticket->reseller->name ?? 'Reseller');

    function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    function poll() {
        const after = thread.dataset.after || 0;
        fetch(url + '?after=' + after, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                (data.messages || []).forEach(m => {
                    const isAdmin = m.sender === 'admin';
                    const div = document.createElement('div');
                    div.className = 'tk-msg ' + (isAdmin ? 'tk-admin' : 'tk-reseller');
                    div.innerHTML = '<div class="tk-sender">' + (isAdmin ? 'Admin / Support' : esc(resellerName)) + '</div>' +
                        '<div class="tk-body">' + esc(m.message) + '</div>' +
                        '<div class="tk-time">' + m.time + '</div>';
                    thread.appendChild(div);
                    thread.dataset.after = m.id;
                    div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            })
            .catch(() => {});
    }

    setInterval(poll, 10000);
})();
</script>
@endsection
