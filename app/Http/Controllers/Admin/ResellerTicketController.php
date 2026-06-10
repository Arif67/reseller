<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResellerTicket;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ResellerTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = ResellerTicket::with(['reseller', 'order'])
            ->withCount('messages')
            ->latest('last_reply_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('subject', 'like', "%{$kw}%")
                    ->orWhereHas('reseller', fn ($r) => $r->where('name', 'like', "%{$kw}%")->orWhere('phone', 'like', "%{$kw}%"));
            });
        }

        $tickets = $query->paginate(25)->withQueryString();

        return view('backEnd.reseller.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = ResellerTicket::with([
            'reseller',
            'order.shipping',
            'messages' => fn ($q) => $q->oldest(),
        ])->findOrFail($id);

        return view('backEnd.reseller.tickets.show', compact('ticket'));
    }

    // polling — notun message gula JSON e (real-time-er moto)
    public function messages(Request $request, $id)
    {
        $ticket = ResellerTicket::with('reseller')->findOrFail($id);

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
        $request->validate(['message' => 'required|string']);

        $ticket = ResellerTicket::findOrFail($id);

        $ticket->messages()->create([
            'sender'  => 'admin',
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'answered', 'last_reply_at' => now()]);

        Toastr::success('Reply pathano hoyeche', 'Success');
        return redirect()->route('admin.reseller_tickets.show', $ticket->id);
    }

    public function status(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:open,answered,closed']);

        $ticket = ResellerTicket::findOrFail($id);
        $ticket->update(['status' => $request->status]);

        Toastr::success('Ticket status update holo', 'Success');
        return back();
    }
}
