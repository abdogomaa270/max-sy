<?php

namespace App\src\News\Controllers;

use App\Models\News;
use Illuminate\Http\Request;


class NewController
{
    public function getall(){
        $news=News::all();
        return response()->json([
            'status' => 'success',
            'data' => $news,
        ], 201);
    }
    public function storeNew(Request $request)
    {
        $new = new News();
        $new->new = $request->new;
        $new->save();

        return response()->json([
            'status' => 'success',
            'msg' => 'تم اضافه الكلمات بنجاح',
            'data' => $new,
        ], 201);
    }


    public function deleteNew($id)
    {
        $new= News::find($id);
        if (!$new) {
            return response()->json(['status' => 'failed', 'msg' => 'new not found'], 404);
        }

        $new->delete();

        return response()->json(['status' => 'success', 'msg' => 'new deleted successfully'], 200);
    }



}
