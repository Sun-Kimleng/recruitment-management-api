<?php

namespace App\Http\Controllers\admins;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('jobs')
            ->join('users', 'users.id','=', 'jobs.added_by')
            ->select('jobs.*', ('users.username as user_added'))
            ->orderBy('created_at', 'desc')->get();

        return response()->json(['data'=>$data, 'status'=> 200]);

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
            'name'=>'required|min:1|max:30|unique:jobs',
            'description'=>'required',
        ],
    [
        'name.unique'=>'This job is already in the list',
    ]);

        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors(), 'status'=> 404]);
        }else{
            $job = new Job;

            $job->name = $request->input(['name']);
            $job->description = $request->input(['description']);
            $job->added_by = auth()->user()->id;

            $job->save();

            return response()->json(['message'=>'Succesful added', 'status'=>200]);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $validator = Validator::make($request->all(), [
            'name'=>'required|min:1|max:30',
            'description'=>'required',
        ]);

        if($validator -> fails()){
            return response()->json(['errors'=>$validator->errors()]);

        }else{
            
            $update = new Job;

            $job = $update->where('id', $id)->update(['name'=>$request->input('name'), 'description'=>$request->input('description')]);
            
            
            if($job){
                return response()->json(['message'=>'Updated succesfull', 'status'=>200]);
            }else{
                return response()->json(['errors'=>'Cannot find this jobs', 'status'=> 404]);
            }
            
            return response()->json(['job'=> $id]);
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

            $job = Job::find($id);
            if (is_array($id)) 
            {
                Job::destroy($id);
            }
            

            if($job){   
                $job->delete();
                return response()->json(['message'=>'Succesful Deleted', 'status'=>200]);
            }else{
                return response()->json(['errors'=> 'Cannot find this job', 'status'=>404]);
            }

        }catch(\Exception $e){
            return response()->json(['errors'=> $e->getMessage(), 'status'=> 401]);
        }
    }

    public function deleteAll($id)
    {   
        $ids = explode(",",$id,);

        // return response()->json(['message'=>$ids]);

        $result = DB::table("jobs")->whereIn('id',$ids)->delete();
        
        if($result){
            return response()->json(['message'=>'succesful', 'status'=>200]);
        }else{
            return response()->json(['errors'=>'cannot delete', 'status'=>404]);
        }
       
    }
}       