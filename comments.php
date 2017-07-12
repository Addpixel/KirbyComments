<?php

include_once('plugin/Comments.php');
include_once('plugin/Comment.php');
include_once('plugin/CommentsEmail.php');
include_once('plugin/CommentsStatus.php');

/*
 * Custom page methods
 */
page::$methods['comments'] = function ($page) {
  return Comments::for_page($page);
};

/*
 * Kirby extension registry
 */
$kirby->set('blueprint', 'comments', __DIR__.'/blueprints/comments.yml');
$kirby->set('blueprint', 'comment', __DIR__.'/blueprints/comment.yml');
$kirby->set('snippet', 'comments-form', __DIR__.'/snippets/comments-form.php');
$kirby->set('snippet', 'comments-list', __DIR__.'/snippets/comments-list.php');
$kirby->set('snippet', 'comments', __DIR__.'/snippets/comments.php');
