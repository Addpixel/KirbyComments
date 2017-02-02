<?php

include_once('plugin/comments.php');
include_once('plugin/comment.php');
include_once('plugin/comments-email.php');
include_once('plugin/comments-status.php');

/**
 * The Kirby extension registry
 */
$kirby->set('blueprint', Comments::option('comment-page.template'),  __DIR__ . '/blueprints/comment.php');
$kirby->set('blueprint', Comments::option('comments-page.template'), __DIR__ . '/blueprints/comments.php');
$kirby->set('snippet',   Comments::option('comments-snippet'), __DIR__ . '/snippets/comments.php');
