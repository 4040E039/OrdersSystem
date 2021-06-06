<?php

namespace App\Http\Controllers\Restaurants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;
use App\Models\RestaurantPhoto;
use Illuminate\Support\Facades\Storage;

class RestaurantPhotosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = array(
          "messages" => "",
        );

        $validator = Validator::make($request->all(), [
          'id' => 'required',
          'images' => 'required|image|max:5000',
        ]);
  
        if($validator->fails()) {
          $result['messages'] = $validator->errors();
          return $result;
        }

        $id = $request->input('id');
        $storage_path  = Storage::put('/public/RestaurantPhotos/'.$id, $request->file('images'));
        $file_name = basename($storage_path);
        $restaurant_photo = new RestaurantPhoto;
        $restaurant_photo->restaurant_id = $id;
        $restaurant_photo->photo_name = $file_name;
        $restaurant_photo->save();
        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $restaurant_photos = RestaurantPhoto::where('restaurant_id', $id)->orderBy('created_at', 'desc')->get();

        $result = array();
        $photo_list = array();
        if(count($restaurant_photos) > 0) {
          foreach($restaurant_photos as $photo) {
            $temp = array(
              'id' => $photo['id'],
              'path' => asset('storage/RestaurantPhotos/'.$photo['restaurant_id'].'/'.$photo['photo_name']),
            );
            array_push($result, $temp);
            array_push($photo_list, $photo['photo_name']);
          };
        }


        return Inertia::render('RestaurantList/RestaurantPhotos', [
          'id' => $id,
          'urlList' => $result,
          'photoList' => $photo_list
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $result = array(
          "messages" => "",
        );
        $restaurant_photo = RestaurantPhoto::find($id);
        if($restaurant_photo['id'] && Storage::exists('public/RestaurantPhotos/'.$restaurant_photo['restaurant_id'].'/'.$restaurant_photo['photo_name'])) {
          RestaurantPhoto::destroy($id);
          Storage::delete('public/RestaurantPhotos/'.$restaurant_photo['restaurant_id'].'/'.$restaurant_photo['photo_name']);
        }
        else $result['messages'] = "delete fail";
        
        return $result;
    }
}
