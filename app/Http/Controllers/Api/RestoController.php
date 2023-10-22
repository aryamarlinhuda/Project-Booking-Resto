<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Detail_Order;
use App\Models\Resto;
use App\Models\Image;
use App\Models\Province;
use App\Models\Review;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RestoController extends Controller
{
    public function list() {
        $data = Resto::all();

        foreach($data as $x => $item) {
            $image = Image::where('Resto_id',$item->id)->pluck('image');
            $data[$x]->photo = $image;
            $url = url("storage");
            $url_image = collect($image)->map(function ($image) use ($url) {
                return $url ."/". $image;
            }); 
            $data[$x]->photo = $url_image;

            if($item->category_id) {
                $data[$x]->category = $item->category->category;
            } else {
                $data[$x]->category = null;
            }

            $data[$x]->province = $item->province->name;
            $data[$x]->city = $item->city->name;

            $reviews = Review::where('Resto_id',$item->id)->first();
            if(!$reviews) {
                $data[$x]->rating = null;
            } else {
                $average_rating = Review::where('Resto_id',$item->id)->average('rating');
                $getRating = substr($average_rating, 0, 3);
                $formattedRating = str_replace('.', ',', $getRating);
                $data[$x]->rating = $formattedRating;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

    public function detail($id) {
        $data = Resto::findOrFail($id);

        $image = Image::where('Resto_id',$data->id)->pluck('image');
        $data->photo = $image;
        $url = url("storage");
        $url_image = collect($image)->map(function ($image) use ($url) {
            return $url ."/". $image;
        }); 
        $data->photo = $url_image;

        if($data->category_id) {
            $data->category = $data->category->category;
        } else {
            $data->category = null;
        }

        $data->province = $data->province->name;
        $data->city = $data->city->name;

        $all_tables = Table::where('resto_id',$id)->count();
        $table_reserved = Detail_Order::where('resto_id',$id)->count();
        
        $data->all_tables = $all_tables;
        $data->table_reserved = $table_reserved;
        $data->empty_table = $all_tables - $table_reserved;

        // $reviews = Review::where('Resto_id',$data->id)->first();
        // if(!$reviews) {
        //     $data->rating = null;
        // } else {
        //     $average_rating = Review::where('Resto_id',$data->id)->average('rating');
        //     $getRating = substr($average_rating, 0, 3);
        //     $formattedRating = str_replace('.', ',', $getRating);
        //     $data->rating = $formattedRating;
        // }

        // $review = Review::where('Resto_id',$id)->get();

        // foreach($review as $x => $item) {
        //     $review[$x]->username = $item->maker->name;

        //     $updated_at = $item->updated_at;

        //     if(now()->diffInSeconds($updated_at) === 0) {
        //         $review[$x]->last_made = "now";
        //     } else if(now()->diffInSeconds($updated_at) < 60) {
        //         $review[$x]->last_made = now()->diffInSeconds($updated_at) . " seconds ago";
        //     } else if(now()->diffInSeconds($updated_at) < 3600) {
        //         $review[$x]->last_made = now()->diffInMinutes($updated_at) . " minutes ago";
        //     } else if(now()->diffInSeconds($updated_at) < 86400) {
        //         $review[$x]->last_made = now()->diffInHours($updated_at) . " hours ago";
        //     } else if(now()->diffInSeconds($updated_at) < 172800) {
        //         $review[$x]->last_made = "yesterday";
        //     } else if(now()->diffInSeconds($updated_at) >= 172800) {
        //         $dateTime = new DateTime($updated_at);
        //         $formated_date = $dateTime->format('H:i d-M-Y');
        //         $date = Carbon::createFromFormat('H:i d-M-Y', $formated_date);
        //         $review[$x]->last_made = $date->format('H.i l, d F Y');
        //     }
        // }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

    public function list_table($id) {
        $tables = Table::where('resto_id',$id)->get();

        foreach ($tables as $x => $table) {
            $booking = Detail_Order::where('table_id',$table->id)->first();
            if($booking) {
                $tables[$x]->booked = true;
            } else {
                $tables[$x]->booked = false;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $tables],
            200
        );
    }

    public function list_category() {
        $data = Category::orderBy('category','asc')->get();

        return response()->json([
            "status" => 200,
            "data" => $data
        ], 200);
    }

    public function filter_by_category($id) {
        $data = Resto::where('category_id',$id)->get();

        foreach($data as $x => $item) {
            $image = Image::where('Resto_id',$item->id)->pluck('image');
            $data[$x]->photo = $image;
            $url = url("storage");
            $url_image = collect($image)->map(function ($image) use ($url) {
                return $url ."/". $image;
            }); 
            $data[$x]->photo = $url_image;

            if($item->category_id) {
                $data[$x]->category = $item->category->category;
            } else {
                $data[$x]->category = null;
            }

            $data[$x]->province = $item->province->name;
            $data[$x]->city = $item->city->name;

            $reviews = Review::where('Resto_id',$item->id)->first();
            if(!$reviews) {
                $data[$x]->rating = null;
            } else {
                $average_rating = Review::where('Resto_id',$item->id)->average('rating');
                $getRating = substr($average_rating, 0, 3);
                $formattedRating = str_replace('.', ',', $getRating);
                $data[$x]->rating = $formattedRating;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }
    public function list_province() {
        $data = Province::orderBy('name','asc')->get();

        return response()->json([
            "status" => 200,
            "data" => $data
        ], 200);
    }

    public function filter_by_province($id) {
        $data = Resto::where('province_id',$id)->get();

        foreach($data as $x => $item) {
            $image = Image::where('Resto_id',$item->id)->pluck('image');
            $data[$x]->photo = $image;
            $url = url("storage");
            $url_image = collect($image)->map(function ($image) use ($url) {
                return $url ."/". $image;
            }); 
            $data[$x]->photo = $url_image;

            if($item->category_id) {
                $data[$x]->category = $item->category->category;
            } else {
                $data[$x]->category = null;
            }

            $data[$x]->province = $item->province->name;
            $data[$x]->city = $item->city->name;

            $reviews = Review::where('Resto_id',$item->id)->first();
            if(!$reviews) {
                $data[$x]->rating = null;
            } else {
                $average_rating = Review::where('Resto_id',$item->id)->average('rating');
                $getRating = substr($average_rating, 0, 3);
                $formattedRating = str_replace('.', ',', $getRating);
                $data[$x]->rating = $formattedRating;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

    public function list_city() {
        $data = City::orderBy('name','asc')->get();

        foreach($data as $x => $item) {
            $data[$x]->province = $item->province->name;
        }

        return response()->json([
            "status" => 200,
            "data" => $data
        ], 200);
    }

    public function list_city_by_province($id) {
        $data = City::where('province_id',$id)->orderBy('name','asc')->get();
        
        foreach($data as $x => $item) {
            $data[$x]->province = $item->province->name;
        }

        return response()->json([
            "status" => 200,
            "data" => $data
        ], 200);
    }

    public function filter_by_city($id) {
        $data = Resto::where('city_id',$id)->get();

        foreach($data as $x => $item) {
            $image = Image::where('Resto_id',$item->id)->pluck('image');
            $data[$x]->photo = $image;
            $url = url("storage");
            $url_image = collect($image)->map(function ($image) use ($url) {
                return $url ."/". $image;
            }); 
            $data[$x]->photo = $url_image;

            if($item->category_id) {
                $data[$x]->category = $item->category->category;
            } else {
                $data[$x]->category = null;
            }

            $data[$x]->province = $item->province->name;
            $data[$x]->city = $item->city->name;

            $reviews = Review::where('Resto_id',$item->id)->first();
            if(!$reviews) {
                $data[$x]->rating = null;
            } else {
                $average_rating = Review::where('Resto_id',$item->id)->average('rating');
                $getRating = substr($average_rating, 0, 3);
                $formattedRating = str_replace('.', ',', $getRating);
                $data[$x]->rating = $formattedRating;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

}
