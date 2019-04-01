<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Receipts;
use App\Temp;
use App\User;
use App\Http\Requests;

class ReceiptsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number'    => 'required|unique:receipts,number',
            'user_id'   => 'required|integer'
        ]);

        if ($validator->fails()) {
           return $validator->errors();
        } else {
            $receipt = new Receipts;
            $receipt->number = $request->number;
            $receipt->user_id = $request->user_id;
            $receipt->save();
            $receiptId = $receipt->id;
            $logId = Temp::toLog($receiptId, $request->user_id);
            if (!is_int($logId)){
               Receipts::find($receiptId)->delete();
               return ['error'=>'Something goes wrong with LOG Method, please contact support and try again later'];
            } else {
                return $logId;
            }
        }
    }
}
