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

$comments = $page->comments();
$status = $comments->process();

?>
<?php if (!$comments->isEmpty()): ?>
	<h2>Comments</h2>
	
	<?php foreach ($comments as $comment): ?>
		<article id="comment-<?php echo $comment->id() ?>" class="comment<?php e($comment->isPreview(), ' preview"') ?>">
			<h3>
				<?php e($comment->isLinkable(), '<a rel="nofollow noopener" href="'.$comment->website().'">') ?>
				<?php echo $comment->name() ?>
				<?php e($comment->isLinkable(), '</a>') ?>
			</h3>
			
			<aside class="comment-info">
				<?php if ($comment->isPreview()): ?>
					<p>This is a preview of your comment. If you’re happy with it, <a href="#comments-submit" title="Jump to the submit button">submit</a> it to the public.</p>
				<?php else: ?>
					<p>Posted on <?php echo $comment->date('Y-m-d') ?>.
						<a href="#comment-<?php echo $comment->id() ?>" title="Permalink" area-label="Permalink">#</a></p>
				<?php endif ?>
			</aside>
			
			<?php echo $comment->message() ?>
		</article>
	<?php endforeach ?>
<?php endif ?>
