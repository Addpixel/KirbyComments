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

$comments = $page->comments();
$status = $comments->process();

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
    <label for="comments-field-name">Name<abbr title="required">*</abbr></label>
    <input id="comments-field-name" type="text" name="<?= $comments->nameName() ?>" value="<?= $comments->nameValue() ?>" maxlength="<?= $comments->nameMaxLength() ?>" required>
    
    <label for="comments-field-email">Email Address<?php if ($comments->requiresEmailAddress()): ?><abbr title="required">*</abbr><?php endif ?></label>
    <input id="comments-field-email" type="email" name="<?= $comments->emailName() ?>" value="<?= $comments->emailValue() ?>" maxlength="<?= $comments->emailMaxLength() ?>" <?php e($comments->requiresEmailAddress(), 'required') ?>>
    
    <label for="comments-field-website">Website</label>
    <input id="comments-field-website" type="url" name="<?= $comments->websiteName() ?>" value="<?= $comments->websiteValue() ?>" maxlength="<?= $comments->websiteMaxLength() ?>">
    
    <?php if ($comments->isUsingHoneypot()): ?>
      <div style="display: none" hidden>
        <input type="text" name="<?= $comments->honeypotName() ?>" value="<?= $comments->honeypotValue() ?>">
      </div>
    <?php endif ?>
    
    <label for="comments-field-message">Message<abbr title="required">*</abbr></label>
    <textarea id="comments-field-message" name="<?= $comments->messageName() ?>" maxlength="<?= $comments->messageMaxLength() ?>" required><?= $comments->messageValue() ?></textarea>
    
    <input type="hidden" name="<?= $comments->sessionIdName() ?>" value="<?= $comments->sessionId() ?>">
    
    <input type="submit" name="<?= $comments->previewName() ?>" value="Preview">
    <?php if ($comments->validPreview()): ?>
      <input id="comments-submit" type="submit" name="<?= $comments->submitName() ?>" value="Submit">
    <?php endif ?>
  </form>
<?php endif ?>
