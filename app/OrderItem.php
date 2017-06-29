<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_item';

    /**
     * moc gia' de biet la san pham co phai la phu kien hay khong: > 10 la phu kien, < 10 la san pham thuong
     */
    public $price_item_assess = 10;

    public function getItemsInOrder($order_id){
        return $this->newQuery()->where([
            'order_id' => $order_id
        ])->get();
    }

    /**
     * @desc Ham kiem tra xem san pham la phu kien hay san pham binh thuong
     * @param $exchange
     * @return bool
     */
    public function checkItemAssess($exchange){
        if($this->price < $exchange * $this->price_item_assess){
            return true;
        }
        return false;
    }

    public function getPriceCalculator(){
        if($this->price > $this->price_promotion):
            return $this->price_promotion;
        endif;

        return $this->price;
    }



}
