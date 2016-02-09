<?php

/**
 * Comments
 */
class Comments implements Iterator
{
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
  
  function valide()
  {
    return isset($this->comments[$this->iterator_index]);
  }
  
  // ===================
  // = Process Comment =
  // ===================
  
  public function process()
  {
    
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
  
  public function hasSend()
  {
    
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