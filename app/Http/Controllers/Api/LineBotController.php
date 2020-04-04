<?php
/*
 LINE callback
 Webhook URL 
*/
namespace App\Http\Controllers\Api;

use App\Config;
use App\Linechats;
use App\Linestate;
use App\Mquestionary;
use App\Tquestionary;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use \LINE\LINEBot\Constant\HTTPHeader;

use \LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;

use \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

use \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;

use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;

//use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
//use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\KitchenSink\EventHandler;



class LineBotController extends Controller
{

     private const NO_STATE = 0;// 何もしてない
     private const QSELECTION = 1;// ｱﾝｹｰﾄ選択待ﾁ
     private const START_WAITING = 2; //回答中
     private const ANSWERING = 3; //回答中
     private const COMPLETE = 4; //回答終了
     private const DIAGNOSIS = 5; //回答診断

     private $channelAccessToken =null;
     private $reply_token=null;

     private $displayName=null;

     private $tbl_msurvey=null; //
     private $Config=null;

    /*
    */

    /**
    * callback from LINE Message API(webhook)
    * @param Request $request
    * @throws \LINE\LINEBot\Exception\InvalidSignatureException
    */
    public function callback(Request $request)
    {
        Log::debug('An LINE callback message.');

         $this->Config = \App\Config::get()->first();
         if(!$this->Config){
                Log::debug('LINE config not fund.');
        }
        $this->channelAccessToken =$this->Config->linetoken;

        $httpClient = new CurlHTTPClient($this->channelAccessToken);
        $bot        = new LINEBot($httpClient, ['channelSecret' => $this->Config->linesecret]);

        try{
            //認証
            $signature = $request->header(LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
         
        } catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
          Log::error('parseEventRequest failed. InvalidSignatureException =>'.var_export($e, true));

        } catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
          Log::error('parseEventRequest failed. UnknownEventTypeException =>'.var_export($e, true));

        } catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
          Log::error('parseEventRequest failed. UnknownMessageTypeException =>'.var_export($e, true));

        } catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
          Log::error('parseEventRequest failed. InvalidEventRequestException =>'.var_export($e, true));
        }

        $events    = $bot->parseEventRequest($request->getContent(), $signature);
        //
        $dt = new Carbon('now');
        // 古ｲLINE作業DBﾊ削除
        \App\Linestate::where('created_at', '<', $dt->subHours(3))->delete();


        foreach ($events as $event) {
            Log::debug(print_r($event,true));
            $line_id = $event->getUserId() ;
            $response = $bot->getProfile($line_id);
            if ($response->isSucceeded()) {
                $profile = $response->getJSONDecodedBody();
                $this->displayName=$profile['displayName'];
                Log::debug(print_r($profile,true));

            }

           //Log::debug(print_r($line_id,true));

            $this->reply_token = $event->getReplyToken();
            $dt = new Carbon('now');

            //ユーザー情報取得 セッションが使えないのでDBに保存する
            $linestate = \App\Linestate::where('userid',$line_id)
                                                ->where('created_at', '>', $dt->subHours(3))
                                                ->first();

            Log::debug("linestate");
            Log::debug(print_r($linestate,true));


            if(!$linestate){
                //新規ユーザ
                $linestate = new \App\Linestate;
                $linestate->state = self::NO_STATE;
                $state=self::NO_STATE;
                $linestate->qid = 0;
                $linestate->userid =$line_id;
                $linestate->surveyid="";
                $linestate->results="";
                $linestate->save();
            } else {
                $qid=$linestate->qid ;
                $state=$linestate->state ;
            }

            $reply_message = 'ｿﾉ操作ﾊｻﾎﾟｰﾄｼﾃﾏｾﾝ｡.[' . get_class($event) . '][' . $event->getType() . ']';
            //Log::debug(print_r($this->reply_token,true));

            // todo: event ﾊ TextMessage ﾀﾞｹｼﾞｬﾅｲﾉﾃﾞ後ﾃﾞ分岐処理
            Log::debug(__LINE__.":".print_r($event->getType(),true));
        switch (true){
            //友達登録&ﾌﾞﾛｯｸ解除
            case $event instanceof LINEBot\Event\FollowEvent:

                if($event->getType()=="follow"){

                 //$bot->replyText($this->reply_token,"友達登録されたからLINE ID引っこ抜いたわー" );
                $items=[] ;
				$cnt_mes=0;
                 if( ! (trim($this->Config->follow_message1)=="")){
                        $message=$this->ReplaceMessageWord($this->Config->follow_message1);
                        $items[] = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
						$cnt_mes++;
                 }

                 if( ! (trim($this->Config->follow_imageurl1)=="")){
                        $items[] = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($this->Config->follow_imageurl1,$this->Config->follow_imageurl1);
						$cnt_mes++;
                 }

                 if( ! (trim($this->Config->follow_message2)=="")){
                        $message=$this->ReplaceMessageWord($this->Config->follow_message2);
                        $items[] = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
						$cnt_mes++;
                 }

                 if( ! (trim($this->Config->follow_imageurl2)=="")){
                        $items[] = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($this->Config->follow_imageurl2,$this->Config->follow_imageurl2);
						$cnt_mes++;
                 }

                 if( ! (trim($this->Config->follow_message3)=="")){
                        $message=$this->ReplaceMessageWord($this->Config->follow_message3);
                        $items[] = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
						$cnt_mes++;
                 }
                }
            Log::debug(__LINE__.":".print_r($cnt_mes,true));

				if($cnt_mes<3){
					//自動開始
						$follow_message=$items;
		                $this->GetQuestionaryList();
		                list($quickreplay,$items,$cnt,$qlsit)=$this->questionarylist("アンケートを番号で選んでください\n。\n\n途中でやめるときは99を入力してください。\n");

                        //アンケート一覧表示                   
                        //アンケートがあるかチェック
                        // 有効なアンケートはないときはメッセージを返信してNO_STATE
                        // 一択の場合アンケート選択START_WAITING
                        // 二以上一択の場合アンケート選択QSELECTION
                        if($qlsit==null){
                           $bot->replyText($this->reply_token,"現在、診断は行っておりません!");
                           break;
                        }   
                        if(count($this->tbl_msurvey)==1){
                            // 一択の場合アンケート選択START_WAITING
                            Log::debug(__LINE__.print_r($qlsit,true));
                            $id=$qlsit[0];
                            $count = DB::table('msurveydetails')->where('surveyid', $id)->count();
                            $linestate->qid=0 ;
                            $linestate->numques=$count ;
                            $linestate->surveyid=$id ;
                            $linestate->state =self::ANSWERING;
                            $linestate->save();
                            $this->replyMultiCarouselMessage($bot,$linestate->surveyid,$linestate->qid,$follow_message);
                           break;
                        }   
                        //アンケートセットが2セット以上ある
                        Log::debug(__LINE__.print_r(count($this->tbl_msurvey),true));
                        Log::debug(__LINE__.print_r($this->tbl_msurvey,true));


			        $columns = []; // カルーセル型カラムを追加する配列
			        $id=1;
			        foreach ($this->tbl_msurvey as $value){
			            $action = new  \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("開始する",$id);
			            // カルーセルのカラムを作成する

			            $imageurl=$value->imageurl;
			            if($imageurl=="" || $imageurl==null) {
			                $imageurl="https://yaku-ten.net/QuickSurvey/img/noimage.png";
			            } 

			            $message=$value->title;
			            if($message=="") {
			                $message="　";
			            } 

			            $column = new   \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($message, "　", $imageurl, [$action]);
			            $columns[] = $column;
			            $id++;
			        }

					// カラムの配列を組み合わせてカルーセルを作成する
					$carousel = new CarouselTemplateBuilder($columns);
					// カルーセルを追加してメッセージを作る
					$carousel_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("アンケート一覧", $carousel);
					  Log::debug(__LINE__.print_r($carousel_message,true));
					$this->replyMultiMessage($bot, $this->reply_token, $follow_message, $carousel_message);
                        $linestate->state = self::QSELECTION;
                        $linestate->save();
                    break;

				} else {
					if($cnt_mes<5){
				        $actions = array(
				            new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("診断を開始する", "診断を開始する"),
				        );

				        $message = "▼下のボタンをタップしてスタート！";
				        $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder(null, $message, null, $actions);
				        $messageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($message, $button);
						 $items[] =   $messageBuilder;
					}

				}

                $ret=$this->replyMultiMessageArray($bot, $this->reply_token, $items);

                if($event->getType()=="unfollow"){
                        // 初期状態にクリア
                        DB::table('linestates')->where('userid', '=', $line_id)->delete();
                        DB::table('linechats')->where('userid', '=', $line_id)->delete();
                }
                break;
            //ﾒｯｾｰｼﾞﾉ受信
            case $event instanceof LINEBot\Event\MessageEvent\TextMessage:
               Log::debug("ﾒｯｾｰｼﾞﾉ受信");



                $reply_message =$event->getText();
                if($reply_message=="やめる" || $reply_message=="99"){
                            $bot->replyText($this->reply_token,"アンケートはやめました。" );
                            // 初期状態にクリア
                            DB::table('linestates')->where('userid', '=', $line_id)->delete();
                            DB::table('linechats')->where('userid', '=', $line_id)->delete();
                           break;
                }

                $this->GetQuestionaryList();
                list($quickreplay,$items,$cnt,$qlsit)=$this->questionarylist("アンケートを番号で選んでください\n。\n\n途中でやめるときは99を入力してください。\n");

                        //Log::debug(__LINE__.":".print_r($items,true));
                       //Log::debug(__LINE__.":".print_r($qlsit,true));


                switch ($state){
                    case self::NO_STATE:
//                list($quickreplay,$items,$cnt,$qlsit)=$this->questionarylist("アンケートを番号で選んでください\n。\n\n途中でやめるときは99を入力してください。\n");

                        //アンケート一覧表示                   
                        //アンケートがあるかチェック
                        // 有効なアンケートはないときはメッセージを返信してNO_STATE
                        // 一択の場合アンケート選択START_WAITING
                        // 二以上一択の場合アンケート選択QSELECTION
                        if($qlsit==null){
                           $bot->replyText($this->reply_token,"現在、診断は行っておりません!");
                           break;
                        }   
                        if(count($this->tbl_msurvey)==1){
                            // 一択の場合アンケート選択START_WAITING
                            Log::debug(__LINE__.print_r($qlsit,true));
                            $id=$qlsit[0];
                            $count = DB::table('msurveydetails')->where('surveyid', $id)->count();
                            $linestate->qid=0 ;
                            $linestate->numques=$count ;
                            $linestate->surveyid=$id ;
                            $linestate->state =self::ANSWERING;
                            $linestate->save();
                            $this->replyMultiCarouselMessage($bot,$linestate->surveyid,$linestate->qid);
                           break;
                        }   
                        //アンケートセットが2セット以上ある
                        Log::debug(__LINE__.print_r(count($this->tbl_msurvey),true));
                        Log::debug(__LINE__.print_r($this->tbl_msurvey,true));


        $columns = []; // カルーセル型カラムを追加する配列
        $id=1;
        foreach ($this->tbl_msurvey as $value){
            $action = new  \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("開始する",$id);
            // カルーセルのカラムを作成する

            $imageurl=$value->imageurl;
            if($imageurl=="" || $imageurl==null) {
                $imageurl="https://yaku-ten.net/QuickSurvey/img/noimage.png";
            } 

            $message=$value->title;
            if($message=="") {
                $message="　　";
            } 

            $column = new   \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($message, "　", $imageurl, [$action]);
            $columns[] = $column;
            $id++;
        }

//


//
    // カラムの配列を組み合わせてカルーセルを作成する
    $carousel = new CarouselTemplateBuilder($columns);
    // カルーセルを追加してメッセージを作る
    $carousel_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("アンケート一覧", $carousel);
      Log::debug(__LINE__.print_r($carousel_message,true));
    $this->replyMultiMessage($bot, $this->reply_token, $carousel_message);

/*
//                        $bot->replyText($this->reply_token,$mes);
                        Log::debug(__LINE__.":".print_r($items,true));
                        Log::debug(__LINE__.":".print_r($quickreplay,true));

                        $ret=$this->createQuickReplyMessageAction($quickreplay,$items);
                        Log::debug(print_r($ret,true));

                        $this->simpleReplyMessage($this->reply_token, $ret );
//
*/

                        $linestate->state = self::QSELECTION;
                        $linestate->save();
                    break;

                    //アンケート選択
                    case self::QSELECTION:
                            Log::debug(__LINE__.":".print_r(self::QSELECTION,true) );

                        if($reply_message>count($this->tbl_msurvey) || (!is_numeric($reply_message)) ){
                                $bot->replyText($this->reply_token,$reply_message."は選択できないよ｡");
                            } else {


                                // 「はい」ボタン
                                 $yes_post = new  \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("はい","はい");

                                // 「いいえ」ボタン
                                 $no_post = new  \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("いいえ","いいえ");

                                // Confirmテンプレートを作る
                                $confirm = new ConfirmTemplateBuilder("アンケートを開始します", [$yes_post, $no_post]);
                                // Confirmメッセージを作る
                                $confirm_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("確認", $confirm);
                                
                                $message = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    
                                $message->add($confirm_message);
                                // リプライTokenを付与して返信する
                                $res = $bot->replyMessage($this->reply_token, $message);


                                //状態保存
                                $id=$this->tbl_msurvey[intval($reply_message)-1]->id;
                                Log::debug(__LINE__.":".print_r($id,true) );
                                $count = DB::table('msurveydetails')->where('surveyid', $id)->count();
                                $linestate->surveyid=$id ;
                                $linestate->state =self::START_WAITING;
                                $linestate->qid=0 ;
                                $linestate->numques=$count ; //設問数
                                $linestate->save();
                            }
                    break;
                    case self::START_WAITING:

                           if($reply_message=="1" || $reply_message=="はい"){
                                $this->replyMultiCarouselMessage($bot,$linestate->surveyid,$linestate->qid);
                                $linestate->state =self::ANSWERING;
                                $linestate->qid=0 ;
                                $linestate->save();

                            } else if($reply_message=="99" || $reply_message=="いいえ") {
                                //やめる
                                $bot->replyText($this->reply_token,"アンケートはやめました。" );
                                  \App\Linestate::where('userid', $line_id)->delete();
                            }

                    break;

                    // 回答待ち
                    case self::ANSWERING:
                            Log::debug(__LINE__."ANSWERING");

                            // 回答数ﾁｪｯｸ 全問回答ﾅﾗ 完了回答登録処理
                            $is_answer=false;

                            if(is_numeric($reply_message)){
                                // 番号選択
                                $disnum=$reply_message-1;
                                $msurveyselects = DB::table('msurveyselects')
                                             ->where('surveyid',$linestate->surveyid)
                                             ->where('qid',$qid)
                                             ->where('disnum',$disnum)
                                             ->first();
                                if($msurveyselects){
                                    $is_answer=true;
                                    Log::debug(__LINE__.print_r($linestate->surveyid,true));
                                    Log::debug(__LINE__.print_r($qid,true));
                                    Log::debug(__LINE__.print_r($disnum,true));

                                    //Log::debug(__LINE__.print_r($msurveyselects,true));

                                    $linechat = new \App\Linechat;
                                    $linechat->userid = $line_id;
                                    $linechat->surveyid =$linestate->surveyid;
                                    $linechat->qid = $linestate->qid;
                                    $linechat->results =intval($reply_message)-1;

                                    $linechat->eval =$msurveyselects->eval; //
                                    $linechat->save();
                                }
                            } else {
                                    Log::debug(__LINE__.":".$linestate->surveyid);
                                    Log::debug(__LINE__.":". $linestate->qid);
                                    Log::debug(__LINE__.":".$reply_message);

                                $msurveyselects = DB::table('msurveyselects')
                                             ->where('surveyid',$linestate->surveyid)
                                             ->where('qid', $linestate->qid)
                                             ->where('title',$reply_message)
                                             ->first();
                                if($msurveyselects){
                                    $is_answer=true;
                                    Log::debug(__LINE__.print_r($linestate->surveyid,true));
                                    Log::debug(__LINE__.print_r($qid,true));
                                    Log::debug(__LINE__.print_r($msurveyselects,true));
                                    $linechat = new \App\Linechat;
                                    $linechat->userid = $line_id;
                                    $linechat->surveyid =$linestate->surveyid;
                                    $linechat->qid = $linestate->qid;
                                    $linechat->results =$msurveyselects->disnum;

                                    $linechat->eval =$msurveyselects->eval; //
                                    $linechat->save();
                                }
                            }

                                if(!$is_answer){
                                    // 不明回答
                                    Log::debug(__LINE__."不明回答");

                                       $linestate->qid=$linestate->qid;
                                    //質問再表示
                                    $this->replyMultiCarouselMessage($bot,$linestate->surveyid,$linestate->qid);
                                    $bot->replyText($this->reply_token,"わからない❓\n" );
                                    break ;
                                } else{
                                        Log::debug(__LINE__."回答");
                                       $linestate->qid=$linestate->qid+1;
                                        Log::debug(__LINE__.$linestate->qid);

                                }
                                    $linestate->save();

                                /*
                                最終回答ｶﾁｪｯｸ
                                */
                                if($linestate->qid == ($linestate->numques)){
                                    //最終回答
                                    Log::debug(__LINE__."最終回答");
                                    $flg= FALSE ; //送信確認はしない
                                    if( !$flg){
                                        Log::debug(__LINE__.":".print_r($flg,true));

                                        //確認なし
                                        //回答の評価値を算出
                                        $answer_eval = DB::table('linechats')
                                        ->where('surveyid', $linestate->surveyid)
                                        ->sum('eval');
                                        Log::debug(__LINE__.":answer_eval=".print_r($answer_eval,true));

                                        // 該当の回答結果を表示する。
                                         $this->replyDiagnosisResult($bot, $linestate->surveyid,$answer_eval) ;


                                        // 結果を保存
                                        $this->ResultSave($linestate->surveyid, $line_id);

                                        // 初期状態にクリア
                                        DB::table('linestates')->where('userid', '=', $line_id)->delete();
                                        DB::table('linechats')->where('userid', '=', $line_id)->delete();
                                    
                                    } else {
                                        $items = array();
                                        $value=array();
                                        $value["label"]="[1]送信する";
                                        $value["text"]="1";
                                        $items[]=$value;
                                        $value=array();
                                        $value["label"]="[9]やめる";
                                        $value["text"]="9";
                                        $items[]=$value;
                                        $ret=$this->createQuickReplyMessageAction("アンケートは完了しました｡\n\n1.アンケートを送信する \n9.アンケートやめる",$items);
                                        $this->simpleReplyMessage($this->reply_token, $ret );
                                        Log::debug("createQuickReplyMessageAction;;".print_r($ret,true) );
                                        $linestate->state =self::COMPLETE;
                                        $linestate->save();

                                    }
                                
                                } else {
                                    //Log::debug(__LINE__.":".print_r($linestate->surveyid,true) );
                                    //Log::debug(__LINE__.":".print_r($linestate->qid,true) );
                                    $this->replyMultiCarouselMessage($bot,$linestate->surveyid,$linestate->qid);
                                    //$ret=$this->createQuickReplyMessageAction($choice,$items);
                                    //$this->simpleReplyMessage($this->reply_token, $ret );

                                }

///------------------------------------
                            break;
                            if( ($reply_message) >0 && ($reply_message<$no+1)) {
                                //回答保存
                                $linechat = new \App\Linechat;
                                $linechat->userid = $line_id;
                                $linechat->surveyid =$linestate->surveyid;
                                $linechat->qid =$qid;
                                $linechat->results =intval($reply_message)-1;


                                // 設問文取得
                                //Log::debug(__LINE__.print_r($linestate->surveyid,true));

                                $disnum=$reply_message-1;
                                $msurveydetails = DB::table('msurveyselects')
                                             ->where('surveyid',$linestate->surveyid)
                                             ->where('qid',$qid)
                                             ->where('disnum',$disnum)
                                             ->first();

                                Log::debug(__LINE__.print_r($linestate->surveyid,true));
                                Log::debug(__LINE__.print_r($qid,true));
                                Log::debug(__LINE__.print_r($disnum,true));

                                //Log::debug(__LINE__.print_r($msurveydetails,true));

                                $linechat->eval =$msurveydetails->eval; //
                                $linechat->save();

                                //Log::debug(__LINE__.":".print_r($linechat->qid,true));
                                //Log::debug(__LINE__.":".print_r($linestate->numques,true));



                                $linestate->save();

                            } else {
                                    // 不明回答
                                       $linestate->qno=$linestate->qno;
                                    //質問再表示
                                    $this->replyMultiCarouselMessage($bot,$linestate->surveyid,$linestate->qid);

//                                    $ret=$this->createQuickReplyMessageAction("わからない\n");
//                                    $this->simpleReplyMessage($this->reply_token, $ret );
                                    $bot->replyText($this->reply_token,"わからない❓\n" );

                            }
                    break;

                    case self::COMPLETE:


                           if($reply_message=="1"){
                                $this->questionRegist($linestate->serial,$line_id);
                                $bot->replyText($this->reply_token,"ご協力ありがとうございました。" );
                                  \App\Linestate::where('userid', $line_id)->delete();
                                  \App\Linechat::where('userid', $line_id)->delete();

                            } else if($reply_message=="9") {
                                $bot->replyText($this->reply_token,"アンケート送信はやめました" );
                                \App\Linestate::where('userid', $line_id)->delete();
                                \App\Linechat::where('userid', $line_id)->delete();

                            }



                    break;

                
                    default;
                        $bot->replyText($this->reply_token,"ちょっとわからないよ\n");
                    break;

                    }
                    

               break;

            //位置情報ﾉ受信
            case $event instanceof LINEBot\Event\MessageEvent\LocationMessage:
                //$service = new RecieveLocationService($bot);
                //$reply_message = $service->execute($event);
                $reply_message = $event->getText();
                break;

            //選択肢ﾄｶ選ﾝﾀﾞ時ﾆ受信ｽﾙｲﾍﾞﾝﾄ
            case $event instanceof LINEBot\Event\PostbackEvent:
                break;
            //ﾌﾞﾛｯｸ
            case $event instanceof LINEBot\Event\UnfollowEvent:
                break;
            default:
                $body = $event->getEventBody();
                logger()->warning('Unknown event. ['. get_class($event) . ']', compact('body'));
        }


        }
    }

    private function ReplaceMessageWord($mes ){
        $body = str_replace("%Nackname%", $this->displayName, $mes);
        $body = str_replace("%Nickname%", $this->displayName, $body);
        return $body;
    }

    //結果を保存
    private function ResultSave($surveyid,$userid ){
        // ユニークID
        $max_id = \App\Tresult::max('id');
        srand(time());
        do {
            $answerid = rand(1,9).sprintf("%05d",$max_id).rand(10,999);
            $exists =  \App\Tresult::where('answerid',$answerid)->exists();
        } while ($exists);

        $linechats =  \App\Linechat::where([['surveyid','=',$surveyid],['userid','=',$userid]])->orderBy('qid','ASC')->get();
        //Log::debug(__LINE__.":linechats=".print_r($linechats,true));

      foreach($linechats as $value) {
                $tresult = new \App\Tresult;
                $tresult->answerid = $answerid;
                $tresult->surveyid =$surveyid;
                $tresult->qid=$value->qid;
                $tresult->userid =$userid;
                $tresult->results=$value->results;
                $tresult->save();
      }
    
    }

    //TEST中
     private function QuickReplyButton($bot, $title,$message) {

            // 「はい」ボタン
            $action1 = new  \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("はい","はい");
            $yes_post =   new  \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("はい","はい","aa",[$action1]); 
            // 「いいえ」ボタン
            // 「はい」ボタン
            $action2 =  new  \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("いいえ","いいえ");
            $no_post =  new  \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("いいえ","いいえ","aa",[$action2]); 
            // Confirmテンプレートを作る
            $confirm = new            \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($message, [$yes_post, $no_post]);
            // Confirmメッセージを作る
            $confirm_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($title, $confirm);
            //Log::debug(__LINE__.print_r($confirm_message,true) );

            $response = $bot->replyMessage($this->reply_token , $confirm_message);
            if(!$response->isSucceeded()){
                Log::debug(__LINE__.'Failed! '. $response->getHTTPStatus() . ' :'.$response->getRawBody());
            }
    }

    // 診断結果を表示する
     private function replyDiagnosisResult($bot, $surveyid,$eval) {


        $mresults = DB::table('mresults')
                        ->where([
                        ['surveyid', '=', $surveyid],
                        ['eval', '<', ($eval+1)]
                        ])  
                     ->orderBy('eval','DESC')
                     ->first();

        //Log::debug(__LINE__."mresults=".print_r($eval,true) );
        //Log::debug(__LINE__."mresults=".print_r($mresults,true) );
    $mresultsid =$mresults->id;
    $mresultitems = DB::table('mresultitems')
                    ->where([
                    ['surveyid', '=', $surveyid],
                    ['mresultsid', '=', $mresultsid]
                    ])  
                 ->get();

    $columns = []; // カルーセル型カラムを追加する配列
    foreach($mresultitems as $value) {
        $action = new   \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("詳しく見る",$value->link_url);
        // カルーセルのカラムを作成する
        if($value->imageurl=="") {
            $imageurl=null;
        }  else {
            $imageurl=$value->imageurl;

        }
        if($value->message=="") {
            $message="　";
        }  else {
            $message=$this->ReplaceMessageWord($value->message);

        }

        if($value->title=="") {
            $title="　";
        }  else {
            $title=$this->ReplaceMessageWord($value->title);

        }


        $column = new   \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($title, $message." ", $imageurl, [$action]);

        $columns[] = $column;

    }

//


//
    // カラムの配列を組み合わせてカルーセルを作成する
    $carousel = new CarouselTemplateBuilder($columns);
    // カルーセルを追加してメッセージを作る
    $carousel_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($title, $carousel);
    $this->replyMultiMessage($bot, $this->reply_token, $carousel_message);

    }
    
    // Buttonsテンプレートを送信。引数はLINEBot、返信先、代替テキスト、画像URL、タイトル、本文、アクション(可変長引数)
     private function replyButtonsTemplate($bot, $replyToken, $alternativeText,$imageUrl,$title,$text, ...$actions) {
      // アクションを格納する配列
      $actionArray = array();
      // アクションをすべて追加
      foreach($actions as $value) {
        array_push($actionArray, $value);
      }
      //TemplateMessageBuilderの引数は代替テキスト、ButtonTemplateBuilder

      $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
        $alternativeText,
        // ButtonTemplateBuilderの引数はタイトル、本文
        // 画像URL、アクションの配列
        $imageurl=null,
        new \LINE\LINEBot\MessageBuilder\ButtonTemplateBuilder($title,$text,$imageUrl,$actionArray)
      );

