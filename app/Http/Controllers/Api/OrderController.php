<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Detail_Cart;
use App\Models\Detail_Order;
use App\Models\Image;
use App\Models\Order;
use App\Models\Resto;
use App\Models\Table;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function list() {
        $user = auth()->id();

        $orders = Order::where('ordered_by',$user)->get();
        foreach($orders as $x => $order ) {
            $tables = Detail_Order::where('order_id',$order->id)->get();
            $tables = $tables->map(function ($table) {
                $table->table = Table::where('id',$table->table_id)->first();
                return $table;
            });
            $orders[$x]->tables = $tables;

            $orders[$x]->resto = Resto::where('id',$order->resto_id)->first();

            $dateTime = new DateTime($order->created_at);
            $formated_date = $dateTime->format('H:i d-M-Y');
            $date = Carbon::createFromFormat('H:i d-M-Y', $formated_date);
            $orders[$x]->last_made = $date->format('H.i l, d F Y');
        }

        return response()->json([
            "status" => 200,
            "data" => $orders],
            200
        );
    }

    public function detail($id) {
        $user = auth()->id();

        $order = Order::where('id',$id)->where('ordered_by',$user)->first();
        if(!$order) {
            return response()->json([
                "status" => 404,
                "message" => "Order data not found"],
                404
            );
        }
        $tables = Detail_Order::where('order_id',$order->id)->get();
        $tables = $tables->map(function ($table) {
            $table->table = Table::where('id',$table->table_id)->first();
            return $table;
        });
        $order->tables = $tables;

        $order->resto = Resto::where('id',$order->resto_id)->first();

        $dateTime = new DateTime($order->created_at);
        $formated_date = $dateTime->format('H:i d-M-Y');
        $date = Carbon::createFromFormat('H:i d-M-Y', $formated_date);
        $order->last_made = $date->format('H.i l, d F Y');

        return response()->json([
            "status" => 200,
            "data" => $order],
            200
        );
    }

    public function order(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "name" => "required",
                "resto_id" => "required",
                "total_table" => "required",
                "total_price" => "required"
            ],[
                "name.required" => "Name is required!",
                "resto_id.required" => "Resto ID is required!",
                "total_table.required" => "Total Table is required!",
                "total_price.required" => "Total Price is required!",
            ]);

            $resto = Resto::find($request->input('resto_id'));
            if (!$resto) {
                return response()->json([
                    "status" => 404,
                    "message" => "Resto Not Found!"],
                    404
                );
            }

            $order = Order::where('resto_id',$request->input('resto_id'))->where('ordered_by',$user)->first();
            if($order) {
                $order->resto = Resto::find($request->input('resto_id'));
                return response()->json([
                    "status" => 201,
                    "message" => "Order has been created",
                    "data" => $order],
                    201
                );
            } else {
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $shuffleCharacters = str_shuffle($characters);
                $randomString = substr($shuffleCharacters, 0, 5);
                $numbers = '1234567890';
                $shuffleNumbers = str_shuffle($numbers);
                $randomNumber = substr($shuffleNumbers, 0, 10);
                $orders = Order::create([
                    "order_id" => "BR" . $randomString . $randomNumber,
                    "name" => $request->input('name'),
                    "status" => "success",
                    "total_table" => $request->input('total_table'),
                    "total_price" => $request->input('total_price'),
                    "resto_id" => $request->input("resto_id"),
                    "ordered_by" => $user
                ]);
                $order = Order::where('id',$orders->id)->first();
                $order->resto = Resto::find($request->input('resto_id'));

                return response()->json([
                    "status" => 200,
                    "message" => "Resto has been successfully added to cart",
                    "data" => $order],
                    200
                );
            }

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function order_table(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "order_id" => "required",
                "table_id" => "required"
            ],[
                "order_id.required" => "Order ID is required!",
                "table_id.required" => "Table ID is required!"
            ]);

            $order = Order::where('id',$request->input('order_id'))->where('ordered_by',$user)->first();
            if (!$order) {
                return response()->json([
                    "status" => 404,
                    "message" => "order ID Not Found!"],
                    404
                );
            }

            $table = Table::where('id',$request->input('table_id'))->where('resto_id',$order->resto_id)->first();
            if(!$table) {
                return response()->json([
                    "status" => 404,
                    "message" => "Table Not Found!"],
                    404
                );
            }
            if($table->booked === 1) {
                return response()->json([
                    "status" => 403,
                    "message" => "Table already booked"],
                    403
                );
            }

            Detail_order::create([
                "order_id" => $request->input("order_id"),
                "table_id" => $request->input('table_id')
            ]);

            $tables = Table::where("id",$request->input("table_id"))->first();
            $tables->booked = true;
            $tables->save();

            return response()->json([
                "status" => 200,
                "message" => "Resto Table has been successfully added to order"],
                200
            );

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function order_via_cart(Request $request) {
        $user = auth()->id();
        
        try {
            $request->validate([
                "cart_id" => "required"
            ],[
                "cart_id.required" => "Cart ID is required!"
            ]);

            $cart = Cart::where('id',$request->input('cart_id'))->where('carted_by',$user)->first();
            if(!$cart) {
                return response()->json([
                    "status" => 404,
                    "message" => "cart not found"],
                    404
                );
            }

            $tables = Detail_Cart::where('cart_id',$request->input('cart_id'))->get();
            foreach($tables as $x => $table) {
                $booked = Table::where('id',$table->table_id)->first();
                if($booked->booked === true) {
                    return response()->json([
                        "status" => 400,
                        "message" => "Sorry, tables has already been booked"],
                        400
                    );
                }
            }

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $shuffleCharacters = str_shuffle($characters);
            $randomString = substr($shuffleCharacters, 0, 5);
            $numbers = '1234567890';
            $shuffleNumbers = str_shuffle($numbers);
            $randomNumber = substr($shuffleNumbers, 0, 10);

            $order = Order::create([
                "order_id" => "BR" . $randomString . $randomNumber,
                "name" => $request->input('name'),
                "status" => "success",
                "total_table" => $cart->total_table,
                "total_price" => $cart->total_price,
                "resto_id" => $cart->resto_id,
                "ordered_by" => $user
            ]);

            if($tables) {
                foreach($tables as $table) {
                    $table_id = $table->table_id;
                    Detail_Order::create([
                        "order_id" => $order->id,
                        "table_id" => $table_id
                    ]);
                }
            }

            return response()->json([
                "status" => 200,
                "message" => "Resto Table has been successfully ordered"],
                200
            );
            
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }
}
