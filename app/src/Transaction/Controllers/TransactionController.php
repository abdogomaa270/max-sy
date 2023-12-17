<?php

namespace App\src\Transaction\Controllers;

use App\Models\ProfitsTransaction;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionController
{
    public function getAllTransactions($direction,$weekNumber=null){
        //validation
      
        if($direction && ($direction != "h" && $direction != "v"  && $direction != "a" )){
            return response()->json(['status'=>'faild',"msg"=>"invalid direction"],200);
        }
     
        if (is_null($weekNumber)) {
            
             $now = Carbon::now();
             $startDate = $now->startOfWeek(Carbon::SATURDAY)->startOfDay();
             $endDate = $now->endOfWeek(Carbon::FRIDAY)->endOfDay();
             $weekNumber = $now->week;
         }
            $year = Carbon::now()->year;
             // Set start date to the beginning of Saturday
            $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek(Carbon::SATURDAY)->startOfDay();	
             // Set end date to the end of Friday
            $endDate = Carbon::now()->setISODate($year, $weekNumber)->endOfWeek(Carbon::FRIDAY)->endOfDay();

        //first scenario    
        if($direction=="h"){
            $transactions=Transaction::where('direction','horizontal')
            ->whereBetween('created_at', [$startDate,$endDate])->get();

            return response()->json(
             [
                'status'=>'success',
                'data'=>$transactions,
                'weekNumber'=>$weekNumber,
                'direction'=>"رأسي"
             ],200);
        }
        //second scenario
        else if($direction=="v"){
            $transactions=Transaction::where('direction','vertical')
            ->whereBetween('created_at', [$startDate, $endDate])->get();

            return response()->json(
            [
                'status'=>'success',
                'data'=>$transactions,
                'weekNumber'=>$weekNumber,
                'direction'=>"افقي"
            ],200);
        }
       //third scenario
       else{
           $transactions=Transaction::whereBetween('created_at', [$startDate, $endDate])->get();
           return response()->json(
            [
                'status'=>'success',
                'data'=>$transactions,
                'weekNumber'=>$weekNumber,
                'direction'=>"الجميع"
            ],200);
        }
}
//--------------------------------------------------------------------------------------------------------------------
    public function getAllProfitTransactions($weekNumber,$userId=''){
     
        if ($weekNumber==0) {
            
             $now = Carbon::now();
             $startDate = $now->startOfWeek(Carbon::SATURDAY)->startOfDay();
             $endDate = $now->endOfWeek(Carbon::FRIDAY)->endOfDay();
             $weekNumber = $now->week;
         }
            $year = Carbon::now()->year;
             // Set start date to the beginning of Saturday
            $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek(Carbon::SATURDAY)->startOfDay();	
             // Set end date to the end of Friday
            $endDate = Carbon::now()->setISODate($year, $weekNumber)->endOfWeek(Carbon::FRIDAY)->endOfDay();

        if($userId){
            $profitTransactions = ProfitsTransaction::where('user_id',$userId)->whereBetween('created_at', [$startDate,$endDate])->get();
            return response()->json(
                [
                    'status'=>'success',
                    'data'=>$profitTransactions,
                    'weekNumber'=>$weekNumber
                ],200);
        }
        else{
            $profitTransactions=ProfitsTransaction::whereBetween('created_at', [$startDate,$endDate])->get();;
            return response()->json(
            [
                'status'=>'success',
                'data'=>$profitTransactions,
                'weekNumber'=>$weekNumber
            ],200);
        }
    }
}
