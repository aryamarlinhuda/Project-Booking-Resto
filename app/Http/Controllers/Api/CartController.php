<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Detail_Cart;
use App\Models\Resto;
use App\Models\Table;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function list() {
        $user = auth()->id();

        $carts = Cart::where('carted_by',$user)->get();
        if($carts === null) {
            return response()->json([
                "status" => 200,
                "data" => []],
                200
            );
        }
        
        foreach ($carts as $x => $cart) {
            $carts[$x]->resto = Resto::where('id',$cart->resto_id)->first();
            $carts[$x]->overall_price += $cart->total_price; 
        }

        return response()->json([
            "status" => 200,
            "data" => $carts],
            200
        );
    }

    public function add(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "resto_id" => "required",
                "table_number.*" => "required"
            ],[
                "resto_id.required" => "Resto ID is required!",
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

            $cart = Cart::where('resto_id',$request->input('resto_id'))->where('carted_by',$user)->first();
            if($cart) {
                foreach ($tables as $x => $table) {
                    $carted = Detail_Cart::where('id',$cart->id)->where('table_id',$table);
                    if(!$carted) {
                        Detail_Cart::create([
                            "cart_id" => $cart->id,
                            "table_id" => $table
                        ]);
                    }
                }
            } else {
                $resto = Resto::find($request->input('resto_id'));

                $total_table = count($tables);
                foreach ($tables as $x => $table) {
                    $price = Table::find($table);
                    $tables[$x]->total_price += $price->price;
                }
                $total_price = $tables->total->price;

                $cart = Cart::create([
                    "total_table" => $total_table,
                    "total_price" => $total_price,
                    "resto_id" => $resto->id,
                    "carted_by" => $user
                ]);

                foreach($tables as $x => $table) {
                    Detail_Cart::create([
                        "cart_id" => $cart->id,
                        "table_id" => $table
                    ]);
                }
            }

            return response()->json([
                "status" => 200,
                "message" => "resto has been successfully added to cart"],
                200
            );

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function edit(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "cart_id" => "required",
                "table_number.*" => "required"
            ],[
                "cart_id.required" => "Cart ID is required!",
                "table_number.required" => "Table Number is required!"
            ]);
            $tables = $request->input('table_number');

            $cart = Cart::where('id',$request->input('cart_id'))->where('carted_by',$user)->first();
            if(!$cart) {
                return response()->json([
                    "status" => 404,
                    "message" => "cart not found"],
                    404
                );
            }

            if(count($tables) === 0) {
                $cart->delete();
                return response()->json([
                    "status" => 200,
                    "message" => "Cart Deleted"],
                    200
                );
            }

            $tables = Detail_Cart::where('Cart_id',$cart->id)->get();
            $tables->delete();
            foreach ($tables as $x => $table) {
                $carted = Detail_Cart::where('id',$cart->id)->where('table_id',$table);
                if(!$carted) {
                    Detail_Cart::create([
                        "cart_id" => $cart->id,
                        "table_id" => $table
                    ]);
                }
            }
            $carts = Cart::find($request->input('cart_id'));
            $carts->detail_cart = Detail_Cart::where('Cart_id',$cart->id)->get();

            return response()->json([
                "status" => 200,
                "message" => "Cart has been successfully edited",
                "data" => $carts
                ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function remove(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "cart_id" => "required",
            ],[
                "cart_id.required" => "Cart ID is required!",
            ]);

            $cart = Cart::where('id',$request->input('cart_id'))->where('carted_by',$user)->first();
            if($cart) {
                $cart->delete();
                return response()->json([
                    "status" => 200,
                    "message" => "Resto has been successfully removed from cart"],
                    200
                );
            } else {
                return response()->json([
                    "status" => 404,
                    "message" => "Cart not found"],
                    404
                );
            }
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }
}
