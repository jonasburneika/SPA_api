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
            $userId = $request->user_id;
            $user = User::find($userId);
            if (!$user){
                return ['error'=>'User with given ID doesn\'t exist!']; 
            }
            $receipt = new Receipts;
            $receipt->number = $request->number;
            $receipt->user_id = $userId;
            $receipt->save();
            $receiptId = $receipt->id;
            $logId = Temp::toLog($receiptId, $userId, 0);
            if (!is_int($logId)){
               Receipts::find($receiptId)->delete();
               return ['error'=>'Something goes wrong with LOG Method, please contact support and try again later!'];
            } else {
                $points = $this->getPointsAmount($userId);
                if ($user){
                    $user->points += $points;
                    $user->update();
                    $logId = Temp::toLog($receiptId, $userId, 1);
                    return [
                        'userId'=>$user->id,
                        'points'=>$user->points,
                    ];
                }
            }
        }
    }

    public function getPointsAmount($userId)
    {
        $receipts = Receipts::where('user_id',$userId)->where('created_at', '>=', date('Y-m-d').' 00:00:00')->count();
        if ($receipts == 3) {
            return 30;
        } else {
            return 5;
        }
    }
}
