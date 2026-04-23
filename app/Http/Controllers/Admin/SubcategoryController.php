<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SubcategoryService\SubcategoryService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class SubcategoryController extends Controller
{
    public function __construct(
        private readonly SubcategoryService $service
    ) {
    }

    public function getCategory(Request $request)
    {
        $category = DB::table('categories')
            ->where('service_category', $request->service_category)
            ->pluck('name', 'id');

        return response()->json($category);
    }

    public function index(): View|Factory|RedirectResponse
    {
        $response = $this->service->getListData();

        if (! $response['success']) {
            Toastr::error($response['message'], 'Error');

            return back()->withErrors($response['message']);
        }

        return view('backEnd.subcategory.index', $response['data']);
    }

    public function create(): View|Factory|RedirectResponse
    {
        $response = $this->service->getCreateData();

        return $response['success']
            ? view('backEnd.subcategory.create', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'category_id' => 'required',
            'subcategoryName' => 'required',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $response = $this->service->storeSubcategory($request->all());

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('subcategories.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message'])->withInput();
    }

    public function edit(int|string $id): View|Factory|RedirectResponse
    {
        $response = $this->service->getEditData($id);

        return $response['success']
            ? view('backEnd.subcategory.edit', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'category_id' => 'required',
            'subcategoryName' => 'required',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $response = $this->service->updateSubcategory($request->all());

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('subcategories.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message'])->withInput();
    }

    public function inactive(Request $request): RedirectResponse
    {
        $response = $this->service->changeSubcategoryStatus([
            'hidden_id' => $request->hidden_id,
            'status' => 0,
        ]);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function active(Request $request): RedirectResponse
    {
        $response = $this->service->changeSubcategoryStatus([
            'hidden_id' => $request->hidden_id,
            'status' => 1,
        ]);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $response = $this->service->deleteSubcategory($request->all());

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('subcategories.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }
}
