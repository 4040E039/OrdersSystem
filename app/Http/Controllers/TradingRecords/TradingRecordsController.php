<?php

namespace App\Http\Controllers\TradingRecords;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingRecord;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TradingRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = Auth::user();
        $search = $request->input('search');

        if($search !== null && $search !== "") {
          $trading_record = TradingRecord::where("trading_item", 'like' ,'%'.$search.'%')->where("deleted_at", NULL)->where('user_id', $user['id'])->orderBy('created_at', 'desc')->get();

        } else {
          $trading_record = TradingRecord::where("deleted_at", NULL)->where('user_id', $user['id'])->orderBy('created_at', 'desc')->get();
        }

        $result = array();
        if(count($trading_record) > 0) {
          foreach($trading_record as $row) {
            $temp = array(
              'id' => $row['id'],
              'trading_item' => $row['trading_item'],
              'trading_cost' => '$ '.$row['trading_cost'],
            );
            array_push($result, $temp);
          }
        }

        return $result;
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
        $user = Auth::user();
        $trading_record = TradingRecord::where('id', $id)->where('user_id', $user['id'])->firstOrFail();
        $result = array(
          'Trading Item' => $trading_record['trading_item'],  
          'Trading Cost' => '$ '.$trading_record['trading_cost'],
          'Memo' => $trading_record['memo'],
          'Created Time' => date('Y-m-d H:i:s', strtotime($trading_record['created_at'])),
        );

        return Inertia::render('TradingRecord/TradingRecordDetail', [
          'tradingRecordDetail' => $result,
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
        $user = Auth::user();
        $trading_record = TradingRecord::where('id', $id)->where('user_id', $user['id'])->firstOrFail();

        return Inertia::render('TradingRecord/EditTradingRecordForm', [
          'trading_record' => $trading_record,
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
        //
        $this->validate($request, [
          'trading_item' => 'required',
          'trading_cost' => 'required',
        ]);
        $user = Auth::user();
        $trading_record = TradingRecord::where('id', $id)->where('user_id', $user['id'])->firstOrFail();
        $trading_record->trading_item = $request->input('trading_item');
        $trading_record->trading_cost = $request->input('trading_cost');
        $trading_record->memo = $request->input('memo');
        $trading_record->save();
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
        $user = Auth::user();
        $result = array(
          "messages" => "",
        );
        $trading_record = TradingRecord::where('id', $id)->where('user_id', $user['id'])->first();
        if($trading_record) TradingRecord::destroy($id);
        else $result['messages'] = "delete fail";

        return $result;
    }
    
    public function chat() {
      return Inertia::render('TradingRecord/TradingRecordChat');
    }

    public function monthly_cost()
    {
        //
        $user = Auth::user();

        $result = array(
          "datasets" => array(
            array(
              "label" => 'Monthly Cost',
              "data" => array(),
            )
          ),
          "labels" => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
        );
        // 大於今年
        $trading_record = TradingRecord::where('user_id', $user['id'])->where('created_at', '>=', date('Y-01-01'))->where('deleted_at', NULL)->get();
        foreach($trading_record as $row){
          $row['cost_date'] = date('m', strtotime($row['created_at']));
        }
        $collection = collect($trading_record);
        $collection = $collection->sortBy('created_at');
        $collection = $collection->groupBy('cost_date');
        $collection->toArray();

        for($i = 1; $i <= 12; $i++) {
          array_push($result["datasets"][0]["data"], 0);
        }
        foreach($collection as $key => $row) {
          $temp_cost = 0;
          foreach($row as $val) {
            $temp_cost += $val['trading_cost'];
          }
          $result["datasets"][0]["data"][(int)$key -1] = $temp_cost;
        }
        return $result;
    }
}
