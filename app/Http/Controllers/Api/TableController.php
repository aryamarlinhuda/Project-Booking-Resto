<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Detail_Cart;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function single($id) {
        $user = auth()->id();
        $data = Table::where('resto_id',$id)->where('load',1)->orderBy('name','asc')->get();
        foreach ($data as $x => $item) {
            $data[$x]->resto = $item->resto;

            if($item->booked === 1) {
                $data[$x]->ordered = true;
            } else {
                $data[$x]->ordered = false;
            }

            $cart = Cart::where('resto_id',$id)->where('carted_by',$user)->first();
            $carted = Detail_Cart::where('cart_id',$cart->id)->where('table_id',$item->id)->first();
            if($carted) {
                $data[$x]->carted = true;
            } else {
                $data[$x]->carted = false;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

    public function double($id) {
        $user = auth()->id();
        $data = Table::where('resto_id',$id)->where('load',2)->orderBy('name','asc')->get();
        foreach ($data as $x => $item) {
            $data[$x]->resto = $item->resto;

            if($item->booked === 1) {
                $data[$x]->ordered = true;
            } else {
                $data[$x]->ordered = false;
            }

            $cart = Cart::where('resto_id',$id)->where('carted_by',$user)->first();
            $carted = Detail_Cart::where('cart_id',$cart->id)->where('table_id',$item->id)->first();
            if($carted) {
                $data[$x]->carted = true;
            } else {
                $data[$x]->carted = false;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

    public function triple($id) {
        $user = auth()->id();
        $data = Table::where('resto_id',$id)->where('load',3)->orderBy('name','asc')->get();
        foreach ($data as $x => $item) {
            $data[$x]->resto = $item->resto;

            if($item->booked === 1) {
                $data[$x]->ordered = true;
            } else {
                $data[$x]->ordered = false;
            }

            $cart = Cart::where('resto_id',$id)->where('carted_by',$user)->first();
            $carted = Detail_Cart::where('cart_id',$cart->id)->where('table_id',$item->id)->first();
            if($carted) {
                $data[$x]->carted = true;
            } else {
                $data[$x]->carted = false;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

    public function quadruple($id) {
        $user = auth()->id();
        $data = Table::where('resto_id',$id)->where('load',4)->orderBy('name','asc')->get();
        foreach ($data as $x => $item) {
            $data[$x]->resto = $item->resto;

            if($item->booked === 1) {
                $data[$x]->ordered = true;
            } else {
                $data[$x]->ordered = false;
            }

            $cart = Cart::where('resto_id',$id)->where('carted_by',$user)->first();
            if($cart) {
                $carted = Detail_Cart::where('cart_id',$cart->id)->where('table_id',$item->id)->first();
                if($carted) {
                    $data[$x]->carted = true;
                } else {
                    $data[$x]->carted = false;
                }
            } else {
                $data[$x]->carted = false;
            }
            
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }
}
