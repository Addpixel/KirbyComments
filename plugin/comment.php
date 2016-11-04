<?php

/**
 * Comment
 */
class Comment
{
  /**
   * The per-page unique identifier of the comment. Ids start at 1, not a 0.
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
    if (gettype($id) !== 'integer') {
      throw new Exception('The id of a comment must be of the type integer.', 100);
    } elseif ($id <= 0) {
      throw new Exception('The id of a comment must be bigger than 0.', 101);
    } elseif (trim($name) == '') {
      throw new Exception('The name field is required.', 301);
    } elseif (strlen($name) > Comments::option('max-field-length')) {
      throw new Exception('The name is too long.', 302);
    } elseif (Comments::option('require.email') && preg_match('/^\s*$/', $email)) {
      throw new Exception('The email address field is required.', 303);
    } elseif (Comments::option('require.email') && !v::email($email)) {
      throw new Exception('The email address is not valid.', 304);
    } elseif (strlen($email) > Comments::option('max-field-length')) {
      throw new Exception('The email address is too long.', 305);
    } elseif (preg_match('/^\s*javascript:/i', $website)) {
      throw new Exception('The website address may not contain JavaScript code.', 306);
    } elseif (strlen($website) > Comments::option('max-field-length')) {
      throw new Exception('The website address is too long.', 307);
    } elseif (trim($message) == '') {
      throw new Exception('The message must not be empty.', 308);
    } elseif (strlen($message) > Comments::option('max-character-count')) {
      throw new Exception('The message is to long. (A maximum of '.Comments::option('max-character-count').' characters is allowed.)', 309);
    }
    
    $this->content_page = $content_page;
    $this->id           = $id;
    $this->name         = htmlspecialchars(trim(strip_tags($name)));
    $this->email        = htmlspecialchars(trim(strip_tags($email)));
    $this->website      = htmlspecialchars(trim(strip_tags($website)));
    $this->message      = trim($message);
    $this->datetime     = $datetime;
    $this->is_preview   = $is_preview === true;
    
    if (trim($this->email)   == '') { $this->email   = null; }
    
    if (trim($this->website) == '') {
      $this->website = null;
    } else if (!preg_match('/^https?:/', $this->website)) {
      $this->website = 'http://'.$this->website;
    }
  }
  
  public static function from_post($content_page, $id, $datetime)
  {
    if (Comments::option('use.honeypot')) {
      $post_value = Comments::option('form.honeypot');
      $human_value = Comments::option('human-honeypot-value');
        
      if ($_POST[$post_value] != $human_value) {
        throw new Exception('Comment must be written by a human being.', 310);
      }
    }
    
    return new Comment(
      $content_page,
      $id,
      $_POST[Comments::option('form.name')],
      $_POST[Comments::option('form.email')],
      $_POST[Comments::option('form.website')],
      $_POST[Comments::option('form.message')],
      $datetime,
      isset($_POST[Comments::option('form.preview')])
    );
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
    return strip_tags(markdown($this->message), Comments::option('allowed_tags'));
  }
  
  public function rawMessage()
  {
    return $this->message;
  }
  
  public function date($format='Y-m-d')
  {
    return $this->datetime->format($format);
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
