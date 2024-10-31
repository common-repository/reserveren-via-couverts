(function( $ ) {
    'use strict';

    $(window).load(function(){

        /** Switches to clicked tab in the back-end when clicked. **/
        $(document).on('click', '.van-tabs li', function(e) {
            e.preventDefault();
            var target = this;

            if (!$(this).hasClass('active')) {
                $($('.active').find('a').attr('href')).fadeOut('fast', 'linear', function() {
                    $($(target).find('a').attr('href')).fadeIn('fast', 'linear');
                    $('.active').removeClass('active');
                    $(target).addClass('active');
                })
            }
        });

        /** Dismiss the .update-panel **/
        $('.close-button').on('click', function(e){
            e.preventDefault();
            $(this).parent('.update-panel').slideUp();
        })

        /** Decode the URL and opens the corresponding tab if &tab is defined **/
        var pageUrl = decodeURIComponent(window.location.search.substring(1)),
            urlVariables = pageUrl.split('&'), parameterName, i;
        if (pageUrl != '') {
            for (i = 0; i < urlVariables.length; i++) {
                parameterName = urlVariables[i].split('=');
                var urlName = parameterName[0];
                var urlValue = parameterName[1];
                if (urlName == 'tab') {
                    $('a[href*="'+urlValue+'"]').trigger('click');
                    return;
                }
            }
        }
    });
})(jQuery);
