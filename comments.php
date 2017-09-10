<?php

/**
 * Kirby Comments
 * 
 * File-based comments stored as subpages.
 * Easy to setup. Easy to use. Flexible as hell.
 *
 * @package   Kirby Comments
 * @author    Florian Pircher <florian@addpixel.net>
 * @link      https://addpixel.net/kirby-comments/
 * @copyright Florian Pircher
 * @license   https://addpixel.net/kirby-comments/LICENSE
 */

include_once('plugin/Comments.php');
include_once('plugin/Comment.php');
include_once('plugin/NestedComment.php');
include_once('plugin/CommentsEmail.php');
include_once('plugin/CommentsStatus.php');
include_once('plugin/CommentsField.php');
include_once('plugin/CommentsFieldType.php');

/*
 * Initialize Comments with default option values
 */
Comments::init(array(
	'pages.comments.title'      => function ($page) {
		return 'Comments for “' . $page->title() . '”';
	},
	'pages.comments.dirname'    => 'comments',
	'pages.comments.template'   => 'comments',
	'pages.comment.dirname'     => 'comment',
	'pages.comment.template'    => 'comment',
	'pages.comment.visible'     => true,
	'form.submit'               => 'submit',
	'form.preview'              => 'preview',
	'form.name'                 => 'name',
	'form.name.required'        => true,
	'form.name.max-length'      => 64,
	'form.email'                => 'email',
	'form.email.required'       => false,
	'form.email.max-length'     => 64,
	'form.website'              => 'website',
	'form.website.max-length'   => 64,
	'form.message'              => 'message',
	'form.message.allowed_tags' => '<p><br><a><em><strong><code><pre>',
	'form.message.smartypants'  => true,
	'form.message.max-length'   => 1024,
	'form.session_id'           => 'session_id',
	'form.honeypot'             => 'subject',
	'honeypot.enabled'          => true,
	'honeypot.human-value'      => '',
	'email.enabled'             => false,
	'email.to'                  => array(),
	'email.subject'             => 'New Comment on {{ page.title }}',
	'email.undefined-value'     => '(not specified)',
	'session.key'               => 'comments',
	'custom-fields'             => array(),
	'hooks.block-comment'       => null,
	'setup.content-page.title'  => function ($page) {
		return $page->title();
	}
));

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
