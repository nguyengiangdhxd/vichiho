//=========== begin config =========

//=========== end config =========

var Helper = {
    resizeImage: function(image){
        return image.replace(/[0-9]{2,3}[x][0-9]{2,3}/g, '150x150');
    },
    getPriceFromString: function(string){
        var price = 0;
        try{
            price = string.replace('¥', '').trim();
        }catch (e){

        }
        return price;
    },
    getBackgroundImageOfDiv: function(element){
        // Get the image id, style and the url from it
        var img = element,
            style = img.currentStyle || window.getComputedStyle(img, false),
            bi = style.backgroundImage.slice(4, -1);

        // For IE we need to remove quotes to the proper url
        bi = style.backgroundImage.slice(4, -1).replace(/"/g, "");

        return bi;
    },
    getURLParameters: function(paramName) {
        var sURL = window.document.URL.toString();
        if (sURL.indexOf("?") > 0)
        {
            var arrParams = sURL.split("?");
            var arrURLParams = arrParams[1].split("&");
            var arrParamNames = new Array(arrURLParams.length);
            var arrParamValues = new Array(arrURLParams.length);

            var i = 0;
            for (i = 0; i<arrURLParams.length; i++)
            {
                var sParam =  arrURLParams[i].split("=");
                arrParamNames[i] = sParam[0];
                if (sParam[1] != "")
                    arrParamValues[i] = unescape(sParam[1]);
                else
                    arrParamValues[i] = "No Value";
            }

            for (i=0; i<arrURLParams.length; i++)
            {
                if (arrParamNames[i] == paramName)
                {
                    //alert("Parameter:" + arrParamValues[i]);
                    return arrParamValues[i];
                }
            }
            return "No Parameters Found";
        }
    },
    processPrice: function (price) {
        if (price == null || parseFloat(price) == 0)
            return 0;
        var p = 0;
        if(price.constructor === Array){
            p = String(price[0]).replace(',', '.').match(/[0-9]*[\.]?[0-9]+/g);
        }else{
            p = String(price).replace(',', '.').match(/[0-9]*[\.]?[0-9]+/g);
        }

        if(isNaN(p) || parseFloat(price) == 0){
            return 0;
        }
        return parseFloat(p);
    },
    getExchangeRate: function(){
        var exchange_rate = 0;
        var $dom = document.querySelectorAll('#_nhatminh247-exchange-rate');
        if($dom.length){
            exchange_rate = parseFloat($dom[0].value);
        }
        return exchange_rate;
    },
    formatPrice: function(price){
        if(Helper.isFloat(price)){
            return price.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
        }else{
            return price.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
        }
    },
    isFloat: function(n){
        return n === +n && n !== (n|0);
    },
};

/* Adds Element BEFORE NeighborElement */
Element.prototype.appendBefore = function (element) {
    element.parentNode.insertBefore(this, element);
}, false;

/* Adds Element AFTER NeighborElement */
Element.prototype.appendAfter = function (element) {
    element.parentNode.insertBefore(this, element.nextSibling);
}, false;

Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
}
NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
    for(var i = this.length - 1; i >= 0; i--) {
        if(this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
        }
    }
}

