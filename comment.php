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
  private $datetime;
  /**
   * Whether the comment is just a preview and has not been stored yet.
   *
   * @var bool
   */
  private $is_preview;
  
  function __construct($id, $name, $email, $website, $message, $datetime, $is_preview = false)
  {
    if (gettype($id) !== 'integer') {
      throw new Exception('The id of a comment must be of the type integer.', 300);
    } else if ($id <= 0) {
      throw new Exception('The id of a comment must be bigger than 0.', 301);
    } else if (preg_match('/^\s*$/', $name)) {
      throw new Exception('The name field is required.', 200);
    } else if (Comments::option('require.email') && preg_match('/^\s*$/', $email)) {
      throw new Exception('The e-mail address field is required.', 201);
    } else if (Comments::option('require.email') && !v::email($email)) {
      throw new Exception('The e-mail address is not valid.', 202);
    } else if (preg_match('/^\s*javascript:/i', $website)) {
      throw new Exception('The website address may not contain JavaScript code.', 203);
    } else if (preg_match('/^\s*$/m', $message)) {
      throw new Exception('The message must not be empty.', 204);
    }
    
    $this->id         = $id;
    $this->name       = htmlspecialchars(trim(strip_tags($name)));
    $this->email      = htmlspecialchars(trim(strip_tags($email)));
    $this->website    = htmlspecialchars(trim(strip_tags($website)));
    $this->message    = htmlspecialchars(trim(strip_tags($message)));
    $this->datetime   = $datetime;
    $this->is_preview = $is_preview === true;
    
    if ($this->email   == '') { $this->email   = null; }
    if ($this->website == '') { $this->website = null; }
  }
  
  public static function from_post($id, $datetime, $is_preview)
  {
    return new Comment(
      $id,
      $_POST[Comments::option('form.name')],
      $_POST[Comments::option('form.email')],
      $_POST[Comments::option('form.website')],
      $_POST[Comments::option('form.message')],
      $datetime,
      isset($_POST[Comments::option('form.submit')])
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