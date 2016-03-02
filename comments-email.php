<?php

/**
 * Email
 */
class CommentsEmail
{
  public $to;
  public $subject;
  public $message;
  private $status;
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
      'page.title' => $this->comment->content_page->{Comments::option('setup.page.title_key')}(),
      'page.url' => $this->comment->content_page->url()
    );
    
    return preg_replace_callback('/\{\{\s*(\S+)\s*\}\}/', function ($matches) use ($placeholders)
    {
      $identifer = $matches[1];
      
      if (isset($placeholders[$identifer])) {
        return $placeholders[$identifer];
      } else {
        return Comments::option('email.undefined-value');
      }
    }, $x);
  }
  
  public function send()
  {
    $template = file_get_contents(__DIR__.'/email.template.txt');
    
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