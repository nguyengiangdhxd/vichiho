/**
 * Created by hosi on 5/21/14.
 */


/**
 * Created by hosi on 5/21/14.
 */

$(document).ready(function () {
    //fill data from local storage if have
    if(typeof(Storage) !== "undefined") {
        // Retrieve
        var get_history_scanning = localStorage.getItem("history_scanning");
       if(get_history_scanning!=null){
           $('.list-box-primary').html(get_history_scanning);
           $('.list-box-primary li:not(:first) .code').removeClass('font-color-ec0423');

           var get_total_scanning = localStorage.getItem('total_scanning');
           if(get_total_scanning!=null){
               $('.total-barcode').html(get_total_scanning);
           }
       }

        $('.custom-content-select-box li').each(function(){
            //setTimeout(function(){ $(this).click() }, 1000);
            var action = localStorage.getItem('bcc_action');

            if ($(this).data('type') == action)
            {
                var value = $(this).data('value');
                var target = $(this).parents('.custom-select-box');
                target.find('.title-select-box').html(value);
                target.find('.custom-content-select-box').addClass('hidden');
                $('#type_warehouse').val($(this).data('type'));
                var input_bacode = $('._input-barcode');
                input_bacode.removeAttr('disabled', 'disabled');
                input_bacode.focus();
                input_bacode.addClass('custom-input-active');
            }
        });

        showHideAction();
    }
    //fix primary

    var height = $(window).height() - 98;
    var w_primary = $('.box-primary').width();

    /**
     * Hiển thị/ẩn nút tạo bao và nút kiểm kê
     */
    function showHideAction()
    {

    }

    $('.primary').css({'height': height, 'overflow': 'hidden'});
    $('.sidebar-left').css({'height': height});
    $('.sidebar-right').css({'height': height});

    $('.list-box-primary.v2').slimscroll({
        height: height - 62
    });

    $('.list-box-primary').slimscroll({
        height: height - 110
    });



    $('.list-box-primary').parent().css('z-index', 9);


    $('.custom-panel-select-box').click(function () {
        $(this).parent().find('.custom-content-select-box').removeClass('hidden');
    });

    $('.custom-content-select-box').mouseleave(function () {
        $(this).addClass('hidden');
    });

    $('.custom-list-group-item li').click(function (e) {
        var value = $(this).data('value');
        var target = $(this).parents('.custom-select-box');
        target.find('.title-select-box').html(value);
        target.find('.custom-content-select-box').addClass('hidden');
        $('#type_warehouse').val($(this).data('type'));
        $('._input-barcode').removeAttr('disabled', 'disabled');
        $('._input-barcode').focus();
        $('._input-barcode').addClass('custom-input-active');
        localStorage.setItem('bcc_action',$(this).data('type'));
        showHideAction();
    });

    $('.clear-result').click(function () {
        if (confirm('Bạn có chắc chắn?')) {
            $('.total-barcode').html('0');
            $('.list-box-primary li.item-box-primary').remove();
            showHideAction();
            if(typeof(Storage) !== "undefined") {
                localStorage.removeItem("history_scanning");
            }
        }
    });

    $('._input-barcode').hover(function () {
//        console.log('hover barcode');
//        console.log($(this).hasClass('custom-input-active'));
        if (!$(this).hasClass('custom-input-active')) {
            $(this).css('cursor', 'not-allowed');
        } else {
            $(this).css('cursor', '');
        }
    });

    $('._sub-title-bag').click(function () {
        var $this = $(this).parents('.panel-list-bag');
        if ($this.find('.list-bag').is(':hidden')) {
            $this.find('.list-bag').slideDown();
            $(this).find('i').removeClass('fa-caret-right');
            $(this).find('i').addClass('fa-caret-down');
        } else {
            $this.find('.list-bag').slideUp();
            $(this).find('i').addClass('fa-caret-right');
            $(this).find('i').removeClass('fa-caret-down');
        }
    });

    /*export excel*/
    $('._export_excel').click(function (e) {
        e.preventDefault();
        var string_data = '';
        $('._count_li').each(function () {
            string_data += $(this).attr('data-code') + ',';
        });
        string_data = encodeURI(string_data);
        console.log(string_data);
        var warehouse_activity = $('#type_warehouse').val();
        location.href = export_excel_url + "?need_to_export=" + string_data + '&type=' + warehouse_activity;
        //window.open('data:application/vnd.ms-excel,' + string_data);
    });
    /* */
    var barcode = $("._input-barcode");
    barcode.keyup(function (event) {
        if (event.keyCode == 13) {
            var that = $(this);
            barcode = that.val().trim().toUpperCase();
            if (barcode == '') {
                return false;
            }
            var type = $('#type_warehouse').val();
            var data = {
                barcode: barcode,
                type: type,
                order: $('._count_li').length
            };
            that.val('');
            that.focus();
            //fill data before call ajax
            $('.list-box-primary li:first-child .code').removeClass('font-color-ec0423');
            var new_item_barcode_scanning = Handlebars.compile($("#new_item_barcode_scanning").html());
            var website_item_barcode_scanning = Handlebars.compile($("#website_item_barcode_scanning").html());
            var error_web_item_barcode_scanning = Handlebars.compile($("#error_web_item_barcode_scanning").html());
            var barcode_link = barcode.replace("/","_");
            var render_data = {barcode: barcode, barcode_link: barcode_link};
            $('.list-box-primary').prepend(new_item_barcode_scanning(render_data));
            /* cộng vào chỗ đếm */
            var count = $('._count_li').length;
            $('.total-barcode').html(count);
            $('.list-box-primary li:first-child ').addClass('_li_' + count);
            $('.list-box-primary li:first-child ._div_order_barcode').html(count);
            $('.list-box-primary li:first-child ._warehouse_name').html($('._select_warehouse_name').html());

            $('.list-box-primary').slimscroll({
                height: height - 110,
                width: 313,
                scrollTo: '0px'
            });

            $.ajax({
                url: barcode_url,
                data: data,
                type: 'post',
                success: function (response) {
                    /* append data liên quan vào */
                    if (response.type == 1) {
                        $('._li_' + count + ' ._div_web_time').html(website_item_barcode_scanning(response.data));


                        //add class if barcode scanning ok, use when create packing
                        var type_class='';
                        if(response.data.is_packing){
                            type_class='_packing_item';
                        }else{
                            type_class='_barcode_item';
                        }
                        $('._li_' + count).addClass(type_class);
                        //save history scanning in local storage
                        if(typeof(Storage) !== "undefined") {
                            // Retrieve
                            var old_history_scanning = localStorage.getItem("history_scanning");
                            // Store
                            var new_data_history ='<li class="'+type_class+' item-box-primary _count_li _li_'+count+'" data-code="'+barcode+'">'
                            new_data_history += $('.list-box-primary ._li_'+count).html()+"</li>";
                            if(old_history_scanning!=null){
                                new_data_history+=old_history_scanning;
                            }
                            localStorage.setItem("history_scanning", new_data_history);
                            localStorage.setItem('total_scanning', count);
                        }
                        showHideAction();
                    } else {
                        $('._li_' + count).addClass('border-radius-red');
                        $('._li_' + count + ' ._div_web_time').html(error_web_item_barcode_scanning());

                        //save history scanning in local storage
                        if(typeof(Storage) !== "undefined") {
                            // Retrieve
                            var old_history_scanning = localStorage.getItem("history_scanning");
                            // Store
                            var new_data_history ='<li class="'+' item-box-primary _count_li _li_'+count+' border-radius-red" data-code="'+barcode+'">'
                            new_data_history += $('.list-box-primary ._li_'+count).html()+"</li>";
                            if(old_history_scanning!=null){
                                new_data_history+=old_history_scanning;
                            }
                            localStorage.setItem("history_scanning", new_data_history);
                            localStorage.setItem('total_scanning', count);
                        }
                    }

                }
            }).fail(function(){
                    $('._li_' + count).addClass('border-radius-red');
                    $('._li_' + count + ' ._div_web_time').html(error_web_item_barcode_scanning());

                    //save history scanning in local storage
                    if(typeof(Storage) !== "undefined") {
                        // Retrieve
                        var old_history_scanning = localStorage.getItem("history_scanning");
                        // Store
                        var new_data_history ='<li class="'+' item-box-primary _count_li _li_'+count+' border-radius-red" data-code="'+barcode+'">'
                        new_data_history += $('.list-box-primary ._li_'+count).html()+"</li>";
                        if(old_history_scanning!=null){
                            new_data_history+=old_history_scanning;
                        }
                        localStorage.setItem("history_scanning", new_data_history);
                        localStorage.setItem('total_scanning', count);
                    }
                });
        }
    });




});

