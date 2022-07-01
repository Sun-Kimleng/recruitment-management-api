<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Helpers\Helper;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\File;
use Laravel\Ui\Presets\React;
use phpDocumentor\Reflection\Types\Null_;

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
            'confirmPassword'=>'required|same:password',
        ],
    );
        
        if($validator->fails()){
            
        return response()->json(['error'=>$validator->errors()]);
        
        }else{
            // ROLES 1:Admin, 2:Editor, 3:Moderator
            $user = User::create([
                'user_id'=>$user_id,
                'role'=>1,
                'username'=>$request->input('username'),
                'email'=>$request->input('email'),
                'password'=> Hash::make($request->input('password')),
            ]);
            event(new Registered($user));
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

                if(!$user->hasVerifiedEmail()){
                    $token = $user->createToken($request->email.'_token')->plainTextToken;
                    return response()->json(['message'=>'You need to verify your email first', 'status'=>401 , 'token'=>$token]);
                }
                else{
                    if($user->role === 1){
                        $token = $user->createToken($request->email.'_token', ['server:admin'])->plainTextToken;
                        return response()->json(['status'=>200 ,'message'=>'You\'re logged in', 'token'=>$token, 'username'=>$user->username, 'role'=> 1]);
                    }else{
                        $token = $user->createToken($request->email.'_token', [''])->plainTextToken;
                        return response()->json(['status'=>200 ,'message'=>'You\'re logged in', 'token'=>$token, 'username'=>$user->username, 'role'=> 'no role']);
                    }
                
                    
                }
                
            }
        }
    }

    public function logout(){
        
        auth()->user()->tokens()->where('id', auth()->user()->currentAccessToken()->id)->delete();
        return response()->json(['status'=>200, 'message'=>'You have been logged out succcesful' ]);

    }

    public function index(){

        $user = User::find(auth()->user()->id);

        return response()->json(['user'=>$user, 'status'=>200]);
    }

    public function updateEmail(Request $request){

        $validator = Validator::make($request->all(), [
            'email'=>'required|email|unique:users'
        ]);

        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            $user = User::find(auth()->user()->id);
            $user->email = $request->input('email');
            $user->email_verified_at = null;
            $user->update();

            $user->sendEmailVerificationNotification();

            return response()->json(['message'=>'succesful updated the email!', 'status'=>200]);
        }
        
    }

    public function updateUsername(Request $request){
        $validator = Validator::make($request->all(), [
            'username'=>'required|max:12'
        ]);

        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            $user = User::find($request->user()->id);
            $user->username = $request->input('username');
            $user->update();

            return response()->json(['message'=>'Successful updated the username', 'status'=>200]);
        }
    }

    public function changePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'oldPassword'=> 'required',
            'newPassword'=> 'required',
            'confirmNewPassword'=>'required|same:newPassword',
        ]);
        
        $user = User::where('email', auth()->user()->email)->first();

        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        }else{

            if(!$user || !Hash::check($request->input('oldPassword'), $user->password)){
                return response()->json(['status'=>403, 'errors'=>'Your old password is invalid']);
            }else{
                $user_pw = User::find(auth()->user()->id);
                $user_pw->password = Hash::make($request->input('newPassword'));
                $user_pw->update();
                auth()->user()->tokens()->delete();
                return response()->json(['status'=>200, 'message'=>'Your password has been updated. you will be logged out after this.']);
            }

        }
    }

    public function changeAvatar(Request $request){
        
        $validator = validator::make($request->all(), [
            'avatar'=>'required|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){

            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        
        }else{
            $user = User::find(auth()->user()->id);

            $file = $request->file('avatar');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;

            if(File::exists(public_path($user->avatar))){

                File::delete($user->avatar);
                $file->move('uploads/users/', $filename);
                $user->avatar = 'uploads/users/'.$filename;
                $user->update();

                return response()->json(['status'=>200, 'message'=>'Successful updated avatar']);
            }else{

                $file->move('uploads/users/', $filename);
                $user->avatar = 'uploads/users/'.$filename;
                $user->save();

                return response()->json(['status'=>200, 'message'=>'Successful added avatar']);
            }
            
            

            
        
        }
    }
}