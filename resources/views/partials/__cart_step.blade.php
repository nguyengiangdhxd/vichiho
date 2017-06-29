<?php

$steps = [
    [
        'title' => 'Giỏ hàng ',
        'description' => 'Bước 1 '
    ],
    [
        'title' => 'Đặt cọc & Thanh toán ',
        'description' => 'Bước 2 '
    ],
    [
        'title' => 'NM247 tiếp nhận & xử lý ',
        'description' => 'Bước 3 '
    ],
    [
        'title' => 'Nhận hàng ',
        'description' => 'Bước 4 '
    ],
];

?>
<div class="card">

    <div class="card-body">
        <div class="step">
            <ul class="nav nav-tabs nav-justified" role="tablist">

                @foreach($steps as $idx => $step)
                    <li role="step" class="@if($idx + 1 == $active) active @endif">
                        <a href="#step{{ $idx + 1  }}" id="step1-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                            <div class="icon fa fa-shopping-cart"></div>
                            <div class="heading">
                                <div class="title">{{$step['title']}}</div>
                                <div class="description">{{$step['description']}}</div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
</div>

<br>