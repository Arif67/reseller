<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoStrip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Toastr;

class PromoStripController extends Controller
{
    public function index()
    {
        $items = PromoStrip::orderBy('sort_order')->orderBy('id')->get();
        return view('backEnd.promo_strip.index', compact('items'));
    }

    public function create()
    {
        return view('backEnd.promo_strip.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'link'          => ['required', 'string', 'max:500'],
            'countdown_end' => ['nullable', 'date'],
            'bg_color'      => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'sort_order'    => ['nullable', 'integer', 'min:0'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . $file->getClientOriginalName();
            $dir      = public_path('uploads/promo_strip');
            if (!file_exists($dir)) mkdir($dir, 0775, true);
            $file->move($dir, $filename);
            $imagePath = 'uploads/promo_strip/' . $filename;
        }

        PromoStrip::create([
            'title'         => $request->title,
            'image'         => $imagePath,
            'link'          => $request->link,
            'countdown_end' => $request->countdown_end ?: null,
            'bg_color'      => $request->bg_color ?: '#ffffff',
            'sort_order'    => $request->sort_order ?? 0,
            'status'        => $request->has('status') ? 1 : 0,
        ]);

        Cache::forget('shared_view_data_v4');
        Toastr::success('Success', 'Promo strip created successfully');
        return redirect()->route('promo_strip.index');
    }

    public function edit($id)
    {
        $item = PromoStrip::findOrFail($id);
        return view('backEnd.promo_strip.edit', compact('item'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'            => ['required', 'exists:promo_strips,id'],
            'link'          => ['required', 'string', 'max:500'],
            'countdown_end' => ['nullable', 'date'],
            'bg_color'      => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'sort_order'    => ['nullable', 'integer', 'min:0'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $item = PromoStrip::findOrFail($request->id);

        $imagePath = $item->image;
        if ($request->hasFile('image')) {
            if ($item->image && file_exists(public_path($item->image))) {
                @unlink(public_path($item->image));
            }
            $file     = $request->file('image');
            $filename = time() . $file->getClientOriginalName();
            $dir      = public_path('uploads/promo_strip');
            if (!file_exists($dir)) mkdir($dir, 0775, true);
            $file->move($dir, $filename);
            $imagePath = 'uploads/promo_strip/' . $filename;
        }

        $item->update([
            'title'         => $request->title,
            'image'         => $imagePath,
            'link'          => $request->link,
            'countdown_end' => $request->countdown_end ?: null,
            'bg_color'      => $request->bg_color ?: '#ffffff',
            'sort_order'    => $request->sort_order ?? 0,
            'status'        => $request->has('status') ? 1 : 0,
        ]);

        Cache::forget('shared_view_data_v4');
        Toastr::success('Success', 'Promo strip updated successfully');
        return redirect()->route('promo_strip.index');
    }

    public function destroy(Request $request)
    {
        $item = PromoStrip::findOrFail($request->id);
        if ($item->image && file_exists(public_path($item->image))) {
            @unlink(public_path($item->image));
        }
        $item->delete();
        Cache::forget('shared_view_data_v4');
        Toastr::success('Success', 'Promo strip deleted');
        return redirect()->route('promo_strip.index');
    }

    public function toggleStatus(Request $request)
    {
        $item = PromoStrip::findOrFail($request->id);
        $item->update(['status' => $item->status ? 0 : 1]);
        Cache::forget('shared_view_data_v4');
        return response()->json(['status' => $item->status]);
    }
}
