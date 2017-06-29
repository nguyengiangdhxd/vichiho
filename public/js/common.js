var xhr = null;

$(document).ready(function() {

    // show - hide menu sidebar mobile
    $(document).on('click', '.sidebar-toggle', function(){
        var $dom = $('.app.app-default');
        var $sidebar = $('.app-sidebar');
        if($dom.hasClass('active')){
            $dom.removeClass('active');
            $sidebar.removeClass('active');
        }else{
            $dom.addClass('active');
            $sidebar.addClass('active');
        }
    });

    // $('.sidebar-nav > li').each(function(i){
    //     var len = $(this).find('.dropdown-menu > ul > li').length;
    //     if(!len){
    //         $(this).removeClass('dropdown');
    //     }
    // });

    renderOnPage('html');

    $(document).ajaxStop(function(){
        NProgress.done();
    });

    $.ajaxSetup({
        beforeSend:function(){
            NProgress.start();
        },
        complete:function(){

        }
    });

    /**
     * created_by: vanhs
     * created_time: 04:27 22/06/2015
     * desc: Ham lay toan bo du lieu tu form, kieu object
     */
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            var value = this.value;
            //if use autoNumeric
            var $this = $("[name='" + this.name + "']");
            if($this.hasClass("autoNumeric")) { value = $this.autoNumeric('get'); }

            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(value || '');
            } else {
                o[this.name] = value || '';
            }
        });
        return o;
    };

    /**
     * created_by: vanhs
     * created_time: 04:27 22/06/2015
     * desc: Ham do du lieu vao form, du lieu truyen vao la 1 object
     */
    $.fn.setFormData = function(data) {
        try{
            $.each(data, function (name, value) {
                var $self = $("[name='" + name + "']");
                if($self.length){
                    var tagName = $self.prop("tagName").toUpperCase();
                    var type = $self.prop("type").toUpperCase();
                    switch (tagName){
                        case "INPUT":
                            switch (type){
                                case "TEXT":
                                case "HIDDEN":
                                    $self.val(value);
                                    if($self.hasClass("autoNumeric")){
                                        $self.autoNumeric('set', value);
                                    }
                                    break;
                                case "RADIO":
                                    $self.filter("[value='" + value + "']").prop("checked", true);
                                    break;
                                case "CHECKBOX":
                                    if(typeof value == "string"){ value = [value]; }//convert value to array if value is string
                                    for(var i = 0; i < value.length; i++){
                                        $self.filter("[value='" + value[i] + "']").prop("checked", true);
                                    }
                                    break;
                                case "DATE":
                                    $self.val(value);
                                    if($self.hasClass("autoNumeric")){
                                        $self.autoNumeric('set', value);
                                    }
                                    break;
                                default:
                                    console.warn("Not support type: " + type);
                                    break;
                            }
                            break;
                        case "SELECT":
                            switch (type){
                                case "SELECT-ONE":
                                    $self.find("option[value='" + value + "']").prop("selected", true);
                                    break;
                                case "SELECT-MULTIPLE":
                                    if(typeof value == "string"){ value = [value]; }//convert value to array if value is string
                                    for(var j = 0; j < value.length; j++){
                                        $self.find("option[value='" + value[j] + "']").prop("selected", true);
                                    }
                                    break;
                                default:
                                    console.warn("Not support type: " + type);
                                    break;
                            }
                            break;
                        case "TEXTAREA":
                            $self.val(value);
                            break;
                        default:
                            console.warn("Not support tagName: " + tagName);
                            break;
                    }
                }
            });
        }catch (e){
            console.warn("Exception: " + e.message);
        }
    };
});

function renderOnPage(parent){
    if ($('[data-toggle="tooltip"]', parent).length) {
        $('[data-toggle="tooltip"]', parent).tooltip();
    }

    if ($('[data-toggle="popover"]', parent).length) {
        $('[data-toggle="popover"]', parent).popover();
    }

    if ($('._autoNumeric', parent).length) {
        $('._autoNumeric', parent).each(function (i) {
            var tagName = $(this).prop("tagName").toLowerCase();
            if (tagName == 'input') {
                $(this).autoNumeric({maximumValue: 9999999999999.99, digitGroupSeparator: '.', decimalCharacter: ','});
            } else {
                //todo
            }
        })
    }

    $('.modal', parent).on('shown.bs.modal', function() {
        $("._autofocus").focus();
    });
}

//============= begin event global ===========
$(document).on('click', '.___btn-action', function(){
    var $that = $(this);

    if($that.hasClass('disabled')) return false;

    $that.addClass('disabled');

    var tagName = $that.prop("tagName").toUpperCase();
    var type = $that.prop("type").toUpperCase();
    var data_send = $that.parents('.___form').serializeObject();

    if(tagName == 'INPUT' && type == 'CHECKBOX'){
        data_send.checkbox = $that.is(':checked') ? 'check' : 'uncheck';
    }

    if($that.parents('.___form').find('div#summernote').length){
        data_send.summernote = $that.parents('.___form').find('div#summernote').summernote('code');
    }

    if(data_send.confirm){
        bootbox.confirm(data_send.confirm, function(result) {
            if (result) {
                call_ajax($that, data_send);
            }else{
                $that.removeClass('disabled');
            }
        });
    }else{
        call_ajax($that, data_send);
    }

});

$(document).on('keypress', '.___input-action', function(e){

    if(e.keyCode == 13){
        e.preventDefault();
        var $that = $(this);
        var data_send = $that.parents('.___form').serializeObject();
            data_send.key = $(this).attr('data-key-global');

        var name = $(this).attr('name');
        var value = data_send[name];
        if($(this).hasClass('_autoNumeric')){
            data_send[name] = $(this).autoNumeric('get');
        }

        if(!value){
            return false;
        }
        call_ajax($that, data_send);
    }
});

$(document).on('change', '.___select-action', function(){
    var $that = $(this);
    var data_send = $that.parents('.___form').serializeObject();
    data_send.select = $(this).val();

    call_ajax($that, data_send);
});

//============= end event global ===========

//============= begin function global ===========
function request(url, method, data){
    return $.ajax({
      url: url,
      method: method,
      data: data,
    });
}

function call_ajax($that, data_send){
    return $.ajax({
        url: data_send.url,
        method: data_send.method,
        data: data_send,
        success:function(response) {
            if(response.success){

                if(response.html){
                    var parent = 'html';

                    if(response.anchor){
                        $(response.anchor).html(response.html);
                        parent = response.anchor;
                    }else{
                        $('#_content').html(response.html);
                        parent = '#_content';
                    }

                    renderOnPage(parent);

                    //focus input keypress
                    if(data_send.key){
                        $('[data-key-global="' + data_send.key + '"]').focus();
                    }

                }else{
                    if(response.redirect){
                        location.href = response.redirect;
                    }else{
                        window.location.reload();
                    }
                }

            }else{
                bootbox.alert(response.message);
            }
            $that.removeClass('disabled');
        },
        error: function(){
            $that.removeClass('disabled');
        }
    });
}
//============= end function global ===========