<?php

include_once('comment.php');
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
    'form.submit'            => 'submit',
    'form.preview'           => 'preview',
    'form.name'              => 'name',
    'form.email'             => 'email',
    'form.website'           => 'website',
    'from.message'           => 'message',
    'form.honeypot'          => 'subject', 
    'form.session_id'        => 'session_id',
    'require.email'          => false,
    'allowed_tags'           => '<p><br><a><em><strong><code><pre>'
  );
  private $status;
  private $iterator_index;
  private $comments;
  
  function __construct($page)
  {
    $this->status = new CommentsStatus(0);
    $this->iterator_index = 0;
    $this->comments = array();
    
    $now = new DateTime();
    
    if ($comments_page != null) {
      foreach ($comments_page->children() as $comment_page) {
        try {
          $this->comments[] = new Comment(
            intval($page->cid()),
            strval($page->name()),
            strval($page->email()),
            strval($page->website()),
            strval($page->message()),
            new DateTime($page->date('Y-m-d H:i:s'))
          );
        } catch (Exception $e) {
          $this->status = new CommentsStatus(102);
        }
      }
    }
    
    if (isset($_POST['preview']) || isset($_POST['submit'])) {
      $comments_page = $page->find('comments');
      $new_comment = Comment::from_post(count($comments), $now, true);
      $new_comment_id = count($this->comments) + 1;
      
      if ($comments_page == null) {
        try {
          $comments_page = $page->children()->create(
            Comments::option('comments_page.dirname'),
            Comments::option('comments_page.template'),
            array(
              'title' => Comments::option('comments_page.title'),
              'date' => $now->format('Y-m-d H:i:s')
            )
          );
        } catch (Exception $e) {
          $this->status = new CommentsStatus(200, $e);
        }
      }
      
      try {
        $new_comment_page = $comments_page->children()->create(
          "$new_comment_id-".Comments::option('comment_page.dirname'),
          Comments::option('comment_page.template'),
          array(
            'cid'     => $new_comment_id,
            'date'    => $new_comment->date('Y-m-d H:i:s'),
            'name'    => $new_comment->name(),
            'email'   => $new_comment->email(),
            'website' => $new_comment->website(),
            'message' => $new_comment->message()
          )
        );
      } catch (Exception $e) {
        $this->status = new CommentsStatus(201, $e);
      }
      
      $this->comments[] = $new_comment;
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
    return $this->status;
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
    // TODO: replace this demo data with real stuff
    return Comments::option('form.honeypot');
  }
  
  public function sessionIdName()
  {
    // TODO: replace this demo data with real stuff
    return Comments::option('form.session_id');
  }
  
  public function sessionId()
  {
    // TODO: replace this demo data with real stuff
    return 'APX_FLORIAN_PIRCHER_5BT_2016';
  }
  
  public function previewName()
  {
    // TODO: replace this demo data with real stuff
    return Comments::option('form.preview');
  }
   
  public function validePreview()
  {
    // TODO: replace this demo data with real stuff
    return false;
  }
  
  public function submitName()
  {
    // TODO: replace this demo data with real stuff
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