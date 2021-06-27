<?php

namespace App\Http\Controllers\Restaurants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Models\RestaurantsComment;
use Illuminate\Support\Facades\Auth;

class RestaurantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // get Restaurant list
        $restaurant = array();
        if($request->input('search') !== null && $request->input('search') !== "") {
          $restaurant = Restaurant::where('restaurant_name', 'like' , "%".$request->input('search')."%")->select('restaurant_name', 'restaurant_telephone', 'restaurant_address')->orderBy('id', 'desc')->get();
        } else {
          $restaurant = Restaurant::select('id', 'restaurant_name', 'restaurant_telephone', 'restaurant_address')->orderBy('id', 'desc')->get();
        }
        return $restaurant;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //

      $this->validate($request, [
        'name' => 'required',
      ]);

      $restaurant = new Restaurant;
      $restaurant->restaurant_name = $request->input('name');
      $restaurant->restaurant_telephone = $request->input('telephone');
      $restaurant->restaurant_address = $request->input('address');
      $restaurant->memo = $request->input('memo');
      $restaurant->save();
      return back();
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
        $restaurant = Restaurant::findOrFail($id);
        $result = array(
          'Name' => $restaurant['restaurant_name'],  
          'Telephone' => $restaurant['restaurant_telephone'],
          'Address' => $restaurant['restaurant_address'],
          'Memo' => $restaurant['memo'],
          'Created Time' => date('Y-m-d H:i:s', strtotime($restaurant['created_at'])),
        );

        $restaurants_comment = RestaurantsComment::where('restaurant_id', $id)->join('users', 'users.id', '=', 'restaurants_comments.user_id')->select('restaurants_comments.*', 'users.profile_photo_path' ,'users.name')->orderBy('updated_at', 'desc')->get();
        $user_id = Auth::id();

        $is_release = false;
        if(count($restaurants_comment) > 0) {
          foreach($restaurants_comment as $row) {
            if($row['profile_photo_path']) {
              $row['profile_photo_path'] = asset('storage/'.$row['profile_photo_path']);
            } else {
              $row['profile_photo_path'] = "https://ui-avatars.com/api/?name=". $row['name']."&color=7F9CF5&background=EBF4FF";
            }
            if($row['user_id'] === $user_id) $is_release = true;
          }
        }

        $collection = collect($restaurants_comment);

        $un_user_id_filter = $collection->filter(function ($row) use($user_id) {
          return $row['user_id'] !== $user_id;
        });
        $user_id_filter = $collection->filter(function ($row) use($user_id) {
          return $row['user_id'] === $user_id;
        });

        foreach($user_id_filter as $row) $un_user_id_filter->prepend($row);
        
        $sort_array  = $un_user_id_filter->toArray();

        return Inertia::render('RestaurantList/RestaurantDetail', [
          'restaurantDetail' => $result,
          'restaurantComments' => $sort_array,
          'isRelease' => $is_release,
          'id' => $id
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
        $restaurant = Restaurant::findOrFail($id);
        
        $result = array(
          'name' => $restaurant['restaurant_name'],  
          'telephone' => $restaurant['restaurant_telephone'],
          'address' => $restaurant['restaurant_address'],
          'memo' => $restaurant['memo'],
        );

        return Inertia::render('RestaurantList/EditRestaurantForm', [
          'restaurant' => $result,
          'id' => $id
        ]);
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

        $this->validate($request, [
          'name' => 'required',
        ]);

        $restaurant = Restaurant::findOrFail($id);
        $restaurant->restaurant_name = $request->input('name');
        $restaurant->restaurant_telephone = $request->input('telephone');
        $restaurant->restaurant_address = $request->input('address');
        $restaurant->memo = $request->input('memo');
        $restaurant->save();
        return back();
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
        $restaurant = Restaurant::find($id);
        if($restaurant) Restaurant::destroy($id);
        else $result['messages'] = "delete fail";

        Storage::deleteDirectory('public/RestaurantPhotos/'.$id);
        return $result;
    }
}
