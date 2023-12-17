<?php

namespace App\src\Marketing\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\IncrementChildrenCounterJob;
use App\Models\AllSigned;
use App\Models\Permission;
use App\Models\ProfitsTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserDetail;
use App\src\Marketing\Requests\createUserRequest;
use Carbon\Carbon;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MarketController extends Controller
{
    //create new user
    public function createUser(createUserRequest $request){

        //select the creator
        $creator=User::find(Auth::id());
        $side = 'right_user_id';
        $sideNumbers = 'right_children';
        $direction='right';
        $level=Auth::user()->level+1;
        //deciding if user hasn't added 2 users

        try {
        if ($creator->$side) {

            if ($creator->left_user_id) {
                return response()->json(['status' => 'faild', 'msg' => 'لا يمكنك اضافه المزيد من الوكالات'], 400);
            }
            $side = 'left_user_id';
            $sideNumbers = 'left_children';
            $direction='left';
        }
        if($creator->total_points<880000){
            return response()->json(['status' => 'faild', 'msg' => 'رصيدك غير كافي لشراء المنتج'], 400);
        }
        //begin transaction
        DB::beginTransaction();

        $user=User::create([
        'name' => $request->name,
        'nickname'=>$request->nickname,
        'father_name'=>$request->father_name,
        'mother_name'=>$request->mother_name,
        'email' => $request->email,
        'password'=> Hash::make($request->national_number),
        'bocket_password'=> Hash::make($request->national_number),
        'parent'=>Auth::id(), //parent of this child
        'level'=>$level,
        'product_id'=>$request->product_id
        ]);
        //store user deatils
        $flag=$this->storeUserDetails($request,$user->id);
        if(!$flag){
            //rollback
            DB::rollBack();
            return response()->json(['status'=>$flag,'msg'=>'حدث خطأ في تسجيل هذا المستخدم , الرجاء المحاوله مره اخري'],400);
        }
        $creator->$side=$user->id;
        $creator->$sideNumbers+=1;
        $creator->total_points-=880000;
        $creator->total_work+=1;
        $creator->save();
        //save creator and child in allsigned
        AllSigned::create([
            'parent_id' => $creator->id,
            'child_id' => $user->id,
            'direction' => $direction,
        ]);
        //calculate profits
        $result=$this->incrementChildrenCounter($creator,$user->id);
        //check that the profits has been calculated successfully
        if(!$result){
            DB::rollBack();
            return response()->json(['status'=>$flag,'msg'=>'حدث خطأ في تسجيل هذا المستخدم , الرجاء المحاوله مره اخري'],400);
        }
//        dispatch(new IncrementChildrenCounterJob($creator, $user->id));
        DB::commit();
        return response()->json(['status'=>'success','msg'=>'تمت اضافه هذا المستخدم بنجاح'],200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'msg' => 'حدث خطأ في تسجيل هذا المستخدم, الرجاء المحاوله مره اخرى',"dd"=>$e->getMessage()], 400);
        }

    }


    private function storeUserDetails(Request $request,$userId){

try {
        $currentDate = date('Y-m-d');

        $identity = $request->file('identity_front');
        $identityImageName = $currentDate . '_' . uniqid() . '.' . $identity->getClientOriginalExtension();
        $identity->storeAs('public/identities_front', $identityImageName);
        $identityImagePath = 'identities_front/' . $identityImageName;

        $identity2 = $request->file('identity_back');
        $identityImageName2 = $currentDate . '_' . uniqid() . '.' . $identity2->getClientOriginalExtension();
        $identity2->storeAs('public/identities_back', $identityImageName2);
        $identityImagePath2 = 'identities_back/' . $identityImageName2;

        $healthDoc = $request->file('healthDoc');
        $healthImageName = $currentDate . '_' . uniqid() . '.' . $healthDoc->getClientOriginalExtension();
        $healthDoc->storeAs('public/healthDocs', $identityImageName);
        $healthImagePath = 'healthDocs/' . $healthImageName;

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

            'shipping_country' => $request->shipping_country,
            'shipping_city' => $request->shipping_city,
            'shipping_street' => $request->shipping_street,
            'identity_front' => $identityImagePath,
            'identity_back' => $identityImagePath2,
            'healthDoc'=>$healthImagePath
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

//---------------------------------------------------------------------------------------------------------
    private function incrementChildrenCounter($creator,$newSignedChild)
    {
        while ($creator->parent) {

            $parent = User::find($creator->parent);
            $parent->total_work+=1;
            $direction = $parent->left_user_id === $creator->id ? 'left' : 'right';

            AllSigned::create([
                'parent_id' => $parent->id,
                'child_id' => $newSignedChild,
                'direction' => $direction,
            ]);

            if ($direction === 'left') {
                $parent->left_children += 1;
            } else {
                $parent->right_children += 1;
            }

            $parent->save();
            $creator = $parent;
        }
        return true;
    }
    //---------------------------------------------------------------------------------------------------------

    public function getUsersByWeek($weekNumber=null)
    {
        // If no weekNumber is provided, use the current week number
       // If no weekNumber is provided, use the current week number
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

        $leftUsers = AllSigned::with('user')
            ->where('parent_id', Auth::id())
            ->where('direction', 'left')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $rightUsers = AllSigned::with('user')
            ->where('parent_id', Auth::id())
            ->where('direction', 'right')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $leftCount = $leftUsers->count();
        $rightCount = $rightUsers->count();

        return response()->json([
            'leftCount'=>$leftCount,
            'leftUsers'=>$leftUsers,
            'rightCount'=>$rightCount,
            'rightUsers'=>$rightUsers
        ],200);
    }


    //-----------------------------------------------------------------------------------------------------------------//
    public function updateUser(Request $request, $userId)
    {

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['status' => 'failed', 'msg' => 'هذه الوكاله غير موجوده'], 404);
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            $fillableFields = ['name', 'nickname', 'father_name', 'mother_name', 'email', 'product_id'];
            foreach ($fillableFields as $field) {
                if ($request->has($field)) {
                    $user->$field = $request->input($field);
                }
            }
            $user->save();

            // Update user details
            $flag = $this->updateUserDetails($request, $userId);

            if (!$flag) {
                // Rollback if user details update fails
                DB::rollBack();

                return response()->json(['status' => $flag, 'msg' => 'An error occurred while updating user details. Please try again.'], 400);
            }

            // Commit the transaction if all updates are successful
            DB::commit();
            return response()->json(['status' => 'success', 'msg' => 'User details updated successfully'], 200);
        } catch (\Exception $e) {
            // Rollback in case of any exceptions
            DB::rollBack();
            return response()->json(['status' => 'failed', 'msg' => $e->getMessage()], 500);
        }
    }
    //---------------------------------------------------------------------------------------------
    private function updateUserDetails(Request $request, $userId)
    {
        try {

            // Update each field if it exists in the request
            $updateFields = [
                'gender', 'phone', 'inheritor', 'national_number', 'qid', 'amana',
                'birth_country', 'birth_city', 'birth_street', 'birthday', 'man7_history',
                'address_country', 'address_city', 'address_street', 'shipping_country',
                'shipping_city', 'shipping_street',
            ];
            $hasUpdateFields = array_intersect(array_keys($request->all()), $updateFields);

            if (empty($hasUpdateFields)) {
                return true; // No fields to update, return true
            }

            $currentDate = date('Y-m-d');

            $userDatails = UserDetail::where('user_id', $userId)->first();

            // If user details do not exist, create a new one
            if (!$userDatails) {
                $userDatails = new UserDetail(['user_id' => $userId]);
            }

            foreach ($updateFields as $field) {
                if ($request->has($field)) {
                    $userDatails->$field = $request->input($field);
                }
            }

            // Handle file uploads if they exist in the request
            if ($request->hasFile('identity_front')) {
                $existingFile = $userDatails->identity_front;
                if ($existingFile) {
                    Storage::disk('public')->delete($existingFile);
                }

                $identity = $request->file('identity_front');
                $identityImageName = $currentDate . '_' . uniqid() . '.' . $identity->getClientOriginalExtension();
                $identity->storeAs('public/identities_front', $identityImageName);
                $identityImagePath = 'identities_front/' . $identityImageName;
                $userDatails->identity_front = $identityImagePath;
            }

            if ($request->hasFile('identity_back')) {
                //deleting the existing file
                $existingFile = $userDatails->identity_back;
                if ($existingFile) {
                    Storage::disk('public')->delete($existingFile);
                }

                $identity2 = $request->file('identity_back');
                $identityImageName2 = $currentDate . '_' . uniqid() . '.' . $identity2->getClientOriginalExtension();
                $identity2->storeAs('public/identities_back', $identityImageName2);
                $identityImagePath2 = 'identities_back/' . $identityImageName2;
                $userDatails->identity_back = $identityImagePath2;
            }

            if ($request->hasFile('healthDoc')) {
                $existingFile = $userDatails->healthDoc;
                if ($existingFile) {
                    Storage::disk('public')->delete($existingFile);
                }

                $healthDoc = $request->file('healthDoc');
                $healthImageName = $currentDate . '_' . uniqid() . '.' . $healthDoc->getClientOriginalExtension();
                $healthDoc->storeAs('public/healthDocs', $healthImageName);
                $healthImagePath = 'healthDocs/' . $healthImageName;
                $userDatails->healthDoc = $healthImagePath;
            }

            $userDatails->save();

            return true; // Indicate that the user details were successfully updated
        } catch (\Exception $e) {
            return false; // Handle the case when an exception occurs
        }
    }


}
