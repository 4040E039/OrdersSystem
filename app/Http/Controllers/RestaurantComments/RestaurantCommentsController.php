<?php

namespace App\Http\Controllers\RestaurantComments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;
use App\Models\RestaurantsComment;
use Illuminate\Support\Facades\Auth;

class RestaurantCommentsController extends Controller
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
        //
        $result = array(
          "messages" => "",
        );

        $validator = Validator::make($request->all(), [
          'score' => 'required|numeric|min: 1|max: 5',
        ]);

        if($validator->fails()) {
          $result['messages'] = $validator->errors();
          return $result;
        }

        $user_id = Auth::id();
        if($request->input('restaurantId')) {
          $restaurant_comment = new RestaurantsComment;
          $restaurant_comment->restaurant_id = $request->input('restaurantId');
          $restaurant_comment->user_id = $user_id;
          $restaurant_comment->score = $request->input('score');
          $restaurant_comment->message = $request->input('message');
          $restaurant_comment->save();
        }

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
        $restaurant_comment = RestaurantsComment::findOrFail($id);
        return $restaurant_comment;
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
        $result = array(
          "messages" => "",
        );

        $validator = Validator::make($request->all(), [
          'score' => 'required|numeric|min: 1|max: 5',
        ]);

        if($validator->fails()) {
          $result['messages'] = $validator->errors();
          return $result;
        }

        $user_id = Auth::id();
        if($request->input('restaurantId')) {
          $restaurant_comment = RestaurantsComment::findOrFail($id);;
          $restaurant_comment->restaurant_id = $request->input('restaurantId');
          $restaurant_comment->user_id = $user_id;
          $restaurant_comment->score = $request->input('score');
          $restaurant_comment->message = $request->input('message');
          $restaurant_comment->save();
        }

        return $result;
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
        $restaurant_comment = RestaurantsComment::find($id);
        if($restaurant_comment) RestaurantsComment::destroy($id);
        else $result['messages'] = "delete fail";

        return $result;
    }
}
