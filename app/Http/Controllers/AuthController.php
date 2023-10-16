<?php

namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


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
            if($user->ispunished){
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
        $user->save();


        return response()->json(['status' => 'Success','user'=>$user], 200);
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
    public function changePassword(ChangePasswordRequest $request){


        $email = $request->email;
        $key = $request->key;
        $new_password = $request->new_password;
        $user = User::where('email', $email)->first();
        if($user === null){
            return response()->json(['status'=>'not exist'],400);
        }
        if (Hash::check($key, $user->key)){
            $user->password = Hash::make($new_password);
            $user->save();
            return response()->json(['status'=>'Password changed successfully Success'],200);
        }
        return response()->json(['status'=>'key or email is wrong'],400);

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