//	  Log::debug(__LINE__.': '. print_r($builder,true) );

      $response = $bot->replyMessage($replyToken, $builder);
      if(!$response->isSucceeded()){
        error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
      }
    }



    //指定　設問を画像・テキスト、選択肢をカールセールで画像付きで表示
    // 
    private function replyMultiCarouselMessage($bot,$msurveyid,$qid,$message=null){
             Log::debug(__LINE__.'msurveyid='. $msurveyid);
             Log::debug(__LINE__.'qid='. $qid);
            // 設問文取得
            $msurveydetails = DB::table('msurveydetails')
                         ->where('surveyid','=',$msurveyid)
                         ->where('disnum','=',$qid)
                         ->orderBy('disnum', 'ASC')
                         ->first();

            // 選択肢
            $msurveyselects = DB::table('msurveyselects')
                         ->where('surveyid','=',$msurveyid)
                         ->where('qid','=',$msurveydetails->disnum)
                         ->orderBy('disnum', 'ASC')
                         ->get();
        $items=[] ;

		if(is_array($message)){
	       foreach ($message as $value){
		        $items[]=$value;
			}
		}

        $type=$msurveydetails->type;

        if($type == "1"  ){
            if(!($msurveydetails->message =="") ){
                $items[] = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($msurveydetails->message." 　　  ");
            }
            if(!($msurveydetails->imageurl =="") ){
                $items[] = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($msurveydetails->imageurl,$msurveydetails->imageurl);
            }
        } else {
            if(!($msurveydetails->imageurl =="") ){
                $items[] = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($msurveydetails->imageurl,$msurveydetails->imageurl);
            }
            if(!($msurveydetails->message =="") ){
                $items[] = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($msurveydetails->message."　　 ");
            }
        }
        $columns = []; // カルーセル型カラムを追加する配列
        $id=1;
        foreach ($msurveyselects as $value){


            $message = $value->message;
            $title   = $value->title;

            if( $title == "" ) {
                $ans=$id;
            } else {
                $ans=$title;
            }

            $action = new  \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder($value->button_title,$ans);
            // カルーセルのカラムを作成する
            $imageurl=$value->imageurl;

            if($imageurl=="" ) {
                $imageurl=null;
            } 
            if($message=="") {
                $message="　";
            } 
            if($title=="") {
                $title="　";
            } 

            $column = new   \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder( $title,$message, $imageurl, [$action]);
            $columns[] = $column;
            $id++;
        }

        // カラムの配列を組み合わせてカルーセルを作成する
        $carousel = new CarouselTemplateBuilder($columns);
        // カルーセルを追加してメッセージを作る
        $items[] = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($msurveydetails->message."　", $carousel);
        //Log::debug(__LINE__.":=".print_r($items,true));
        $ret=$this->replyMultiMessageArray($bot, $this->reply_token, $items);
    
    }


    //複数のメッセージをまとめて返信。引数はLINEBot、返信先、メッセージ(可変長引数)
    private function replyMultiMessageArray($bot, $replyToken, $msgs) {
      //MultiMessageBuilderをインスタンス化
      $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
      // ビルダーにメッセージをすべて追加
      foreach($msgs as $value){
        $builder->add($value);
      }
      $response = $bot->replyMessage($replyToken,$builder);
      if(!$response->isSucceeded()){
         Log::debug('Failed! '. $response->getHTTPStatus() . ' :'.$response->getRawBody());

      }
    }


    //複数のメッセージをまとめて返信。引数はLINEBot、返信先、メッセージ(可変長引数)
    private function replyMultiMessage($bot, $replyToken, ...$msgs) {
      //MultiMessageBuilderをインスタンス化
      $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
      // ビルダーにメッセージをすべて追加
      foreach($msgs as $value){
        $builder->add($value);
      }
      $response = $bot->replyMessage($replyToken,$builder);
      if(!$response->isSucceeded()){
         Log::debug('Failed! '. $response->getHTTPStatus() . ' :'.$response->getRawBody());

      }
    }

    private function simpleReplyMessage($replyToken, $message )
    {
        $header = array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $this->channelAccessToken,
        );
        $body=array('replyToken' => $replyToken,'messages' => $message);



        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $header),
                'content' => json_encode($body)
            ],
        ]);
