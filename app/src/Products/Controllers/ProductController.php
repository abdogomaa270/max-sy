<?php

namespace App\src\Products\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductController
{
    public function getall(){
        $products=Product::with('images')->get();
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ], 201);
    }
    public function createProduct(Request $request)
    {
        $product = new Product();
        $product->title = $request->input('title');
        $product->desc = $request->input('desc');
        $product->price = $request->input('price');
        $product->save();

        // Storing product images
        $images = $request->file('images');
        if($images){
            $imagesArray = is_array($images) ? $images : [$images];


            if (is_array($imagesArray) && count($imagesArray) > 0) {
                foreach ($imagesArray as $image) {
                    $bool=1;
                    $extension = $image->getClientOriginalExtension();
                    $imageName = Str::random(40) . '.' . $extension; // Generating a random name for the image
                    $path = Storage::disk('public')->putFileAs('product_images', $image, $imageName);

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = $path;
                    $productImage->save();
                }
            } else {
                // Handle the case where no images are found
                return response()->json(['status' => 'failed', 'message' => 'No images found in the request'], 400);
            }
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }


    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['status' => 'failed', 'msg' => 'Product not found'], 404);
        }

        // Update users before deletion
        User::where('product_id', $id)->update(['product_id' => null]);

        // Deleting associated images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        return response()->json(['status' => 'success', 'msg' => 'Product deleted successfully'], 200);
    }




}
