<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ProductService\ProductService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $service
    ) {
    }

    public function getSubcategory(Request $request): JsonResponse
    {
        return response()->json($this->service->getSubcategoryOptions($request->category_id));
    }

    public function getChildcategory(Request $request): JsonResponse
    {
        return response()->json($this->service->getChildcategoryOptions($request->subcategory_id));
    }

    public function index(Request $request): View|Factory|JsonResponse|RedirectResponse
    {
        $response = $this->service->getIndexData($request);

        if ($request->ajax() && $response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }

        if (! $response['success']) {
            Toastr::error($response['message'], 'Error');

            return back()->withErrors($response['message']);
        }

        return view('backEnd.product.index', $response['data']);
    }

    public function create(Request $request): View|Factory|RedirectResponse
    {
        $response = $this->service->getCreateData($request);

        return $response['success']
            ? view('backEnd.product.create', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function store(Request $request): RedirectResponse
    {
        $response = $this->service->storeProduct($request);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('products.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message'])->withInput();
    }

    public function edit(int|string $id, Request $request): View|Factory|RedirectResponse
    {
        $response = $this->service->getEditData($id, $request);

        return $response['success']
            ? view('backEnd.product.edit', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function price_edit(): View|Factory|RedirectResponse
    {
        $response = $this->service->priceEditData();

        return $response['success']
            ? view('backEnd.product.price_edit', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function price_update(Request $request): RedirectResponse
    {
        $response = $this->service->updatePrices($request);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('products.price_edit')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message'])->withInput();
    }

    public function update(Request $request): RedirectResponse
    {
        $response = $this->service->updateProduct($request);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('products.index')->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message'])->withInput();
    }

    public function barcodeLabels(int|string $id): View|Factory|RedirectResponse
    {
        $response = $this->service->barcodeLabelsData($id);

        return $response['success']
            ? view('backEnd.product.barcode-labels', $response['data'])
            : back()->withErrors($response['message']);
    }

    public function copy(int|string $id): RedirectResponse
    {
        $response = $this->service->copyProduct($id);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return redirect()->route('products.edit', $response['data']['product']->id)->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function inactive(Request $request): RedirectResponse
    {
        $response = $this->service->changeStatus($request->hidden_id, 0, 'Data inactive successfully');

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function active(Request $request): RedirectResponse
    {
        $response = $this->service->changeStatus($request->hidden_id, 1, 'Data active successfully');

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $response = $this->service->deleteProduct($request->hidden_id);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function imgdestroy(Request $request): RedirectResponse
    {
        $response = $this->service->deleteImage($request->id);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function pricedestroy(int|string $id): RedirectResponse
    {
        $response = $this->service->deleteVariable($id);

        if ($response['success']) {
            Toastr::success($response['message'], 'Success');

            return back()->with($response);
        }

        Toastr::error($response['message'], 'Error');

        return back()->withErrors($response['message']);
    }

    public function update_deals(Request $request): JsonResponse
    {
        $productIds = collect($request->input('product_ids', []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        $response = $this->service->updateDeals($productIds, (int) $request->input('status', 0));

        return response()->json([
            'status' => $response['success'] ? 'success' : 'error',
            'message' => $response['message'],
        ], $response['success'] ? 200 : 422);
    }

    public function update_feature(Request $request): JsonResponse
    {
        $productIds = collect($request->input('product_ids', []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        $response = $this->service->updateFeature($productIds, (int) $request->input('status', 0));

        return response()->json([
            'status' => $response['success'] ? 'success' : 'error',
            'message' => $response['message'],
        ], $response['success'] ? 200 : 422);
    }

    public function update_status(Request $request): JsonResponse
    {
        $productIds = collect($request->input('product_ids', []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        $response = $this->service->updateStatus($productIds, (int) $request->input('status', 0));

        return response()->json([
            'status' => $response['success'] ? 'success' : 'error',
            'message' => $response['message'],
        ], $response['success'] ? 200 : 422);
    }
}
