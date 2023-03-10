<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrdersCustomerResources;
use App\Models\Order;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function createOrder(CreateOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction(); //Inicializamos la transacción
        try {
            $date = Carbon::now();
            $order = Order::create([
                'customer_id' => $request['customerId'],
                'purchase_date' => $date->addDays(3),
                'total' => $request['total'],
            ]); // Creamos la orden

            //Relacionamos los productos a la orden de compra
            foreach ($request['products'] as $product) {
                OrderProduct::create([
                    'quantity' =>  $product['quantity'],
                    'product_id' => $product['id'],
                    'order_id' => $order->id
                ]);
            }

            DB::commit(); // Si todo sale bien realizamos el commit o transacción a la base de datos
            return response()->json([
                'success' => true,
                'message' => 'Create Order',
                'response' => 'create_order',
                'data' => $order
            ], 200);

        } catch (\Throwable $th) {
            /* Si sale un error realizamos un rollback a la base de datos, mostramos el error en el en archivo Log*/
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR CREATE ORDER.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }

    public function getOrdersCustomer($id): \Illuminate\Http\JsonResponse
    {
        $orders = OrdersCustomerResources::collection(
            Order::where('customer_id', $id)->with('products.product','customer.user')->get()
        );
        if (count($orders) === 0) return response()->json(['No hay ordenes para este cliente.'], 401);
        return response()->json([
            'success' => true,
            'message' => 'Get Users',
            'response' => 'get_users',
            'total' => $orders->count(),
            'data' => $orders,
        ], 200);
    }

    public function getOrders(): \Illuminate\Http\JsonResponse
    {
        $orders = OrdersCustomerResources::collection(
            Order::with('products.product','customer.user')->get()
        );
        if (count($orders) === 0) return response()->json(['No hay ordenes creadas.'], 401);
        return response()->json([
            'success' => true,
            'message' => 'Get Users',
            'response' => 'get_users',
            'total' => $orders->count(),
            'data' => $orders,
        ], 200);
    }
}
