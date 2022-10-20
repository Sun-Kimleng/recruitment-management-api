<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'job_title'=> 'required',
            'job_type'=>'required',
            'job_level'=>'required',
            'salary'=>'required',
            'experience'=>'required',
            'description'=>'required',
        ]);
        
        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }else{
            $user = User::find(auth()->user()->id);
        

            try{

                $post = new Post([ 
                    'job_type'=>$request->input('job_type'),
                    'job_title'=>$request->input('job_title'),
                    'job_level'=>$request->input('job_level'),
                    'experience'=>$request->input('experience'),
                    'status'=>'Open',
                    'description'=>$request->input('description'),
                    'salary'=>$request->input('salary'),
                ]);
    
                $user->posts()->save($post);
    
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

        try{
            $post = DB::table('posts')
                ->join('users','users.id','=', 'posts.posted_by')
                ->join('jobs', 'jobs.id', '=', 'posts.job_title')
                ->select('posts.*', 'users.username', 'jobs.name as job_name')
                ->where('posts.id','=', $id)->first();

            $job = Job::where('id',$post->job_title)->get();

            return response()->json(['post'=>$post,'job'=>$job, 'status'=>200]);

        }catch(\Exception $e){
            return response()->json(['status'=>404, 'errors'=>$e->getMessage()]);
        }
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
        $validator = Validator::make($request->all(), [
            'job_title'=> 'required',
            'job_type'=>'required',
            'job_level'=>'required',
            'salary'=>'required',
            'experience'=>'required',
            'description'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }else{
            try{
                $post = Post::find($id);
    
                $post->job_type = $request->input('job_type');
                $post->job_title = $request->input('job_title');
                $post->salary = $request->input('salary');
                $post->job_level = $request->input('job_level');
                $post->experience = $request->input('experience');
                $post->description = $request->input('description');
    
                $post->save();
    
                return response()->json(['status'=>200]);
    
            }catch(\Exception $e){
                return response()->json(['status'=>404, 'errors'=>$e->getMessage()]);
            }
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

            $post = Post::find($id);
            $post->delete();
            return response()->json(['message'=>'Succesful Deleted', 'status'=>200]);
            
        }catch(\Exception $e){
            return response()->json(['errors'=> $e->getMessage(), 'status'=> 401]);
        }
        
    }

    public function getAllPost(Request $request){
        
        $name = $request->input('name');

        try{
            $post = DB::table('posts')
                ->join('users','users.id', '=', 'posts.posted_by')
                ->join('jobs', 'jobs.id', '=', 'posts.job_title')
                ->select('posts.*', 'users.username', 'jobs.name as job_name');

            if($request->filled('name')){
                $data = $post->where('jobs.name','LIKE','%'.$name.'%')
                        ->orderBy('created_at', 'desc')->paginate(15);
            }else{
                $data = $post->orderBy('created_at', 'desc')->paginate(15);
            }

            return response()->json(['post'=>$data, 'status'=>200]);

        }catch(\Exception $e){
            return response()->json(['errors'=>$e->getMessage()]);
        }
    
    }

    public function changeStatus(Request $request, $id){
        try{
            
            $post = Post::find($id);
            $post->status = $request->input('status');
            $post->update();
            return response()->json(['message'=>'Succesful Updated', 'status'=>200]);
            
        }catch(\Exception $e){
            return response()->json(['errors'=> $e->getMessage(), 'status'=> 401]);
        }
    }

    public function getAllPostForCandidate(Request $request){
        
        $name = $request->input('name');
        $filter = $request->input('filter');

        try{
            $post = DB::table('posts')
                ->join('users','users.id', '=', 'posts.posted_by')
                ->join('jobs', 'jobs.id', '=', 'posts.job_title')
                ->select('posts.*', 'users.username', 'jobs.name as job_name');
            
            if($request->filled('fitler') && $request->filled('filter')){
                $data = $post->where('job_level','LIKE', '%'.$name.'%')
                        ->orWhere('jobs.name','LIKE', '%'.$filter.'%')
                        ->orWhere('experience','LIKE', '%'.$filter.'%')
                        ->orWhere('salary','LIKE', '%'.$filter.'%')
                        ->orWhere('job_type','LIKE', '%'.$filter.'%')
                        ->orderBy('created_at', 'desc')->paginate(15);
            }

            if($request->filled('filter')){
                $data = $post->where('job_level','LIKE', '%'.$filter.'%')
                        ->orWhere('jobs.name','LIKE', '%'.$filter.'%')
                        ->orWhere('experience','LIKE', '%'.$filter.'%')
                        ->orWhere('salary','LIKE', '%'.$filter.'%')
                        ->orWhere('job_type','LIKE', '%'.$filter.'%')
                        ->orderBy('created_at', 'desc')->paginate(15);
            }
            
            if($request->filled('name')){
                $data = $post->where('job_level','LIKE','%'.$name.'%')
                        ->orderBy('created_at', 'desc')->paginate(15);
            }

            if(!$request->filled('name') || !$request->filled('filter')){
                $data = $post->orderBy('created_at', 'desc')->paginate(15);
            }

            return response()->json(['post'=>$data, 'status'=>200]);

        }catch(\Exception $e){
            return response()->json(['errors'=>$e->getMessage()]);
        }
    
    }
}
