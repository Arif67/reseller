@extends('resellerPanel.layouts.master')
@section('title', 'Support Tickets')

@section('content')
    <div class="row align-items-center mb-2">
        <div class="col"><div class="page-title-box"><h4 class="page-title">Support Tickets</h4></div></div>
        <div class="col-auto">
            <a href="{{ route('reseller.tickets.create') }}" class="btn btn-sm btn-success">
                <i class="mdi mdi-plus"></i> New Ticket
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th><th>Subject</th><th>Order</th><th>Status</th>
                                    <th>Messages</th><th>Last Update</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    @php [$stN, $stC] = $ticket->statusBadge(); @endphp
                                    <tr>
                                        <td>#{{ $ticket->id }}</td>
                                        <td style="font-size:13px;">{{ $ticket->subject }}</td>
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
                                            <a href="{{ route('reseller.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="mdi mdi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">
                                        Kono ticket nei. Kono order niye somossa hole <a href="{{ route('reseller.tickets.create') }}">notun ticket</a> korun.
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $tickets->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
