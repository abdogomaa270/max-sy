<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AccSettings extends Controller
{
    public function getMe(){
        $user=User::with('details')->find(Auth::id());
        return response()->json(['user'=>$user],200);
    }
    //---------------------------------------------------------------------------------------------------------
    public function setBucketPassword(Request $request){
        //select user
        $user=User::find(Auth::id());
        if(!$user){
            return response()->json(['status'=>'faild','msg'=>'user not found'],404);
        }
        //check if he has bucketPass
        if($user->bocket_password){
            return response()->json(['status'=>'faild','msg'=>'انت بالفعل لديك كلمه سر للخزنه , يمكنك تغييرها'],400);
        }
        //set bocket_password
        $user->bocket_password=Hash::make($request->bocket_password);
        $user->save();
        return response()->json(['status'=>'success','msg'=>'تم حفظ كلمه السر بنجاح'],200);

    }
    //---------------------------------------------------------------------------------------------------------
    public function chnageBucketPassword(Request $request){
        //select user
        $user=User::find(Auth::id());
        if(!$user){
            return response()->json(['status'=>'faild','msg'=>'user not found'],404);
        }
        //check if he has bucketPass
        if(!$user->bocket_password){
            return response()->json(['status'=>'faild','msg'=>'ليس لديك كلمه سر للخزنه ف الاساس'],400);
        }

        $bool=Hash::check($request->current_bocket_password,$user->bocket_password);
        //check current password
        if(!$bool){
            return response()->json(['status'=>'faild','msg'=>'كلمه السر الحاليه الخاصه ب الخزنه خطأ'],400);
        }
        //set bocket_password
        $user->bocket_password=Hash::make($request->new_bocket_password);
        $user->save();
        return response()->json(['status'=>'success','msg'=>'تم تغيير كلمه سر الخزنه بنجاح'],200);

    }
    //---------------------------------------------------------------------------------------------------------
    //@params amount recieverEmail bocketPassword
    public function transferePoints(Request $request){
        //select user
        $user=User::find(Auth::id());
        if(!$user){
            return response()->json(['status'=>'faild','msg'=>'user not found'],404);
        }
        $reciever=User::where('id',$request->reciever_id)->first();
        if(!$reciever){
            return response()->json(['status'=>'faild','msg'=>'لا يوجد شخص ب هذا المعرف'],404);
        }
        //check bocketPassword
        if(! Hash::check($request->bocketPassword,$user->bocket_password) ){
            return response()->json(['status'=>'faild','msg'=>'الرقم السري الخاص بالخزنه غير صحيح'],400);
        }
        if($user->total_points<$request->amount){
            return response()->json(['status'=>'faild','msg'=>'الرصيد غير كافي'],400);
        }
        // start the transaction
        DB::beginTransaction();

        try {
            $user->total_points -= $request->amount;
            $reciever->total_points += $request->amount;
            $user->save();
            $reciever->save();

            // save transaction
            $result = $this->saveTransaction($user->id, $reciever->id, $request->amount);

            if (!$result) {
                DB::rollBack(); // Rollback the transaction if the result is false
                return response()->json(['status' => 'failed', 'msg' => 'فشلت العمليه , حاول مره اهري'], 500);
            }

            DB::commit(); // Commit the transaction if everything is successful

            return response()->json(['status' => 'success', 'msg' => 'تم تحويل المبلغ بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if an exception occurs
            return response()->json(['status' => 'failed', 'msg' => 'فضل التحويل بسبب بعض الاعطال, حاول مره اخري'], 500);
        }
    }
    //---------------------------------------------------------------------------------------------------------
    //@access admin
    //@params amount
    public function chrageMyAcc(Request $request){
        $user=User::find(Auth::id());
        $user->total_points+=$request->amount;
        $user->save();
        return response()->json(['status'=>'success','user'=>$user],200);
    }
    //---------------------------------------------------------------------------------------------------------
    private function saveTransaction($senderId,$recieverId,$amount){
        $transaction=Transaction::create([
           'sender'=>$senderId,
           'reciever'=>$recieverId,
           'amount'=>$amount
        ]);
        if($transaction){
            return true;
        }
        else{
            return false;
        }
    }
    //-------------------------------------------------------------------------------------------------------------
    //get my transaction desc by created_at
    //either reciever or sender
    //@params $option->[sender,reciever]
    public function getMyTransactions($option='sender')
    {
        $transactions = Transaction::where($option, Auth::user()->id)
        ->orderBy('created_at', 'desc')
        ->get();
        if(sizeof($transactions)==0){
            return response()->json(['status'=>'faild','msg'=>'لا يوجد معاملات'],404);
        }
        return response()->json(['status'=>'success','data'=>$transactions],200);
    }
    //----------------------------------------------------------------------------------------------------------
    public function getChildrenAndGrandchildren($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => 'failed', 'msg' => 'User not found'], 404);
        }

        $user->leftChild = $user->leftChild()->with('leftChild', 'rightChild')->first();
        $user->rightChild = $user->rightChild()->with('leftChild', 'rightChild')->first();


        return response()->json(['status' => 'success', 'data' => $user], 200);
    }


}
