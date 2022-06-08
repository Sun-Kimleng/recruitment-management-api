<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Helpers\Helper;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class UserController extends Controller
{
    public function create(Request $request){
        $users = new User;
        $user_id = Helper::IDGenerator($users, 'user_id', 5, 'AGB');

        $validator = Validator::make($request->all(),[
            'username'=>'required|max: 12',
            'email'=>'required|unique:users',
            'password'=>'required',
            'confirmPassword'=>'required|same:password'
        ],
    );
        
        if($validator->fails()){
            
        return response()->json(['error'=>$validator->errors()]);
        
        }else{

            $user = User::create([
                'user_id'=>$user_id,
                'username'=>$request->input('username'),
                'email'=>$request->input('email'),
                'password'=> Hash::make($request->input('password')),
            ]);
           
            $token = $user->createToken($user->email. "_token")->plainTextToken;
            

            return response()->json([
                'status'=>200,
                'message'=>'registered successful',
                'token'=>$token,
            ]);
            
        }
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            'email'=> 'required',
            'password'=> 'required'
        ]);
        
        if($validator->fails()){
            return response()->json(['error'=> $validator->errors(),]);
        }else{
            $user = User::where('email', $request->email)->first();

            if(! $user || ! Hash::check($request->password, $user->password, )){

                return response()->json(['status'=>404, 'message'=>'Invalid Credentials']);
            }else{
                $token =$user->createToken($request->email.'_token')->plainTextToken;
                
                return response()->json(['status'=>200 ,'message'=>'You\'re logged in', 'token'=>$token, 'username'=>$user->username]);
            }
        }
    }

    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json(['status'=>200, 'message'=>'You have been logged out succcesful' ]);

    }
}