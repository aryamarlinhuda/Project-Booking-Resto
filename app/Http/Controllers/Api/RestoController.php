<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Detail_Order;
use App\Models\Resto;
use App\Models\Image;
use App\Models\Menu;
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
            $image = Image::where('resto_id',$item->id)->first();
            if($image) {
                $data[$x]->photo = url('storage/'.$image->image);
            } else {
                $data[$x]->photo = null;
            }

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

        $images = Image::where('resto_id',$id)->get();
        if($images) {
            foreach ($images as $x => $image) {
                $images[$x]->photo_url = url('storage/'.$image->image);
            }
            $data->photo_url = $images;
        } else {
            $data->photo_url = null;
        }

        if($data->category_id) {
            $data->category = $data->category->name;
        } else {
            $data->category = null;
        }

        $data->province = $data->province->name;
        $data->city = $data->city->name;

        $all_tables = Table::where('resto_id',$id)->count();
        $table_reserved = Table::where('resto_id',$id)->count();
        
        $data->all_tables = $all_tables;
        $data->table_reserved = $table_reserved;
        $data->empty_table = $all_tables - $table_reserved;

        $reviews = Review::where('Resto_id',$data->id)->first();
        if(!$reviews) {
            $data->rating = null;
        } else {
            $average_rating = Review::where('Resto_id',$data->id)->average('rating');
            $getRating = substr($average_rating, 0, 3);
            $formattedRating = str_replace('.', ',', $getRating);
            $data->rating = $formattedRating;
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );
    }

    public function list_table($id) {
        $tables = Table::where('resto_id',$id)->get();

        foreach ($tables as $x => $table) {
            if($table->ordered === null) {
                $tables[$x]->booked = false;
            } else {
                $tables[$x]->booked = true;
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $tables],
            200
        );
    }

    public function list_menu($id) {
        $menus = Menu::where('resto_id',$id)->get();
        foreach ($menus as $x => $menu) {
            $menus[$x]->image = url('storage/'.$menu->photo);
        }

        return response()->json([
            "status" => 200,
            "data" => $menus],
            200
        );
    }

    public function list_category() {
        $data = Category::orderBy('name','asc')->get();

        return response()->json([
            "status" => 200,
            "data" => $data
        ], 200);
    }

    public function filter_by_category($id) {
        $data = Resto::where('category_id',$id)->get();

        foreach($data as $x => $item) {
            $image = Image::where('resto_id',$item->id)->first();
            if($image) {
                $data[$x]->photo = url('storage/'.$image->image);
            } else {
                $data[$x]->photo = null;
            }

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
            $image = Image::where('resto_id',$item->id)->first();
            if($image) {
                $data[$x]->photo = url('storage/'.$image->image);
            } else {
                $data[$x]->photo = null;
            }

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
            $image = Image::where('resto_id',$item->id)->first();
            if($image) {
                $data[$x]->photo = url('storage/'.$image->image);
            } else {
                $data[$x]->photo = null;
            }

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
