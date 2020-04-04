@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
		<div class="col-md-10">

			<div class="card">
			<h4 class="card-title">システム設定</h4>
			<div class="card-body bg-light  border border-dark  ">



                    <form role="form" method="POST" action="">
                        {!! csrf_field() !!}



                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">Webhook&nbsp;URL</label>

                            <div class="col-lg-6">
							   <b>{{url("/")}}/api/line/callback</b>

                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">LINE シークレット</label>

                            <div class="col-lg-6">
                                <input
                                        type="text"
                                        class="form-control{{ $errors->has('linesecret') ? ' is-invalid' : '' }}"
                                        name="linesecret"
                                        value="{{ $linesecret }}"
                                        required
                                >
                                @if ($errors->has('linesecret'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('linesecret') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">LINE トークン</label>

                            <div class="col-lg-6">
                                <input
                                        type="text"
                                        class="form-control{{ $errors->has('linetoken') ? ' is-invalid' : '' }}"
                                        name="linetoken"
                                        value="{{ $linetoken }}"
                                        required
                                >
                                @if ($errors->has('linetoken'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('linetoken') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-2">
                            </div>

                            <div class="col-lg-10">
							<hr>
							<h2> 友達追加挨拶</h2>
							<p style="font-size:90%"> 応答設定であいさつメッセージをオフにする</p>
							<p style="font-size:90%"> ブランク(空白)で無効</p>
							<p style="font-size:90%"> 有効メッセージが2個以下で自動スタート</p>
                            </div>

                        </div>


                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">友達追加挨拶メッセージ</label>
                            <div class="col-lg-6">
                                <textarea rows="3" cols="30"
                                        type="text"
                                        class="form-control{{ $errors->has('follow_message1') ? ' is-nvalid' : '' }}"
                                        name="follow_message1"
                                        
                                >{{ $follow_message1 }}</textarea>
                                @if ($errors->has('follow_message1'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('follow_message1') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">友達追加挨拶画像URL</label>

                            <div class="col-lg-6">
                                <input
                                        type="text"
                                        class="form-control{{ $errors->has('follow_imageurl1') ? ' is-invalid' : '' }}"
                                        name="follow_imageurl1"
                                        value="{{ $follow_imageurl1 }}"
                                >
                                @if ($errors->has('follow_imageurl1'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('follow_imageurl1') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">友達追加挨拶メッセージ</label>

                            <div class="col-lg-6">
                                <textarea rows="3" cols="30"
                                        type="text"
                                        class="form-control{{ $errors->has('follow_message2') ? ' is-nvalid' : '' }}"
                                        name="follow_message2"
                                        
                                >{{ $follow_message2 }}</textarea>
                                @if ($errors->has('follow_message2'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('follow_message2') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">友達追加挨拶画像URL</label>

                            <div class="col-lg-6">
                                <input
                                        type="text"
                                        class="form-control{{ $errors->has('follow_imageurl2') ? ' is-invalid' : '' }}"
                                        name="follow_imageurl2"
                                        value="{{ $follow_imageurl2 }}"
                                >
                                @if ($errors->has('follow_imageurl2'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('follow_imageurl2') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">友達追加挨拶メッセージ</label>

                            <div class="col-lg-6">
                                <textarea rows="3" cols="30"
                                        type="text"
                                        class="form-control{{ $errors->has('follow_message3') ? ' is-nvalid' : '' }}"
                                        name="follow_message3"
                                        
                                >{{ $follow_message3 }}</textarea>
                                @if ($errors->has('follow_message2'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('follow_message3') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-2">
                            </div>

                            <div class="col-lg-10">
							<hr>
                            </div>

                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">PC表示件数</label>

                            <div class="col-lg-6">
                                <input
                                        type="number"
                                        class="form-control{{ $errors->has('dispnum') ? ' is-invalid' : '' }}"
                                        name="dispnum"
                                        min=1 
                                        max =100
                                        value="{{ $dispnum }}"
                                        required
                                >
                                @if ($errors->has('dispnum'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('dispnum') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">モバイル表示件数</label>

                            <div class="col-lg-6">
                                <input
                                        type="number"
                                        class="form-control{{ $errors->has('dispnumsp') ? ' is-invalid' : '' }}"
                                        name="dispnumsp"
                                        min=1 
                                        max =100
                                        value="{{ $dispnumsp }}"
                                        required
                                >
                                @if ($errors->has('dispnumsp'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('dispnumsp') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-lg-6 offset-lg-4">
                                <button type="submit" class="btn btn-primary">
                                    登録する
                                </button>
                            </div>
                        </div>
                    </form>




















			</div>
		</div>

    </div>
</div>
@endsection
