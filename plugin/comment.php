<?php

/**
 * Comment
 */
class Comment
{
  /**
   * The per-page unique identifier of the comment. Ids start at 1, not at 0.
   *
   * @var integer
   */
  private $id;
  /**
   * The name of the author of the comment.
   *
   * @var string
   */
  private $name;
  /**
   * The email address of the author of the comment. `null` if no email
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
  private $datetime;
  /**
   * Whether the comment is just a preview and has not been stored yet.
   *
   * @var bool
   */
  private $is_preview;
  /**
   * The content page (the page with the comment and not the page of the 
   * comment).
   *
   * @var Page
   */
  public $content_page;
  
  function __construct($content_page, $id, $name, $email, $website, $message, $datetime, $is_preview = false)
  {
    $this->content_page = $content_page;
    $this->id           = $id;
    $this->name         = trim(strip_tags($name));
    $this->email        = trim(strip_tags($email));
    $this->website      = trim(strip_tags($website));
    $this->message      = trim($message);
    $this->datetime     = $datetime;
    $this->is_preview   = $is_preview === true;
    
    if (trim($this->email) == '') { $this->email   = null; }
    
    if (trim($this->website) == '') {
      $this->website = null;
    } elseif (!preg_match('/^https?:/', $this->website)) {
      $this->website = 'http://'.$this->website;
    }
  }
  
  private static function qq($array, $key, $default)
  {
    if (isset($array[$key])) {
      return $array[$key];
    }
    return $default;
  }
  
  public static function from_post($content_page, $id, $datetime)
  {
    if (Comments::option('honeypot.enabled')) {
      $post_value = $_POST[Comments::option('form.honeypot')];
      $human_value = Comments::option('honeypot.human-value');
      
      if ($post_value != $human_value) {
        throw new Exception('Comment must be written by a human being.', 310);
      }
    }
    
    // Check POST data
    $name       = trim(Comment::qq($_POST, Comments::option('form.name'), ''));
    $email      = trim(Comment::qq($_POST, Comments::option('form.email'), ''));
    $website    = trim(Comment::qq($_POST, Comments::option('form.website'), ''));
    $message    = trim(Comment::qq($_POST, Comments::option('form.message'), ''));
    $is_preview = isset($_POST[Comments::option('form.preview')]);
    
    if (gettype($id) !== 'integer') {
      throw new Exception('The ID of a comment must be of the type integer.', 100);
    } elseif ($id <= 0) {
      throw new Exception('The ID of a comment must be bigger than 0.', 101);
    } elseif ($name == '') {
      throw new Exception('The name field is required.', 301);
    } elseif (strlen($name) > Comments::option('form.name.max-length')) {
      throw new Exception('The name is too long.', 302);
    } elseif (Comments::option('form.email.required') && $email == '') {
      throw new Exception('The email address field is required.', 303);
    } elseif ($email != '' && !v::email($email)) {
      throw new Exception('The email address is not valid.', 304);
    } elseif (strlen($email) > Comments::option('form.email.max-length')) {
      throw new Exception('The email address is too long.', 305);
    } elseif (preg_match('/^\s*javascript:/i', $website)) {
      throw new Exception('The website address may not contain JavaScript code.', 306);
    } elseif (strlen($website) > Comments::option('form.website.max-length')) {
      throw new Exception('The website address is too long.', 307);
    } elseif ($message == '') {
      throw new Exception('The message must not be empty.', 308);
    } elseif (strlen($message) > Comments::option('form.message.max-length')) {
      throw new Exception('The message is to long. (A maximum of '.Comments::option('form.message.max-length').' characters is allowed.)', 309);
    }
    
    return new Comment($content_page, $id, $name, $email, $website, $message, $datetime, $is_preview);
  }
  
  public function id()
  {
    return $this->id;
  }
  
  public function name()
  {
    return htmlentities($this->name);
  }
  
  public function email()
  {
    return htmlentities($this->email);
  }
  
  public function website()
  {
    return htmlentities($this->website);
  }
  
  public function message()
  {
    $message = markdown(htmlentities($this->message));
    
    if (Comments::option('form.message.smartypants')) {
      $message = smartypants($message);
    }
    
    return strip_tags($message, Comments::option('form.message.allowed_tags'));
  }
  
  public function rawMessage()
  {
    return $this->message;
  }
  
  public function date($format='Y-m-d')
  {
    return $this->datetime->format($format);
  }
  
  public function datetime()
  {
    return $this->datetime;
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
