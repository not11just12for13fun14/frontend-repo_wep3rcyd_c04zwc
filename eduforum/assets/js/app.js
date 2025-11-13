// Basic AJAX for likes and comments (jQuery for simplicity)
(function(){
  function loadComments(postId, container){
    $.get('comments.php', {post_id: postId}, function(html){
      container.html(html);
    });
  }

  $(document).on('click', '.btn-like', function(){
    var btn = $(this);
    var postId = btn.data('post-id');
    $.post('like.php', {post_id: postId}, function(res){
      if(res && res.like_count !== undefined){
        btn.find('.like-count').text(res.like_count);
      }
    }, 'json');
  });

  $(document).on('click', '.btn-comment-toggle', function(){
    var postId = $(this).data('post-id');
    var container = $(this).closest('.post-card').find('.comments');
    container.toggle();
    if(container.is(':visible')){
      loadComments(postId, container);
    }
  });

  $(document).on('click', '.btn-comment-submit', function(){
    var postCard = $(this).closest('.post-card');
    var postId = $(this).data('post-id');
    var input = postCard.find('.comment-input');
    var text = input.val();
    if(!text) return;
    $.post('comment.php', {post_id: postId, text: text}, function(html){
      postCard.find('.comments').html(html).show();
      input.val('');
    });
  });
})();
