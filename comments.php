<?php

include_once('comment.php');
include_once('commentstatus.php');

/**
 * Comments
 */
class Comments implements Iterator
{
  private $defaults = array(
    'comments.names.honeypot' => 'subject'
  );
  private $iterator_index;
  private $comments;
  
  function __construct($page)
  {
    $this->iterator_index = 0;
    $this->comments = array();
  }
  
  // ============
  // = Iterator =
  // ============
  
  function rewind()
  {
    $this->iterator_index = 0;
  }
  
  function current()
  {
    return $this->comments[$this->iterator_index];
  }
  
  function key()
  {
    return $this->iterator_index;
  }
  
  function next()
  {
    $this->iterator_index += 1;
  }
  
  function valid()
  {
    return isset($this->comments[$this->iterator_index]);
  }
  
  // ===================
  // = Process Comment =
  // ===================
  
  public function process()
  {
    // TODO: Return Real Status
    return new CommentStatus(0);
  }
  
  // =================
  // = Comments List =
  // =================
  
  public function isEmpty()
  {
    return count($this->comments) == 0;
  }
  
  // ========
  // = Form =
  // ========
  
  public function nextCommentId()
  {
    $stored_comments = array_filter($this->comments, function ($x)
    {
      return $x->isPreview() === false;
    });
    
    return count($stored_comments);
  }
  
  public function userHasSubmitted()
  {
    return isset($_POST['submit']);
  }
  
  public function value($name)
  {
    if (isset($_POST['preview'])) {
      return strip_tags(htmlentities(trim($_POST[$name])));
    } else {
      return '';
    }
  }
  
  public function honeypotName()
  {
    
  }
  
  public function sessionName()
  {
    
  }
  
  public function sessionId()
  {
    
  }
  
  public function previewName()
  {
    
  }
   
  public function validePreview()
  {
    
  }
  
  public function submitName()
  {
    
  }
}