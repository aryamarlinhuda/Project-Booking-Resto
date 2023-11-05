<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function list(Request $request) {
        $katakunci = $request->katakunci;
        if(strlen($katakunci)) {
            $data = City::where('name','like','%'.$katakunci.'%')->orderBy('name','asc')->paginate(10);
        } else {
            $data = City::orderBy('name','asc')->paginate(10);
        }
        return view('city.list-city')->with('data',$data);
    }

    public function getCitiesByProvince(Request $request) {
        $provinceId = $request->input('province_id');
        $cities = City::where('province_id', $provinceId)->get();
        return response()->json($cities);
    }
    
    public function create() {
        $provinces = Province::orderBy('name','asc')->get();

        return view('city.create-city')->with('provinces',$provinces);
    }

    public function create_process(Request $request) {
        $request->validate([
            "name" => "required",
            "province" => "required"
        ],[
            "name.required" => "City Name is required!",
            "province.required" => "Province is required!"
        ]);

        $unique = City::where('name',$request->input('name'))->first();
        if($unique) {
            return redirect('city/create')->with('unique','City Name already exists!');
        }

        $province_id = Province::where('name',$request->input('province'))->first();
        if(!$province_id) {
            return redirect('city/create')->with('wrong','Province name not found, please create it first!');
        }
        City::create([
            "name" => $request->input('name'),
            "province_id" => $province_id->id
        ]);

        return redirect('city/list')->with('success','City created successfully!');
    }

    public function edit($id) {
        $data = City::find($id);
        $province = Province::where('id',$data->province_id)->first();
        $provinces = Province::orderBy('name','asc')->get();

        return view('city.edit-city', compact('data','province','provinces'));
    }

    public function edit_process(Request $request, $id) {
        $data = $request->validate([
            "name" => "required",
            "province_id" => "required"
        ],[
            "name.required" => "City Name is required!",
            "province_id.required" => "Province is required!"
        ]);

        $unique = City::where('name',$request->input('name'))->whereNotIn('id',[$id])->first();
        if($unique) {
            return redirect('city/edit/'.$id)->with('unique','City Name already exists!');
        }

        $province_id = Province::where('name',$request->input('province'))->whereNotIn('id',[$id])->first();
        if(!$province_id) {
            return redirect('city/create')->with('wrong','Province name not found, please create it first!');
        }
        Province::where('id',$id)->update([
            "name" => $request->input('name'),
            "province_id" => $province_id->id
        ]);

        return redirect('city/list')->with('success','City edited successfully!');
    }

    public function delete($id) {
        City::where('id',$id)->delete();

        return redirect('city/list')->with('success','City successfully deleted');
    }
}
