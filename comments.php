<?php

include_once('comment.php');
include_once('commentstatus.php');

/**
 * Comments
 */
class Comments implements Iterator
{
  private $defaults = array(
    'comments.names.honeypot' => 'subject'
  );
  private $status;
  private $iterator_index;
  private $comments;
  
  function __construct($page)
  {
    $this->status = new CommentStatus(0);
    $this->iterator_index = 0;
    $this->comments = array();
    
    $now = new DateTime();
    $comments_page = $page->find('comments');
    
    if ($comments_page != null) {
      foreach ($comments_page->children() as $comment_page) {
        try {
          $comments[] = new Comment(
            intval($page->cid()),
            strval($page->name()),
            strval($page->email()),
            strval($page->website()),
            strval($page->message()),
            new DateTime($page->datetime())
          );
        } catch (Exception $e) {
          $this->status = new CommentStatus(100);
        }
      }
    } else {
      $comments_page = $page->children()->create(
        Comments::option('comments_page.dirname'),
        Comments::option('comments_page.template'),
        array(
          'title' => Comments::option('comments_page.title'),
          'date' => $now
        )
      );
    }
    
    if (isset($_POST['preview']) || isset($_POST['submit'])) {
      $new_comment = Comment::from_post(count($comments), $now, true);
    }
    
    session_start();
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
  
  // ===================
  // = Process Comment =
  // ===================
  
  public function process()
  {
    // TODO: Return Real Status
    return new CommentStatus(0);
  }
  
  // =================
  // = Comments List =
  // =================
  
  public function isEmpty()
  {
    return count($this->comments) == 0;
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
    
    return count($stored_comments);
  }
  
  public function userHasSubmitted()
  {
    return isset($_POST['submit']);
  }
  
  public function value($name)
  {
    if (isset($_POST['preview'])) {
      return strip_tags(htmlentities(trim($_POST[$name])));
    } else {
      return '';
    }
  }
  
  public function honeypotName()
  {
    // TODO: replace this demo data with real stuff
    return 'honeypot';
  }
  
  public function sessionIdName()
  {
    // TODO: replace this demo data with real stuff
    return 'session_id';
  }
  
  public function sessionId()
  {
    // TODO: replace this demo data with real stuff
    return 'APX_FLORIAN_PIRCHER_5BT_2016';
  }
  
  public function previewName()
  {
    // TODO: replace this demo data with real stuff
    return 'preview';
  }
   
  public function validePreview()
  {
    // TODO: replace this demo data with real stuff
    return false;
  }
  
  public function submitName()
  {
    // TODO: replace this demo data with real stuff
    return 'submit';
  }
}