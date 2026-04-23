<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContactSubmitRequest;
use App\Models\CreatePage;
use App\Services\Frontend\ContactFormService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function __construct(private readonly ContactFormService $contactFormService)
    {
    }

    public function contact(Request $request)
    {
        return view('frontEnd.pages.contact');
    }

    public function page(string $slug)
    {
        return view('frontEnd.pages.page', [
            'page' => CreatePage::query()->where('slug', $slug)->firstOrFail(),
        ]);
    }

    public function contact_submit(ContactSubmitRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->contactFormService->submit([
            'cus_name' => $validated['name'],
            'cus_email' => $validated['email'],
            'cus_phone' => $validated['phone'],
            'cus_subject' => $validated['subject'],
            'cus_message' => $validated['message'],
        ]);

        Toastr::success('Thanks, Message send successfully', 'Success!');

        return redirect()->back();
    }
}
