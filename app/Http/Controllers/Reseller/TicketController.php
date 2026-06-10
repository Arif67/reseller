<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ResellerTicket;
use App\Models\ResellerTicketMessage;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Auth;

class TicketController extends Controller
{
    private function resellerId(): int
    {
        return Auth::guard('reseller')->id();
    }

    public function index()
    {
        $tickets = ResellerTicket::where('reseller_id', $this->resellerId())
            ->with('order')
            ->withCount('messages')
            ->latest()
            ->paginate(20);

        return view('resellerPanel.tickets.index', compact('tickets'));
    }

    public function create(Request $request)
    {
        // order list — ticket-er sathe attach korar jonno
        $orders = Order::where('reseller_id', $this->resellerId())
            ->latest()
            ->limit(100)
            ->get(['id', 'invoice_id', 'created_at']);

        $selectedOrderId = $request->order_id;

        return view('resellerPanel.tickets.create', compact('orders', 'selectedOrderId'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'nullable|integer',
            'subject'  => 'required|string|max:180',
            'message'  => 'required|string',
        ]);

        // order ta ei reseller-er kina verify
        if ($request->order_id) {
            Order::where('reseller_id', $this->resellerId())->findOrFail($request->order_id);
        }

        $ticket = ResellerTicket::create([
            'reseller_id'   => $this->resellerId(),
            'order_id'      => $request->order_id ?: null,
            'subject'       => $request->subject,
            'status'        => 'open',
            'last_reply_at' => now(),
        ]);

        $ticket->messages()->create([
            'sender'  => 'reseller',
            'message' => $request->message,
        ]);

        Toastr::success('Ticket submit hoyeche, admin shigghoi reply debe', 'Success');
        return redirect()->route('reseller.tickets.show', $ticket->id);
    }

    public function show($id)
    {
        $ticket = ResellerTicket::where('reseller_id', $this->resellerId())
            ->with(['order', 'messages' => fn ($q) => $q->oldest()])
            ->findOrFail($id);

        return view('resellerPanel.tickets.show', compact('ticket'));
    }

    // polling — notun message gula JSON e ferত dey (real-time-er moto)
    public function messages(Request $request, $id)
    {
        $ticket = ResellerTicket::where('reseller_id', $this->resellerId())->findOrFail($id);

        $messages = $ticket->messages()
            ->where('id', '>', (int) $request->after)
            ->oldest()
            ->get(['id', 'sender', 'message', 'created_at']);

        return response()->json([
            'status'   => $ticket->status,
            'messages' => $messages->map(fn ($m) => [
                'id'      => $m->id,
                'sender'  => $m->sender,
                'message' => $m->message,
                'time'    => $m->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function reply(Request $request, $id)
    {
        $this->validate($request, ['message' => 'required|string']);

        $ticket = ResellerTicket::where('reseller_id', $this->resellerId())->findOrFail($id);

        if ($ticket->status === 'closed') {
            Toastr::error('Ticket close kora hoyeche, reply kora jabe na', 'Closed');
            return back();
        }

        $ticket->messages()->create([
            'sender'  => 'reseller',
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'open', 'last_reply_at' => now()]);

        Toastr::success('Reply pathano hoyeche', 'Success');
        return redirect()->route('reseller.tickets.show', $ticket->id);
    }
}
