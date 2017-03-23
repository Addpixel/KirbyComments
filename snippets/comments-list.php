<?php

/*
 * This is the example comments list snippet. Feel free to use this code as a
 * reference for creating your own, custom comments snippet.
 * 
 * Custom snippet markup guide:
 * <https://github.com/Addpixel/KirbyComments#custom-markup>
 * 
 * API documentation:
 * <https://github.com/Addpixel/KirbyComments#api-documentation>
 */

if (kirby()->get('option', 'comments.runtime.comments') == null) {
  // Create `Comments` object for the current page
  $comments = new Comments($page);
  $status = $comments->process();
  
  // Store `Comments` object and status for `comments-form` snippet
  kirby()->set('option', 'comments.runtime.comments', $comments);
  kirby()->set('option', 'comments.runtime.status',   $status);
} else {
  // Load `Comments` object and status from
  $comments = kirby()->get('option', 'comments.runtime.comments');
  $status = kirby()->get('option', 'comments.runtime.status');
}

?>
<?php if (!$comments->isEmpty()): ?>
  <h2>Comments</h2>
  
  <?php foreach ($comments as $comment): ?>
    <article id="comment-<?= $comment->id() ?>" class="comment<?php e($comment->isPreview(), ' preview"') ?>">
      <h3>
        <?php e($comment->isLinkable(), "<a rel='nofollow noopener' href='{$comment->website()}'>") ?>
        <?= $comment->name() ?>
        <?php e($comment->isLinkable(), "</a>") ?>
      </h3>
      
      <aside class="comment-info">
        <?php if ($comment->isPreview()): ?>
          <p>This is a preview of your comment. If you’re happy with it, <a href="#submit" title="Jump to the submit button">submit</a> it to the public.</p>
        <?php else: ?>
          <p>
            Posted on <?= $comment->date('Y-m-d') ?>.
            <a href="#comment-<?= $comment->id() ?>" title="Permalink" area-label="Permalink">#</a>
          </p>
        <?php endif ?>
      </aside>
      
      <?= $comment->message() ?>
    </article>
  <?php endforeach ?>
<?php endif ?>
