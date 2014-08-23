/* Support placeholder independent of browser.  */
$(function(){
  $('[placeholder]').focus(function() {
  var input = $(this);
  if (input.val() == input.attr('placeholder')) {
    input.val('');
    input.removeClass('placeholder');
  }
}).blur(function() {
  var input = $(this);
  if (input.val() == '' || input.val() == input.attr('placeholder')) {
    input.addClass('placeholder');
    input.val(input.attr('placeholder'));
  }
}).blur();

$('[placeholder]').parents('form').submit(function() {
  $(this).find('[placeholder]').each(function() {
    var input = $(this);
    if (input.val() == input.attr('placeholder')) {
      input.val('');
    }
  })
});
});
// Returns model id parsed from element id w/ format "prefix-<id>"
jQuery.fn.modelId = function() {
	var id = $(this).attr('id');
	if (id == null) return null; // Just in case; browsers tested return empty string for missing id.

	var idParts = id.split(/[-_]/g); // Split on hyphens and underscores
	return (idParts.length > 1) ? idParts[idParts.length-1] : null;
}

$(document).ready(function(){
    $('ol.mbooks li').mouseover(function (){
	$(this).find('.badge').addClass('badge-pro');
    });
    $('ol.mbooks li').mouseout(function (){
	$(this).find('.badge').removeClass('badge-pro');
    });
    $('ol.ebooks li .desc-body').mouseover(function (){
	$(this).find('.badge').addClass('badge-pro');
    });
    $('ol.ebooks li .desc-body').mouseout(function (){
	$(this).find('.badge').removeClass('badge-pro');
    });
    $('ol.comments li').mouseover(function (){
	$(this).find('.badge').addClass('badge-pro');
    });
    $('ol.comments li').mouseout(function (){
	$(this).find('.badge').removeClass('badge-pro');
    });
    $('a._tooltip').tooltip();
    $('body').keydown(function(e){
        if (e.keyCode == 37){//left
            var prev=$('div.pagination a.prev');
            if ($(prev).length != 0){
                window.location = $(prev).attr('href');
            }
        }
        else if(e.keyCode==39) {//right
            var next = $('div.pagination a.next');
            if ($(next).length != 0){
                window.location = $(next).attr('href');
            }
        }
    });
});
