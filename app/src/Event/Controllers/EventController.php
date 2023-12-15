<?php

namespace App\src\Event\Controllers;

use App\Models\Event;
use App\src\Event\Requests\EventCreationRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController
{
   public function create(EventCreationRequest $request){
        $data=$request->all();
        
        if($request->file("image")){
            $image=$request->file("image");
            $extension = $image->getClientOriginalExtension();
            $imageName = Str::random(40) . '.' . $extension; // Generating a random name for the image
            $path = Storage::disk('public')->putFileAs('event_images', $image, $imageName);

            $data['content']=$path;
        }

        $event = Event::create($data);
         
         return response()->json([
            'status' => 'success',
            'msg' => 'تم اضافه الكلمات بنجاح',
            'data' => $event,
        ], 201);
    }
    
    public function getall(){
      $event=Event::all();

      return response()->json([
          'status' => 'success',
          'data' => $event,
      ], 201);
  }

    public function delete($id)
    {
        $event= Event::find($id);
        if (!$event) {
            return response()->json(['status' => 'failed', 'msg' => 'event not found'], 404);
        }

        $event->delete();

        return response()->json(['status' => 'success', 'msg' => 'event deleted successfully'], 200);
    }


}