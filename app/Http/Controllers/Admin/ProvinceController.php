<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function list(Request $request) {
        $katakunci = $request->katakunci;
        if(strlen($katakunci)) {
            $data = Province::where('name','like','%'.$katakunci.'%')->orderBy('name','asc')->paginate(10);
        } else {
            $data = Province::orderBy('name','asc')->paginate(10);
        }
        return view('province.list-province')->with('data',$data);
    }

    public function auto_complete(Request $request) {
        $query = $request->get('query');
        $filterResult = Province::where('name', 'LIKE', '%'. $query. '%')->get();
        return response()->json($filterResult);
    }

    public function create() {
        return view('province.create-province');
    }

    public function create_process(Request $request) {
        $request->validate([
            "name" => "required"
        ],[
            "name.required" => "Province Name is required!"
        ]);

        $unique = Province::where('name',$request->input('name'))->first();
        if($unique) {
            return redirect('province/create')->with('unique','Province Name already exists!');
        }

        Province::create([
            "name" => $request->input('name')
        ]);

        return redirect('province/list')->with('success','Province created successfully!');
    }

    public function edit($id) {
        $data = Province::find($id);

        return view('province.edit-province')->with('data',$data);
    }

    public function edit_process(Request $request, $id) {
        $data = $request->validate([
            "name" => "required"
        ],[
            "name.required" => "Province Name is required!"
        ]);

        $unique = Province::where('name',$request->input('name'))->whereNotIn('id',[$id])->first();
        if($unique) {
            return redirect('province/edit/'.$id)->with('unique','Province Name already exists!');
        }

        Province::where('id',$id)->update([
            "name" => $request->input('name')
        ]);

        return redirect('province/list')->with('success','Province edited successfully!');
    }

    public function delete($id) {
        Province::where('id',$id)->delete();

        return redirect('province/list')->with('success','Province successfully deleted');
    }
}
