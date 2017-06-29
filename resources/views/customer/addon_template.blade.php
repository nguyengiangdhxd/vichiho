<style>

    .nhatminh-menubar{
        text-decoration: none!important;
        font-size: 15px!important;
        position: fixed;
        height: 50px;
        background: #4ab825;
        width: 100%;
        z-index: 999999999999;
        left: 0;
        bottom: 0;
        text-align: center;
        line-height: 50px;
        /* box-shadow: 0 0 10px rgba(58, 240, 46, 0); */
        padding: 0 10px;
    }
    .nhatminh-menubar .nhatminh-button-add-to-cart{
        background: rgb(255, 255, 255);
        padding: 8px 18px;
        margin-right: 20px;
        /* border-radius: 5px; */
        text-transform: uppercase;
        color: #308c10;
    }
    .nhatminh-menubar .pull-left{
        float: left!important;
    }
    .nhatminh-menubar .pull-right{
        float: right!important;
    }
    .nhatminh-menubar .nhatminh-button-add-to-cart:hover{
        background: rgb(43, 103, 23);
        text-decoration: none!important;
        color: #fff;
    }

    .nhatminh-menubar .nhatminh-button-view-cart{
        color: #fff;
    }

    .nhatminh-menubar .nhatminh-button-view-cart:hover{
        text-decoration: underline;
    }

    .nhatminh-form-add-to-cart{
        position: fixed;
        right: 0;
        bottom: 0;
        width: 400px;
        height: auto;
        background: #fff;
        z-index: 99999999999;
        padding: 10px 30px;
        border-top: 1px solid #000;
        border-left: 1px solid #000;
        display: none;

        text-decoration: none!important;
        font-size: 15px!important;
    }

    .nhatminh-form-add-to-cart .nhatminh-form-input{
        width: 100%;
        margin-bottom: 15px;
        padding: 10px 15px;
        font-size: 14px;
    }

    .nhatminh-form-add-to-cart .nhatminh-form-textarea{
        width: 100%;
        margin-bottom: 15px;
        font-size: 14px;
        padding: 10px 15px;
    }

    .nhatminh-form-add-to-cart .nhatminh-form-button-add-to-cart{
        color: #fff;
        background: #4ab825;
        border: none;
        padding: 10px 15px;
        font-size: 15px;
    }

    .nhatminh-form-add-to-cart .nhatminh-form-button-add-to-cart:hover{
        background: #5fca3b;
        text-decoration: none!important;
    }

    .nhatminh-form-add-to-cart .nhatminh-title-form{
        margin-top: 20px;
        margin-bottom: 30px;
        font-size: 28px;
    }

    .nhatminh-notification{
        position: fixed;
        bottom: 50px;
        background: #f6f2dd;
        width: 100%;
        left: 0;
        padding: 5px 10px;
        color: #a94442;
        text-align: center;
        z-index: 9999999999999999999999999999;
        text-decoration: none!important;
        font-size: 13px!important;
    }

    .nhatminh247-preview-price{
        background: #4ab825;
        padding: 0px 15px;
        font-size: 25px;
        color: #fff;
    }

    .nhatminh247-preview-price.font-small{
        font-size: 15px!important;
    }
</style>

<input type="hidden" id="_nhatminh247-exchange-rate" value="{{$exchange_rate}}">
<input type="hidden" id="_nhatminh247-api-cart-url" value="{{ url('cart/add')  }}">

<div class="nhatminh-notification">
    <p>VUI LÒNG TẮT GOOGLE DỊCH TRƯỚC KHI CHO SẢN PHẨM VÀO GIỎ HÀNG. XIN CÁM ƠN!</p>

</div>

<div class="nhatminh-menubar">

    <span style="color: #fff;
    margin-right: 15px;
    position: absolute;
    top: 0;
    left: 10px;" class="pull-left">
        HOTLINE: 04.2262.6699 – 04.2265.6699
    </span>

    <span style="color: #fff; margin-right: 15px;" class="pull-left1" data-exchange-rage="{{$exchange_rate}}">
        Tỉ giá: {{  App\Util::formatNumber($exchange_rate) }}đ
    </span>

    <a style="margin-right: 15px;" href="{{ url('gio-hang')  }}" target="_blank" class="nhatminh-button-view-cart">Vào giỏ hàng</a>

    <a href="javascript:void(0)" class="nhatminh-button-add-to-cart" id="_add-to-cart">Thêm vào giỏ</a>

    <a style="color: #fff; margin-right: 15px;"
       href="javascript:void(0)" class="_btn-action"
       data-url="{{ url('cart/action')  }}"
       data-method="post"
       data-action="save_product">Lưu SP</a>

    <a style="color: #fff; margin-right: 15px;"
       href="javascript:void(0)" class="_btn-action"
       data-url="{{ url('cart/action')  }}"
       data-method="post"
       data-action="send_link_error">Báo lỗi</a>

</div>

<div class="nhatminh-form-add-to-cart">
    <h3 class="nhatminh-title-form">Công cụ đặt hàng NhatMinh247</h3>

    <form>
        <input autofocus class="nhatminh-form-input" type="text" placeholder="Giá sản phẩm NDT" required>

        <br>

        <input class="nhatminh-form-input" type="number" placeholder="Số lượng sản phẩm" required>

        <br>

        <textarea class="nhatminh-form-textarea" rows="5" placeholder="Nhập thuộc tính của sản phẩm (VD: màu xanh, cỡ XL)"></textarea>
        <br>
        <button class="nhatminh-form-button-add-to-cart" type="submit">ĐẶT HÀNG</button>
        <a href="{{ url('gio-hang')  }}" target="_blank">Vào giỏ hàng</a>
    </form>

</div>