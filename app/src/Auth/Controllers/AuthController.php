<?php

namespace App\src\Auth\Controllers;
use App\Http\Controllers\Controller;
use App\Models\BlackList;
use App\Models\User;
use App\src\Auth\Requests\ChangePassReq;
use App\src\Auth\Requests\LoginRequest;
use App\src\Auth\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['id', 'password']);

        try {
            // Update the User model based on the field you want to use for authentication
            $user = User::where('id', $credentials['id'])->first();


            // Check if the user exists and the provided password is correct
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['error' => 'البيانات غير صحيحه'], 401);
            }

            //check he isn't punished
            $isUserBlocked=BlackList::where('user_id',$user->id)->first();
            if($isUserBlocked){
                return response()->json(['msg' => 'لقد تم وقف حسابك من الاداره'], 401);
            }

            // Generate the token for the user
            $token = Auth::login($user);
        }
        catch (JWTException $e) {
            return response()->json(['error' => 'حدث خطا ما , حاول مره اخري'], 500);
        }



        return response()->json(
            [
                'status'=>'Success',
                'user'=> $user,
                'token'=>$token
            ],200);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /*--------------------------------------------------------------------------------*/
    public function register(RegisterRequest $request)
    {
        //validation


        $user =new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->level=0;
        $user->save();


        return response()->json(['status' => 'Success','user'=>$user], 200);
    }
    /*--------------------------------------------------------------------------------*/
    public function createEmployee(RegisterRequest $request)
    {
        //validation
        $employee =new User();
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->password = Hash::make($request->password);
        $employee->role=2;
        $employee->level=0;
        $employee->save();

        return response()->json(['status' => 'Success','employee'=>$employee], 200);
    }
    /*-------------------------------------------------------------------------------*/
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
    /*---------------------------------------------------------------------------------*/
    public function changePassword(ChangePassReq $request)
    {
        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $user = User::find(Auth::id());
        if($user === null){
            return response()->json(['status'=>'faild','msg'=>'هذا المستخدم غير موجود'],404);
        }
        if (Hash::check($old_password, $user->password)){
            $user->password = Hash::make($new_password);
            $user->save();
            return response()->json(['status'=>'success','msg'=>'تم تعديل كلمه السر بنجاح'],200);
        }
        return response()->json(['status'=>'faild','msg'=>"كلمه السر القديمه خطأ"],400);

    }

    /*------------------------------------------------------------------------------------*/
    public function deleteAccount(){
        // user should be authenticated
        $userId=Auth::id();
        $user=User::find($userId);
        $user->delete();
        return response()->json(['status'=>'Deleted succesffully'],200);

    }


}


