<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\ProductFavorite;
use App\ProductLinkError;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Exchange;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Cart;

class AddonController extends Controller
{

    public $html = '';

    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function executeAction(Request $request){

        $data_send = Request::all();
        $action = '__' . $data_send['action'];
        $this->$action($data_send);

        return Response::json([
            'html' => $this->html,
            'success' => true,
        ]);
    }

    private function __save_product($data_send){
        $user_id = Auth::user()->id;

        $this->html = $this->__addon_alert_template(
            true,
            'Lưu sản phẩm thành công',
            '<a href="' . url('san-pham-da-luu') . '">>> Xem danh sách sản phẩm đã lưu</a>'
        );

        $row = ProductFavorite::isExists($user_id, $data_send['site'], $data_send['current_url']);
        if($row instanceof ProductFavorite){
            $row->updated_at = date('Y-m-d H:i:s');
        }else{
            $row = new ProductFavorite();
        }
        $row->user_id = $user_id;
        $row->site = $data_send['site'];
        $row->link = $data_send['current_url'];
        $row->avatar = $data_send['avatar'] ? urldecode($data_send['avatar']) : '';
        $row->product_name = $data_send['product_name'];
        return $row->save();
    }

    private function __send_link_error($data_send){
        $this->html = $this->__addon_alert_template(
            true,
            'Gửi báo lỗi thành công',
            'Cám ơn bạn đã gửi thông báo về link sản phẩm không đặt được hàng. 
            Chúng tôi sẽ tiến hành sửa lỗi này trong thời gian sớm nhất.
            Xin lỗi về sự bất tiện này!'
        );

        $create_user_id = Auth::user()->id;

        $row = ProductLinkError::isExists($create_user_id, $data_send['site'], $data_send['current_url']);
        if($row instanceof ProductLinkError){
            $row->updated_at = date('Y-m-d H:i:s');
        }else{
            $row = new ProductLinkError();
        }
        $row->create_user_id = $create_user_id;
        $row->site = $data_send['site'];
        $row->link = $data_send['current_url'];
        return $row->save();
    }

    public function getInitData(){
        $exchange_rate = Exchange::getExchange();

        $view = View::make('customer/addon_template', [
            'exchange_rate' => $exchange_rate
        ]);
        $html = $view->render();

        $js_view = View::make('customer/addon_content_script', [
            'exchange_rate' => $exchange_rate
        ]);
        $content_script = $js_view->render();

        return Response::json([
            'html' => $html,
            'exchange_rate' => $exchange_rate,
            'content_script' => $content_script
        ]);
    }

    public function get_template1(Request $request){
        $exchange_rate = Exchange::getExchange();

        $view = View::make('customer/addon_template', [
            'exchange_rate' => $exchange_rate
        ]);
        $html = $view->render();


        return Response::json([
            'html' => $html,
            'exchange_rate' => $exchange_rate
        ]);
    }

    /**
     * @author vanhs
     * @desc API them san pham vao gio hang
     * @param Request $request
     * @return mixed
     */
    public function addCart(Request $request){
        $not_login = Auth::guest();
        $params = Request::all();

        if($not_login):
            $html = $this->__addon_alert_template(
                false,
                'Bạn chưa đăng nhập vào hệ thống',
                'Vui lòng <a style="color:blue;" target="_blank" href="' . url('login') . '">đăng nhập</a> vào hệ thống để tiến hành đặt hàng!'
            );
            return response()->json(['html' => $html]);
        endif;

        if(Cart::addCart($params)):
            $price = $this->__get_price($params['price_origin'], $params['price_promotion']);
            $html = $this->__addon_alert_template(
                true,
                'Thêm SP vào giỏ hàng thành công',
                'Số tiền của sản phẩm phải trả: <span style="color:#a8041f; font-weight: bold;">' . $price . '</span> NDT',
                '<button class="next" type="button" onclick="document.getElementById(\'confirm_main\').parentNode.removeChild(document.querySelectorAll(\'.book-nhatminh247\')[document.querySelectorAll(\'.book-nhatminh247\').length - 1])"><span>Tiếp tục mua hàng</span></button><a class="cart" href="' . url('gio-hang') . '">Vào giỏ hàng</a>'
            );
            return response()->json(['html' => $html]);
        endif;

        $html = $this->__addon_alert_template(
            false,
            'NhatMinh247 Thông báo',
            'Có lỗi xảy ra khi thêm sản phẩm vào giỏ. Vui lòng thử lại!');
        return response()->json(['html' => $html]);
    }

    private function __get_price($price_origin, $price_promotion){
        return $price_origin > $price_promotion
            ? $price_promotion : $price_origin;
    }

    private function __addon_alert_template($success, $header = '', $body = '', $footer = ''){
        $view = View::make('customer/add_to_cart_success', [
            'success' => $success,
            'header' => $header,
            'body' => $body,
            'footer' => $footer,
        ]);
        return $view->render();
    }

    /**
     * @author vanhs
     * @desc Cung cap ti gia cho cong cu dat hang
     * @return int
     */
    public function getExchange(){
        return Exchange::getExchange();
    }
}
