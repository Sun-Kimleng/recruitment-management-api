<?php

namespace App\Http\Controllers\candidates;

use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        try{
        $candidate = Candidate::where('user_id', auth()->user()->id)->first();

        if($candidate){
            return response()->json(['status'=>200, 'candidate'=>$candidate]);
        }else{
            return response()->json(['status'=>404, 'message'=>'We could not find that candidate associate with authentication user']);
        }
        

        }catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage(), 'status'=>404]);
        }
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
       $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'city'=>'required',
            'gender'=> 'required',
            'birthday'=> 'required',
            'phone'=>'required',
            'address'=>'required',
            'interestedJob'=> 'required',
            'jobLevel'=>'required',
            'description'=>'required',
       ]);

       if($validator->fails()){
        return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
       }else{

        try{
            $user = User::find(auth()->user()->id);

            $candidate = new Candidate([
            
            //Overview
            'name'=> $request->input('name'),
            'school'=>'',
            'job_status'=>'Open',
            'workplace'=>'',
            'city'=> $request->input('city'),
            'interested_job'=>$request->input('interestedJob'),
            'job_level'=>$request->input('jobLevel'),
            'description'=> $request->input('description'),

            //Appearance
            'gender'=>$request->input('gender'),
            'birthday'=>$request->input('birthday'),
            'height'=>'N/A',
            'weight'=> 'N/A',

            //Contact
            'phone'=>$request->input('phone'),
            'email'=> auth()->user()->email,
            'address'=> $request->input('address'),

            //Education
            'educations' => json_decode('{}', true),

            //Skills
            'skills'=> json_decode('{}', true),

            //Experiences
            'experiences'=> json_decode('{}', true),

            //Languages
            'languages'=>json_decode('{}', true),

            //CV File
            'cv'=> '',

            ]);

            $user->candidates()->save($candidate);

            return response()->json(['status'=>200]);

        }catch(\Exception $e){
            return response()->json(['errors'=>$e->getMessage()]);
        }
        
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $candidates = DB::table('candidates')
        ->join('users','users.id', '=', 'candidates.user_id')
        ->select('candidates.*','users.avatar')
        ->where('candidates.id','=',$id)
        ->first();

        $props = Candidate::where('id', $id)->first();

        return response()->json(['candidates'=>$candidates, 'props'=>$props, 'status'=>200]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
        $candidate = Candidate::find($id);

        $candidate->educations = $request->input('educations');

        $candidate->update();

        return response()->json(['status'=>200]);
        }catch(\Exception $e){
            return response()->json(['status'=>404, 'error'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function logout(){
        
        // auth()->user()->tokens()->where('id', auth()->user()->currentAccessToken()->id)->delete();
        auth()->user()->token()->revoke();
        return response()->json(['status'=>200, 'message'=>'You have been logged out succcesful' ]);

    }

    public function insertEducation(Request $request){
            $validator = Validator::make($request->all(), [
            'educations'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }

        try{
            $candidate = Candidate::where('user_id', auth()->user()->id)->first();

            $candidate->educations = $request->input('educations');

            $candidate->update();

            return response()->json(['status'=>200, 'message'=>'Added education succesful.']);

        }catch(\Exception $e){
            return response()->json(['status'=>404, 'error'=>$e->getMessage()]);
        }
    }

    public function insertSkill(Request $request){
        $validator = Validator::make($request->all(), [
            'skills'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }

        try{
            $candidate = Candidate::where('user_id', auth()->user()->id)->first();

            $candidate->skills = $request->input('skills');

            $candidate->update();

            return response()->json(['status'=>200, 'message'=>'Added skill succesful.']);

        }catch(\Exception $e){
            return response()->json(['status'=>404, 'error'=>$e->getMessage()]);
        }
    }

    public function insertExperience(Request $request){
        $validator = Validator::make($request->all(), [
            'experiences'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }

        try{
            $candidate = Candidate::where('user_id', auth()->user()->id)->first();

            $candidate->experiences = $request->input('experiences');

            $candidate->update();

            return response()->json(['status'=>200, 'message'=>'Added skill succesful.']);

        }catch(\Exception $e){
            return response()->json(['status'=>404, 'error'=>$e->getMessage()]);
        }
    }

    public function insertLanguage(Request $request){
        $validator = Validator::make($request->all(), [
            'languages'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }

        try{
            $candidate = Candidate::where('user_id', auth()->user()->id)->first();

            $candidate->languages = $request->input('languages');

            $candidate->update();

            return response()->json(['status'=>200, 'message'=>'Added skill succesful.']);

        }catch(\Exception $e){
            return response()->json(['status'=>404, 'error'=>$e->getMessage()]);
        }
    }

    public function uploadCv(Request $request){
        $validator = validator::make($request->all(), [
            'cv'=>'required|mimes:pdf,doc,jpeg,png,jpg|max:2048',
        ]);
        
        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        
        }else{

            try{
                $user = Candidate::where('user_id', auth()->user()->id)->first();;

                $file = $request->file('cv');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                
                if(File::exists(public_path($user->cv))){

                    File::delete($user->cv);
                    $file->move('uploads/cvs/', $filename);
                    $user->cv = 'uploads/cvs/'.$filename;
                    $user->update();

                    return response()->json(['status'=>200, 'message'=>'Successful updated CV']);
                }else{

                    $file->move('uploads/cvs/', $filename);
                    $user->cv = 'uploads/cvs/'.$filename;
                    $user->save();

                    return response()->json(['status'=>200, 'message'=>'Successful added CV']);
                }
            }catch(\Exception $e){
                return response()->json(['status'=>404, 'error'=>$e->getMessage()]);
            }
        }
    }

    public function deleteCv(){

        try{
            $user = Candidate::where('user_id', auth()->user()->id)->first();;
            
            if(File::exists(public_path($user->cv))){

                File::delete($user->cv);
                $user->cv = '';
                $user->update();

                return response()->json(['status'=>200, 'message'=>'Successful deleted CV']);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>404, 'error'=>$e->getMessage()]);
        }

    }

    public function updateDescription(Request $request){

        $validator = Validator::make($request->all(), [
            'description'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        }else{
            try{    
                $user = Candidate::where('user_id', auth()->user()->id)->first();;
                
                $user->description = $request->input('description');

                $user->update();

                return response()->json(['status'=>200, 'message'=>'succesful updated the description']);
            }catch(\Exception $e){
                return response()->json(['status'=>400, 'error'=>$e->getMessage()]);
            }
        }
    }

    public function updateContact(Request $request){

        $validator = Validator::make($request->all(), [
            'phone'=>'required',
            'email'=>'required',
            'address'=>'required'
        ]);
        
        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        }else{
            try{    
                $user = Candidate::where('user_id', auth()->user()->id)->first();;
                
                $user->phone = $request->input('phone');

                $user->email = $request->input('email');

                $user->address = $request->input('address');

                $user->update();

                return response()->json(['status'=>200, 'message'=>'succesful updated the description']);
            }catch(\Exception $e){
                return response()->json(['status'=>400, 'error'=>$e->getMessage()]);
            }
        }
    }

    public function updateAppearance(Request $request){

        $validator = Validator::make($request->all(), [
            'gender'=>'required',
            'birthday'=>'required',
            'height'=>'required',
            'weight'=>'required',
        ]);
        
        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        }else{
            try{    

                $user = Candidate::where('user_id', auth()->user()->id)->first();;

                $user->gender = $request->input('gender');
                $user->birthday = $request->input('birthday');
                $user->height = $request->input('height');
                $user->weight = $request->input('weight');

                $user->update();

                return response()->json(['status'=>200, 'message'=>'succesful updated the description']);
            }catch(\Exception $e){
                return response()->json(['status'=>400, 'error'=>$e->getMessage()]);
            }
        }
    }

    public function updateOverview(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'city'=>'required',
            'interestedJob'=>'required',
            'jobLevel'=>'required',
        ]);
        
        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        }else{
            try{    

                $user = Candidate::where('user_id', auth()->user()->id)->first();;

                $user->name = $request->input('name');
                $user->workplace = $request->input('workplace');
                $user->city = $request->input('city');
                $user->school = $request->input('school');
                $user->interested_job = $request->input('interestedJob');
                $user->job_level = $request->input('jobLevel');

                $user->update();

                return response()->json(['status'=>200, 'message'=>'succesful updated the description']);
            }catch(\Exception $e){
                return response()->json(['status'=>400, 'error'=>$e->getMessage()]);
            }
        }
    }

    public function updateJobStatus(Request $request){

        $validator = Validator::make($request->all(), [
            'jobStatus'=>'required',
        ]);
        
        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        }else{
            try{    

                $user = Candidate::where('user_id', auth()->user()->id)->first();;

                $user->job_status = $request->input('jobStatus');

                $user->update();

                return response()->json(['status'=>200, 'message'=>'succesful updated the description']);
            }catch(\Exception $e){
                return response()->json(['status'=>400, 'error'=>$e->getMessage()]);
            }
        }
    }

    public function getAllCandidates(Request $request){

        $candidates = DB::table('candidates')
                    ->join('users','users.id', '=', 'candidates.user_id')
                    ->select('candidates.*','users.avatar');
                    
        $name = $request->input('name');
        if($request->filled('name')){
            $data = $candidates->where('candidates.name', 'LIKE', '%'.$name.'%')->orderBy('candidates.updated_at', 'asc')
            ->paginate(15);
        }else{
            $data = $candidates->orderBy('candidates.updated_at', 'asc')->paginate(15);
        }

        return response()->json(['candidates'=>$data, 'status'=>200]);
    }

    public function candidateCompany(){
        $company = Company::find(1);
        if($company){
            return response()->json(['status'=>200, 'info'=>$company]);
        }else{
            return response()->json(['status'=>200, 'info'=> ' ']);
        }
    }
}
