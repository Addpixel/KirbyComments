<?php

/**
 * NestedComment
 * 
 * An augmentation of the standard `Comment` class which support nesting.
 * Nested comments are constructed from a standard comment and are assigned
 * to a parent comment by calling `$parent->addChild($child)`.
 *
 * @package   Kirby Comments
 * @author    Florian Pircher <florian@addpixel.net>
 * @link      https://addpixel.net/kirby-comments/
 * @copyright Florian Pircher
 * @license   https://addpixel.net/kirby-comments/LICENSE
 */
class NestedComment extends Comment
{
	/**
	 * Parent comment in the nested structure. `null` iff the comment is nested on
	 * the top level.
	 *
	 * @var NestedComment
	 */
	private $parent = null;
	
	/**
	 * List of comments nested directly underneath this comment.
	 *
	 * @var NestedComment[]
	 */
	private $children = array();
	
	/**
	 * Constructs a nested comment from a normal comment.
	 *
	 * @param Comment $comment
	 */
	function __construct($comment)
	{
		parent::__construct($comment->content_page, $comment->id, $comment->name, $comment->email_address, $comment->website, $comment->message, $comment->custom_fields, $comment->datetime, $comment->is_preview);
	}
	
	/**
	 * Parent comment in the nested structure. `null` iff the comment is nested on
	 * the top level.
	 *
	 * @return NestedComment
	 */
	public function parent()
	{
		return $this->parent;
	}
	
	/**
	 * List of comments nested directly underneath this comment.
	 *
	 * @return NestedComment[]
	 */
	public function children()
	{
		return $this->children;
	}
	
	/**
	 * `true` iff the comment has nested children.
	 *
	 * @return bool
	 */
	public function hasChildren() {
		return count($this->children) > 0;
	}
	
	/**
	 * Adds a comment to the children list and sets this comment as its parent.
	 *
	 * @param NestedComment $child
	 */
	public function addChild($child)
	{
		$child->parent = $this;
		$this->children[] = $child;
	}
}
