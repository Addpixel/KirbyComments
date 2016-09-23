<?php

/**
 * Status
 */
class CommentsStatus
{
  /**
   * The status code.
   *
   * @var integer
   */
  private $code;
  /**
   * The size of a individual status-domain.
   *
   * @var integer
   */
  static private $domain_size = 100;
  /**
   * Defines the individual status-domains.
   *
   * @var array
   */
  static private $domains = array(
    'no_error'     => 0,
    'system_error' => 100,
    'dev_error'    => 200,
    'user_error'   => 300
  );
  /**
   * The exception the status is based on.
   *
   * @var Exception
   */
  private $exception;
  
  function __construct($code, $exception = null)
  {
    $this->code = $code;
    $this->exception = $exception;
  }
  
  public function getCode()
  {
    return $this->code;
  }
  
  public function getMessage()
  {
    if ($this->exception != null) {
      return $this->getException()->getMessage();
    }
    
    return "Status with code {$this->code}.";
  }
  
  public function getException()
  {
    return $this->exception;
  }
  
  public function isUserError()
  {
    $code = $this->code;
    $user_domain = CommentsStatus::$domains['user_error'];
    $size = CommentsStatus::$domain_size;
    
    return ($code >= $user_domain) && ($code < ($user_domain + $size));
  }
  
  public function isError()
  {
    return $this->code >= CommentsStatus::$domain_size;
  }
}
