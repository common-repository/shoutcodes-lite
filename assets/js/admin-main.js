jQuery(document).ready(function($) {

   $("#wpbr_save_setting_top , #wpbr_save_setting_bottom").on('click', function(event) {
      event.preventDefault();
      // $("#shoutcodes_main input[type='submit']").trigger('click');
      if ($("#shoutcodes_main").is(':visible') ){
         $("#shoutcodes_main input[type='submit']").trigger('click');
      }
      // else if($("#shoutcodes_import").is(':visible')){
      //    $("#shoutcodes_import input[type='submit']").trigger('click');
      // }

   });



   $(".button.twitter ,.button.facebook").on('click', function(event) {
      event.preventDefault();

      var width  = 575,
      height = 400,
      left   = ($(window).width()  - width)  / 2,
      top    = ($(window).height() - height) / 2,
      url    = this.href,
      opts   = 'status=1' +
      ',width='  + width  +
      ',height=' + height +
      ',top='    + top    +
      ',left='   + left;

      window.open(url, '', opts);
   });

   $("#shoutcodes_main input[type='radio'] ,#shoutcodes_import input[type='radio'] , #shoutcodes_import select ,#shoutcodes_import input[type='number'] , #shoutcodes-settings input[type='radio'] , #shoutcodes-settings input[type='text'] , #shoutcodes-settings input[type='checkbox']").on('change', function(event) {
      $(".setting-notification").slideDown();
   });

});
