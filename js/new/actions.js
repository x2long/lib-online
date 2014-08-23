var Actions = {
  Comment: {
    submit: function (){
        var form = $(this);
        form.ajaxForm (function (){
          var comment_input = form.find('#id_comment');
          var comment = comment_input.val();
          var button = form.find ('input#id_submit');
          button.val ('提交中');
          button.attr ('disabled','disabled');
          form.replaceWith(comment);
          return false;
        });
    return false;
    }
  },
    Buy: {
      toggle: function (){
        if (! $(this).hasClass('processing')) {
          var $link = $(this);
          var url = $link.attr('href');
          var data = $link.hasClass ('current-user-likes') ? {_method: 'delete'}:{_method: 'like'}
  
          $.ajax({
            type: 'POST',
            url: url,
            data:data,
            beforeSend: function(){
              $link.addClass('processing');
              $link.html ('<span class="ui-button-text">稍等...</span>');
            },
            success: function(responseHtml){
                $link.closest('.ajax-replaced-with').replaceWith(responseHtml);
            }
          });
        }
        return false;
      }
    },
  
  Like: {
    toggle: function (){
      if (! $(this).hasClass('processing')) {
        var $link = $(this);
        var url = $link.attr('href');
        console.log($link);
        var data = $link.hasClass ('current-user-likes') ? {_method: 'delete'}:{_method: 'like'}

        $.ajax({
          type: 'POST',
          url: url,
          data:data,
          beforeSend: function(){
            $link.addClass('processing');
            $link.text ('稍等...');
          },
          success: function(responseHtml){
              $link.closest('#like-action').replaceWith(responseHtml);
          }
        });
      }
      return false;
    }
  },

  Push: {
    toggle: function (){
      if (! $(this).hasClass('processing')) {
        var $link = $(this);
        var url = $link.attr('href');
        var data = {_method: 'push'}

        $.ajax({
          type: 'POST',
          url: url,
          data:data,
          beforeSend: function(){
            $link.addClass('processing');
            $link.text ('稍等...');
          },
          success: function(responseHtml){
              $link.closest('.replace-with-ajax').replaceWith(responseHtml);
          }
        });
      }
      return false;
    }
  },
    
  Vote: {
    toggle: function (){
        var $link = $(this);
        var url = $link.attr('href');
        var data = $link.hasClass ('active') ? {_method: 'clear'}:{_method:'vote'}
	var $replaced = $(this).closest ('.votes');
        $.ajax({
          type: 'POST',
          url: url,
          data:data,
          beforeSend: function(){
          },
          success: function(responseHtml){
            $replaced.replaceWith(responseHtml);
          }
        });
      return false;
    }
  },
  Follow: {
    toggle: function (){
      var $link = $(this);
      var follow = $link.closest('.follow-users');
      var html = follow.html();
      
      $link.closest('.follow-users form').ajaxSubmit({
        beforeSend: function(){
          $link.addClass('processing');
          $link.find('span').text('Wait...');
        },
        success:function(responseHtml) {
          $link.closest('.follow-users').replaceWith(responseHtml);
        },
        error:function(request){
          follow.html(html);
//          alert(request.responseText);
        }
      });
      return false;
    }
  },
  
  Deletex: {
    del: function (){
        var $link = $(this);
        var url = $link.attr('href');
        var book = $link.closest ('li.book');
        $.ajax({
          type: 'POST',
          url: url,
          data:{},
          beforeSend: function(){
            $('.delete-x').addClass('processing');
            $link.text ('请稍等...');
          },
          success: function(responseHtml){
            console.log (responseHtml);
            book.fadeOut('slow');
          }
        });
      return false;
    }
  },
  Toggle: {
    private: function (){
        var $link = $(this);
        var url = $link.attr('href');
        var privatediv = $link.closest ('#private-booklist');
        $.ajax({
          type: 'POST',
          url: url,
          data:{},
          beforeSend: function(){
            $('.make-public').addClass('processing');
            $link.text ('请稍等...');
          },
          success: function(responseHtml){
            console.log (responseHtml);
            $('div.toggle-public-private').replaceWith(responseHtml);
          }
        });
      return false;
    }
  }
};
$(document).ready(function(){
  $('.buy-in-one-click').live ('click', Actions.Buy.toggle);
  $('.also-want-this-book').live ('click', Actions.Buy.toggle);
  $('.get-random-paragraphs').live ('click', Actions.Buy.toggle);
  $('.toggle-follow-book').live ('click', Actions.Buy.toggle);
  $('.like-paragraphs-ajax').live ('click', Actions.Buy.toggle);
  $('.push2kindle').live ('click', Actions.Buy.toggle);
  $('.fav-toggle').live ('click', Actions.Buy.toggle);
  $('.follow-toggle').live ('click', Actions.Buy.toggle);
  $('.get_full_content').live ('click', Actions.Buy.toggle);

  $('.ajax-clicking').live ('click', Actions.Push.toggle);
  $('.ajax-submit-comment').live ('click', function(event){event.preventDefault(); $(this).hide();$('li.comment-submission').css('display','block');});
  $('form.comment_life').focusin(Actions.Comment.submit);
  $('.like a.fav-toggle').live ('click', Actions.Like.toggle);
  $('.votes a.vote-action').live ('click', Actions.Vote.toggle);
  $('.follow-users a').live ('click', Actions.Follow.toggle);
  $('a.delete-x').live ('click', Actions.Deletex.del);
  $('a.make-public').live ('click', Actions.Toggle.private);
  var pdbottom=$('div#left-panel').css('padding-bottom'); 
  $('input#addbook').focusout(function(){$('div#left-panel').css('padding-bottom',pdbottom)});
  $('input#addbook').focusin(function(){$('div#left-panel').css('padding-bottom','200px')});
  $('ol#books li.book').mouseover(function(){$(this).find('span.add-to-list').css('display','block')});
  $('ol#books li.book').mouseout(function(){$(this).find('span.add-to-list').css('display','none')}); 
  $('a.toggle-hidden-div').live ('mouseenter',function () {$(this).css ('opacity','1');});
  $('a.toggle-hidden-div').live ('mouseout',function () {$(this).css ('opacity','0');});
  });



