<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RaiseOrder;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Carbon\Carbon;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
      $now =  Carbon::now();
      $raise_order = RaiseOrder::where('raise_orders.id', $id)->where('raise_orders.start_time', '<=', $now)->where('raise_orders.deleted_at', NULL)->join('users', 'users.id', '=', 'raise_orders.user_id')->join('restaurants', 'restaurants.id', '=', 'raise_orders.restaurant_id')->select('raise_orders.*', 'users.name', 'restaurants.restaurant_name', 'restaurants.restaurant_telephone')->firstOrFail();

      return Inertia::render('Orders/Orders', [
        'id' => $id,
        'raiseOrder' => $raise_order,
      ]);
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
        $form = $request->input('form');
        
        if($form !== null) {
          $validator = Validator::make($form, [
            'order_item' => 'required',
            'order_quantity' => 'required|numeric|min: 1',
            'order_cost' => 'required|numeric',
            'raise_order_id' => 'required|numeric',
          ]);
    
          if($validator->fails()) {
            $result['messages'] = $validator->errors();
            return $result;
          }

          $now =  Carbon::now();
          RaiseOrder::where('raise_orders.id', $form['raise_order_id'])->where('raise_orders.start_time', '<=', $now)->where('raise_orders.end_time', '>=', $now)->where('raise_orders.deleted_at', NULL)->join('users', 'users.id', '=', 'raise_orders.user_id')->join('restaurants', 'restaurants.id', '=', 'raise_orders.restaurant_id')->firstOrFail();

          $user = Auth::user();
          $order = Order::where('raise_order_id', $form['raise_order_id'])->where('deleted_at', NULL)->where('user_id', $user['id'])->where('order_item', trim($form['order_item']))->where('memo', trim($form['memo']))->first();

          if($order) {
            $order->order_quantity =  $form['order_quantity'] + $order['order_quantity'];
            $order->order_cost = ($form['order_quantity'] * $form['order_cost']) + $order['order_cost'];
          } else {
            $order = new Order;
            $order->raise_order_id = $form['raise_order_id'];
            $order->user_id = $user['id'];
            $order->order_item = trim($form['order_item']);
            $order->order_quantity = $form['order_quantity'];
            $order->order_cost = $form['order_quantity'] * $form['order_cost'];
            $order->memo = trim($form['memo']);
          }
          $order->save();
          return $result;
          
        } else abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $raise_order_id = $request->input('raiseOrderId');
        $search = $request->input('search');

        if($search !== null && $search !== "") {
          $orders = Order::where('raise_order_id', $raise_order_id)->where('name','like' ,'%'.$search.'%')->where('deleted_at', NULL)->join('users', 'users.id', '=', 'orders.user_id')->select('orders.*', 'users.profile_photo_path', 'users.name')->orderBy('updated_at', 'desc')->get();
        } else {
          $orders = Order::where('raise_order_id', $raise_order_id)->where('deleted_at', NULL)->join('users', 'users.id', '=', 'orders.user_id')->select('orders.*', 'users.profile_photo_path', 'users.name')->orderBy('updated_at', 'desc')->get();
        }
        
        $litigant_user = Auth::user();
        $order_sort_array = array();

        if(count($orders) > 0) {
          foreach($orders as $row) {
            if($row['profile_photo_path']) {
              $row['profile_photo_path'] = asset('storage/'.$row['profile_photo_path']);
            } else {
              $row['profile_photo_path'] = "https://ui-avatars.com/api/?name=". $row['name']."&color=7F9CF5&background=EBF4FF";
            }
          }

          $collection = collect($orders);
          $un_litigant_id_filter = $collection->filter(function ($row) use($litigant_user) {
            return $row['user_id'] !== $litigant_user['id'];
          });
          $litigant_id_filter = $collection->filter(function ($row) use($litigant_user) {
            return $row['user_id'] === $litigant_user['id'];
          });

          $litigant_id_filter = $litigant_id_filter->reverse();
          foreach($litigant_id_filter as $row) $un_litigant_id_filter->prepend($row);
          
          $order_sort_array = $un_litigant_id_filter->groupBy('user_id');
          $order_sort_array->toArray();
        }
        return $order_sort_array;
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
        $order = Order::findOrFail($id);
        $order['order_cost'] = ($order['order_cost'] / $order['order_quantity']);
        return $order;
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
        $form = $request->input('form');
        
        $old_order = Order::findOrFail($id);
        $user = Auth::user();
        
        if($form !== null && $user['id'] === $old_order['user_id']) {
          $validator = Validator::make($form, [
            'order_item' => 'required',
            'order_quantity' => 'required|numeric|min: 1',
            'order_cost' => 'required|numeric',
            'raise_order_id' => 'required|numeric',
          ]);
    
          if($validator->fails()) {
            $result['messages'] = $validator->errors();
            return $result;
          }
          
          $now =  Carbon::now();
          RaiseOrder::where('raise_orders.id', $form['raise_order_id'])->where('raise_orders.start_time', '<=', $now)->where('raise_orders.end_time', '>=', $now)->where('raise_orders.deleted_at', NULL)->join('users', 'users.id', '=', 'raise_orders.user_id')->join('restaurants', 'restaurants.id', '=', 'raise_orders.restaurant_id')->firstOrFail();
          
          $order = Order::where('raise_order_id', $form['raise_order_id'])->where('deleted_at', NULL)->where('user_id', $user['id'])->where('order_item', trim($form['order_item']))->where('memo', trim($form['memo']))->first();

          if($order && $old_order['id'] !== $order['id']) {
            $order->order_quantity =  $form['order_quantity'] + $order['order_quantity'];
            $order->order_cost = ($form['order_quantity'] * $form['order_cost']) + $order['order_cost'];
            Order::destroy($id);
            $order->save();
          } else {
            $old_order->order_item = trim($form['order_item']);
            $old_order->order_quantity = $form['order_quantity'];
            $old_order->order_cost = $form['order_quantity'] * $form['order_cost'];
            $old_order->memo = trim($form['memo']);
            $old_order->save();
          }
          return $result;
          
        } else abort(404);
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
        $user = Auth::user();
        $result = array(
          "messages" => "",
        );
       
        $order = Order::where('id', $id)->where('user_id', $user['id'])->where('deleted_at', NULL)->first();
        if($order) Order::destroy($id);
        else $result['messages'] = "delete fail";

        return $result;
    }
    
    public function sum($id)
    {

      $orders = Order::where('raise_order_id', $id)->where('deleted_at', NULL)->select('order_item', 'memo', DB::raw('sum(order_cost) as order_cost_sum'), DB::raw('sum(order_quantity) as order_quantity_sum'))->groupBy('order_item', 'memo')->get();
      
      $restaurant = RaiseOrder::where('raise_orders.id', $id)->where('raise_orders.deleted_at', NULL)->join('restaurants', 'restaurants.id', '=', 'raise_orders.restaurant_id')->select('restaurants.restaurant_name', 'restaurants.restaurant_telephone', 'restaurants.restaurant_address')->firstOrFail();

      $result = array(
        'orders' => $orders,
        'restaurant' => $restaurant
      );

      return $result;
    }

}
