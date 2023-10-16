<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MarketController extends Controller
{
    //create new user
    public function createUser(Request $request){

        //select the creator
        $creator=User::find(Auth::id());
        $side=$request->side.'_user_id';
        $sidePoints=$request->side.'_points';


        if($creator->$side){
            return response()->json(['status'=>'faild','msg'=>'لقد قمت باضافه شخص بالفعل ف هذا الجانب'],400);
        }
        //begin transaction
        DB::beginTransaction();

        $user=User::create([
        'name' => $request->name,
        'nickname'=>$request->nickname,
        'father_name'=>$request->father_name,
        'mother_name'=>$request->mother_name,
        'email' => $request->email,
        'password'=> Hash::make($request->password),
        'parent'=>Auth::id() //parent of this child
        ]);
        //store user deatils
        $flag=$this->storeUserDetails($request,$user->id);
        if(!$flag){
            //rollback
            DB::rollBack();
            return response()->json(['status'=>$flag,'msg'=>'حدث خطأ في تسجيل هذا المستخدم , الرجاء المحاوله مره اخري1'],400);
        }
        $creator->$side=$user->id;
        $creator->$sidePoints+=40;
        $creator->save();

        //calculate profits
        $result=$this->calculateProfits($creator);
        DB::commit();
        return response()->json(['status'=>'success','msg'=>'تمت اضافه هذا المستخدم بنجاح','result'=>$result],200);

    }


    private function storeUserDetails(Request $request,$userId){

        //reformat birthday  d
        //reformat mn7a  d
        //store el btaka
try {
        $image = $request->file('identity');
        $currentDate = date('Y-m-d');
        $imageName = $currentDate . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/identities', $imageName);

        // Optionally, you can also store the image path in your database
        $imagePath = 'identities/' . $imageName;

        $userDatails = UserDetail::create([
            'user_id' => $userId,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'inheritor' => $request->inheritor,
            'national_number' => $request->national_number,
            'qid' => $request->qid,
            'amana' => $request->amana,
            'birth_country' => $request->birth_country,
            'birth_city' => $request->birth_city,
            'birth_street' => $request->birth_street,
            'birthday' => $request->birthday,
            'man7_history' => $request->man7_history,
            'address_country' => $request->address_country,
            'address_city' => $request->address_city,
            'address_street' => $request->address_street,
            'identity' => $imagePath,
        ]);


        if ($userDatails) {
            return true;
        } else {
            return false;
        }
    }
    catch (\Exception $e)
    {
     return $e;
    }


}










    private function calculateProfits($creator){
        //creator is a child for another persons
        for($i=1;$i<=5;$i++){
            if(!$creator->parent){
                return "this user number ".$i."does't has a parent";
            }
            $parent=User::find($creator->parent);
            if($parent->left_user_id===$creator->id){
                $parent->left_points+=40;
                $parent->save();
                $creator=$parent;
            }
            else if($parent->right_user_id===$creator->id){
                $parent->right_points+=40;
                $parent->save();
                $creator=$parent;
            }
        }
        return "true";

    }
    private function calculateTotalProfits($user){
        //creator is a child for another persons
        if(!$user->right_points>0 && !$user->left_points>0){
            return false;
        }
        if($user->right_points > $user->left_points){
            $user->total_points=$user->left_points*2;
            $user->right_points-=$user->left_points;
            $user->left_points-=$user->left_points;
            $user->save();
        }
        else if($user->left_points > $user->right_points){
            $user->total_points=$user->right_points*2;
            $user->left_points-=$user->right_points;
            $user->right_points-=$user->right_points;
            $user->save();
        }
        return true;

    }

}
