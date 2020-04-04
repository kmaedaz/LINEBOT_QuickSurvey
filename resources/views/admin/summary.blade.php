@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
			    <h4 class="card-title">アンケート集計</h4>
                <div class="card-body bg-light ">



                    <table class="table table-striped table-bordered">
                     <tr>
                     <th>　</th>

                     <th>surveycode</th>
                     <th style="width:240px;font-size:90%;">件名</th>

                     <th style="width:110px;font-size:85%;">状態</th>
                     <th style="width:180px;">作成日</th>
                     <th style="width:340px;font-size:85%;">有効期間(JST)</th>

                     <th style="width:40px;"><img src="../img/web_icon.png"></th>
                     <th style="width:40px;"><img src="../img/download_24.png"></th>

                     <th style="width:40px;"　　</th>

                     <th style="width:40px;">削除</th>

                     </tr>
                    @foreach($msurvey as $value)
                     <tr>
                     <td><img src="{{ $value->imageurl }}" width="80pt"> </td>

                     <td>{{ $value->surveycode }}</td>
                     <td>{{ $value->title }}</td>

                     <td>
                        @if ( $value->inactive == 0 )
                            <a href="{{ url("/")}}/admin/inactive?id={{ $value->id }}">公開</a>
                        @else
                            <a href="{{ url("/")}}/admin/inactive?id={{ $value->id }}">非公開</a>
                        @endif
                     </td>

                     <td style="font-size:90%;">{{ $value->created_at }}</td>
                     <td style="font-size:90%;">{{ $value->from_at }}～{{ $value->to_at }}</td>

                     <td><a href="{{ url("/")}}/admin/csv?id={{ $value->id}}"><img src="../img/web_icon.png"></a></td>

                     <td><a href="{{ url("/")}}/admin/csv?id={{ $value->id}}"><img src="../img/download_24.png"></a></td>

                     <td>　　</td>

                     <td><a href="{{ url("/")}}/admin/delete?id={{ $value->id}}">削除</a></td>


                     </tr>
                    @endforeach

                    </table>





                </div>
            </div>



        </div>
    </div>
</div>
@endsection
