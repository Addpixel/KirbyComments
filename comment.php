<?php

/**
 * Comment
 */
class Comment
{
  /**
   * The per-page unique identifier of the comment. Ids start at 1 and not a 0.
   *
   * @var int
   */
  private $id;
  /**
   * The name of the author of the comment.
   *
   * @var string
   */
  private $name;
  /**
   * The e-mail address of the author of the comment. `null` if no e-mail
   * address was provided.
   *
   * @var string
   */
  private $email;
  /**
   * The address of the website of the author of the comment. `null` if no
   * website address was provided.
   *
   * @var string
   */
  private $website;
  /**
   * The message of the comment. May contain markdown-like formatting
   * instructions.
   *
   * @var string
   */
  private $message;
  /**
   * The point in time of when the comment was posted.
   *
   * @var \DateTime
   */
  private $date;
  /**
   * Whether the comment is just a preview and has not been stored yet.
   *
   * @var bool
   */
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