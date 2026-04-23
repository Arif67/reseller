<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Services\Admin\ChildcategoryService\ChildcategoryService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ChildcategoryController extends Controller
{
    public function __construct(
        private readonly ChildcategoryService $service
    ) {
    }

    public function getSubCategory(Request $request)
    {
        $category = DB::table('subcategories')
            ->where('subcategorytype', $request->childcategorytype)
            ->pluck('subcategoryName', 'id');

        return response()->json($category);
    }

    public function index(): View|Factory|RedirectResponse
    {
        $response = $this->service->getListData();

        if (! $response['success']) {
            Toastr::error($response['message'], 'Error');

            return back()->withErrors($response['message']);
        }

        return view('backEnd.childcategory.index', $response['data']);
    }

    public function create(): View|Factory
    {
        return view('backEnd.childcategory.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'subcategory_id' => 'required',
            'childcategoryName' => 'required',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $response = $this->service->storeChildcategory($request->all());

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('childcategories.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message'])->withInput();
    }

    public function edit(int|string $id): View|Factory|RedirectResponse
    {
        $response = $this->service->getEditData($id);

        return $response['success']
            ? view('backEnd.childcategory.edit', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'subcategory_id' => 'required',
            'childcategoryName' => 'required',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $response = $this->service->updateChildcategory($request->all());

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('childcategories.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message'])->withInput();
    }

    public function inactive(Request $request): RedirectResponse
    {
        $response = $this->service->changeChildcategoryStatus([
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
        $response = $this->service->changeChildcategoryStatus([
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
        $response = $this->service->deleteChildcategory($request->all());

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }
}
