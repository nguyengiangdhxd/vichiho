chrome.runtime.onMessage.addListener(
    function(request, sender, sendResponse) {
        switch (request.action)
        {
            case "request_server":
                request_server(request, sender, sendResponse);
                break;
            case "get_tab_id":
                get_tab_id(request, sender, sendResponse);
                break;
            default :
                break;

        }
    }
);

function get_tab_id(request, sender, sendResponse) {
    chrome.tabs.sendMessage(sender.tab.id, { action: request.callback, tab_id: sender.tab.id }, function(response) {

    });
}

function request_server(request, sender, sendResponse){
    $.ajax({
        url: request.url,
        data: request.data == undefined ? {} : request.data,
        method: request.method == undefined ? 'GET' : request.method,
        contentType: 'application/x-www-form-urlencoded',
        xhrFields: {
            withCredentials: true
        },
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(res){
            chrome.tabs.sendMessage(sender.tab.id, { action: request.callback, response: res }, function(response) {

            });
        },
        error: function(){
            chrome.tabs.sendMessage(sender.tab.id, { action: request.callback, response: null }, function(response) {

            });
        }
    });
}







