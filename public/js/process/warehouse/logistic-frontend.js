$(document).ready(function(){
    //function goStep(step){
    //    $('.step-next').hide();
    //    $('.main-ct-step' + step).show();
    //}
    //
    //$(".main-ct-step1 .click-new").click(function(){
		//	goStep(3);
    //    });
    //$(".main-ct-step1 .click-step-finish").click(function(){
    //    goStep(4);
    //});
    //
    //$('#popupbarcode').on('hidden.bs.modal', function () {
    //    goStep(1);
    //});
    //
    //
    //$("#popupnewbarcode .click-new-order .arow").click(function(){
    //    $("#popupnewbarcode .click-new-order .arow").toggleClass("open");
    //    $("#popupnewbarcode .new-order").slideToggle();
    //});




//    Height maincontent đóng bao
    var height = $(window).height() - 96;
    $('.package').css({'height': height});
//    $('.package .position-1 .module-content').slimscroll();
//    $('#testDiv2').slimscroll({
//        height: '100px',
//        width: '300px'
//    });

 
  $(window).on('load', function () {
        $('.selectpicker').selectpicker({
            'selectedText': 'cat'
        }); 
    });


	
//	var height = $(".main-ct-step1 .dropdown-menu .selectpicker").innerHeight();
////		alert(height);
//        $(function(){
//            $('.content').slimScroll({
//                height: height
//            });
//
//
//        });
	
//	$(function() {
//        $( "#datepicker" ).datepicker();
//        $( "#datepicker2" ).datepicker();
//    });



    //    focus new popup
    $('.modal').on('shown.bs.modal', function () {
        $('.focusnew').focus();
    })

 // tooltip
    $("* [rel='tooltiptop']").tooltip({
       html: true, 
       placement: 'top'
    }); 

    $("* [rel='tooltipbottom']").tooltip({
       html: true, 
       placement: 'bottom'
    }); 
    
    $("* [rel='tooltipleft']").tooltip({
       html: true, 
       placement: 'left'
    });
    
    $("* [rel='tooltipright']").tooltip({
       html: true, 
       placement: 'right'
    });

    var datepicker = $('#datepicker');
    if(datepicker.length > 0 && datepicker != null){
        $( "#datepicker" ).datepicker({
            onClose: function( selectedDate ) {
                $( "#datepicker2" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#datepicker2" ).datepicker({
            onClose: function( selectedDate ) {
                $( "#datepicker" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    }

    var $loading = $("._loading");
    $.ajaxSetup({
        beforeSend:function(){
            //$loading.show();
            NProgress.start();
        },
        complete:function(){
            //$loading.hide();
            NProgress.done();
        }
    });
}); 

