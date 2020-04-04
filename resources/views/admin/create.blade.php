@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card">
			    <h4 class="card-title">アンケート作成</h4>
                <div class="card-body bg-light ">



                    <form role="form" method="POST" action="" enctype="multipart/form-data">
		    {{--成功時のメッセージ--}}
		    @if ( $success !=="" )
		    <div class="alert alert-success">{{ $success }}</div>
		    @endif
		    @if ($errors->any())
		        <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		        </div>
		    @endif


					  <div id="drag-drop-area">
					    <div class="drag-drop-inside">
                        {!! csrf_field() !!}

		                    <div class="form-group row">
		                        <label class="col-lg-4 col-form-label text-lg-right">JSONファイルのアップロード</label>

		                        <div class="col-lg-7">
									<div class="drag-drop-info-box">
									      <p class="drag-drop-info"></p>
									      <p>JSONファイルを選択する</p>

			                            <input
			                                    type="file"
			                                    class="form-control{{ $errors->has('jsonfile') ? ' is-invalid' : '' }}"
			                                    name="jsonfile"
			                                    value="{{ old('jsonfile') }}"
			                                    required
			                            >
			                            @if ($errors->has('jsonfile'))
			                                <div class="invalid-feedback">
			                                    <strong>{{ $errors->first('jsonfile') }}</strong>
			                                </div>
			                            @endif
			                        </div>
		                        </div>
		                    </div>

	<!--
		                    <div class="form-group row">

		                        <label class="col-lg-4 col-form-label text-lg-right">JSON形式のテキスト</label>

		                        <div class="col-lg-6">
		                            <textarea  rows="5" cols="30"
		                                    class="form-control{{ $errors->has('jsontext') ? ' is-invalid' : '' }}"
		                                    name="jsontext"
		                                    value="{{ old('jsontext') }}"
		                                    required
		                            >
		                            </textarea>
		                            @if ($errors->has('jsontext'))
		                                <div class="invalid-feedback">
		                                    <strong>{{ $errors->first('jsontext') }}</strong>
		                                </div>
		                            @endif
		                        </div>
		                    </div>

	-->




		                    <div class="form-group row">
		                        <div class="col-lg-8 offset-lg-4">
		                            <button type="submit" class="btn btn-primary">
		                                登録する
		                            </button>
		                        </div>
		                    </div>

					    </div>
					  </div>
                    </form>
					<p><a href="https://yaku-ten.net/QuickSurvey/example/example-1.json">JSONファイルサンプル(画像なし) </a></p>

					<p><a href="https://yaku-ten.net/QuickSurvey/example/question0.json">JSONファイルサンプル </a></p>
					<p><a href="https://yaku-ten.net/QuickSurvey/example/question1.json">JSONファイルサンプル(画像なし) </a></p>
					<p><a href="https://yaku-ten.net/QuickSurvey/example/question2.json">JSONファイルサンプル(設問画像なし) </a></p>
					<p><a href="https://yaku-ten.net/QuickSurvey/example/question3.json">JSONファイルサンプル(設問画像のみ) </a></p>

					<br />
					<p><a href="https://lab.syncer.jp/Tool/JSON-Viewer/" target="_blank">JSON形式の構文チェック(外部サイト) </a></p>



                </div>
            </div>



        </div>
    </div>
</div>
@endsection