var taobao = function(){
    this.init_data = null;

    this.init = function () {
        //nothing
        console.log('init taobao');
    };

    this.getPriceRangePromotion = function () {
        var html = '';
        if(document.querySelectorAll('#J_priceStd span[itemprop="lowPrice"]').length
            && document.querySelectorAll('#J_priceStd span[itemprop="highPrice"]').length){
            var lowPricePromo = parseFloat(document.querySelectorAll('#J_priceStd span[itemprop="lowPrice"]')[0].textContent)
                * Helper.getExchangeRate();
            var highPricePromo = parseFloat(document.querySelectorAll('#J_priceStd span[itemprop="highPrice"]')[0].textContent)
                * Helper.getExchangeRate();
            if(!isNaN(lowPricePromo) && !isNaN(highPricePromo)){
                html = 'Giá: ' + Helper.formatPrice(lowPricePromo) + 'đ ~ ' + Helper.formatPrice(highPricePromo) + 'đ';
            }
        }
        return html;
    };

    this.getPriceRange = function () {
        var html = '';
        if(document.querySelectorAll('#J_priceStd span[itemprop="lowPrice"]').length
            && document.querySelectorAll('#J_priceStd span[itemprop="highPrice"]').length){
            var lowPrice = parseFloat(document.querySelectorAll('#J_priceStd span[itemprop="lowPrice"]')[0].textContent)
                * Helper.getExchangeRate();
            var highPrice = parseFloat(document.querySelectorAll('#J_priceStd span[itemprop="highPrice"]')[0].textContent)
                * Helper.getExchangeRate();

            if(!isNaN(lowPrice) && !isNaN(highPrice)){
                html = 'Giá: ' + Helper.formatPrice(lowPrice) + 'đ ~ ' + Helper.formatPrice(highPrice) + 'đ';
            }
        }
        return html;
    };

    this.previewPrice = function () {
        try{
            var $anchor = document.querySelectorAll('#J_PromoWrap');
            if($anchor.length){
                document.querySelectorAll('.nhatminh247-preview-price').remove();

                var html = '';

                //gia cua tung cap thuoc tinh
                var price_cny = this.getPrice();
                if(isNaN(price_cny)) price_cny = 0;
                var price_vnd = parseFloat(price_cny) * Helper.getExchangeRate();
                if(!isNaN(price_cny) && price_cny > 0){
                    html = 'Giá: ' + Helper.formatPrice(price_vnd) + 'đ';
                }

                if(!html){
                    //ton tai khoang gia khuyen mai?
                    html = this.getPriceRangePromotion();
                }

                if(!html){
                    //ton tai khoang gia?
                    html = this.getPriceRange();
                }

                if(html){
                    var NewElement = document.createElement('div');
                    NewElement.className = "nhatminh247-preview-price";
                    NewElement.innerHTML = html;
                    NewElement.appendAfter($anchor[0]);
                }
            }
        }catch (e){

        }
    };

    this.isEmptyProperty = function () {
        if(!document.querySelectorAll('#J_SKU > dl').length){
            return true;
        }
        return false;
    };

    this.validateBeforeSubmit = function () {
        if(this.isEmptyProperty()){
            return true;
        }else{
            var total_choose = document.querySelectorAll('.J_SKU.tb-selected').length;
            var total_sku = document.querySelectorAll('#J_SKU > dl').length;
            if(total_choose == total_sku){
                return true;
            }
        }
        return false;
    };

    /**
     * Ten san pham
     * @returns {string}
     */
    this.getProductName = function(){
        var product_name = '';
        try{
            product_name = document.querySelectorAll('meta[property="og:title"]')[0].getAttribute('content');
        }catch (e){

        }
        return product_name;
    };

    this.getPrice = function(){
        var price = 0;
        try{
            price = document.querySelectorAll('#J_PromoPrice .tb-rmb-num')[0].textContent.trim();
        }catch (e){

        }

        if(!price){
            try{
                price = document.querySelectorAll('#J_priceStd .tb-rmb-num')[0].textContent.trim();
            }catch (e){

            }
        }

        if(price){
            price = Helper.getPriceFromString(price);
        }
        return price;
    };

    this.getPricePromotion = function(){
        return this.getPrice();
    };

    this.getProperty = function(){
        if(this.isEmptyProperty()){
            return '';
        }

        var property = [];
        try{
            var $dom = document.querySelectorAll('.J_SKU.tb-selected > a');
            for(var i = 0; i < $dom.length; i++){
                var property_item = $dom[i].getAttribute('title');
                if(!property_item){
                    $dom[i].textContent.trim();
                }
                if(property_item){
                    property.push(property_item);
                }
            }
        }catch (e){

        }

        return property ? property.join(';') : '';
    };

    this.getProductImage = function(){
        var product_image = '';
        try{
            product_image = document.querySelectorAll('#J_ThumbView')[0].getAttribute('src');
        }catch (e){

        }
        return product_image;
    };

    this.getProductImageModel = function () {
        var product_image_model = '';

        try{
            var $dom = document.querySelectorAll('.J_SKU.tb-selected > a');
            for(var i = 0; i < $dom.length; i++){
                var background = Helper.getBackgroundImageOfDiv($dom[i]);
                if(background){
                    product_image_model = background;
                    break;
                }
            }
        }catch (e){

        }

        if(!product_image_model){
            product_image_model = this.getProductImage();
        }

        if(product_image_model){
            product_image_model = Helper.resizeImage(product_image_model);
        }

        return product_image_model;
    };

    /**
     * duong dan chi tiet san pham site TQ
     * @returns {string}
     */
    this.getProductDetailUrl = function(){
        return window.location.href;
    };

    /**
     * id san pham site trung quoc
     * @returns {string}
     */
    this.getProductId = function(){
        var item_id = '';
        try{
            item_id = document.querySelectorAll('input[name="item_id"]')[0].value;
        }catch(e){

        }
        return item_id;
    };

    /**
     * cho biet san pham thuoc site nao?
     * @returns {string}
     */
    this.getSite = function(){
        return 'taobao';
    };

    this.getShopId = function(){
        var shop_id = '';

        try{
            var shop_tilte_text;
            if(document.querySelector('.shop-title-text')){
                shop_tilte_text = document.querySelector('.shop-title-text').getAttribute("href");
            }else{
                shop_tilte_text = document.querySelectorAll(".tb-shop-name")[0].getElementsByTagName("h3")[0].getElementsByTagName("a")[0].getAttribute("href")
            }
            shop_tilte_text = shop_tilte_text.replace("//shop", "");
            var tmp = shop_tilte_text.split('.');
            shop_id = tmp[0];
        }catch(e){

        }

        shop_id = shop_id ? 'taobao_' + shop_id : shop_id;

        return shop_id;
    };

    this.getQuantity = function(){
        var quantity = 0;
        if(this.isEmptyProperty()){
            try{
                quantity = document.querySelectorAll('#J_IptAmount')[0].value;
            }catch (e){

            }
        }else{
            try{
                quantity = document.querySelectorAll('#J_IptAmount')[0].value;
            }catch (e){

            }
        }
        return quantity;
    };

    this.getDataToSend = function () {
        return {
            title_origin: this.getProductName(),
            price_origin: this.getPrice(),
            price_promotion: this.getPricePromotion(),
            property: this.getProperty(),
            image_origin: this.getProductImage(),
            image_model: this.getProductImageModel(),
            link_origin: this.getProductDetailUrl(),
            item_id: this.getProductId(),
            site: this.getSite(),
            shop_id: this.getShopId(),
            quantity: this.getQuantity(),
        };
    };

};
var tmall = function(){
    this.init_data = null;

    this.init = function () {
        //nothing
    };

    this.previewPrice = function(){
        var origin_price = document.querySelectorAll('#J_StrPrice');

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_StrPrice');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_priceStd');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_priceStd');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_StrPriceModBox');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_StrPriceModBox');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_PromoPrice');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_PromoPrice');
        }

        document.querySelectorAll('.nhatminh247-preview-price').remove();

        var price_cny = this.getPricePromotion();
        // console.log('price_cny: ' + price_cny);
        if(isNaN(price_cny)) price_cny = 0;
        var price_vnd = parseFloat(price_cny) * Helper.getExchangeRate();
        if(price_vnd){
            price_vnd = Helper.formatPrice(price_vnd);
        }

        var NewElement = document.createElement('div');
        NewElement.className = "nhatminh247-preview-price";
        NewElement.innerHTML = 'Giá: ' + price_vnd + 'đ';
        NewElement.appendAfter(origin_price[0]);
    };

    this.isEmptyProperty = function () {

    };

    this.validateBeforeSubmit = function () {
        var props = document.getElementsByClassName('J_TSaleProp');
        if(!((typeof props != 'object' && props != "" && props != null)
            || (typeof props === 'object' && props.length > 0))){

            props = document.querySelectorAll("ul.tb-cleafix");
        }
        var full = true;
        if (props.length > 0) {
            var count_selected = 0;
            for (var i = 0; i < props.length; i++) {
                var selected_props = props[i].getElementsByClassName('tb-selected');
                if (selected_props != null && selected_props != 'undefined')
                    count_selected += selected_props.length;
            }
            if (count_selected < props.length) {
                full = false;
            }
        }
        return full;
    };

    this.getProductName = function(){
        try{
            var _title = this.getDomTitle();
            var title_origin = _title.getAttribute("data-text");
            if(title_origin == "" || typeof title_origin == "undefined" || title_origin == null){
                title_origin = _title.textContent;
            }
            return title_origin;
        }catch(ex){
            return "";
        }
    };

    this.getDomTitle = function(){
        var _title = null;
        if (document.getElementsByClassName("tb-main-title").length > 0) {
            _title =  document.getElementsByClassName("tb-main-title")[0];
        }

        if (_title == null && document.getElementsByClassName("tb-detail-hd").length > 0) {
            var h = document.getElementsByClassName("tb-detail-hd")[0];
            if (h.getElementsByTagName('h3').length > 0 && h != null) {
                _title = h.getElementsByTagName('h3')[0];
            }else{
                _title = h.getElementsByTagName("h1")[0];
            }
        }

        if (_title.textContent == "" && document.getElementsByClassName("tb-tit").length > 0) {
            _title = document.getElementsByClassName("tb-tit")[0];
        }

        if (_title.textContent == "") {
            _title = document.querySelectorAll('h3.tb-item-title');
            if (_title != null) {
                _title = _title[0];
            }else{
                _title = document.getElementsByClassName('tb-item-title');
                if(_title.length > 0){
                    _title = _title[0];
                }
            }
        }
        return _title;
    };

    this.getPriceAnchor = function(){
        var origin_price = document.querySelectorAll('#J_StrPrice .tm-price');

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_StrPrice .tb-rmb-num');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_priceStd .tb-rmb-num');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_priceStd .tm-price');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_StrPriceModBox .tm-price');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_StrPriceModBox .tb-rmb-num');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_PromoPrice .tm-price');
        }

        if(origin_price == null || origin_price.length == 0){
            origin_price = document.querySelectorAll('#J_PromoPrice .tb-rmb-num');
        }
        return origin_price;
    };

    this.getPrice = function(){
        try{
            var origin_price = this.getPriceAnchor();

            var price = origin_price[0].textContent;
            price = price.match(/[0-9]*[\.,]?[0-9]+/g);

            return Helper.processPrice(price);
        }catch(ex){
            return 0;
        }
    };

    this.getPricePromotion = function(){
        try{
            var span_price = null;
            var normal_price = document.getElementById('J_StrPrice');

            if(normal_price == null){
                normal_price = document.getElementById("J_priceStd");
            }

            if(normal_price == null) {
                normal_price = document.getElementById('J_StrPriceModBox');
            }

            if(normal_price == null){
                normal_price = document.getElementById('J_PromoPrice');
            }

            var promotion_price = document.getElementById('J_PromoPrice');

            var price = 0;
            if(promotion_price == null){
                promotion_price = normal_price;
            }

            if(promotion_price != null) {
                try{
                    if(promotion_price.getElementsByClassName('tm-price').length > 0) {
                        span_price = promotion_price.getElementsByClassName('tm-price');
                        if(span_price != null && span_price != "" && span_price != "undefined"){
                            price = span_price[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                        }
                    }else if(promotion_price.getElementsByClassName('tb-rmb-num').length > 0){
                        span_price = promotion_price.getElementsByClassName('tb-rmb-num');
                        if(span_price != null && span_price != "" && span_price != "undefined"){
                            price = span_price[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                        }
                    }else if(promotion_price.getElementsByClassName('tb-wrTuan-num').length > 0){
                        price = document.getElementById('J_PromoPrice').getElementsByClassName('tb-wrTuan-num')[0].childNodes[1].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                    }
                }catch(e){
                    price = 0;
                }

            }
            if(price > 0){
                return Helper.processPrice(price);
            }
        }catch(ex){

        }
        return this.getPrice();
    };

    this.getProperty = function(){
        var selected_props = document.getElementsByClassName('J_TSaleProp');
        var color_size = '';

        if(!((typeof selected_props !== 'object' && selected_props != "" && selected_props != null)
            || (typeof selected_props === 'object' && selected_props.length > 0))){
            selected_props = document.querySelectorAll("ul.tb-cleafix");
        }
        if(selected_props.length > 0) {
            for(var i = 0; i < selected_props.length; i++) {
                var li_origin = selected_props[i].getElementsByClassName('tb-selected')[0];
                if(li_origin != null){
                    var c_s = li_origin.getElementsByTagName('span')[0].getAttribute("data-text");
                    if(c_s == "" || c_s == null || typeof c_s == "undefined"){
                        c_s = li_origin.getElementsByTagName('span')[0].textContent;
                    }
                    color_size+=c_s+';';
                }
            }
        }
        return color_size;
    };

    this.getProductImage = function(){
        var img_src = "";
        try {
            var img_obj = document.getElementById('J_ImgBooth');
            if (img_obj != null) { // Image taobao and t
                img_src = img_obj.getAttribute("src");
                img_src = Helper.resizeImage(img_src);
                return encodeURIComponent(img_src);
            }

            img_obj = document.getElementById('J_ThumbView');

            if(img_obj != null && img_obj != ""){
                img_src = img_obj.getAttribute("src");
                img_src = Helper.resizeImage(img_src);
                return encodeURIComponent(img_src);
            }

            if (document.getElementById('J_ImgBooth').tagName == "IMG") {
                // Find thumb image
                var thumbs_img_tag = document.getElementById('J_UlThumb');
                try {
                    if (thumbs_img_tag != null) {
                        img_src = thumbs_img_tag.getElementsByTagName("img")[0].src;
                    } else {
                        img_src = document.getElementById('J_ImgBooth').src;
                    }
                } catch (e) {
                    console.log(e);
                }
            } else {
                // Find thumb image
                var thumbs_a_tag = document.getElementById('J_UlThumb');
                if (thumbs_a_tag != null) {
                    img_src = thumbs_a_tag.getElementsByTagName("li")[0].style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
                } else {
                    img_src = document.getElementById('J_ImgBooth').style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
                }
            }

        } catch (e) {
            img_src = "";
        }

        img_src = Helper.resizeImage(img_src);
        return encodeURIComponent(img_src);
    };

    this.getProductImageModel = function () {
        return this.getProductImage();
    };

    this.getProductDetailUrl = function(){
        return window.location.href;
    };

    this.getProductId = function(){
        try{
            var home = window.location.href;
            var item_id = Helper.getURLParameters('id');
            var dom_id = document.getElementsByName("item_id");
            if(item_id <= 0 || isNaN(item_id)){
                if (dom_id.length > 0) {
                    dom_id = dom_id[0];
                    item_id = dom_id.value;
                } else item_id = 0;

                if (item_id == 0 || item_id == null || item_id == '') {
                    dom_id = document.getElementsByName("item_id_num");
                    if (dom_id.length > 0) {
                        dom_id = dom_id[0];
                        item_id = dom_id.value;
                    } else item_id = 0;
                }
            }

            if(parseInt(item_id) <= 0 || isNaN(item_id)){
                item_id = home.split('.htm')[0];
                item_id = item_id.split('item/')[1];
            }

            return item_id;
        }catch(ex){
            return "";
        }
    };

    this.getSite = function(){
        return 'tmall';
    };

    this.getShopId = function(){
        var shop_id = '';
        try{
            var string = document.querySelector('meta[name="microscope-data"]').getAttribute("content");
            if(string){
                var array = string.split(';');
                if(array.length > 0){
                    for(var i = 0; i < array.length; i++){
                        var str = array[i];
                        str = str.trim();
                        var params = str.split('=');
                        var key = params[0];
                        var value = params[1];
                        if(key == 'shopId'){
                            shop_id = value;
                            break;
                        }
                    }
                }
            }
        }catch(ex){

        }

        if(!shop_id){
            try{
                var href = document.querySelectorAll(".tb-booth")[0].getElementsByTagName("a")[0].getAttribute('href');
                var a = href.split('?');
                var b = a[1].split('&');
                for(var j = 0; j < b.length; j++){
                    var c = b[j].split('=');
                    if(c[0] == 'shopId'){
                        shop_id = c[1];
                        break;
                    }
                }
            }catch(ex){

            }
        }
        shop_id = 'tmall_' + shop_id;
        return shop_id;
    };

    this.getQuantity = function(){

        var quantity = 0;
        try{
            quantity = document.querySelectorAll('#J_Amount input')[0].value;
        }catch (e){

        }
        return quantity;
    };

    this.getDataToSend = function () {
        return {
            title_origin: this.getProductName(),
            price_origin: this.getPrice(),
            price_promotion: this.getPricePromotion(),
            property: this.getProperty(),
            image_origin: this.getProductImage(),
            image_model: this.getProductImageModel(),
            link_origin: this.getProductDetailUrl(),
            item_id: this.getProductId(),
            site: this.getSite(),
            shop_id: this.getShopId(),
            quantity: this.getQuantity(),
        };
    };
};
var alibaba = function(){
    this.init_data = null;

    this.previewPrice = function(){
        var $target = document.querySelectorAll('.mod-detail-price');
        document.querySelectorAll('.nhatminh247-preview-price').remove();

        //th1: sp co khoang gia di kem so luong
        try{
            var price_range = this.getProductPriceRange();
            var html = '';
            for(var i = 0; i < price_range.length; i++){
                var is_end = (i + 1) == price_range.length;
                var price_vnd = parseFloat(price_range[i].price) * Helper.getExchangeRate();
                if(price_vnd){
                    price_vnd = Helper.formatPrice(price_vnd);
                }

                if(is_end){
                    html += 'Mua số lượng từ '
                        + price_range[i].begin + ' sp trở lên giá ' + price_vnd + 'đ<br>';
                }else{
                    html += 'Mua số lượng từ '
                        + price_range[i].begin + '-'
                        + price_range[i].end
                        + ' sp giá ' + price_vnd + 'đ<br>';
                }
            }

            if(price_range.length && price_range[0].begin > 1){
                html += 'Yêu cầu mua tối thiếu ' + price_range[0].begin + ' sp';
            }

            if($target.length && html){
                var NewElement = document.createElement('div');
                NewElement.className = "nhatminh247-preview-price font-small";
                NewElement.innerHTML = html;
                NewElement.appendAfter($target[0]);
            }

        }catch (e){
            console.log(e.message);
        }
        //th2: sp co gia tu - den (VD: 100k ~ 200k)
        try{
            var $anchor = document.querySelectorAll('.price-discount-sku');
            if(!$anchor.length){
                $anchor = document.querySelectorAll('.price-original-sku');
            }
            if($anchor.length){
                var price_begin = $anchor[0].getElementsByClassName('value')[0] != undefined
                    ? parseFloat($anchor[0].getElementsByClassName('value')[0].textContent.trim()) * Helper.getExchangeRate() : undefined;
                var price_end = $anchor[0].getElementsByClassName('value')[1] != undefined
                    ? parseFloat($anchor[0].getElementsByClassName('value')[1].textContent.trim()) * Helper.getExchangeRate() : undefined;

                var html = '';
                if(!isNaN(price_begin) && !isNaN(price_end)){
                    html = 'Giá ' + Helper.formatPrice(price_begin) + 'đ ~ ' + Helper.formatPrice(price_end) + 'đ';
                }else if(!isNaN(price_begin)){
                    html = 'Giá ' + Helper.formatPrice(price_begin) + 'đ';
                }

                if($target.length && html){
                    var NewElement = document.createElement('div');
                    NewElement.className = "nhatminh247-preview-price";
                    NewElement.innerHTML = html;
                    NewElement.appendAfter($target[0]);
                }
            }
        }catch (e){
            console.log(e.message);
        }
    };

    this.init = function(){
        if(this.init_data){
            return this.init_data;
        }

        var self = this;
        try{
            var scripts = document.querySelectorAll("script");
            for(var i = 0; i < scripts.length; i++){
                var html = scripts[i].textContent;
                var res = html.search("iDetailConfig");
                if(res != -1){
                    eval(html);
                    self.init_data = {
                        iDetailConfig:iDetailConfig,
                        iDetailData:iDetailData
                    };

                    break;
                }
            }
        }catch(e){

        }
        return this.init_data;
    };

    this.product_id_increment = 0;

    /**
     * @desc Neu khong co doan html hien thi tong so san pham khach chon thi la san pham khong co thuoc tinh va nguoc lai
     * @returns {boolean}
     */
    this.isEmptyProperty = function () {
        if(!document.querySelectorAll('.list-total .amount .value').length){
            return true;
        }
        return false;
    };

    this.getTotalQuantityChoose = function () {
        var total_quantity = 0;
        try{
            total_quantity = document.querySelectorAll('.list-total .amount .value')[0].textContent;
        }catch (e){

        }
        return total_quantity;
    };

    this.validateBeforeSubmit = function () {
        if(this.isEmptyProperty()){
            if(this.getQuantity() > 0){
                return true;
            }
        }else{
            if(this.getTotalQuantityChoose() > 0){
                return true;
            }
        }
        return false;
    };

    this.setProductId = function(id){
        this.product_id_increment = id;
    };

    this.getProductId = function(){
        return this.product_id_increment;
    };

    this.getProductPriceRange = function () {
        var product_price_range = [];
        var $dom = document.querySelectorAll('#mod-detail-price .price td');
        for(var i = 0; i < $dom.length; i++){
            var data_range = $dom[i].getAttribute('data-range');
            if(data_range){
                product_price_range.push(JSON.parse(data_range));
            }
        }
        return product_price_range;
    };

    this.getProductName = function(){
        var product_name = '';
        try{
            product_name = document.querySelectorAll('.d-title')[0].textContent;
        }catch (e){

        }
        return product_name;
    };

    this.getPriceWithQuantityAndPriceRange = function (quantity, price_range) {
        var price = 0;
        try{
            for(var i = 0; i < price_range.length; i++){
                var is_end = (i + 1) == price_range.length;
                var begin = price_range[i].begin;
                var end = price_range[i].end;
                begin = parseInt(begin);
                end = parseInt(end);

                if(is_end){
                    if(quantity >= begin){
                        price = price_range[i].price;
                        break;
                    }
                }else{
                    if(quantity >= begin && quantity <= end){
                        price = price_range[i].price;
                        break;
                    }
                }
            }
        }catch (e){

        }

        try{
            if(!price){
                price = price_range[0].price;
            }
        }catch (e){

        }

        return price;
    };

    this.getProductPrice = function(){
        var price = 0;
        try{
            price = document.querySelectorAll('.table-sku tr')[this.getProductId()].getElementsByClassName('price')[0].getElementsByClassName('value')[0].textContent.trim();
        }catch (e){

        }
        if(this.isEmptyProperty()){
            price = this.getPriceWithQuantityAndPriceRange(this.getQuantity(), this.getProductPriceRange());
        }
        return price;
    };

    this.getProductPricePromotion = function(){
        return this.getProductPrice();
    };

    this.getProperty = function(){
        var property = [];

        //property1
        try{
            var property_item = document.querySelectorAll('.list-leading .unit-detail-spec-operator > a.selected')[0].getAttribute('title').trim();
            if(property_item){
                property.push(property_item);
            }
        }catch (e){

        }

        //property2
        try{
            var property_item2 = JSON.parse(document.querySelectorAll('.table-sku tr')[this.getProductId()].getAttribute('data-sku-config')).skuName;
            if(property_item2){
                property.push(property_item2);
            }
        }catch (e){
            console.log(e.message);
        }

        if(this.isEmptyProperty()){
            return '';
        }

        console.log(property);
        return property.length ? property.join(';') : '';
    };

    this.getQuantity = function(){
        var quantity = 0;
        try{
            quantity = document.querySelectorAll('.table-sku tr')[this.getProductId()].getElementsByClassName('amount')[0].getElementsByClassName('amount-input')[0].value;
        }catch (e){

        }

        if(this.isEmptyProperty()){
            try{
                quantity = document.querySelectorAll('.amount-input')[0].value;
            }catch (e){

            }
        }

        return quantity;
    };

    this.getProductImage = function(){
        var product_image = '';
        try{
            product_image = document.querySelectorAll('.box-img')[0].getElementsByTagName('img')[0].getAttribute('src');
        }catch (e){

        }
        return product_image;
    };

    /**
     * @desc Nếu có ảnh ở thuộc tính nào thì lấy ảnh ở thuộc tính đó, các thuộc tính bao gồm: table-sku, list-leading
     * @returns {string}
     */
    this.getProductImageModel = function () {
        var product_image_model = '';
        try{
            product_image_model = document.querySelectorAll('.table-sku tr')[this.getProductId()].getElementsByClassName('box-img')[0].getElementsByTagName('img')[0].getAttribute('src');
            if(!product_image_model){
                product_image_model = document.querySelectorAll('.table-sku tr')[this.getProductId()].getElementsByClassName('box-img')[0].getElementsByTagName('img')[0].getAttribute('data-lazy-src');
            }
        }catch (e){

        }

        if(!product_image_model){
            try{
                product_image_model = document.querySelectorAll('.list-leading .image.selected img')[0].getAttribute('src');
            }catch (e){

            }
        }

        if(product_image_model){
            product_image_model = Helper.resizeImage(product_image_model);
        }

        if(!product_image_model){
            return this.getProductImage();
        }
        return product_image_model;
    };

    this.getProductDetailUrl = function(){
        return window.location.href;
    };

    this.getItemId = function(){
        var item_id = '';
        try{
            item_id = document.querySelectorAll('[name="b2c_auction"]')[0].getAttribute('content');
        }catch (e){

        }
        return item_id;
    };

    this.getSite = function(){
        return '1688';
    };

    this.getShopId = function(){
        //==== step 1: Lấy thông tin trên dom
        try{
            var dataUnitConfigString = document.querySelectorAll('.apply-btn')[0].getAttribute('data-unit-config');
            var dataUnitConfigJSON = JSON.parse(dataUnitConfigString);
            return dataUnitConfigJSON.sellerId;
        }catch (e){

        }
        //==== step 2: Lấy thông tin dữ liệu trả về trên trang
        try{
            return this.init_data.iDetailConfig.userId;
        }catch (e){

        }
        return '';
    };

    this.getDataToSend = function () {
        return {
            title_origin: this.getProductName(),
            price_origin: this.getProductPrice(),
            price_promotion: this.getProductPricePromotion(),
            property: this.getProperty(),
            image_origin: this.getProductImage(),
            image_model: this.getProductImageModel(),
            link_origin: this.getProductDetailUrl(),
            item_id: this.getItemId(),
            site: this.getSite(),
            shop_id: this.getShopId(),
            quantity: this.getQuantity(),
            price_range: this.getProductPriceRange(),
        };
    };
};

document.getElementById('_add-to-cart').addEventListener('click', function(e) {
    addToCart(e);
});

var factory = function () {
    var _class;

    var url = window.location.href;
    if(url.match(/taobao.com/)){
        _class = new taobao();
    }
    if(url.match(/tmall.com|tmall.hk|yao.95095.com/)){
        _class = new tmall();
    }
    if(url.match(/1688.com|alibaba/)){
        _class = new alibaba();
    }
    return _class;
};

var _className = new factory();
_className.init();

var classname = document.getElementsByClassName("_btn-action");

var myFunction = function() {
    var action = this.getAttribute('data-action');
    var send_url = this.getAttribute('data-url');
    var method = this.getAttribute('data-method');

    var current_url = window.location.origin;
    if(window.location.pathname){
        current_url += window.location.pathname;
    }

    chrome.runtime.sendMessage({
        action: "request_server",
        method: method,
        data: {
            current_url:current_url,
            action:action,
            site:_className.getSite(),
            avatar:_className.getProductImage(),
            product_name: _className.getProductName(),
        },
        url: send_url,
        callback: 'after_execute_action_success',
    });
};

for (var i = 0; i < classname.length; i++) {
    classname[i].addEventListener('click', myFunction, false);
}

setInterval(function(){
    _className.previewPrice();
}, 1000);

var product_send_data_list = [];

function addToCart(e){
    var current_site = _className.getSite();
    if(!_className.validateBeforeSubmit()){
        alert('Vui lòng chọn đầy đủ thuộc tính của sản phẩm trước khi cho vào giỏ. Xin Cám Ơn!');
        return false;
    }

    if(current_site == 1688){

        var is_empty_property = _className.isEmptyProperty();
        if(is_empty_property){
            var data = _className.getDataToSend();
            sendAjax(data);
        }else{
            product_send_data_list = [];
            var $dom = document.querySelectorAll('.obj-sku .amount-input');
            for(var i = 0; i < $dom.length; i++){
                var amount_input = $dom[i].value;
                if(amount_input > 0){
                    product_send_data_list.push(i);
                }
            }

            if(product_send_data_list.length){
                _className.setProductId(product_send_data_list[0]);
                var data = _className.getDataToSend();
                sendAjax(data, 'after_add_to_cart_1688');
            }
        }

    }else{
        //site: taobao, tmall
        var data = _className.getDataToSend();
        sendAjax(data);
    }
}

function sendAjax(data, function_callback){
    if(!function_callback){
        function_callback = 'after_request_server';
    }
    var url_add_to_cart = document.querySelectorAll('#_nhatminh247-api-cart-url')[0].value;
    chrome.runtime.sendMessage({
        action: "request_server",
        method: 'post',
        data: data,
        url: url_add_to_cart,
        callback: function_callback,
    });
}

chrome.runtime.onMessage.addListener(
    function(request, sender, sendResponse) {
        var response = request.response;
        switch (request.action)
        {
            case "after_add_to_cart_1688":
                //alert when success
                if(response.html){
                    Common.appendHtml(document.body, response.html);
                }
                product_send_data_list.shift();
                if(product_send_data_list.length){
                    _className.setProductId(product_send_data_list[0]);
                    var data = _className.getDataToSend();
                    sendAjax(data, 'after_add_to_cart_1688');
                }
                break;

            case "after_execute_action_success":
                if(response.html){
                    Common.appendHtml(document.body, response.html);
                }
                break;

            default :
                break;

        }
    }
);
