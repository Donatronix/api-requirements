<?php

namespace App\Domain\Api\Controllers;

use App\Domain\Api\Requests\ProductCreateRequest;
use App\Domain\Api\Requests\ProductUpdateRequest;
use App\Domain\Api\Resources\ProductResource;
use App\Domain\Api\Services\Interfaces\ProductServiceInterface;
use App\Http\Requests;
use App\Infrastructure\Laravel\Controller;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Class ProductsController.
 *
 * @package namespace App\Domain\Api\Controllers;
 */
class ProductsController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @param ProductServiceInterface $service
     *
     * @return JsonResponse
     */
    public function index(ProductServiceInterface $service): JsonResponse
    {
        try {
            $service->getRepository()->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
            $products = $service->getRepository()->all();

            return response()->json([
                'status' => 'success',
                'message' => 'Products retrieved successfully',
                'data' => ProductResource::collection($products),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductServiceInterface $service
     * @param ProductCreateRequest    $request
     *
     * @return JsonResponse
     *
     */
    public function store(ProductServiceInterface $service, ProductCreateRequest $request): JsonResponse
    {
        try {

            $product = $service->store($request->all());

            $response = [
                'status' => 'success',
                'message' => 'Product created.',
                'data' => $product->toArray(),
            ];

            return response()->json($response,201);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param ProductServiceInterface $service
     * @param string                  $id
     *
     * @return JsonResponse
     */
    public function show(ProductServiceInterface $service, string $id): JsonResponse
    {
        try {
            $product = $service->getRepository()->find($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Product retrieved successfully.',
                'data' => new ProductResource($product),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);

        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param ProductServiceInterface $service
     * @param ProductUpdateRequest    $request
     * @param string                  $id
     *
     * @return JsonResponse
     *
     */
    public function update(ProductServiceInterface $service, ProductUpdateRequest $request, string $id): JsonResponse
    {
        try {

            $product = $service->update($request->all(), $id);

            $response = [
                'status' => 'success',
                'message' => 'Product updated.',
                'data' => new ProductResource($product),
            ];

            return response()->json($response);

        } catch (Throwable $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param ProductServiceInterface $service
     * @param string                  $id
     *
     * @return JsonResponse
     */
    public function destroy(ProductServiceInterface $service,string $id): JsonResponse
    {
        try {
            $deleted = $service->delete($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted.',
                'deleted' => $deleted,
            ]);
        } catch (Throwable $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
