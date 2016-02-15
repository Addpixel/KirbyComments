<?php

/**
 * Email
 */
class CommentsEmail
{
  public $to;
  public $subject;
  public $message;
  private $comment;
  
  function __construct($comment)
  {
    $this->comment = $comment;
    $this->to = Comments::option('email.to');
    $this->subject = $this->format(Comments::option('email.subject'));
    $this->message = strip_tags($comment->message());
  }
  
  public function format($x)
  {
    $placeholders = array(
      'comment.user.name' => $this->comment->name(),
      'comment.user.email' => $this->comment->email(),
      'comment.user.website' => $this->comment->website(),
      'comment.message' => strip_tags($this->comment->message()),
      'page.name' => $this->comment->content_page->{Comments::option('setup.page.title_key')}(),
      'page.url' => $this->comment->content_page->url()
    );
    
    return preg_replace_callback('/\{\{\s*(\S+)\s*\}\}/', function ($matches) use ($placeholders)
    {
      return $placeholders[$matches[1]];
    }, $x);
  }
  
  public function send()
  {
    
  }
}