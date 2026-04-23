<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Services\Admin\CategoryService\CategoryService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $service
    ) {
    }

    public function index(Request $request): View|Factory|JsonResponse|RedirectResponse
    {
        $response = $this->service->getListData($request->all());

        if (! $response['success']) {
            if ($request->ajax()) {
                return response()->json(['message' => $response['message']], 500);
            }

            Toastr::error($response['message'], 'Error');

            return back()->withErrors($response['message']);
        }

        return $request->ajax()
            ? response()->json($response['data'])
            : view('backEnd.category.index', $response['data']);
    }

    public function create(): View|Factory|RedirectResponse
    {
        return view('backEnd.category.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $response = $this->service->storeCategory($request->all());

        return $response['success']
            ? redirect()->route('categories.index')->with($response)
            : back()->withErrors($response['message'])->withInput();
    }

    public function edit(int|string $id): View|Factory|RedirectResponse
    {
        $response = $this->service->getEditData($id);

        return $response['success']
            ? view('backEnd.category.edit', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function update(CategoryRequest $request): RedirectResponse
    {
        $response = $this->service->updateCategory($request->all());

        return $response['success']
            ? redirect()->route('categories.index')->with($response)
            : back()->withErrors($response['message'])->withInput();
    }

    public function inactive(Request $request): RedirectResponse
    {
        $response = $this->service->changeCategoryStatus([
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
        $response = $this->service->changeCategoryStatus([
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
        $response = $this->service->deleteCategory($request->all());

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return to_route('categories.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }
}
