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
        foreach ($carts as $x => $cart) {
            $tables = Detail_Cart::where('cart_id',$cart->id)->get();
            $tables = $tables->map(function ($table) {
                $table->table = Table::where('id',$table->table_id)->first();
                return $table;
            });
            $carts[$x]->tables = $tables;

            $carts[$x]->resto = Resto::where('id',$cart->resto_id)->first();
        }

        return response()->json([
            "status" => 200,
            "data" => $carts],
            200
        );
    }

    public function detail($id) {
        $user = auth()->id();

        $cart = Cart::where('id',$id)->where('carted_by',$user)->first();
        if(!$cart) {
            return response()->json([
                "status" => 404,
                "message"=> "Cart data not found"],
                404
            );
        }

        $tables = Detail_Cart::where('cart_id',$cart->id)->get();
        $tables = $tables->map(function ($table) {
            $table->table = Table::where('id',$table->table_id)->first();
            return $table;
        });
        $cart->tables = $tables;

        $cart->resto = Resto::where('id',$cart->resto_id)->first();

        return response()->json([
            "status" => 200,
            "data" => $cart],
            200
        );
    }

    public function add(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "resto_id" => "required",
                "total_table" => "required",
                "total_price" => "required"
            ],[
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

            $cart = Cart::where('resto_id',$request->input('resto_id'))->where('carted_by',$user)->first();
            if($cart) {
                return response()->json([
                    "status" => 201,
                    "message" => "Cart created successfully",
                    "data" => $cart],
                    201
                );
            } else {
                $cart = Cart::create([
                    "resto_id" => $request->input("resto_id"),
                    "total_table" => $request->input("total_table"),
                    "total_price" => $request->input("total_price"),
                    "carted_by" => $user
                ]);
            }

            return response()->json([
                "status" => 200,
                "message" => "Resto has been successfully added to cart",
                "data" => $cart],
                200
            );

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function add_table(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "cart_id" => "required",
                "table_id" => "required"
            ],[
                "cart_id.required" => "Cart ID is required!",
                "table_id.required" => "Table ID is required!"
            ]);

            $cart = Cart::where('id',$request->input('cart_id'))->where('carted_by',$user)->first();
            if (!$cart) {
                return response()->json([
                    "status" => 404,
                    "message" => "Cart ID Not Found!"],
                    404
                );
            }

            $carted = Detail_Cart::where('cart_id',$request->input('cart_id'))->where('table_id',$request->input('table_id'))->first();
            if ($carted) {
                return response()->json([
                    "status" => 400,
                    "message" => "Resto Table has been added to cart"],
                    400
                );
            }

            $table = Table::where('id',$request->input('table_id'))->where('resto_id',$cart->resto_id)->first();
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

            Detail_cart::create([
                "table_id" => $request->input('table_id'),
                "cart_id" => $request->input("cart_id")
            ]);

            return response()->json([
                "status" => 200,
                "message" => "Resto Table has been successfully added to cart"],
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
                "table_id" => "required",
                "form_action" => "required"
            ],[
                "cart_id.required" => "Cart ID is required!",
                "table_id.required" => "Table ID is required!",
                "form_action.required" => "Form Action is required!",
            ]);

            $cart = Cart::where('id',$request->input('cart_id'))->where('carted_by',$user)->first();
            if (!$cart) {
                return response()->json([
                    "status" => 404,
                    "message" => "Cart ID Not Found!"],
                    404
                );
            }

            $table = Table::where('id',$request->input('table_id'))->first();
            if(!$table) {
                return response()->json([
                    "status" => 404,
                    "message" => "Table ID Not Found!"],
                    404
                );
            }

            $carted = Detail_Cart::where('cart_id',$request->input('cart_id'))->where('table_id',$request->input('table_id'))->first();
            if ($carted) {
                return response()->json([
                    "status" => 400,
                    "message" => "Resto Table has been added to cart"],
                    400
                );
            }
            
            if($request->input('form_action') === "add") {
                if($table->booked === 1) {
                    return response()->json([
                        "status" => 403,
                        "message" => "Table already booked"],
                        403
                    );
                }
                Detail_cart::create([
                    "table_id" => $request->input('table_id'),
                    "cart_id" => $request->input("cart_id")
                ]);
            } else if($request->input("form_action") === "delete") {
                Detail_cart::where('table_id', $request->input('table_id'))->delete();
            } else {
                return response()->json([
                    "status" => 400,
                    "message" => "Invalid Form Action"],
                    400
                );
            }

            return response()->json([
                "status" => 200,
                "message" => "Resto Table has been successfully edited"],
                200
            );

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
