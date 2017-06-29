<?php
    $user = App\User::find(Auth::user()->id);
    $comments_public = null;
    $comments_private = null;
?>


<div class="card" id="box-chat-right">
    {{--<div class="card-header">--}}
        {{--Trao đổi về đơn hàng--}}
    {{--</div>--}}
    <div class="card-body">

        @if(!isset($scope_view) || (isset($scope_view) && $scope_view == App\Comment::TYPE_EXTERNAL))

            <?php

            $comments_public = App\Comment::where([
                'object_id' => $object_id,
                'object_type' => $object_type,
                'scope' => App\Comment::TYPE_EXTERNAL
            ])
                ->orderBy('created_at', 'desc')
                ->get();

            ?>

        <form class="___form">
            <input type="hidden" name="action" value="comment">
            <input type="hidden" name="method" value="post">
            <input type="hidden" name="url" value="{{ url('comment')  }}">
            <input type="hidden" name="object_id" value="{{$object_id}}">
            <input type="hidden" name="object_type" value="{{$object_type}}">
            <input type="hidden" name="scope_view" value="{{@$scope_view}}">
            <input type="hidden" name="scope" value="{{ App\Comment::TYPE_EXTERNAL  }}">
            <input type="hidden" name="is_public_profile" value="1">
            <input type="hidden" name="anchor" value="#anchor-box-comment">
            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
            <input type="hidden" name="response" value="partials/__comment">

            <input
                    name="message"
                    type="text"
                    style="width: 100%; margin-bottom: 20px;"
                    class="___input-action"
                    data-key-global="box-chat-external"
                    placeholder="Trao đổi thông tin...">
        </form>

        @if($comments_public)
            <ul class="comment-panel-view" style="">
            @foreach($comments_public as $comment_public)
                <li style="margin-bottom: 10px;

                    @if(in_array($comment_public->type_context, [App\Comment::TYPE_CONTEXT_ACTIVITY, App\Comment::TYPE_CONTEXT_LOG]))
                        color:grey;
                    @endif
                    ">
                    @if($comment_public->type_context != App\Comment::TYPE_CONTEXT_LOG)
                        <?php
                        $user_comment = App\User::find($comment_public->user_id);
                        ?>
                        <strong>{{$user_comment->name}}</strong>
                    @endif
                    {{$comment_public->message}}
                    <small>{{ App\Util::formatDate($comment_public->created_at)  }}</small>
                </li>
            @endforeach
            </ul>
        @endif

        <br>

        @endif


        @if(!isset($scope_view) || (isset($scope_view) && $scope_view == App\Comment::TYPE_INTERNAL))

            <?php

                $comments_private = App\Comment::where([
                    'object_id' => $object_id,
                    'object_type' => $object_type,
                    'scope' => App\Comment::TYPE_INTERNAL
                ])
                    ->orderBy('created_at', 'desc')
                    ->get();

            ?>

        <form class="___form">
            <input type="hidden" name="action" value="comment">
            <input type="hidden" name="method" value="post">
            <input type="hidden" name="url" value="{{ url('comment')  }}">
            <input type="hidden" name="object_id" value="{{$object_id}}">
            <input type="hidden" name="object_type" value="{{$object_type}}">
            <input type="hidden" name="scope_view" value="{{@$scope_view}}">
            <input type="hidden" name="scope" value="{{ App\Comment::TYPE_INTERNAL  }}">
            <input type="hidden" name="is_public_profile" value="1">
            <input type="hidden" name="anchor" value="#anchor-box-comment">
            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
            <input type="hidden" name="response" value="partials/__comment">

            <input
                    name="message"
                    type="text"
                    style="width: 100%; margin-bottom: 20px;"
                    data-key-global="box-chat-internal"
                    class="___input-action"
                    placeholder="Chat nội bộ...">
        </form>

        @if($comments_private)
            <ul class="comment-panel-view" style="">
                @foreach($comments_private as $comment_private)
                    <li style="margin-bottom: 10px;

                    @if(in_array($comment_private->type_context, [App\Comment::TYPE_CONTEXT_ACTIVITY, App\Comment::TYPE_CONTEXT_LOG]))
                            color:grey;
                    @endif
                            ">
                        @if($comment_private->type_context != App\Comment::TYPE_CONTEXT_LOG)
                            <?php
                            $user_comment = App\User::find($comment_private->user_id);
                            ?>
                            <strong>{{$user_comment->name}}</strong>
                        @endif
                        {{$comment_private->message}}
                        <small>{{ App\Util::formatDate($comment_private->created_at)  }}</small>
                    </li>
                @endforeach
            </ul>
        @endif

        @endif
        <div id="_content"></div>
    </div>
</div>

