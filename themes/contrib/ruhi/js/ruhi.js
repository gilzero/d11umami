/* Load jQuery.
--------------------------*/
jQuery(document).ready(function ($) {
  // Homepage blocks
  $(".region-content-home-top .block, .region-content-home-bottom .block").wrapInner( '<div class="container"></div>' );
  $('.region-content-home-top .block:nth-child(2), .region-content-home-top .block:nth-child(5)').prepend('<div class="circle-lines"></div>');
  $('.region-content-home-top .block:nth-child(2), .region-content-home-top .block:nth-child(4)').prepend('<div class="square"></div>');
  $('.region-content-home-top .block:nth-child(3), .region-content-home-top .block:nth-child(6)').prepend('<div class="circle-double"></div>');
  $('.region-content-home-top .block:nth-child(3), .region-content-home-top .block:nth-child(5)').prepend('<div class="triangle-left"></div>');
  $('.region-content-home-top .block:nth-child(2), .region-content-home-top .block:nth-child(6)').prepend('<div class="triangle-right"></div>');
  $('.region-content-home-top .block:nth-child(4)').prepend('<div class="circle-lines-right"></div>');
  $('.region-content-home-top .block:nth-child(3), .region-content-home-top .block:nth-child(5)').prepend('<div class="square-dot"></div>');
/* End document
--------------------------*/
});