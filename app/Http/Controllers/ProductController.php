<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductDetailResources;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(){
        $products = Products::all();
        return ProductDetailResources::collection($products->loadMissing(['writer:id,username', 'category:id,name']));

    }

    public function show($id){
        $products = Products::with(['writer:id,username', 'category:id,name'])->findOrFail($id);
        return new ProductDetailResources($products);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
            'category_id' => 'required',
        ]);
        
        $image = null;
        if($request->file){
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName.'.'.$extension;

            Storage::putFileAs('image', $request->file, $image);
        }

        $request['image'] = $image;
        $request['author'] = Auth::user()->id;
        $products = Products::create($request->all());
        return new ProductDetailResources($products->loadMissing(['writer:id,username', 'category:id,name']));
        
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
            'category_id' => 'required',
        ]);

        $products = Products::findOrFail($id);
        $products->update($request->all());
        return new ProductDetailResources($products->loadMissing(['writer:id,username', 'category:id,name']));
    }

    public function destroy($id){
        $products = Products::findOrFail($id);
        $products->delete();
        return new ProductDetailResources($products->loadMissing(['writer:id,username', 'category:id,name']));
    }

    function generateRandomString($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
