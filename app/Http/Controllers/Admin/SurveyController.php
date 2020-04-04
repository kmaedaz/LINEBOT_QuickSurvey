<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

use App\User;
use App\Msurvey;
use App\Msurveydetail;
use App\Msurveyselect;


class SurveyController extends Controller
{
    //アンケート作成
    public function create(Request $request)
    {
         $configs="";

        
        if ($request->isMethod('post')) {
        // アンケート作成
            //dd($request->all());
            // ① フォームの入力値を取得
            $inputs = \Request::all();
         
            // ② デバッグ： $inputs の内容確認
            //    dd($inputs);
             
            $this->validate($request, [
                'jsonfile' => [
                    // 必須
                    'required',
                    // アップロードされたファイルであること
                    'file',
                    // ファイルであること
                    //'json',
                    // MIMEタイプを指定
                    //'mime: json',
                ]
            ]);
         
            if ($request->file('jsonfile')->isValid([])) {
                $mes = $request->file('jsonfile')->getClientOriginalName();
            $jsonName = str_shuffle(time().$request->file('jsonfile')->getClientOriginalName()). '.' . $request->file('jsonfile')->getClientOriginalExtension();//ファイル名をユニックするためstr_shuffleを使う
            $request->file('jsonfile')->move(
                base_path() . '/storage/app/json/', $jsonName
            );

            $content = file_get_contents(base_path() . '/storage/app/json/'. $jsonName);
            //dd($content);
            $json = mb_convert_encoding($content, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $arr = json_decode($json,true);
            //dd($arr);

            if(! is_null($arr)){
                //パース　ok!
                if( !(isset($arr["surveycode"]) && isset($arr["title"]) && isset($arr["qlist"]) && isset($arr["result"]) ) ){
                        unlink(base_path() . '/storage/app/json/'. $jsonName);
                    return view('admin/create')->with('success', '登録に失敗しました。'.$mes);

                }
                
                 $exists =  \App\Msurvey::where('surveycode',$arr["surveycode"])->exists();
                 if($exists){
                    return view('admin/create')->with('success', 'surveycode重複エラー。');
                }
                
                
                 $params=$arr;
//                return DB::transaction(function () use ($params) {
                    // クエリ処理

//                  dd($params);
                    $surveycode=$params["surveycode"];
                    $title=$params["title"];
                    $qlist=$params["qlist"];
                    $result=$params["result"];

                    $msurvey = new  \App\Msurvey();
                    $msurvey->surveycode=$surveycode;
                    $msurvey->title=$title;
                    if(isset($params["from_at"])){
                        $msurvey->from_at=$params["from_at"];
                    }
                    if(isset($params["to_at"])){
                        $msurvey->to_at=$params["to_at"];
                    }
                    if(isset($params["imageurl"])){
                        $msurvey->imageurl=$params["imageurl"];
                    } else{
                        $msurvey->imageurl="";
                    }


                    $msurvey->save();

                    $survey_id=$msurvey->id;
                
                    $detail_order=0;
                    // 各設問
                    foreach($qlist as $qlist_value){
                        // var_dump($qlist_value['imageurl']);

                        $msurveydetail           = new  \App\Msurveydetail();
                        $msurveydetail->surveyid = $survey_id;
						if(isset($qlist_value["q"]) ){
							$message=$qlist_value["q"];
						} else {
							$message="";
						}

                        $msurveydetail->title    = $message;

                        $msurveydetail->message  = $message;
                        if(isset($qlist_value['imageurl'])){
                            $msurveydetail->imageurl=$qlist_value['imageurl'];
                        } else{
                            $msurveydetail->imageurl="";
                        }


						    $msurveydetail->qid       = $detail_order;
						//
						if(isset($qlist_value["qid"]) ){
						    $msurveydetail->qid_code       = $qlist_value["qid"];
						} else{
						    $msurveydetail->qid_code       = $detail_order;
						}
	                        

                        $msurveydetail->disnum   = $detail_order;
                        //$msurveydetail->eval     = $qlist_value["eval"];
                        $msurveydetail->eval     = 1; //不要なのでダミー

						if(isset($qlist_value["type"]) ){
	                        $msurveydetail->type     = $qlist_value["type"];
						}

                        $msurveydetail->save();
                       // $msurveydetail_id=$msurveydetail->id;
                        //dd($value);
                        $alist=$qlist_value["alist"]; //選択

                        //設問毎の選択
                        $select_order=0;
                        foreach($alist as $value_list){
                            $msurveyselect           = new  \App\Msurveyselect();
                            $msurveyselect->surveyid = $survey_id;
                            $msurveyselect->qid      = $detail_order;

                            if(isset($value_list["imageurl"])){
                                $msurveyselect->imageurl=$value_list["imageurl"];
                            } else{
                                $msurveyselect->imageurl="";
                            }

                            if(isset($value_list["message"])){
                                $msurveyselect->message=$value_list["message"];
                            } else{
                                $msurveyselect->message="";
                            }
                            if(isset($value_list["title"])){
                                $msurveyselect->title=$value_list["title"];
                            } else{
                                $msurveyselect->title="";
                            }

                            if(isset($value_list["button_title"])){
                                $msurveyselect->button_title=$value_list["button_title"];
                            }

                            $msurveyselect->disnum   = $select_order;
                            $msurveyselect->eval     = $value_list["eval"];
                            $msurveyselect->save();
                            $select_order++;
                        }

                        $detail_order++;

                    }

                    //結果
                    foreach($result as $result_list){

                        $mresult            = new  \App\Mresult();
                        $mresult->surveyid  = $survey_id;
                        $mresult->imageurl  = "";//$result_list["imageurl"];

                        if(isset($result_list["message"])){
                            $mresult->message=$value_list["message"];
                        } else{
                            $mresult->message="";
                        }

                        $mresult->title   = "";//$result_list["title"];
                        $mresult->link_url   ="";// $result_list["link_url"];

                        $mresult->eval      = $result_list["eval"];
                        $mresult->save();

                            foreach($result_list["result"] as $resultitem){
//dd($resultitem["title"])  ;
                                $mresultitem               = new  \App\Mresultitem();
                                $mresultitem->surveyid     =$survey_id;
                                $mresultitem->mresultsid   =$mresult->id;
								if(isset($resultitem["title"])){
	                                $mresultitem->title        =$resultitem["title"];
								} else {
	                              $mresultitem->title        ="";
								}

                            if(isset($resultitem["imageurl"])){
                                $mresultitem->imageurl=$resultitem["imageurl"];
                            } else{
                                $mresultitem->imageurl="";
                            }

                            if(isset($resultitem["message"])){
                                $mresultitem->message=$resultitem["message"];
                            } else{
                                $mresultitem->message="";
                            }
                                $mresultitem->link_url    =$resultitem["link_url"];
                                $mresultitem->save();
                            }



                    }

//                  dd($qlist);

                   // return $this->fill($params)->save();

//                });

                unlink(base_path() . '/storage/app/json/'. $jsonName);

                return view('admin/create')->with('success', $mes.'を登録しました。');
            } else {
            // JSON Error

                unlink(base_path() . '/storage/app/json/'. $jsonName);
            return view('admin/create')->with('success', '登録に失敗しました。'.$mes);
            }




            //dd($user->id);
         
         
         
            } else {
            // error
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['file' => 'アップロードされていないか不正なデータです。']);
            }


        } else {
        // GET
            return view('admin/create')->with('success', '');

        }   
         return view('admin/create');

    }

