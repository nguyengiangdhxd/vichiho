//=========== begin config =========
// var sitename = 'nhatminh247.dev:8000';
var sitename = 'nhatminh247.vn';
var url_get_init_data = 'http://' + sitename + '/api/get_init_data';
//=========== end config =========

//========= begin function helper ========

var Common = {
    appendHtml: function(el, str){
        var div = document.createElement('div');
        div.innerHTML = str;
        while (div.children.length > 0) {
            el.appendChild(div.children[0]);
        }
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
    }
};

//========= end function helper ========
var str = window.location.href;
if (str.match(/item.taobao/)){
    try{
        var item_id = Common.getURLParameters('id');
        if(item_id){
            item_id = item_id.split('#')[0];
            window.location.href = 'https://world.taobao.com/item/' + item_id + '.htm';
        }
    }catch (e){

    }
}else{
    start();
}

function start(){
    if ((str.match(/item.taobao/) || str.match(/detail.ju.taobao.com/) || str.match(/detail.tmall/) || str.match(/detail.1688/)
        || str.match(/.1688.com\/offer/)
        || str.match(/.tmall.hk/)
        || str.match(/.yao.95095.com/)
        || str.match(/tmall.com\/item\//) || str.match(/taobao.com\/item\//))) {

        chrome.runtime.sendMessage({
            action: "request_server",
            method: 'get',
            url: url_get_init_data,
            callback: 'after_request_server'
        });
    }
}

chrome.runtime.onMessage.addListener(
    function(request, sender, sendResponse) {
        switch (request.action)
        {
            case "after_request_server":
                Action.after_request_server(request);
                break;
            case "after_get_tab_id":
                console.log(request.tab_id);
                break;
            default :
                break;

        }
    }
);

var Action = {
    after_request_server: function (request) {
        console.log(request);
        var response = request.response;
        if(response.html){
            Common.appendHtml(document.body, response.html);
        }
        if(response.content_script){
            eval(response.content_script);
        }
    },
};

