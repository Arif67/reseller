@extends('backEnd.layouts.master')
@section('title','Reseller Tickets')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col"><div class="page-title-box"><h4 class="page-title">Reseller Support Tickets</h4></div></div>
        <div class="col-auto pt-2"><a href="{{ route('admin.resellers.index') }}" class="btn btn-light btn-sm">← Resellers</a></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3" method="GET">
                        <div class="col-sm-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach(['open','answered','closed'] as $s)
                                    <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Subject / reseller name / phone">
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-info">Filter</button>
                            <a href="{{ route('admin.reseller_tickets.index') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th><th>Reseller</th><th>Subject</th><th>Order</th>
                                    <th>Status</th><th>Msgs</th><th>Last Update</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    @php [$stN, $stC] = $ticket->statusBadge(); @endphp
                                    <tr>
                                        <td>#{{ $ticket->id }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $ticket->reseller->name ?? '—' }}</div>
                                            <div class="small text-muted">{{ $ticket->reseller->phone ?? '' }}</div>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}</td>
                                        <td>
                                            @if($ticket->order)
                                                <span class="badge bg-light text-dark">#{{ $ticket->order->invoice_id }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-{{ $stC }}">{{ $stN }}</span></td>
                                        <td>{{ $ticket->messages_count }}</td>
                                        <td class="small text-muted">{{ optional($ticket->last_reply_at ?? $ticket->updated_at)->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('admin.reseller_tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">
                                                <i class="fe-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">Kono ticket nei.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $tickets->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
