$(function(){
  //テキストカウント
  $('#js-count').keyup(function(){
    var count = $(this).val().length;
    $('.show-count').text(count);
  });
});