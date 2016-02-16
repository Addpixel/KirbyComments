<?php

include_once('comment.php');
include_once('comments-email.php');
include_once('commentsstatus.php');

/**
 * Comments
 */
class Comments implements Iterator
{
  private static $defaults = array(
    'comments_page.title'    => 'Comments',
    'comments_page.dirname'  => 'comments',
    'comments_page.template' => 'comments',
    'comment_page.dirname'   => 'comment',
    'comment_page.template'  => 'comment',
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
    'use.email'              => true,
    'allowed_tags'           => '<p><br><a><em><strong><code><pre>',
    'email.to'               => array('kirby-comments@leuchtschirm.com'),
    'email.subject'          => 'New Comment by {{ comment.user.name }}',
    'setup.page.title_key'   => 'title'
  );
  private $page;
  private $status;
  private $iterator_index;
  private $comments;
  private $valid_preview;
  
  function __construct($page)
  {
    $this->page = $page;
    $this->status = new CommentsStatus(0);
    $this->iterator_index = 0;
    $this->comments = array();
    $this->valid_preview = false;
    
    $comments_page = $this->page->find('comments');
    
    if ($comments_page != null) {
      foreach ($comments_page->children() as $comment_page) {
        try {
          $this->comments[] = new Comment(
                          $page,
            intval(strval($comment_page->cid())),
                   strval($comment_page->name()),
                   strval($comment_page->email()),
                   strval($comment_page->website()),
                   strval($comment_page->text()),
             new DateTime($comment_page->date('Y-m-d H:i:s'))
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
        return new CommentsStatus(305);
      }
    }
    
    // Generate new Session ID
    
    $new_session_id = md5(c::get('license').uniqid('comments_session_id'));
    $_SESSION[Comments::option('session.key')] = $new_session_id;
    
    // Session is Valid
    
    if (!$is_send) { return $this->status; }
    
    $comments_page = $this->page->find('comments');
    $now = new DateTime();
    $new_comment_id = count($this->comments) + 1;
    $new_comment = null;
    
    try {
      $new_comment = Comment::from_post($new_comment_id, $now);
    } catch (Exception $e) {
      return new CommentsStatus($e->getCode(), $e);
    }
    
    if ($comments_page == null) {
      // No comments page has been created yet. Create the comments subpage.
      try {
        $comments_page = $this->page->children()->create(
          Comments::option('comments_page.dirname'),
          Comments::option('comments_page.template'),
          array(
            'title' => Comments::option('comments_page.title'),
            'date'  => $now->format('Y-m-d H:i:s')
          )
        );
      } catch (Exception $e) {
        return new CommentsStatus(200, $e);
      }
    }
    
    if ($is_submit) {
      // The commentator is happy with the preview and has submitted the
      // comment to be published on the website.
      try {
        $new_comment_page = $comments_page->children()->create(
          "$new_comment_id-".Comments::option('comment_page.dirname')."-$new_comment_id",
          Comments::option('comment_page.template'),
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
        return new CommentsStatus(201, $e);
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
    return isset($_POST[Comments::option('form.submit')]);
  }
  
  public function value($name)
  {
    if (isset($_POST[Comments::option('form.preview')])) {
      return strip_tags(htmlentities(trim($_POST[$name])));
    } else {
      return '';
    }
  }
  
  public function honeypotName()
  {
    return Comments::option('form.honeypot');
  }
  
  public function isUsingHoneypot()
  {
    return Comments::option('use.honeypot');
  }
  
  public function sessionIdName()
  {
    return Comments::option('form.session_id');
  }
  
  public function sessionId()
  {
    return $_SESSION[Comments::option('session.key')];
  }
  
  public function previewName()
  {
    return Comments::option('form.preview');
  }
   
  public function validPreview()
  {
    return $this->valid_preview;
  }
  
  public function submitName()
  {
    return Comments::option('form.submit');
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