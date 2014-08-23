$(document).ready(function(){
  $('textarea').live('focus', function() { $(this).autosize ();});
  $('.shortened').click (function(){$(this).next().removeClass('hide');$(this).parent().removeClass('pointer');$(this).parent().find('.show-more').addClass('hide')});
  $('.shortened').mouseover (function(){$(this).parent().find('.show-more').addClass('hovered');});
  $('.shortened').mouseleave (function(){$(this).parent().find('.show-more').removeClass('hovered');});
  $('.chapter-content-bref').click (function(){$(this).parent().removeClass('pointer'); $(this).parent().find('.show-more').click();});
  $('.chapter-content-bref').mouseover (function(){$(this).parent().find('.show-more').addClass('hovered');});
  $('.chapter-content-bref').mouseleave (function(){$(this).parent().find('.show-more').removeClass('hovered');});
});
