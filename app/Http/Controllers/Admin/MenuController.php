<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Menu;
use App\Models\Image;
use App\Models\Province;
use App\Models\Resto;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function list(Request $request) {
        $katakunci = $request->katakunci;

        if(strlen($katakunci)) {
            $data = Menu::where('name','like','%'.$katakunci.'%')->paginate(10);
        } else {
            $data = Menu::paginate(10);
        }
        
        return view('menu.list-menu')->with('data',$data);
    }
    
    public function create($id) {
        $data = Resto::find($id);

        return view('menu.create-menu')->with('data',$data);
    }

    public function create_process($id, Request $request) {
        $request->validate([
            "name" => "required",
            "photo" => "max:3048",
            "description" => "required",
            "price" => "required"
        ],[
            "name.required" => "menu Name is required!",
            "photo.max" => "Photo size must be less than 3MB!",
            "description.required" => "Description is required!",
            "price.required" => "price is required!",
        ]);

        $named = menu::where('name',$request->input('name'))->first();
        if($named) {
            return redirect('menu/create')->with('unique','menu Name already exists!');
        }

        $file = $request->file('photo');
        if($file) {
            $format = $file->getClientOriginalExtension();
            if(strtolower($format) === 'jpg' || strtolower($format) === 'jpeg' || strtolower($format) === 'png') {
                $photo = $request->file('photo')->store('resto_menu');
            } else {
                return redirect('user/create')->with('format','The photo format must be jpg, jpeg, or png!');
            }
        } else {
            $photo = null;
        }

        Menu::create([
            "name" => $request->input('name'),
            "photo" => $photo,
            "description" => $request->input('description'),
            "price" => $request->input('price'),
            "resto_id" => $id
        ]);

        return redirect('menu/list/'.$id)->with('success','menu successfully created');
    }

    public function edit($id) {
        $data = menu::find($id);
        $photos = Image::where('menu_id',$id)->get();
        $categories = Category::whereNotIn('id',[$data->category_id])->orderBy('name','asc')->get();
        $provinces = Province::whereNotIn('id',[$data->province_id])->orderBy('name','asc')->get();
        $cities = City::whereNotIn('id',[$data->city_id])->orderBy('name','asc')->get();

        return view('menu.edit-menu', compact('data','photos','categories','provinces','cities'));
    }

    public function edit_process($id, Request $request) {
        $data = menu::find($id);
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
            "name.required" => "menu Name is required!",
            "description.required" => "Description is required!",
            "category_id.required" => "Category is required!",
            "province_id.required" => "Province is required!",
            "city.required" => "City is required!",
            "address.required" => "Address is required!",
            "latitude.required" => "Latitude is required!",
            "longitude.required" => "Longitude is required!",
        ]);

        $named = menu::whereNotIn('id',[$id])->where('name',$request->input('name'))->first();
        if($named) {
            return redirect('menu/edit/'.$id)->with('unique','menu Name already exists!');
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
                    return redirect('menu/edit/'.$id)->with('format','The photo menu format must be jpg, jpeg, or png!');
                }
            }
        }

        menu::where('id',$id)->update([
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
                $photo = $file->store('menu_photo');
                $menu_id = menu::where('name',$request->input('name'))->first();
                Image::create([
                    "image" => $photo,
                    "menu_id" => $menu_id->id
                ]);
            }
        }

        return redirect('menu/list')->with('success','menu successfully edited');
    }

    public function del_photo($id) {
        $data = Image::where('id',$id)->first();
        Image::where('id',$id)->delete();

        return redirect('menu/edit/'.$data->menu_id)->with('deleted','Image successfully deleted');
    }
    public function delete($id) {
        menu::where('id',$id)->delete();
        Image::where('menu_id',$id)->delete();

        return redirect('menu/list')->with('success','menu successfully deleted');
    }
}
