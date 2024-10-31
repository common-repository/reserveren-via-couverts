(function( $ ) {
    'use strict';

    $(window).load(function(){

        if ($('#couvertsFormHolder').length) {
            /** Basic AJAX actions **/
            var command = 'getForm';

            var data = {
                action: 'couvertsForm',
                command: command,
            }

            $.post(ajaxurl, data, function(res) {
                // var response = jQuery.parseJSON(res);
                $('#couvertsFormHolder').prepend(res);
                $('.spinner').fadeOut();

                $('#couvertsFormHolder input[name=date]').datepicker({
                    dateFormat: "dd-mm-yy",
                    numberOfMonths: 1,
                    minDate: 0,
                });
            }).fail(function() {
                //error
            }); 
        }


        $(document.body).on('click', '#nextStep', function(e){
            e.preventDefault();
            $('#couvertsFormHolder').fadeOut(400, function(){
                $('.spinner').show();
            });

            var command = 'getFormNext';
            var data = {
                action: 'couvertsForm',
                command: command,
                step: $('#couvertsFormHolder input[name=currentStep]').val(),
                info: $('#couvertsFormHolder form').serialize(),
            }

            $.post(ajaxurl, data, function(res) {
                // var response = jQuery.parseJSON(res);
                $('#couvertsFormHolder').empty().prepend(res).fadeIn(400, function(){
                    $('.spinner').fadeOut();
                });

                $('#couvertsFormHolder input[name=date]').datepicker({
                    dateFormat: "dd-mm-yy",
                    numberOfMonths: 1,
                    minDate: 0,
                });
            }).fail(function() {
                //error
            }); 
        });


        $(document.body).on('click', '#prevStep', function(e){
            e.preventDefault();
            $('#couvertsFormHolder input[name=currentStep]').val(+$('#couvertsFormHolder input[name=currentStep]').val() - 2);
            $('#nextStep').trigger('click');
        });


        $(document.body).on('click', '#timeHolder a', function(e){
            e.preventDefault();
            $('#timeHolder a.active').removeClass('active');
            $(this).addClass('active');
            $('#couvertsFormHolder input[name=timeSelected]').val($(this).text());
            $('#couvertsFormHolder #nextStep').slideDown();
        });

    });
})(jQuery);
