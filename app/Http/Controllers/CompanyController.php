<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Company::find(1);
        
        if($company){
            return response()->json(['status'=>200, 'info'=>$company]);
        }else{
            return response()->json(['status'=>200, 'info'=> '']);
        }
        //if null return none
        
        
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
        try{
            $com = Company::find(1);

            if(!$com){
                $company = new Company();

                $company->description   = $request->input('description');
                $company->facebook      = $request->input('facebook');
                $company->email         = $request->input('email');
                $company->location      = $request->input('location');
                $company->youtube       = $request->input('youtube');
                $company->phone         = $request->input('phone');
                $company->save();
                
                return response()->json(['status'=>200, 'message'=>'created successful']);
            }else{
    
                $company = Company::find(1);
                $company->description   = $request->input('description');
                $company->facebook      = $request->input('facebook');
                $company->email         = $request->input('email');
                $company->location      = $request->input('location');
                $company->youtube       = $request->input('youtube');
                $company->phone         = $request->input('phone');
                $company->update();
                return response()->json(['status'=>200, 'message'=>'updated successful']);
            }
            
        }catch(\Exception $e){
            return response()->json(['status'=>404, 'message'=>$e->getMessage()]);
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
        try{

        }catch(\Exception $e){
            
        }
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
        //
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
}
