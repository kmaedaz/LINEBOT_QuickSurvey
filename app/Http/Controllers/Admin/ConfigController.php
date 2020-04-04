<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    //
    public function setup(Request $request)
    {
         $configs="";

         $Config = \App\Config::get()->first();
         //var_dump($Config);
         $configs=array();
        if ($request->isMethod('post')) {
			$Config->linetoken = $request->linetoken;
			$Config->linesecret = $request->linesecret;
			$Config->dispnum = $request->dispnum;
			$Config->dispnumsp = $request->dispnumsp;


			$Config->follow_message1 = $request->follow_message1;
			$Config->follow_imageurl1 = $request->follow_imageurl1;
			$Config->follow_message2 = $request->follow_message2;
			$Config->follow_imageurl2 = $request->follow_imageurl2;
			$Config->follow_message3 = $request->follow_message3;

            $Config->save();
            $configs["linetoken"]= $request->linetoken;
            $configs["linesecret"]=$request->linesecret;
            $configs["dispnum"]=$request->dispnum;
            $configs["dispnumsp"]=$request->dispnum;

            $configs["follow_message1"]=$request->follow_message1;
            $configs["follow_imageurl1"]=$request->follow_imageurl1;
            $configs["follow_message2"]=$request->follow_message2;
            $configs["follow_imageurl2"]=$request->follow_imageurl2;
            $configs["follow_message3"]=$request->follow_message3;


       
        } 

         if(!$Config){
            $Config = new  \App\Config();
            $Config->linetoken = "";
            $Config->linesecret = "";
            $Config->dispnum =5;
            $Config->dispnumsp = 3;
            $Config->save();
            $configs["linetoken"]="";
            $configs["linesecret"]="";
            $configs["dispnum"]=5;
            $configs["dispnumsp"]=3;
		} else{
            $configs["linetoken"]=$Config->linetoken;
            $configs["linesecret"]=$Config->linesecret;

            $configs["follow_message1"]=$Config->follow_message1;
            $configs["follow_imageurl1"]=$Config->follow_imageurl1;
            $configs["follow_message2"]=$Config->follow_message2;
            $configs["follow_imageurl2"]=$Config->follow_imageurl2;
            $configs["follow_message3"]=$Config->follow_message3;

            $configs["dispnum"]=$Config->dispnum;
            $configs["dispnumsp"]=$Config->dispnumsp;

		}

         return view('admin/setup',  $configs);
         //return view('admin/setup');

    }


}
