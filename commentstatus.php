<?php

/**
 * Status
 */
class CommentStatus
{
  private $code;
  private $domain_size = 100;
  private $domains = array(
    'no_error'     => 0,
    'system_error' => 100,
    'user_error'   => 200,
    'dev_error'    => 300
  );
  
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
  
  public function isUserError()
  {
    $code = $this->code;
    $user_domain = $this->domains['user_error'];
    
    return $code >= $user_domain && $code < $user_domain + $this->domain_size;
  }
}