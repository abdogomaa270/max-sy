<?php

namespace App\src\Transaction\Controllers;

use App\Models\ProfitsTransaction;
use App\Models\Transaction;

class TransactionController
{
    public function getAllTransactions($direction=''){
        //validation
        if($direction && ($direction != "h" && $direction != "v")){
            return response()->json(['status'=>'faild',"msg"=>"invalid direction"],200);
        }

        if($direction=="h"){
            $transactions=Transaction::where('direction','horizontal')->get();
            return response()->json(['status'=>'success','data'=>$transactions,'direction'=>"افقي"],200);
        }
        else if($direction=="v"){
            $transactions=Transaction::where('direction','vertical')->get();
            return response()->json(['status'=>'success','data'=>$transactions,'direction'=>"رأسي"],200);
        }
        else{
            $transactions=Transaction::all();
            return response()->json(['status'=>'success','data'=>$transactions,'direction'=>"الجميع"],200);
        }
    }
    public function getAllProfitTransactions($userId=''){

        if($userId){
            $profitTransactions = ProfitsTransaction::where('user_id',$userId)->get();
            return response()->json(['status'=>'success','data'=>$profitTransactions],200);
        }
        else{
            $profitTransactions=ProfitsTransaction::all();
            return response()->json(['status'=>'success','data'=>$profitTransactions,'direction'=>"الجميع"],200);
        }
    }
}
