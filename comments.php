<?php

include_once('plugin/comments.php');
include_once('plugin/comment.php');
include_once('plugin/comments-email.php');
include_once('plugin/comments-status.php');

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
