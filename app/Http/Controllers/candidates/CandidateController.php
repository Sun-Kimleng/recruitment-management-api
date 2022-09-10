<?php

namespace App\Http\Controllers\candidates;

use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;

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
            'skills'=> '',

            //Experiences
            'experiences'=> '',

            //Languages
            'languages'=>'',

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
}
