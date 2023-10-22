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

        $orders = Detail_Order::where('order_id',$id)->get();
        foreach($orders as $x => $order) {
            $resto_data = Resto::where('id',$order->resto_id)->first();
            $orders[$x]->resto = $resto_data;
            $image = Image::where('resto_id',$order->resto_id)->first();
            if($image) {
                $orders[$x]->resto->photo = url('storage/'.$image->image);
            } else {
                $orders[$x]->resto->photo = null;
            }
            $orders[$x]->total_price += $order->table->price; 
        }
        $orders->overall_price += $orders->total_price;

        return response()->json([
            "status" => 200,
            "data" => $orders],
            200
        );
    }

    public function order_resto(Request $request) {
        $user = auth()->id();
        
        try {
            $request->validate([
                "name" => "required",
                "resto_id" => "required",
                "table_number.*" => "required"
            ],[
                "name.required" => "Name is required!" ,
                "resto_id.required" => "resto ID is required!",
                "table_number.required" => "Table Number is required!"
            ]);

            $resto = Resto::find($request->input('resto_id'));
            if (!$resto) {
                return response()->json([
                    "status" => 404,
                    "message" => "Resto Not Found!"],
                    404
                );
            }

            $tables = $request->input('table_number');
            foreach ($tables as $x => $table) {
                $booked = Detail_Order::where('table_id')->get();
                if($booked) {
                    return response()->json([
                        "status" => 400,
                        "message" => "Sorry, table . $booked->table->name . has already been booked"
                    ]);
                }
            }

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $shuffleCharacters = str_shuffle($characters);
            $randomString = substr($shuffleCharacters, 0, 5);
            $numbers = '1234567890';
            $shuffleNumbers = str_shuffle($numbers);
            $randomNumber = substr($shuffleNumbers, 0, 10);

            $resto = Resto::find($request->input('resto_id'));

            $total_table = count($tables);
            foreach ($tables as $x => $table) {
                $price = Table::find($table);
                $tables[$x]->total_price += $price->price;
            }
            $total_price = $tables->total->price;

            $order = Order::create([
                "order_id" => "BR" . $randomString . $randomNumber,
                "name" => $request->input('name'),
                "status" => "success",
                "total_table" => $total_table,
                "total_price" => $total_price,
                "resto_id" => $resto->id,
                "ordered_by" => $user
            ]);

            if($tables) {
                foreach($tables as $x => $table) {
                    Detail_Order::create([
                        "order_id" => $order->id,
                        "table_id" => $table
                    ]);
                }
            }

            return response()->json([
                "status" => 200,
                "message" => "resto has been successfully ordered"],
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
                $booked = Detail_Order::where('table_id')->get();
                if($booked) {
                    return response()->json([
                        "status" => 400,
                        "message" => "Sorry, table . $booked->table->name . has already been booked"
                    ]);
                }
            }

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $shuffleCharacters = str_shuffle($characters);
            $randomString = substr($shuffleCharacters, 0, 5);
            $numbers = '1234567890';
            $shuffleNumbers = str_shuffle($numbers);
            $randomNumber = substr($shuffleNumbers, 0, 10);

            $resto = Resto::find($request->input('resto_id'));

            $total_table = count($tables);
            foreach ($tables as $x => $table) {
                $price = Table::find($table);
                $tables[$x]->total_price += $price->price;
            }
            $total_price = $tables->total->price;

            $order = Order::create([
                "order_id" => "BR" . $randomString . $randomNumber,
                "name" => $request->input('name'),
                "status" => "success",
                "total_table" => $total_table,
                "total_price" => $total_price,
                "resto_id" => $resto->id
            ]);

            if($tables) {
                foreach($tables as $x => $table) {
                    Detail_Order::create([
                        "order_id" => $order->id,
                        "table_id" => $table
                    ]);
                }
            }

            return response()->json([
                "status" => 200,
                "message" => "resto has been successfully ordered"],
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
