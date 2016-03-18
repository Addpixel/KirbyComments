<?php $comments = new Comments($page); ?>
<?php $status = $comments->process(); ?>

<?php if (!$comments->isEmpty()): ?>
  <h2>Comments</h2>
  
  <?php foreach ($comments as $comment): ?>
    <article id="comment-<?php echo $comment->id() ?>" <?php e($comment->isPreview(), ' class="preview"') ?>>
      <h3>
        <?php e($comment->isLinkable(), "<a href='{$comment->website()}'>") ?>
        <?php echo $comment->name() ?>
        <?php e($comment->isLinkable(), "</a>") ?>
      </h3>
      
      <aside class="comment-info">
        <?php if ($comment->isPreview()): ?>
          <p>This is a preview of you comment. If you’re happy with it, <a href="#submit">submit</a> it to the public.</p>
        <?php else: ?>
          <p>
            Posted on <?php echo $comment->date('Y-m-d') ?>.
            <a href="#comment-<?php echo $comment->id() ?>">#</a>
          </p>
        <?php endif ?>
      </aside>
      
      <?php echo $comment->message() ?>
    </article>
  <?php endforeach ?>
<?php endif ?>

<?php if ($comments->userHasSubmitted()): ?>
  <p class="thank-you">Thank you for your comment!</p>
<?php else: ?>
  <h2>Write your comment</h2>
  
  <?php if ($status->isUserError()): ?>
    <p id="comment-<?php echo $comments->nextCommentId() ?>" class="error">
      <?php echo $status->getMessage() ?>
    </p>
  <?php endif ?>
  
  <form action="#comment-<?php echo $comments->nextCommentId() ?>" method="post" accept-charset="utf-8">
    <label for="name">Name<abbr title="required">*</abbr></label>
    <input type="text" name="name" value="<?php echo $comments->value('name') ?>" id="name" required>
    
    <label for="email">Email Address<?php if ($comments->requiresEmailAddress()): ?><abbr title="required">*</abbr><?php endif ?></label>
    <input type="email" name="email" value="<?php echo $comments->value('email') ?>" id="email" <?php e($comments->requiresEmailAddress(), 'required') ?>>
    
    <label for="website">Website</label>
    <input type="url" name="website" value="<?php echo $comments->value('website') ?>" id="website">
    
    <?php if ($comments->isUsingHoneypot()): ?>
      <div style="display: none" hidden>
        <input type="text" name="<?php echo $comments->honeypotName() ?>" value="<?php echo $comments->value($comments->honeypotName()) ?>">
      </div>
    <?php endif ?>
    
    <label for="message">Message<abbr title="required">*</abbr></label>
    <textarea name="message" id="message" maxlength="<?php echo $comments->messageMaxlength() ?>" required><?php echo $comments->value('message') ?></textarea>
    
    <input type="hidden" name="<?php echo $comments->sessionIdName() ?>" value="<?php echo $comments->sessionId() ?>">
    
    <input type="submit" name="<?php echo $comments->previewName() ?>" value="Preview">
    <?php if ($comments->validPreview()): ?>
      <input type="submit" name="<?php echo $comments->submitName() ?>" value="Submit" id="submit">
    <?php endif ?>
  </form>
<?php endif ?>