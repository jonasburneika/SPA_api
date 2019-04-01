<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Temp extends Model
{
    /**
     * Save data for log and traceability
     */
    static function toLog($receiptId, $userId, $status = 0){
        $temp = new Temp;
        $temp->user_id = $userId;
        $temp->receipt_id = $receiptId;
        $temp->status = $status;
        $temp->save();
        return $temp->id;
    }
}
