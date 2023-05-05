<?php

namespace App\Http\Controllers;

use App\Models\ImageCrud;
// use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File as FacadesFile;

class ImageCrudController extends Controller
{
    public function all_products(){
        $products = ImageCrud::all();
        return view('products',compact('products'));
    }
    public function add_new_product(){
        return view('add_new_product');
    }
    public function store_product(Request $request){
        $request->validate([
            'name'=> 'required',
            'image'=> 'required|mimes:png,jpg,jpeg',
        ]);

        $imageName = '';
        if($image = $request->file('image')){
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('images/products',$imageName);
            // Storage::put($imageName, $image);
        };
        ImageCrud::create([
            'name'=>$request->name,
            'image'=>$imageName,
        ]);
        Session()->flash('msg','Product Added Success');
        return redirect()->back();
    }
    public function edit_product($id){
        $product = ImageCrud::findOrFail($id);
        return view('edit_product',compact('product'));
    }
    public function update_product(Request $request, $id){
        $product = ImageCrud::findOrFail($id);
        $request->validate([
            'name'=> 'required',
        ]);

        $imageName = '';
        $deleteOldImage = 'images/products/'.$product->image;
        if($image = $request->file('image')){
            @unlink(public_path($deleteOldImage));
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('images/products',$imageName);
        }else{
            $imageName = $product->image;
        }
        ImageCrud::where('id',$id)->update([
            'name'=>$request->name,
            'image'=>$imageName,
        ]);
        Session()->flash('msg','Product Updated Success');
        return redirect()->back();
    }
    public function delete_product($id){
        $product = ImageCrud::findOrFail($id);
        $deleteOldImage = 'images/products/'.$product->image;
        if(file_exists($deleteOldImage)){
            // Storage::delete('images/products',$product->image);
            // Storage::delete($deleteOldImage);
            @unlink(public_path($deleteOldImage));
        }
        $product->delete();
        Session()->flash('msg','Product deleted');
        return redirect()->back();
    }
    // public function add_new_product(){
    //     // return('working');
    //     Storage::put('example.txt', 'rafiul');
    //     echo asset('storage/file.txt');
    //     // return redirect()->back();
    // }
    public function check(){
        Cache::put('name','rafiul');
        dd(cache()->get('name'));
    }

}
