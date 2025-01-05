<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Brand;
class BrandController extends Controller
{

    public function add_brand(){
        return view('dashboard.brands.add');

    
    }//End Method


    public function store_brand(Request $request){
        
        $validated = $request->validate([
            'name' => 'required|unique:brands',
            'img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ],[
            'name.required' => 'Brand Name is required.',
            'name.unique'   => 'This Brand Name is already added.',
            'img.required'  => 'Brand Image is required.',
            'img.image'     => 'The file must be an image.',
            'img.mimes'     => 'The image must be of type jpeg, png, jpg,',
            'img.max'       => 'The image size must not exceed 2MB.',
        ]);

        $brand = strip_tags($request->name);

        $img = $request->file('img');

        $gen    = hexdec(uniqid());
        $ex     = strtolower($img->getClientOriginalExtension());
        $name   = $gen . '.' . $ex;
        $location = 'brand/';
        $source = $location.$name;
        $img->move($location,$name);

        $Brand = Brand::insert([
            'name' => $brand,
            'img'  => $source,
            'created_at'=>Carbon::now()
        ]);

        if($brand==true){

            return redirect()->back()->with('msg','Brand Added Successfully');

        }else{

            return redirect()->back()->with('msg','Brand Not Added Successfully');
        }
        
    }//End Method

    public function view_brand(){

        $data = Brand::latest()->paginate(10);
        return view('dashboard.brands.index',compact('data'));
    }//End Method

    public function edit_brand($id){

        $data = Brand::findOrfail($id);

        return view('dashboard.brands.edit',compact('data'));

    }//End Method

    public function update_brand(Request $request){
        
        $validated = $request->validate([
            'name' => 'required|unique:brands',
            
        ],[
            'name.required'   => 'Brand Name Is Requiredd',
            'name.unique'     => 'The Brand Name Already Exist'
        ]);

        $id = $request->id;
        $name = strip_tags($request->name);

        $data = Brand::where('id', '=',$id)->first();

        $data->name = $name;

        if($request->hasFile('img')){

            unlink($data->img);

            $img = $request->file('img');
            $gen = hexdec(uniqid());
            $ex  = strtolower($img->getClientOriginalExtension());
            $photo = $gen. '.' .$ex;
            $location = 'brand/';
            $source = $location . $photo;
            $img->move($location,$photo);

            $data->img = $source;
        }
        $data->save();
        return redirect()->back()->with('msg','Brand Updated Successfully');
    }//End Method


    public function delete_brand($id){

        $brand  = Brand::findOrfail($id);
        unlink($brand->img);

        $brand->delete();

        return redirect()->back()->with('msg', 'Brand Deleted Successfully');
    }//End Method 
}
