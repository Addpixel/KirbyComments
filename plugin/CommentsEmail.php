<?php

/**
 * CommentsEmail
 * 
 * Data and mechanisms used to send email notifications about new comments to
 * a list of recipients.
 * 
 * @package   Kirby Comments
 * @author    Florian Pircher <florian@addpixel.net>
 * @link      https://addpixel.net/kirby-comments/
 * @copyright Florian Pircher
 * @license   https://addpixel.net/kirby-comments/LICENSE
 */
class CommentsEmail
{
	/**
	 * List of email recipients.
	 *
	 * @var string[]
	 */
	private $to;
	
	/**
	 * Subject of the email. May contain placeholders.
	 *
	 * @var string
	 */
	private $subject;
	
	/**
	 * Message of the email. May contain placeholders.
	 *
	 * @var string
	 */
	private $message;
	
	/**
	 * Status of the email.
	 *
	 * @var CommentsStatus
	 */
	private $status;
	
	/**
	 * Comment about which the email informs the list of recipients.
	 *
	 * @var Comment
	 */
	private $comment;
	
	/**
	 * CommentsEmail constructor.
	 * 
	 * @param string[] $to
	 * @param string $subject
	 * @param Comment $comment
	 */
	function __construct($to, $subject, $comment)
	{
		$this->comment = $comment;
		$this->to = $to;
		$this->message = strip_tags($comment->message());
		$this->status = new CommentsStatus(0);
		$this->subject = CommentsEmail::format($comment, $subject);
	}
	
	/**
	 * Replaces placeholders of the pattern `{{ some.key }}` with the
	 * corresponding value using the data of a comment.
	 *
	 * @param Comment $comment
	 * @param string $text Template string.
	 * @return string
	 */
	private static function format($comment, $text)
	{
		$placeholders = array(
			'comment.user.name' => $comment->name(),
			'comment.user.email' => $comment->email(),
			'comment.user.website' => $comment->website(),
			'comment.message' => $comment->rawMessage(),
			'page.title' => Comments::option('setup.content-page.title', $comment->page()),
			'page.url' => $comment->page()->url()
		);
		
		return preg_replace_callback('/\{\{\s*(\S+?)\s*\}\}/', function ($matches) use ($placeholders)
		{
			$identifier = $matches[1];
			
			if ($placeholders[$identifier]) {
				return $placeholders[$identifier];
			} else {
				return Comments::option('email.undefined-value');
			}
		}, $text);
	}
	
	/**
	 * Send the email to the recipients.
	 *
	 * @return CommentsStatus
	 */
	public function send()
	{
		$template_file = 'email.template.txt';
		$plugin_template_file = __DIR__.'/../assets/'.$template_file;
		$custom_template_file = __DIR__.'/../../../../assets/plugins/comments/'.$template_file;
		
		if (file_exists($custom_template_file)) {
			$template = file_get_contents($custom_template_file);
		} else {
			$template = file_get_contents($plugin_template_file);
		}
		
		if ($template === false) {
			return new CommentsStatus(202);
		}
		
		$body = CommentsEmail::format($this->comment, $template);
		$headers = 'Content-type: text/plain; charset=utf-8';
		
		foreach ($this->to as $to) {
			if (!mail($to, $this->subject, $body, $headers)) {
				return new CommentsStatus(203);
			}
		}
		
		return $this->status;
	}
}
