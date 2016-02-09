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
  private $stored_comments;
  
  function __construct($page)
  {
    $this->iterator_index = 0;
    $this->comments = array();
    $this->stored_comments = array();
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
    return count($this->stored_comments);
  }
  
  public function hasSubmit()
  {
    return isset($_POST['submit']);
  }
  
  public function value($name)
  {
    
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