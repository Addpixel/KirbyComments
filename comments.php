<?php

include_once('plugin/comment.php');
include_once('plugin/comments-email.php');
include_once('plugin/comments-status.php');

/**
 * Comments
 */
class Comments implements Iterator
{
  /**
   * All default values linked to their options-keys.
   *
   * @var array
   */
  private static $defaults = array(
    'comments-page.title'    => 'Comments',
    'comments-page.dirname'  => 'comments',
    'comments-page.template' => 'comments',
    'comment-page.dirname'   => 'comment',
    'comment-page.template'  => 'comment',
    'comments-snippet'       => 'comments',
    'form.submit'            => 'submit',
    'form.preview'           => 'preview',
    'form.name'              => 'name',
    'form.email'             => 'email',
    'form.website'           => 'website',
    'form.message'           => 'message',
    'form.honeypot'          => 'subject', 
    'form.session_id'        => 'session_id',
    'session.key'            => 'comments',
    'require.email'          => false,
    'use.honeypot'           => true,
    'use.email'              => false,
    'allowed_tags'           => '<p><br><a><em><strong><code><pre>',
    'max-character-count'    => 1000,
    'max-field-length'       => 64,
    'human-honeypot-value'   => '',
    'email.to'               => array(),
    'email.subject'          => 'New Comment on {{ page.title }}',
    'email.undefined-value'  => '(not specified)',
    'setup.page.title_key'   => 'title'
  );
  /**
   * The Kirby page the comments object is about.
   *
   * @var Page
   */
  private $page;
  /**
   * The status of the comments.
   *
   * @var CommentsStatus
   */
  private $status;
  /**
   * The index of the iterator.
   *
   * @var integer
   */
  private $iterator_index;
  /**
   * An array of comments on $this->page (published and unpublished).
   *
   * @var array
   */
  private $comments;
  /**
   * Whether the current comment preview is valid.
   *
   * @var string
   */
  private $valid_preview;
  
  function __construct($page)
  {
    $this->page = $page;
    $this->status = new CommentsStatus(0);
    $this->iterator_index = 0;
    $this->comments = array();
    $this->valid_preview = false;
    
    $comments_page = $this->page->find(Comments::option('comments-page.dirname'));
    
    if ($comments_page != null) {
      foreach ($comments_page->children() as $comment_page) {
        try {
          $this->comments[] = new Comment(
                          $this->page,
            intval(strval($comment_page->cid())),
                   strval($comment_page->name()),
                   strval($comment_page->email()),
                   strval($comment_page->website()),
                   strval($comment_page->text()),
            new DateTime(date('c', $comment_page->date()))
          );
        } catch (Exception $e) {
          $this->status = new CommentsStatus(102, $e);
        }
      }
    }
    
    session_start();
  }
  
  // ===========
  // = Options =
  // ===========
  
  public static function option($name)
  {
    return c::get("comments.$name", Comments::$defaults[$name]);
  }
  
  // ===================
  // = Process Comment =
  // ===================
  
