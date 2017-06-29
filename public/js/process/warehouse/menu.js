/**
 * Created by hosi on 5/21/14.
 */

/**
 * Created by hosi on 5/21/14.
 */

$(document).ready(function(){
    $('.custom-navbar-nav a.navbar-nav-a').hover(function(){
        $('.custom-nav-dropdown').hide();
        $('.custom-navbar-nav li.first').removeClass('dropdown-toggle open');
        $('.dropdown-menu-item-arrow').addClass('hidden');

        var $this = $(this).parent();

        $this.addClass('dropdown-toggle open');
        $this.find('.custom-nav-dropdown').show();
        $this.find('.dropdown-menu-item-arrow').removeClass('hidden');
    });

    $('.custom-nav-dropdown').mouseleave(function(){
        $(this).hide();
        $(this).parent().find('.dropdown-menu-item-arrow').addClass('hidden');
    });

    $('.custom-navbar-nav li').mouseleave(function(){
        $(this).find('.custom-nav-dropdown').hide();
        $(this).find('.dropdown-menu-item-arrow').addClass('hidden');
        $(this).removeClass('open');
    });

    $( ".module-zopim" ).click(function() {
        $('.zopim').toggleClass("open");
        $('.module-zopim').toggleClass("close-zopim");
    });

    $( ".zopim.open .meshim_widget_components_chatButton_Button .meshim_widget_widgets_BorderOverlay" ).click(function() {
        $('.module-zopim').addClass("opacity");
    });
});
