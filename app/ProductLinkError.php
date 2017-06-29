<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLinkError extends Model
{
    protected $table = 'product_link_error';

    /**
     * @param $create_user_id
     * @param $site
     * @param $link
     * @return bool
     */
    public static function isExists($create_user_id, $site, $link){
        $row = self::where([
            'create_user_id' => $create_user_id,
            'site' => $site,
            'link' => $link,
        ])->first();
        if($row instanceof ProductLinkError){
            return $row;
        }
        return false;
    }
}
