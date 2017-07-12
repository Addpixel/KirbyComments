<?php

/**
 * Email
 */
class CommentsEmail
{
  /**
   * A list of the recipients. An array of email addresses/strings.
   *
   * @var array
   */
  public $to;
  /**
   * The subject of the email.
   *
   * @var string
   */
  public $subject;
  /**
   * The message of the email. May contain placeholders.
   *
   * @var string
   */
  public $message;
  /**
   * The status of the email.
   *
   * @var CommentsStatus
   */
  private $status;
  /**
   * The comment about which this the email informs.
   *
   * @var Comment
   */
  private $comment;
  
  function __construct($to, $subject, $comment)
  {
    $this->comment = $comment;
    $this->to = $to;
    $this->subject = $this->format($subject);
    $this->message = strip_tags($comment->message());
    $this->status = new CommentsStatus(0);
  }
  
  public function format($x)
  {
    $placeholders = array(
      'comment.user.name' => $this->comment->name(),
      'comment.user.email' => $this->comment->email(),
      'comment.user.website' => $this->comment->website(),
      'comment.message' => $this->comment->rawMessage(),
      'page.title' => Comments::option('setup.content-page.title', $this->comment->content_page),
      'page.url' => $this->comment->content_page->url()
    );
    
    return preg_replace_callback('/\{\{\s*(\S+?)\s*\}\}/', function ($matches) use ($placeholders)
    {
      $identifer = $matches[1];
      
      if ($placeholders[$identifer]) {
        return $placeholders[$identifer];
      } else {
        return Comments::option('email.undefined-value');
      }
    }, $x);
  }
  
  public function send()
  {
    $template_file = 'email.template.txt';
    $plugin_template_file = __DIR__."/../assets/$template_file";
    $custom_template_file = __DIR__."/../../../../assets/plugins/comments/$template_file";
    
    $template = "";
    
    if (file_exists($custom_template_file)) {
      $template = file_get_contents($custom_template_file);
    } else {
      $template = file_get_contents($plugin_template_file);
    }
    
    if ($template === false) {
      return new CommentsStatus(202);
    }
    
    $body = $this->format($template);
    $headers = 'Content-type: text/plain; charset=utf-8';
    
    foreach ($this->to as $to) {
      if (!mail($to, $this->subject, $body, $headers)) {
        return new CommentsStatus(203);
      }
    }
    
    return $this->status;
  }
}
