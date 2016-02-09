<?php

/**
 * Comment
 */
class Comment
{
  private $id;
  private $name;
  private $email;
  private $website;
  private $message;
  private $date;
  private $is_preview;
  
  function __construct()
  {
    
  }
  
  public function id()
  {
    return $this->id;
  }
  
  public function name()
  {
    return $this->name;
  }
  
  public function email()
  {
    return $this->email;
  }
  
  public function website()
  {
    return $this->website;
  }
  
  public function message()
  {
    return $this->message;
  }
  
  public function date($format='Y-m-d')
  {
    return date($format, $this->date);
  }
  
  public function isPreview()
  {
    return $this->is_preview === true;
  }
  
  public function isLinkable()
  {
    return $this->website != null;
  }
}