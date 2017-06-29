<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    const TYPE_EXTERNAL = 'EXTERNAL';
    const TYPE_INTERNAL = 'INTERNAL';
    const TYPE_NONE = 'NONE';

    const TYPE_CONTEXT_CHAT = 'CHAT';
    const TYPE_CONTEXT_ACTIVITY = 'ACTIVITY';
    const TYPE_CONTEXT_LOG = 'LOG';


    const TYPE_OBJECT_ORDER = 'ORDER';
    const TYPE_OBJECT_ORDER_ITEM = 'ORDER_ITEM';

    public static function createComment(User $create, $object, $message, $scope, $type_context, $parent_object = null){
        $object_id = null;
        $object_type = null;
        if($object instanceof Order){
            $object_id = $object->id;
            $object_type = self::TYPE_OBJECT_ORDER;
        }else if($object instanceof OrderItem){
            $object_id = $object->id;
            $object_type = self::TYPE_OBJECT_ORDER_ITEM;
        }

        $parent_object_id = null;
        $parent_object_type = null;
        if($parent_object instanceof Order){
            $parent_object_id = $parent_object->id;
            $parent_object_type = self::TYPE_OBJECT_ORDER;
        }

        $comment = new self();
        $comment->user_id = $create->id;
        $comment->object_id = $object_id;
        $comment->object_type = $object_type;

        $comment->parent_object_id = $parent_object_id;
        $comment->parent_object_type = $parent_object_type;

        $comment->scope = $scope;
        $comment->message = $message;
        $comment->type_context = $type_context;
        return $comment->save();
    }
}
