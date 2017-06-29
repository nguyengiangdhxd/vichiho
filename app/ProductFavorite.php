<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductFavorite extends Model
{
    protected $table = 'product_favorite';

    /**
     * @param $user_id
     * @param $site
     * @param $link
     * @return bool
     */
    public static function isExists($user_id, $site, $link){
        $row = self::where([
            'user_id' => $user_id,
            'site' => $site,
            'link' => $link,
        ])->first();
        if($row instanceof ProductFavorite){
            return $row;
        }
        return false;
    }
}
