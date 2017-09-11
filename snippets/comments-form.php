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
<?php if ($comments->isSuccessfulSubmission()): ?>
	<p class="thank-you">Thank you for your comment!</p>
<?php else: ?>
	<h2 id="comments-form-headline">Write your comment</h2>
	
	<?php if ($status->isUserError()): ?>
		<p id="comment-<?php echo $comments->nextCommentId() ?>" class="error">
			<?php echo $status->getMessage() ?>
		</p>
	<?php endif ?>
	
	<form action="#comment-<?php echo $comments->nextCommentId() ?>" method="post" accept-charset="utf-8" role="form" aria-labelledby="comments-form-headline">
		<label for="comments-field-name">Name<?php if ($comments->requiresName()): ?><abbr title="required">*</abbr><?php endif ?></label>
		<input id="comments-field-name" type="text" name="<?php echo $comments->nameName() ?>" value="<?php echo $comments->nameValue() ?>" maxlength="<?php echo $comments->nameMaxLength() ?>" <?php e($comments->requiresName(), 'required') ?>>
		
		<label for="comments-field-email">Email Address<?php if ($comments->requiresEmailAddress()): ?><abbr title="required">*</abbr><?php endif ?></label>
		<input id="comments-field-email" type="email" name="<?php echo $comments->emailName() ?>" value="<?php echo $comments->emailValue() ?>" maxlength="<?php echo $comments->emailMaxLength() ?>" <?php e($comments->requiresEmailAddress(), 'required') ?>>
		
		<label for="comments-field-website">Website</label>
		<input id="comments-field-website" type="url" name="<?php echo $comments->websiteName() ?>" value="<?php echo $comments->websiteValue() ?>" maxlength="<?php echo $comments->websiteMaxLength() ?>">
		
		<?php if ($comments->isUsingHoneypot()): ?>
			<div style="display: none" hidden>
				<input type="text" name="<?php echo $comments->honeypotName() ?>" value="<?php echo $comments->honeypotValue() ?>">
			</div>
		<?php endif ?>
		
		<label for="comments-field-message">Message<abbr title="required">*</abbr></label>
		<textarea id="comments-field-message" name="<?php echo $comments->messageName() ?>" maxlength="<?php echo $comments->messageMaxLength() ?>" required><?php echo $comments->messageValue() ?></textarea>
		
		<input type="hidden" name="<?php echo $comments->sessionIdName() ?>" value="<?php echo $comments->sessionId() ?>">
		
		<input type="submit" name="<?php echo $comments->previewName() ?>" value="Preview">
		<?php if ($comments->isValidPreview()): ?>
			<input id="comments-submit" type="submit" name="<?php echo $comments->submitName() ?>" value="Submit">
		<?php endif ?>
	</form>
<?php endif ?>
