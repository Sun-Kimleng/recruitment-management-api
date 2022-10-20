<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class FacebookController extends Controller
{
    public function loginUrl()
    {
        return response()->json([
            'url' => Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl(),
        ]);

    }

    public function loginCallback()
    {
            try {
            $users = new User;
            $user = Socialite::driver('facebook')->stateless()->user();
            $isUser = User::where('fb_id', $user->id)->first();
            $user_id = Helper::IDGenerator($users, 'user_id', 5, 'AGB');
                if($isUser){
                    
                    $token = $isUser->createToken($user->email. "_token", ['candidate'])->accessToken;

                    return response()->json(['token' =>$token, 'information'=>$user, "data"=>$isUser,'status'=>'200']);
                
                }else{
                    $createUser = User::create([
                        'user_id'=> $user_id,
                        'username' => $user->name,
                        'role'=>'dfsfsfsnvs252',
                        'email' => $user->email,
                        'fb_id' => $user->id,
                        'user_status'=>'active',
                        'avatar'=> $user->avatar_original,
                        'password' => encrypt('sdfndfGFk429sDFc8786323')
                    ]);
                    // Auth::login($createUser);
                    $token = $createUser->createToken($user->email. "_token", ['candidate'])->accessToken;
                    return response()->json(['data'=>$user, 'token'=>$token,'information'=>$createUser,'status'=>200]);
                }
        
            } catch (Exception $exception) {
                return response()->json(['error'=>$exception->getMessage(), 'status'=>404]);
            }
    }
}
