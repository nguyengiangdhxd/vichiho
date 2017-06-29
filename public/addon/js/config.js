var site_using_https = false;
var site_name = "nhatminh247.dev:8000";
// var site_name = "nhatminh247.vn";

// append css to page
var NewStyles = document.createElement("link");
    NewStyles.rel = "stylesheet";
    NewStyles.type = "text/css";
    NewStyles.href = chrome.extension.getURL('css/main.css');
document.head.appendChild(NewStyles);

var service_host = "http://" + site_name + "/";
var add_to_cart_url = service_host+'cart/add';
var cart_url = service_host+'checkout.html';
var link_detail_cart = service_host + "gio-hang";
var add_to_favorite_url = service_host + "i/favoriteLink/saveLink";
var button_add_to_cart_url = service_host + "assets/images/add_on/icon-bkv1-small.png";

var translate_url = 'http://' + site_name + '/i/goodies_util/translate';
var isUsingSetting = false;
var translate_keyword_url = "";
var version_tool = "1.0";
var exchange_rate;
var link_store_review_guidelines = "#";
var exchange_rate_url = 'http://' + site_name + '/api/exchange';
var catalog_scalar_url = null;