//        Log::debug("header:".print_r($header,true));
//        Log::debug(__LINE__."simpleReplyMessage:".json_encode($message));

        $response = @file_get_contents('https://api.line.me/v2/bot/message/reply', false, $context);
        if (strpos($http_response_header[0], '200') === false) {
            http_response_code(500);
            //error_log('Request failed: ' . $response);
        }
        Log::debug("simpleReplyMessage:::".print_r($http_response_header,true));
        return  $response;
    }

    private function replyCarouselTemplate($bot, $replyToken, $alternativeText, $columnArray) {
          $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
          $alternativeText,
          new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder (
           $columnArray)
          );
          $response = $bot->replyMessage($replyToken, $builder);
          if (!$response->isSucceeded()) {
            //error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
            Log::debug($response->getRawBody());

          }
        }


    private function createQuickReplyMessageAction($message,$quickrReplyMessage ){
        $items= array();
        foreach ($quickrReplyMessage as $item) {
         $data=array();
         $data["type"]= "action";
         $data["action"]= array(
                            'type' => 'message',
                            'label' => $item['label'],
                            'text' => $item['text'],
                        );
        $items[]=$data;
        }   

        $send_messages = array(
            array(
            "type" => "text",
           "text" => $message,
                'quickReply' => array(
                    'items' =>  $items
                )
            )
        );

        return $send_messages;
    }

   private function replyMessageText($send_messages, $channel_access_token){
        $reply_message = array(
            'replyToken' => $this->reply_token,
            'messages' => $send_messages
        );

        return $bot->replyMessage($reply_message, $channel_access_token);
    }
     private function GetQuestionaryList() {
        $now = date('Y-m-d h:i:s e');
        //$now = localtime();

        Log::debug(__LINE__.":".print_r($now,true));

        $this->tbl_msurvey = DB::table('msurveys')
                     ->where([['inactive','=',0],['from_at','<',Carbon::now()],['to_at','>',Carbon::now()]])
                     ->orderBy('created_at', 'DESC')
                     ->get();


    }
    /*
    * ｱﾝｹｰﾄ一覧表示
    */
     private function questionarylist($mes) {
        $now = date('Y-m-d h:i:s e');
        //$now = localtime();

        Log::debug(__LINE__.":".print_r($now,true));

        $mquestionary = DB::table('msurveys')
                     ->where([['inactive','=',0],['from_at','<',Carbon::now()],['to_at','>',Carbon::now()]])
                     ->distinct()
                     ->select('title','id','created_at')
                     ->orderBy('created_at', 'DESC')
                     ->get();
        $no=1;
        $data=array();
        $items = array();
        $questionarylist=$mes."\n";
        foreach ($mquestionary as $value){
            $questionarylist.="[".$no ."]".$value->title."\n";
            $data[]=$value->id;
            $item=array();
            $item["label"]="[".$no."]";
            $item["text"]= $no;

            $items[]=$item;
            $no++;

        }

        return array ($questionarylist,$items,$no-1,$data);

    }


    /*
        全角--> 半角
    */
     private function convert_hankau($string) {
        return( mb_convert_kana($string, 'kvrn'));
    }

    /*
        ｱﾝｹｰﾄﾆ全問回答ｼﾀｶ判定
    */

     private function is_complete_question($serial, $line_id) {
        Log::debug("282".$serial);

        $mcount = DB::table('mquestionarys')
                     ->where('serial','=',$serial)
                     ->count();

        $linecnt = DB::table('linechats')
                     ->distinct()
                     ->select('serial','lineuserid','qid')
                     ->where('serial',$serial)
                     ->where('lineuserid',$line_id)
                     ->count();
        Log::debug("291".$mcount);
        Log::debug("292".$linecnt);

        if($linecnt==$mcount){
            return true;
        } 
            return false;

    }


     private function questionRegist($serial, $line_id) {
        $linechat = DB::table('linechats')
                     ->distinct()
                     ->select('serial','lineuserid','qid','choice')
                     ->where('serial',$serial)
                     ->where('lineuserid',$line_id)
                     ->get();

        list($usec, $sec) = explode(' ', microtime());
        mt_srand($sec + $usec * 1000000);

        $uniqueid= mt_rand(); //回答ｺﾞﾄﾆﾕﾆｰｸIDｦ設定
        foreach ($linechat as $value){

                Log::debug("349.".print_r($value,true));
    
               $tquestionary = new \App\Tquestionary;
                $tquestionary->serial=$serial;
                $tquestionary->uniqueid=$uniqueid;
                $tquestionary->user= $line_id;
                $tquestionary->qid=str_replace("`","",$value->qid);
                $tquestionary->choice=$value->choice;
                $tquestionary->info="line";
                $tquestionary->save();

        }

    }



}
