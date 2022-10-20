<?php

namespace App\Http\Controllers;

use App\Models\Apply;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Post;
use App\Models\Status;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        try{
            $applies = DB::table('applies')
            ->join('posts', 'posts.id', '=','applies.post_id')
            ->join('candidates', 'candidates.id','=','applies.candidate_id')
            ->join('jobs','jobs.id','=','posts.job_title')
            ->select('candidates.name', 'posts.job_title as job_name_id', 'jobs.name as job_name','applies.*')
            ->orderBy('created_at', 'desc')->paginate(15);

            return response()->json(['status'=>200, 'apply'=>$applies]);

        }catch(\Exception $e){
            return response()->json(['status'=>402, 'errors'=>$e->getMessage()]);
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
            'post_id'=> 'required',
            'candidate_id'=>'required',
        ]); 

        if($validator->fails()){
            return response()->json(['status'=>404, 'errors'=>$validator->errors()]);
        }else{
            try{
                $apply = new Apply([
                    'post_id' => $request->input('post_id'),
                    'candidate_id'=>$request->input('candidate_id'),
                    'status'=>'Initial'
                ]);

                $apply->save();

                return response()->json(['status'=>200, 'message'=>'Succesful Created']);

            }catch(\Exception $e){
                return response()->json(['status'=>402, 'errors'=>$e->getMessage()]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            
            $apply = Apply::find($id);

            $apply->status = $request->input('status');
            $apply->update();
    
            return response()->json(['status'=>200, 'message'=>'already Applied']);
            

        }catch(\Exception $e){
            return response()->json(['status'=>402, 'errors'=>$e->getMessage()]);
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
        
        try{
            
            $apply = Apply::find($id);

            $apply->delete();

            return response()->json(['status'=>200, 'message'=>'Succesful Deleted']);

        }catch(\Exception $e){
            return response()->json(['status'=>402, 'errors'=>$e->getMessage()]);
        }
        
    }

    public function checkIfApplied($id){

        try{
            $post = Post::find($id);
            $candidate = Candidate::where('user_id', '=', auth()->user()->id)->first();

            $check = Apply::where('post_id', '=', $post->id)
                    ->where('candidate_id', '=', $candidate->id)
                    ->first();
    
            if($check){
                return response()->json(['status'=>200, 'message'=>'already Applied', 'check'=>$check]);
            }else{
                return response()->json(['status'=>404, 'message'=>'not yet apply', 'check'=>$check]);
            }

        }catch(\Exception $e){
            return response()->json(['status'=>402, 'errors'=>$e->getMessage()]);
        }
    }

    public function getMyApplies(){
        $candidate = Candidate::where('user_id', '=', auth()->user()->id)->first();
        $apply = Apply::join('posts', 'posts.id', '=', 'applies.post_id')
        ->join('jobs', 'jobs.id', '=', 'posts.job_title')
        ->select('applies.status as status_apply', 'applies.id as apply_id', 'posts.*', 'posts.id as post_id', 'jobs.name as job_name')
        ->where('candidate_id', '=', $candidate->id)->get();

        return response(['status'=>200, 'apply'=>$apply]);
    }

    public function getAllApply(Request $request){
        
        try{
            $applies = DB::table('applies')
            ->join('posts', 'posts.id', '=','applies.post_id')
            ->join('candidates', 'candidates.id','=','applies.candidate_id')
            ->join('jobs','jobs.id','=','posts.job_title')
            ->select('candidates.name', 'posts.job_title as job_name_id', 'jobs.name as job_name','applies.*');
            // ->orderBy('created_at', 'desc')->paginate(15);

            $job = $request->input('jobName');

            $status = $request->input('statusName');

            if($request->filled('statusName') && $request->filled('jobName')){
                $data = $applies->where('jobs.name','=',$job)
                ->where('applies.status','=',$status)
                ->orderBy('created_at', 'desc')->paginate(15);
            }

            if($request->filled('statusName')){
                $data = $applies->where('applies.status','=',$status)
                ->orderBy('created_at', 'desc')->paginate(15);
            }

            
            if($request->filled('jobName')){
                $data = $applies->where('jobs.name','=',$job)
                ->orderBy('created_at', 'desc')->paginate(15);
            }

            if(!$request->filled('jobName') || !$request->filled('statusName')){
                $data = $applies->orderBy('created_at', 'desc')->paginate(15);
            }

            return response()->json(['status'=>200, 'apply'=>$data]);

        }catch(\Exception $e){
            return response()->json(['status'=>402, 'errors'=>$e->getMessage()]);
        }
    }
}
