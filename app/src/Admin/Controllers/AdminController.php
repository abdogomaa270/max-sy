<?php

namespace App\src\Admin\Controllers;

use App\Models\BlackList;
use App\Models\Permission;
use App\Models\ProfitsTransaction;
use App\Models\User;
use App\Models\UserDetail;
use App\src\Admin\Requests\ChangeUserPassReq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController
{
    //@params  null
    //@desc get all users in the system
    //@access admin
    public function getAllUsers($flag = false)
    {
        $users = ($flag)
            ? User::where('total_points', '>', 0)->get()
            : User::get();

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }
    //@params  null
    //@desc get specific user with his all details
    //@access admin
    public function getUser($userId){
        $user=User::with('details')->with('product.images')
            ->find($userId);
        if(!$user){
            return response()->json(['status'=>'faild','data'=>'هذا المستخدم غير موجود'],404);
        }
        return response()->json(['status'=>'success','data'=>$user],200);
    }
    //@params  null
    //@desc get all users in the system
    //@access admin
    public function searchUser($userName) {
        $users = User::where('name', 'like', '%' . $userName . '%')->get();
        return response()->json(['status' => 'success', 'data' => $users], 200);
    }
    //@params :  userId as queryparam
    //@desc : prevent user from login untill boss allow him
    //@access : admin
    public function punishUser($userId){
        $user=BlackList::create([
            'user_id'=>$userId
        ]);
        if(!$user){
            return response()->json(['status'=>'faild','msg'=>'حدث خطا اثناء معاقبه المستخدمين'],404);
        }
        return response()->json([
            'status'=>'success',
            'msg'=>'تم حرمان هذا المستخدم من الدخول الي الموقع بنجاح'],
            200);
    }
    //------------------------------------------------------------------------------------------------------------//
    public function unPunishUser($userId){
        $user=BlackList::where('user_id',$userId)->first();
        if(!$user){
            return response()->json(['status'=>'faild','msg'=>'هذا المستخدم غير موجود ف قائمه الممنوعين من الدخول'],404);
        }
        $user->delete();
        return response()->json([
            'status'=>'success',
            'msg'=>'تم السماح لهذا المستخدم الدخول الي الموقع بنجاح'],
            200);
    }
    //------------------------------------------------------------------------------------------------------------//

    //@params : null
    //@desc : get all punished users
    //@access : admin
    public function getPunishedUsers(){
        $users=User::where('isPunished',true)->get();
        if(sizeof($users)){
            return response()->json(['status'=>'faild','msg'=>'لا يوجد مستخدمين معاقبين'],404);
        }
        return response()->json(['status'=>'success','data'=>$users],200);
    }

    //------------------------------------------------------------------------------------------------------------//

    public function openSite()
    {
        $permission = Permission::first();
        $permission->isSiteOpened = true;
        $permission->save();

        return response()->json(['status' => 'success', 'msg' => 'تم فتح الموقع بنجاح'], 200);
    }
    //------------------------------------------------------------------------------------------------------------//

    public function closeSite()
    {
        $permission = Permission::first();
        $permission->isSiteOpened = false;
        $permission->save();

        return response()->json(['status' => 'success', 'msg' => 'تم غلق الموقغ بنجاح'], 200);
    }
    //------------------------------------------------------------------------------------------------------------//

    public function checkSiteAvailability()
    {
        $permission = Permission::first();
        $permission->save();
        return response()->json(['status' => $permission->isSiteOpened], 200);
    }
    /*---------------------------------------------------------------------------------*/
    public function changeUserPassword(ChangeUserPassReq $request,$userId)
    {
        $new_password = $request->new_password;
        $user = User::find($userId);
        if($user === null){
            return response()->json(['status'=>'faild','msg'=>'هذا المستخدم غير موجود'],404);
        }
        $user->password = Hash::make($new_password);
        $user->save();
        return response()->json(['status'=>'success','msg'=>'تم تعديل كلمه السر بنجاح'],200);

    }
    /*---------------------------------------------------------------------------------*/
    public function changeUserBocketPassword(ChangeUserPassReq $request,$userId)
    {
        $new_password = $request->new_password;
        $user = User::find($userId);
        if($user === null){
            return response()->json(['status'=>'faild','msg'=>'هذا المستخدم غير موجود'],404);
        }
        $user->bocket_password = Hash::make($new_password);
        $user->save();
        return response()->json(['status'=>'success','msg'=>'تم تعديل كلمه سر الخزنه بنجاح'],200);

    }
    //----------------------------------------------------------------------------------
    public static function getUserName($userId)
    {
        $user = User::find($userId);
        if ($user) {
            return response()->json(['name'=>$user->name]);

        } else {
            return 'User not found';
        }
    }
    //----------------------------------------------------------------------------------
    public function calculateTotalProfitsForAllUsers()
    {
        $users = User::all();

        foreach ($users as $user) {
            DB::transaction(function () use ($user) {

                //children that will be awarded for
                $precalculated=$user->calculated_children;

                $leftChildren = $user->left_children-$precalculated;
                $rightChildren= $user->right_children-$precalculated;

                $profitableChildren = min($leftChildren, $rightChildren);

                // If there are profits to withdraw
                if ($profitableChildren > 0) {
                    $user->calculated_children+=$profitableChildren;

                    if($profitableChildren>32){
                        //charge admin account and override profitableChildren
                        $this->chargeAdminAcc($profitableChildren-32);
                        $profitableChildren=32;
                    }
                    $user->total_points += $profitableChildren * 2 * 40000;
                    $user->save();

                    $this->handleProfitsTransaction($user, $profitableChildren * 2 * 40000);
                }
            });
        }

        return response()->json(['status' => 'success', 'msg' => 'تم حساب الأرباح بنجاح لجميع المستخدمين'], 200);
    }
//-------------------------------------------------------------------------------------------------------------------------//
    public function chargeAdminAcc($children)
    {
        $admin = User::where('id','WO100001')->first();
        $admin->total_points+=$children*2*40000;
        $admin->save();
        $this->handleProfitsTransaction($admin, $children * 2 * 40000);
        return true;
    }
//--------------------------------------------------------------------------------------------------------------------------//
    public function handleProfitsTransaction($user, $amount)
    {
        $profitsTransaction = new ProfitsTransaction();
        $profitsTransaction->user_id = $user->id;
        $profitsTransaction->amount = $amount;
        $profitsTransaction->save();
        return true;
    }
//--------------------------------------------------------------------------------------------------------------------------//

}
