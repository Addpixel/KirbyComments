<?php

/**
 * Status
 */
class CommentStatus
{
  private $code;
  static private $domain_size = 100;
  static private $domains = array(
    'no_error'     => 0,
    'system_error' => 100,
    'dev_error'    => 200,
    'user_error'   => 300
  );
  private $exception = null;
  
  function __construct($code)
  {
    $this->code = $code;
  }
  
  public function getCode()
  {
    return $this->code;
  }
  
  public function getMessage()
  {
    return "[Status code {$this->code}]";
  }
  
  public function getException()
  {
    return $this->exception;
  }
  
  public function isUserError()
  {
    $code = $this->code;
    $user_domain = CommentStatus::$domains['user_error'];
    $size = CommentStatus::$domain_size;
    
    return $code >= $user_domain && $code < $user_domain + $size;
  }
}