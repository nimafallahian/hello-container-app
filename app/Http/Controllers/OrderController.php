<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $unprocessedOrders = Order::where('bl_release_date', null)
            ->where('freight_payer_self', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('unprocessedOrders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(Order::validationRules());

        Order::create($validated);

        return redirect()->route('orders.create')->with('success', 'Order created successfully!');
    }
}
