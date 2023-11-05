<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Resto;
use App\Models\Image;
use App\Models\Province;
use App\Models\Review;
use Illuminate\Http\Request;

class RestoController extends Controller
{
    public function list(Request $request) {
        $katakunci = $request->katakunci;

        if(strlen($katakunci)) {
            $data = Resto::where('name','like','%'.$katakunci.'%')->paginate(10);
        } else {
            $data = Resto::paginate(10);
        }
        
        return view('Resto.list-Resto')->with('data',$data);
    }

    public function detail($id) {
        $data = Resto::findOrFail($id);
        $images = Image::where('Resto_id',$id)->get();
        $reviews = Review::where('Resto_id',$id)->get();

        if(!$data->budget) {
            $data->budget = "Free";
        } else {
            $data->budget = "Rp ".number_format($data->budget).",00";
        }

        $check = Review::where('Resto_id',$id)->first();
        if(!$check) {
            $data->rating = "No Reviews Yet";
        } else {
            $average_rating = Review::where('Resto_id',$id)->average('rating');
            $getRating = substr($average_rating, 0, 3);
            $formattedRating = str_replace('.', ',', $getRating);
            $data->rating = $formattedRating;
        }

        return view('Resto.detail-Resto', compact('data','images','reviews')); 
    }
    
    public function create() {
        $categories = Category::orderBy('name','asc')->get();
        $provinces = Province::orderBy('name','asc')->get();
        $cities = City::orderBy('name','asc')->get();

        return view('Resto.create-Resto', compact('categories','provinces','cities'));
    }

    public function create_process(Request $request) {
        $request->validate([
            "name" => "required",
            "description" => "required",
            "category_id" => "required",
            "province_id" => "required",
            "city" => "required",
            "address" => "required",
            "contact" => "required",
            "latitude" => "required",
            "longitude" => "required"
        ],[
            "name.required" => "Resto Name is required!",
            "description.required" => "Description is required!",
            "category_id.required" => "Category is required!",
            "province_id.required" => "Province is required!",
            "city.required" => "City is required!",
            "address.required" => "Address is required!",
            "contact.required" => "Contact Resto is required!",
            "latitude.required" => "Latitude is required!",
            "longitude.required" => "Longitude is required!",
        ]);

        $named = Resto::where('name',$request->input('name'))->first();
        if($named) {
            return redirect('Resto/create')->with('unique','Resto Name already exists!');
        }

        $request->validate([
            'files.*' => 'max:3048'
        ],[
            "photo.max" => "Photo size must be less than 3MB!",
        ]);

        $files = $request->file('files');

        if($files) {
            foreach($files as $file) {
                $format = $file->getClientOriginalExtension();
                if(!strtolower($format) === 'jpg' || !strtolower($format) === 'jpeg' || !strtolower($format) === 'png') {
                    return redirect('resto/create')->with('format','The photo Resto format must be jpg, jpeg, or png!');
                }
            }
        }

        Resto::create([
            "name" => $request->input('name'),
            "description" => $request->input('description'),
            "category_id" => $request->input('category_id'),
            "province_id" => $request->input('province_id'),
            "city_id" => $request->input('city'),
            "address" => $request->input('address'),
            "contact" => $request->input('contact'),
            "latitude" => $request->input('latitude'),
            "longitude" => $request->input('longitude'),
        ]);

        if($files) {
            foreach($files as $file) {
                $photo = $file->store('resto_photo');
                $resto = Resto::where('name',$request->input('name'))->first();
                Image::create([
                    "image" => $photo,
                    "resto_id" => $resto->id
                ]);
            }
        }

        return redirect('menu/add')->with('success','Resto successfully created');
    }

    public function edit($id) {
        $data = Resto::find($id);
        $photos = Image::where('resto_id',$id)->get();
        $categories = Category::whereNotIn('id',[$data->category_id])->orderBy('name','asc')->get();
        $provinces = Province::whereNotIn('id',[$data->province_id])->orderBy('name','asc')->get();
        $cities = City::whereNotIn('id',[$data->city_id])->orderBy('name','asc')->get();

        return view('resto.edit-Resto', compact('data','photos','categories','provinces','cities'));
    }

    public function edit_process($id, Request $request) {
        $data = Resto::find($id);
        $request->validate([
            "name" => "required",
            "description" => "required",
            "category_id" => "required",
            "province_id" => "required",
            "city" => "required",
            "address" => "required",
            "latitude" => "required",
            "longitude" => "required"
        ],[
            "name.required" => "Resto Name is required!",
            "description.required" => "Description is required!",
            "category_id.required" => "Category is required!",
            "province_id.required" => "Province is required!",
            "city.required" => "City is required!",
            "address.required" => "Address is required!",
            "latitude.required" => "Latitude is required!",
            "longitude.required" => "Longitude is required!",
        ]);

        $named = Resto::whereNotIn('id',[$id])->where('name',$request->input('name'))->first();
        if($named) {
            return redirect('Resto/edit/'.$id)->with('unique','Resto Name already exists!');
        }

        $request->validate([
            'files.*' => 'max:3048'
        ],[
            "photo.max" => "Photo size must be less than 3MB!",
        ]);

        $files = $request->file('files');

        if($files) {
            foreach($files as $file) {
                $format = $file->getClientOriginalExtension();
                if(!strtolower($format) === 'jpg' || !strtolower($format) === 'jpeg' || !strtolower($format) === 'png') {
                    return redirect('Resto/edit/'.$id)->with('format','The photo Resto format must be jpg, jpeg, or png!');
                }
            }
        }

        Resto::where('id',$id)->update([
            "name" => $request->input('name'),
            "description" => $request->input('description'),
            "category_id" => $request->input('category_id'),
            "province_id" => $request->input('province_id'),
            "city_id" => $request->input('city'),
            "address" => $request->input('address'),
            "contact" => $request->input('contact'),
            "latitude" => $request->input('latitude'),
            "longitude" => $request->input('longitude'),
        ]);

        if($files) {
            foreach($files as $file) {
                $photo = $file->store('resto_photo');
                $Resto_id = Resto::where('name',$request->input('name'))->first();
                Image::create([
                    "image" => $photo,
                    "Resto_id" => $Resto_id->id
                ]);
            }
        }

        return redirect('Resto/list')->with('success','Resto successfully edited');
    }

    public function del_photo($id) {
        $data = Image::where('id',$id)->first();
        Image::where('id',$id)->delete();

        return redirect('Resto/edit/'.$data->Resto_id)->with('deleted','Image successfully deleted');
    }
    public function delete($id) {
        Resto::where('id',$id)->delete();
        Image::where('Resto_id',$id)->delete();

        return redirect('Resto/list')->with('success','Resto successfully deleted');
    }
}
