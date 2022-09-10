<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Helpers\Helper;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;

class UserController extends Controller
{
    public function create(Request $request){

        $users = new User;
        $user_id = Helper::IDGenerator($users, 'user_id', 5, 'AGB');

        $validator = Validator::make($request->all(),[
            'username'=>'required|max: 22|unique:users',
            'email'=>'required|unique:users',
            'password'=>'required',
            'confirmPassword'=>'required|same:password',
        ],
    );
        
        if($validator->fails()){
            
        return response()->json(['error'=>$validator->errors()]);
        
        }else{
            // ROLES nfoqbehdk283:Admin, dbqqajdnbe921:Editor, zjeklsnbn323:Moderator
            $user = User::create([
                'user_id'=>$user_id,
                'role'=>'nfoqbehdk283',
                'user_status'=>'active',
                'username'=>$request->input('username'),
                'email'=>$request->input('email'),
                'password'=> Hash::make($request->input('password')),
            ]);
            event(new Registered($user));
            $token = $user->createToken($user->email. "_token")->accessToken;

            return response()->json([
                'status'=>200,
                'message'=>'registered successful',
                'token'=>$token,
            ]);
            
        }
    }

    public function adminCreatesUsers(Request $request){
        $users = new User;
        $user_id = Helper::IDGenerator($users, 'user_id', 5, 'AGB');

        $validator = Validator::make($request->all(),[
            'username'=>'required|max: 22|unique:users',
            'email'=>'required|unique:users',
            'role'=>'required',
            'password'=>'required|min:8',
            'confirmPassword'=>'required|same:password',
        ],
        [
            'password.regex'=>'You password is not match with our requirement',
        ]
    );
        
        if($validator->fails()){
            
        return response()->json(['error'=>$validator->errors()]);
        
        }else{
            // ROLES nfoqbehdk283:Admin, dbqqajdnbe921:Editor, zjeklsnbn323:Moderator
            $user = User::create([
                'user_id'=>$user_id,
                'role'=>$request->input('role'),
                'user_status'=>'active',
                'username'=>$request->input('username'),
                'email'=>$request->input('email'),
                'password'=> Hash::make($request->input('password')),
            ]);
            event(new Registered($user));

            return response()->json([
                'status'=>200,
                'message'=>'registered successful',
            ]);
            
        }
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            'email'=> 'required|exists:users',
            'password'=> 'required'
        ],
        [
            'email.exists'=>'Your email is not register yet.'
        ]
        
        );

        if($validator->fails()){
            return response()->json(['error'=> $validator->errors(), 'status'=>404]);
        }else{

            $user = User::where('email', $request->email)->first();

            $executed = RateLimiter::attempt(
                'send-message:'.$user->id,
                $perMinute = 5,
                function() {
                    // Send message...
                }
            );
            
            //Too many Attempt
            if (! $executed) {
                return response()->json(['status'=>429, 'message'=>'Too many attemps.']);

            }else{
            //Approve Request
            
            //Authenticate User
            if(!$user || !Hash::check($request->password, $user->password, )){

                return response()->json(['status'=>402, 'message'=>'Invalid Credentials']);
            }else{

                if($user->user_status === 'deactivated' || $user->user_status !== 'active'){
                    
                    return response()->json(['status'=>204, 'user_status'=>$user->user_status, 'message'=>'your account has been activated.']);

                }else if($user->user_status === 'active'){

                    if(!$user->hasVerifiedEmail()){
                        $token = $user->createToken($user->email. "_token")->accessToken;
                        return response()->json(['message'=>'You need to verify your email first', 'status'=>401 , 'token'=>$token]);
                    }
                    else{
                            if($user->role == 'nfoqbehdk283'){
                                $token = $user->createToken($request->email.'_token', ['admin'])->accessToken;
                                return response()->json(['status'=>200 ,'message'=>'You\'re logged in', 'token'=>$token, 'username'=>$user->username, 'permission'=> 'admin']);
                            }else{
                                $token = $user->createToken($request->email.'_token', ['admin'])->accessToken;
                                return response()->json(['status'=>200 ,'message'=>'You\'re logged in', 'token'=>$token, 'username'=>$user->username, 'permission'=> 'none']);
                            }
                        }
                    }
                }
            }
        }
    }

    public function logout(){
        
        // auth()->user()->tokens()->where('id', auth()->user()->currentAccessToken()->id)->delete();
        auth()->user()->token()->revoke();
        return response()->json(['status'=>200, 'message'=>'You have been logged out succcesful' ]);

    }

    public function index(){

        $user = User::find(auth()->user()->id)->makeHidden(['created_at', 'updated_at', 'role']);;

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
            if(Hash::check($request->input('newPassword'), $user->password)){

                return response()->json(['status'=>402, 'message'=>'Tf? why did you change your password when your new password is same as the old one?']);

            }else if(!$user || !Hash::check($request->input('oldPassword'), $user->password)){

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

    public function getAdmin(){
        $user = User::where('role', 'nfoqbehdk283')->get()->makeHidden(['created_at', 'updated_at', 'role']);
        return response()->json(['status'=>200,'user'=>$user, 'message'=>'Successful']);
    }

    public function getEditor(){
        $user = User::where('role', 'dbqqajdnbe921')->get()->makeHidden(['created_at', 'updated_at', 'role']);
        return response()->json(['status'=>200,'user'=>$user, 'message'=>'Successful']);
    }

    public function getModerator(){
        $user = User::where('role', 'zjeklsnbn323')->get()->makeHidden(['created_at', 'updated_at', 'role']);
        return response()->json(['status'=>200,'user'=>$user, 'message'=>'Successful']);
    }

    public function editUserRole($id, Request $request){
        $user = User::find($id);

        $validator = Validator::make($request->all(), [
            'role'=>'required'
        ]);

        if(Hash::check($request->input('password'), auth()->user()->password)){

            if($validator->fails() || $user->role === $request->input('role')){
                
                return response()->json(['status'=>404, 'error'=>$validator->errors()]);
            }else{
                if($user->id === auth()->user()->id){
                    return response()->json(['status'=>402, 'message'=>'You cannot edit your own role']);
                }else{
                    $user->role = $request->input('role');
                    $user->tokens()->delete();
                    $user->update();
                    return response()->json(['status'=> 200, 'message'=>'successfully edited the role']);
                }
            }
        }else{
            return response()->json(['status'=>429, 'message'=>'you have entered the wrong password']);
        }
    }

    public function deleteUser(Request $request, $id){

        $user = User::find($id);

        if(!Hash::check($request->input('password'), auth()->user()->password)){

            return response()->json(['status'=>429, 'message'=>'You entered the wrong password.']);

        }
        else{

            try {

                $user->delete(); 

                return response()->json(['status'=>200, 'message'=>'Successful removed user']);
                
            } catch (\Exception $e) {

                return response()->json(['message'=>'You cannot remove this user! This user is
                associated with the data inside the application. 
                You may first try to delete the data that associated with this 
                user before you remove this user otherwise you can interrupt this user instead.'
                , 'status'=>402]);
            }

        }
    }

    public function deactivateAccount($id){

        $user= User::find($id);

        if(auth()->user()->id === $user->id){
            return response()->json(['status'=>404, 'message'=>'You cannot deactivate your own account.']);
        }else{
            if($user->user_status === 'active'){
            
                $user->user_status = 'deactivated';
                $user->update();
                $user->tokens()->delete();
                return response()->json(['status'=>200, 'message'=>'your account has been deactivated']);
    
            }else{
                $user->user_status = 'active';
                $user->update();
                
                return response()->json(['status'=>201, 'message'=>'your account has been reactivated']);
    
            }

        }
    }

    public function getUserById($id){
        
        $user = User::where('id', $id)->get()->makeHidden(['role']);

        if($user->isEmpty()){
            return response()->json(['status'=>404, 'message'=>'We cannot find this user.']);
        }else{
            return response()->json(['status'=>200, 'data'=>$user]);
        }
    }
}