    //アンケート集計
    public function summary(Request $request)
    {
        $msurvey = DB::table('msurveys')
                 ->get();


          return view('admin/summary')->with('msurvey',$msurvey);

    }

    //状態の反転
    public function inactive(Request $request)
    {
    $id = $request->input('id');
    $items = \App\Msurvey::where('id','=', $id)->get();
   // dd($items);
    $item=$items[0];
    $item->inactive = $item->inactive == 1  ? 0: 1;
    $item->save();
	DB::table('linechats')->delete();
	DB::table('linestates')->delete();

    return redirect('/admin/SummarySurvey');


    }

    //CSV 
    public function download(Request $request)
    {


    return "将来の拡張ため準備中";


    }


    public function delete(Request $request)
    {

    $id = $request->input('id');

    $data =\App\Mresult::where("surveyid" ,$id)->exists();
    if($data){
        \App\Mresult::where("surveyid", $id)->delete();
    }

    $data =\App\Msurveydetail::where("surveyid", $id)->exists();
    if($data){
        \App\Msurveydetail::where("surveyid", $id)->delete();
    }
    $data =\App\Msurvey::where("id", $id)->exists();
    if($data){
        \App\Msurvey::where("id", $id)->delete();
    }

    $data =\App\Msurveyselect::where("surveyid", $id)->exists();
    if($data){
        \App\Msurveyselect::where("surveyid", $id)->delete();
    }

    $data =\App\Mresultitem::where("surveyid",$id)->exists();
    if($data){
        \App\Mresultitem::where("surveyid", $id)->delete();
    }

    $data =\App\Tresult::where("surveyid",$id)->exists();
    if($data){
        \App\Tresult::where("surveyid", $id)->delete();
    }
	DB::table('linechats')->delete();
	DB::table('linestates')->delete();

    return redirect('/admin/SummarySurvey');
    }


}
