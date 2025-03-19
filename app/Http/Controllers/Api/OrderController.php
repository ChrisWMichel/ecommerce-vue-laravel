<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Enums\OrderStatus;
//use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderListResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sortField', 'updated_at');
        $sortDirection = request('sortDirection', 'asc');

        $query = Order::query()
            ->where('id', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return OrderListResource::collection($query);
    }

        /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function view(Order $order)
    {
        // Eager load relationships
        $order->load(['user', 'items.product']);
        
        // Return as a resource
        return new OrderResource($order);
    }

    public function getStatuses(){
        return OrderStatus::getStatuses();
    }

    public function updateStatus(Order $order, $status)
    {
        $order->status = $status;
        $order->save();

        Mail::to($order->user->email)->send(new \App\Mail\OrderUpdateEmail($order));

        return response()->json(['message' => 'Order status updated successfully']);

    }
}
