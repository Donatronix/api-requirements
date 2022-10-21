<?php

namespace App\Domain\Api\Controllers;

use App\Domain\Api\Resources\ProductResource;
use App\Domain\Api\Services\Interfaces\ProductServiceInterface;
use App\Infrastructure\Laravel\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Prettus\Repository\Criteria\RequestCriteria;
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
     * @param Request                 $request
     *
     * @return JsonResponse
     */
    public function index(ProductServiceInterface $service, Request $request): JsonResponse
    {
        try {

            $service->getRepository()->pushCriteria(app(RequestCriteria::class));

            if ($request->query->get('category')) {
                $products = $service->getProductsByCategory($request->query->get('category'));
            } elseif ($request->query->get('price')) {
                $products = $service->getProductsByPrice($request->query->get('price'));
            } else {
                $products = $service->getRepository()->all();
            }

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
     * Get products with discounts.
     *
     * @param ProductServiceInterface $service
     *
     * @return JsonResponse
     */
    public function getProductsWithOutDiscount(ProductServiceInterface $service,): JsonResponse
    {
        try {
            $service->getRepository()->pushCriteria(app(RequestCriteria::class));
            $products = $service->getProductsWithOutDiscount();

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
     * @param Request                 $request
     *
     * @return JsonResponse
     *
     */
    public function store(ProductServiceInterface $service, Request $request): JsonResponse
    {
        try {

            $validator = Validator::make($request->all(), [
                'sku' => 'required|string|unique:products',
                'name' => 'required|string|unique:products',
                'category' => 'required|string',
                'original' => 'required|numeric',
                'final' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            $product = $service->store($validator->validated());

            $response = [
                'status' => 'success',
                'message' => 'Product created.',
                'data' => new ProductResource($product),
            ];

            return response()->json($response, 201);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ProductServiceInterface $service
     * @param string                  $product
     *
     * @return JsonResponse
     */
    public function show(ProductServiceInterface $service, string $product): JsonResponse
    {
        try {
            $product = $service->getRepository()->find($product);

            return response()->json([
                'status' => 'success',
                'message' => 'Product retrieved successfully.',
                'data' => new ProductResource($product),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);

        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param ProductServiceInterface $service
     * @param Request                 $request
     * @param string                  $product
     *
     * @return JsonResponse
     *
     */
    public function update(ProductServiceInterface $service, Request $request, string $product): JsonResponse
    {
        try {

            $validator = Validator::make($request->all(), [
                'sku' => 'required|string',
                'name' => 'required|string',
                'category' => 'required|string',
                'original' => 'required|numeric',
                'final' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }


            $product = $service->update($validator->validated(), $service->getRepository()->find($product)->id);

            $response = [
                'status' => 'success',
                'message' => 'Product updated.',
                'data' => new ProductResource($product),
            ];

            return response()->json($response);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);

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
     * @param string                  $product
     *
     * @return JsonResponse
     */
    public function destroy(ProductServiceInterface $service, string $product): JsonResponse
    {
        try {
            $deleted = $service->delete($service->getRepository()->find($product)->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted.',
                'deleted' => $deleted,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);

        } catch (Throwable $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