  public function process()
  {
    if ($this->status->isError()) { return $this->status; }
    
    $is_preview = isset($_POST[Comments::option('form.preview')]);
    $is_submit  = isset($_POST[Comments::option('form.submit')]);
    $is_send    = $is_preview || $is_submit;
    
    // Check Session.ID == POST.Session.ID
    
    if ($is_send) {
      $session_id = $_SESSION[Comments::option('session.key')];
      $post_session_id = $_POST[Comments::option('form.session_id')];
    
      if ($session_id !== $post_session_id) {
        $this->status = new CommentsStatus(300);
        return $this->status;
      }
    }
    
    // Generate new Session ID
    
    $new_session_id = md5(uniqid('comments_session_id').c::get('license'));
    $_SESSION[Comments::option('session.key')] = $new_session_id;
    
    // Session is Valid
    
    if (!$is_send) { return $this->status; }
    
    $comments_page = $this->page->find(Comments::option('comments-page.dirname'));
    $now = new DateTime();
    $new_comment_id = count($this->comments) + 1;
    $new_comment = null;
    
    try {
      $new_comment = Comment::from_post($this->page, $new_comment_id, $now);
    } catch (Exception $e) {
      $this->status = new CommentsStatus($e->getCode(), $e);
      return $this->status;
    }
    
    if ($comments_page == null) {
      // No comments page has been created yet. Create the comments subpage.
      try {
        $comments_page = $this->page->children()->create(
          Comments::option('comments-page.dirname'),
          Comments::option('comments-page.template'),
          array(
            'title' => Comments::option('comments-page.title'),
            'date'  => $now->format('Y-m-d H:i:s')
          )
        );
      } catch (Exception $e) {
        $this->status = new CommentsStatus(200, $e);
        return $this->status;
      }
    }
    
    if ($is_submit) {
      // The commentator is happy with the preview and has submitted the
      // comment to be published on the website.
      try {
        $new_comment_page = $comments_page->children()->create(
          "$new_comment_id-".Comments::option('comment-page.dirname')."-$new_comment_id",
          Comments::option('comment-page.template'),
          array(
            'cid'     => $new_comment_id,
            'date'    => $new_comment->date('Y-m-d H:i:s'),
            'name'    => $new_comment->name(),
            'email'   => $new_comment->email(),
            'website' => $new_comment->website(),
            'text'    => $new_comment->rawMessage()
          )
        );
      } catch (Exception $e) {
        $this->status =  new CommentsStatus(201, $e);
        return $this->status;
      }
      
      if (Comments::option('use.email')) {
        // Send Email Notification
        $email = new CommentsEmail(
          Comments::option('email.to'),
          Comments::option('email.subject'),
          $new_comment
        );
        $email_status = $email->send();
        
        if ($email_status->getCode() != 0) {
          $this->status = $email_status;
        }
      }
    }
    
    // Add the new comment to the current list of comments.
    $this->comments[] = $new_comment;
    
    if ($is_preview) {
      // This is a valid preview, because any illegal data would have obliged
      // this function to retun a `CommentsStatus` instance.
      $this->valid_preview = true;
    }
    
    return $this->status;
  }
  
  // =================
  // = Comments List =
  // =================
  
  public function isEmpty()
  {
    return count($this->comments) === 0;
  }
  
  // ========
  // = Form =
  // ========
  
  public function nextCommentId()
  {
    $stored_comments = array_filter($this->comments, function ($x)
    {
      return $x->isPreview() === false;
    });
    
    return count($stored_comments) + 1;
  }
  
  public function userHasSubmitted()
  {
    return !$this->status->isError() && isset($_POST[Comments::option('form.submit')]);
  }
  
  public function value($name)
  {
    if (isset($_POST[Comments::option('form.preview')])) {
      return strip_tags(htmlentities(trim($_POST[$name])));
    } else {
      return '';
    }
  }
  
  public function submitName()
  {
    return Comments::option('form.submit');
  }
  
  public function previewName()
  {
    return Comments::option('form.preview');
  }
  
  public function nameName()
  {
    return Comments::option('form.name');
  }
  
  public function emailName()
  {
    return Comments::option('form.email');
  }
  
  public function websiteName()
  {
    return Comments::option('form.website');
  }
  
  public function messageName()
  {
    return Comments::option('form.message');
  }
  
  public function honeypotName()
  {
    return Comments::option('form.honeypot');
  }
  
  public function sessionIdName()
  {
    return Comments::option('form.session_id');
  }
  
  public function isUsingHoneypot()
  {
    return Comments::option('use.honeypot');
  }
  
  public function requiresEmailAddress()
  {
    return Comments::option('require.email');
  }
  
  public function messageMaxlength()
  {
    return Comments::option('max-character-count');
  }
  
  public function fieldMaxlength()
  {
    return Comments::option('max-field-length');
  }
  
  public function sessionId()
  {
    return $_SESSION[Comments::option('session.key')];
  }
   
  public function validPreview()
  {
    return $this->valid_preview;
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
}

/**
 * The Kirby extension registry
 */
$kirby->set('blueprint', Comments::option('comment-page.template'),  __DIR__ . '/blueprints/comment.php');
$kirby->set('blueprint', Comments::option('comments-page.template'), __DIR__ . '/blueprints/comments.php');
$kirby->set('snippet',   Comments::option('comments-snippet'), __DIR__ . '/snippets/comments.php');
