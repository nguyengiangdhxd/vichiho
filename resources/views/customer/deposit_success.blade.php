@extends('layouts.app')
{{--@extends('layouts.app_blank')--}}

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            @include('partials/__cart_step', ['active' => 3])
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">

                <div class="card-body">
                    <h3>{{$page_title}}</h3>

                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="2">&nbsp;</th>
                            </tr>
                        </thead>

                        <tbody>

                        @if(count($orders))

                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <img class="lazy" style="float: left;
                                                margin-right: 10px;
                                                margin-top: 10px;" data-src="{{ $order->avatar  }}" width="50px" alt="">

                                        <h4>
                                            <a href="{{ url('don-hang', $order->id)  }}" title="{{$order->code}}">{{$order->code}}</a>


                                        </h4>
                                        <p>
                                            Đã đặt cọc
                                        </p>
                                    </td>
                                </tr>
                            @endforeach

                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_bottom')
    @parent

    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script>
        $(function() {
            $('.lazy').lazy();
        });
    </script>

    <script>
        $(document).ready(function(){


        });

    </script>
@endsection

