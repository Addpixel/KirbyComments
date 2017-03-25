<?php

/*
 * This is the example comments form snippet. Feel free to use this code as a
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
  
  // Store `Comments` object and status for `comments-list` snippet
  kirby()->set('option', 'comments.runtime.comments', $comments);
  kirby()->set('option', 'comments.runtime.status',   $status);
} else {
  // Load `Comments` object and status from
  $comments = kirby()->get('option', 'comments.runtime.comments');
  $status = kirby()->get('option', 'comments.runtime.status');
}

?>
<?php if ($comments->userHasSubmitted()): ?>
  <p class="thank-you">Thank you for your comment!</p>
<?php else: ?>
  <h2 id="comments-form-headline">Write your comment</h2>
  
  <?php if ($status->isUserError()): ?>
    <p id="comment-<?= $comments->nextCommentId() ?>" class="error">
      <?= $status->getMessage() ?>
    </p>
  <?php endif ?>
  
  <form action="#comment-<?= $comments->nextCommentId() ?>" method="post" accept-charset="utf-8" role="form" aria-labelledby="comments-form-headline">
    <label for="name">Name<abbr title="required">*</abbr></label>
    <input type="text" name="<?= $comments->nameName() ?>" value="<?= $comments->value($comments->nameName()) ?>" id="name" maxlength="<?= $comments->nameMaxLength() ?>" required>
    
    <label for="email">Email Address<?php if ($comments->requiresEmailAddress()): ?><abbr title="required">*</abbr><?php endif ?></label>
    <input type="email" name="<?= $comments->emailName() ?>" value="<?= $comments->value($comments->emailName()) ?>" id="email" maxlength="<?= $comments->emailMaxLength() ?>"<?php e($comments->requiresEmailAddress(), ' required') ?>>
    
    <label for="website">Website</label>
    <input type="url" name="<?= $comments->websiteName() ?>" value="<?= $comments->value($comments->websiteName()) ?>" id="website" maxlength="<?= $comments->websiteMaxLength() ?>">
    
    <?php if ($comments->isUsingHoneypot()): ?>
      <div style="display: none" hidden>
        <input type="text" name="<?= $comments->honeypotName() ?>" value="<?= $comments->value($comments->honeypotName()) ?>">
      </div>
    <?php endif ?>
    
    <label for="message">Message<abbr title="required">*</abbr></label>
    <textarea name="<?= $comments->messageName() ?>" id="message" maxlength="<?= $comments->messageMaxLength() ?>" required><?= $comments->value($comments->messageName()) ?></textarea>
    
    <input type="hidden" name="<?= $comments->sessionIdName() ?>" value="<?= $comments->sessionId() ?>">
    
    <input type="submit" name="<?= $comments->previewName() ?>" value="Preview">
    <?php if ($comments->validPreview()): ?>
      <input type="submit" name="<?= $comments->submitName() ?>" value="Submit" id="submit">
    <?php endif ?>
  </form>
<?php endif ?>
