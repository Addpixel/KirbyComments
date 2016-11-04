<?php $comments = new Comments($page) ?>
<?php $status = $comments->process() ?>

<?php if (!$comments->isEmpty()): ?>
  <h2>Comments</h2>
  
  <?php foreach ($comments as $comment): ?>
    <article id="comment-<?= $comment->id() ?>" class="comment<?php e($comment->isPreview(), ' preview"') ?>">
      <h3>
        <?php e($comment->isLinkable(), "<a href='{$comment->website()}'>") ?>
        <?= $comment->name() ?>
        <?php e($comment->isLinkable(), "</a>") ?>
      </h3>
      
      <aside class="comment-info">
        <?php if ($comment->isPreview()): ?>
          <p>This is a preview of your comment. If you’re happy with it, <a href="#submit">submit</a> it to the public.</p>
        <?php else: ?>
          <p>
            Posted on <?= $comment->date('Y-m-d') ?>.
            <a href="#comment-<?= $comment->id() ?>">#</a>
          </p>
        <?php endif ?>
      </aside>
      
      <?= $comment->message() ?>
    </article>
  <?php endforeach ?>
<?php endif ?>

<?php if ($comments->userHasSubmitted()): ?>
  <p class="thank-you">Thank you for your comment!</p>
<?php else: ?>
  <h2>Write your comment</h2>
  
  <?php if ($status->isUserError()): ?>
    <p id="comment-<?= $comments->nextCommentId() ?>" class="error">
      <?= $status->getMessage() ?>
    </p>
  <?php endif ?>
  
  <form action="#comment-<?= $comments->nextCommentId() ?>" method="post" accept-charset="utf-8">
    <label for="name">Name<abbr title="required">*</abbr></label>
    <input type="text" name="<?= $comments->nameName() ?>" value="<?= $comments->value($comments->nameName()) ?>" id="name" maxlength="<?= $comments->fieldMaxlength() ?>" required>
    
    <label for="email">Email Address<?php if ($comments->requiresEmailAddress()): ?><abbr title="required">*</abbr><?php endif ?></label>
    <input type="email" name="<?= $comments->emailName() ?>" value="<?= $comments->value($comments->emailName()) ?>" id="email" maxlength="<?= $comments->fieldMaxlength() ?>" <?php e($comments->requiresEmailAddress(), 'required') ?>>
    
    <label for="website">Website</label>
    <input type="url" name="<?= $comments->websiteName() ?>" value="<?= $comments->value($comments->websiteName()) ?>" id="website" maxlength="<?= $comments->fieldMaxlength() ?>">
    
    <?php if ($comments->isUsingHoneypot()): ?>
      <div style="display: none" hidden>
        <input type="text" name="<?= $comments->honeypotName() ?>" value="<?= $comments->value($comments->honeypotName()) ?>">
      </div>
    <?php endif ?>
    
    <label for="message">Message<abbr title="required">*</abbr></label>
    <textarea name="<?= $comments->messageName() ?>" id="message" maxlength="<?= $comments->messageMaxlength() ?>" required><?= $comments->value($comments->messageName()) ?></textarea>
    
    <input type="hidden" name="<?= $comments->sessionIdName() ?>" value="<?= $comments->sessionId() ?>">
    
    <input type="submit" name="<?= $comments->previewName() ?>" value="Preview">
    <?php if ($comments->validPreview()): ?>
      <input type="submit" name="<?= $comments->submitName() ?>" value="Submit" id="submit">
    <?php endif ?>
  </form>
<?php endif ?>
