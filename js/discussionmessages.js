/* Copyright 2014 Zachary Doll */
jQuery(document).ready(function($) {
  $('#MobileBodyRow').hide();
  $('#MobileBodyCheck input').removeAttr('checked').click(function(e) {
    $('#MobileBodyRow').slideToggle();
  });
});
