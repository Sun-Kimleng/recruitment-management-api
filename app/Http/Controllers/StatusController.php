<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = Status::orderBy('created_at', 'desc')->paginate(9);

        return response()->json(['status'=>200, 'data'=>$status]);
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
            'name'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }else{
            
            try{

                $status = new Status([ 
                    'name'=>$request->input('name'),
                ]);
    
                $status->save();
    
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
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
        
        ]);

        if($validator->fails()){
            return response()->json(['status'=>402, 'errors'=>$validator->errors()]);
        }else{
            try{
                $status = Status::find($id);
    
                $status->name = $request->input('name');
    
                $status->update();
    
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
        $status = Status::find($id);

        $status->delete();

        return response()->json(['status'=>200]);
    }

    public function getAllStatus(){

    try{

            $status = Status::all();

            return response()->json(['status'=>200, 'data'=>$status]); 

        }catch(\Exception $e){
            return response()->json(['errors'=>$e->getMessage()]);
        }
    }
}
