<?php

namespace App\Http\Controllers\Api;

use App\Actions\Orders\InitiateBlRelease;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index(): JsonResponse
    {
        $unprocessedOrders = Order::where('bl_release_date', null)
            ->where('freight_payer_self', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $unprocessedOrders,
            'meta' => [
                'total' => $unprocessedOrders->count(),
            ],
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'data' => $order,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(Order::validationRules());

        $order = Order::create($validated);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'bl_release_date' => 'nullable|date',
            'bl_release_user_id' => 'nullable|integer|exists:users,id',
            'freight_payer_self' => 'sometimes|required|boolean',
            'contract_number' => 'sometimes|required|string|max:255',
            'bl_number' => 'sometimes|required|string|max:255',
        ]);

        $order->update($validated);

        (new InitiateBlRelease)->execute($order);

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $order->fresh(),
        ]);
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
}
