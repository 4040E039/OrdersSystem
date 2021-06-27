<?php

namespace App\Http\Controllers\RaiseOrders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RaiseOrder;
use App\Models\Order;
use App\Models\TradingRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RaiseOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // get Restaurant list
        $raise_orders = array();
        $now =  Carbon::now();
        $status = $request->input('status');
        $search = $request->input('search');
        $search_date_range = $request->input('searchDateRange');

        $raise_orders = RaiseOrder::where(function($query) use($now, $status, $search, $search_date_range){
          $query->where('deleted_at', NULL);
          if($status === 1)  $query->where('start_time', '<=', $now)->where('end_time', '>=', $now);
          elseif($status === 2)  $query->where('end_time', '<' ,$now);
          elseif($status === 3)  $query->where('start_time', '>', $now);
          if($search !== null && $search !== "") $query->where('raise_order_theme', 'like' , "%".$search."%");
          if($search_date_range !== null) {
            $start_range = Carbon::parse($search_date_range['start']);
            $end_range = Carbon::parse($search_date_range['end'].' 23:59:59');
            if($search_date_range['start'] === $search_date_range['end']) $query->where('start_time', '>=',  $start_range);
            else $query->where('start_time', '>=',  $start_range)->where('end_time', '<=', $end_range);
          }
        })->join('users', 'users.id', '=', 'raise_orders.user_id')->join('restaurants', 'restaurants.id', '=', 'raise_orders.restaurant_id')->select('raise_orders.*', 'users.profile_photo_path','users.name')->orderBy('start_time', 'desc')->get();

        foreach($raise_orders as $row) {
          $row['status'] = $status;
        }
        return $raise_orders;
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

        $this->validate($request, [
          'start_time' => 'required|date',
          'open_duration' => 'required|numeric|min: 5|max: 120',
          'raise_order_theme' => 'required|max: 10',
          'restaurant_selected' => 'required',
        ]);
        
        $form = $request->all();
        $open_duration = '+'.$form['open_duration'].' minutes';
        $user_id = Auth::id();
        $raise_order = new RaiseOrder;
        $raise_order->user_id = $user_id;
        $raise_order->restaurant_id = $form['restaurant_selected']['id'];
        $raise_order->raise_order_token = time();
        $raise_order->raise_order_theme = trim($form['raise_order_theme']);
        $raise_order->start_time = date("Y-m-d H:i:s", strtotime($form['start_time']));
        $raise_order->end_time = date("Y-m-d H:i:s", strtotime($open_duration, strtotime($form['start_time'])));
        $raise_order->memo = $form['memo'];
        $raise_order->save();
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
        $RaiseOrder = RaiseOrder::findOrFail($id);
        return $RaiseOrder;
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
          'open_duration' => 'required|numeric|min: 5|max: 120',
          'raise_order_theme' => 'required|max: 10',
        ]);
        
        $form = $request->all();
        $open_duration = '+'.$form['open_duration'].' minutes';
        $raise_order = RaiseOrder::findOrFail($id);
        $raise_order->raise_order_theme = trim($form['raise_order_theme']);
        $raise_order->end_time = date("Y-m-d H:i:s", strtotime($open_duration, strtotime($raise_order['start_time'])));
        $raise_order->memo = $form['memo'];
        $raise_order->save();
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
        $user_id = Auth::id();

        $result = array(
          "messages" => "",
        );
        $raise_order = RaiseOrder::where('id', $id)->where('user_id', $user_id)->where('deleted_at', NULL)->first();

        if($raise_order) RaiseOrder::destroy($id);
        else $result['messages'] = "delete fail";

        return $result;
    }

    public function completed($id)
    {
        //
        $result = array(
          "messages" => "",
        );
        $raise_order = RaiseOrder::find($id);
        if($raise_order) {
          $raise_order->is_found = 1;
          $raise_order->save();

          $orders = Order::where('raise_order_id', $id)->get();
          $collection = collect($orders);
          $order_sort_array = $collection->groupBy('user_id');
          $order_sort_array->toArray();
            // $trading_records = new TradingRecord;
          $sort_array = array();
          foreach($order_sort_array as $key => $row) {
            $temp = array(
              'theme' => $raise_order['raise_order_theme'],
              'user_id' => $key,
              'cost' => 0,
              'memo' => $raise_order['created_at'],
            );
            foreach($row as $val) {
              $temp['cost'] += $val['order_cost'];
            }
            array_push($sort_array, $temp);
          }
          foreach($sort_array as $row) {
            $trading_record = new TradingRecord;
            $trading_record->user_id = $row['user_id'];
            $trading_record->trading_item = $row['theme'];
            $trading_record->trading_cost = $row['cost'];
            $trading_record->memo = 'Cost Time : '.$row['memo'];
            $trading_record->save();
          }
        }
        else $result['messages'] = "delete fail";
        return $result;
    }
}
