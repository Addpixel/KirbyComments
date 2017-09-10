<?php

/**
 * Comment
 * 
 * A `Comment` object stores information about the comment author, the comment’s
 * message, and metadata like the publication date and the comment ID.
 * 
 * Additionally, it provides methods for accessing its values in unescaped and
 * escaped form for rendering templates.
 *
 * @package   Kirby Comments
 * @author    Florian Pircher <florian@addpixel.net>
 * @link      https://addpixel.net/kirby-comments/
 * @copyright Florian Pircher
 * @license   https://addpixel.net/kirby-comments/LICENSE
 */
class Comment
{
	/**
	 * Unique identifier of the comment. The first comment on a page has an ID
	 * of 1, incrementing by 1 per page.
	 *
	 * @var integer
	 */
	private $id;
	
	/**
	 * Name of the comment author. `null` iff no email address was provided by
	 * the comment author.This value does not contain HTML tags but may still
	 * contain HTML entities.
	 *
	 * @var string|null
	 */
	private $name;
	
	/**
	 * Email address of the comment author. `null` iff no email address was
	 * provided by the comment author. This value does not contain HTML tags but
	 * may still contain HTML entities.
	 *
	 * @var string|null
	 */
	private $email_address;
	
	/**
	 * Absolute website address (including scheme) of the comment author. `null`,
	 * iff no website address was provided by the comment author. This value does
	 * not contain HTML tags but may still contain HTML entities.
	 *
	 * @var string|null
	 */
	private $website;
	
	/**
	 * Message of the comment. This value is unescaped and may therefore contain
	 * Markdown syntax, HTML tags, or HTML entities.
	 *
	 * @var string
	 */
	private $message;
	
	/**
	 * List of `CommentsField` objects used as custom fields.
	 *
	 * @var CommentsField[]
	 */
	private $custom_fields;
	
	/**
	 * Date and time of the publication of the comment.
	 *
	 * @var \DateTime
	 */
	private $datetime;
	
	/**
	 * `true` iff the comment is in preview mode and not stored as Kirby page.
	 *
	 * @var bool
	 */
	private $is_preview;
	
	/**
	 * Page on which the comment was posted or iff the comment is in preview mode,
	 * the page on which the comment is previewed.
	 *
	 * @var Page
	 */
	private $content_page;
	
	/**
	 * Implementation of the null coalescing operator; searching an array for an
	 * key and returning the value on success, `$default` otherwise.
	 *
	 * @param mixed[string] $array
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	private static function nc($array, $key, $default)
	{
		if (isset($array[$key])) {
			return $array[$key];
		}
		return $default;
	}
	
	/**
	 * Constructs a `Comment` from `$_POST`.
	 *
	 * @param Page $content_page
	 * @param integer $id ID of the comment to be constructed.
	 * @param \DateTime $datetime Date and time of the publication of the comment.
	 * @return Comment
	 * @throws Exception
	 */
	public static function from_post($content_page, $id, $datetime)
	{
		if (Comments::option('honeypot.enabled')) {
			$post_value = $_POST[Comments::option('form.honeypot')];
			$human_value = Comments::option('honeypot.human-value');
			
			if ($post_value !== $human_value) {
				throw new Exception('Comment must be written by a human being.', 310);
			}
		}
		
		// Set state
		$is_preview = isset($_POST[Comments::option('form.preview')]);
		
		// Read main fields
		$name          = trim(Comment::nc($_POST, Comments::option('form.name'), ''));
		$email_address = trim(Comment::nc($_POST, Comments::option('form.email'), ''));
		$website       = trim(Comment::nc($_POST, Comments::option('form.website'), ''));
		$message       = trim(Comment::nc($_POST, Comments::option('form.message'), ''));
		
		// Read custom fields
		$custom_fields = array_map(function ($type) use ($content_page) {
			$key = $type->httpPostName();
			
			if ($type->isRequired() && (!isset($_POST[$key]) || $_POST[$key] === '')) {
				throw new Exception('The '.$type->title().' field is required.', 312);
			}
			
			$value = isset($_POST[$key]) ? $_POST[$key] : '';
			
			return new CommentsField($type, $value, $content_page, true);
		}, CommentsFieldType::$instances);
		
		// Validate fields
		if (gettype($id) !== 'integer') {
			throw new Exception('The ID of a comment must be of the type integer.', 100);
		} elseif ($id <= 0) {
			throw new Exception('The ID of a comment must be bigger than 0.', 101);
		} elseif (Comments::option('form.name.required') && $name == '') {
			throw new Exception('The name field is required.', 301);
		} elseif (strlen($name) > Comments::option('form.name.max-length')) {
			throw new Exception('The name is too long.', 302);
		} elseif (Comments::option('form.email.required') && $email_address == '') {
			throw new Exception('The email address field is required.', 303);
		} elseif ($email_address != '' && !v::email($email_address)) {
			throw new Exception('The email address is not valid.', 304);
		} elseif (strlen($email_address) > Comments::option('form.email.max-length')) {
			throw new Exception('The email address is too long.', 305);
		} elseif (preg_match('/^\s*javascript:/i', $website)) {
			throw new Exception('The website address may not contain JavaScript code.', 306);
		} elseif (strlen($website) > Comments::option('form.website.max-length')) {
			throw new Exception('The website address is too long.', 307);
		} elseif ($message == '') {
			throw new Exception('The message must not be empty.', 308);
		} elseif (strlen($message) > Comments::option('form.message.max-length')) {
			throw new Exception('The message is to long. (A maximum of '.Comments::option('form.message.max-length').' characters is allowed.)', 309);
		}
		
		return new Comment($content_page, $id, $name, $email_address, $website, $message, $custom_fields, $datetime, $is_preview);
	}
	
	/**
	 * Comment constructor. Trims the `$name`, `$email_address`, `$website` and
	 * `$message` values and strips HTML tags from the name, email address and
	 * website.
	 *
	 * @param Page $content_page
	 * @param integer $id
	 * @param string $name
	 * @param string $email_address
	 * @param string $website
	 * @param string $message
	 * @param CommentsField[string] $custom_fields
	 * @param \DateTime $datetime
	 * @param bool $is_preview
	 */
	function __construct($content_page, $id, $name, $email_address, $website, $message, $custom_fields, $datetime, $is_preview=false)
	{
		$this->content_page  = $content_page;
		$this->id            = $id;
		$this->name          = trim(strip_tags($name));
		$this->email_address = trim(strip_tags($email_address));
		$this->website       = trim(strip_tags($website));
		$this->message       = trim($message);
		$this->custom_fields = $custom_fields;
		$this->datetime      = $datetime;
		$this->is_preview    = $is_preview === true;
		
		if ($this->email_address === '') {
			// Replace empty string value with `null`
			$this->email_address = null;
		}
		
		if ($this->website === '') {
			// Replace empty string value with `null`
			$this->website = null;
		} elseif (!preg_match('/^https?:/', $this->website)) {
			// Make address absolute (e.g. "example.org" to "http://example.org")
			$this->website = 'http://'.$this->website;
		}
	}
	
	/**
	 * Unique identifier of the comment. The first comment on a page has an ID
	 * of 1, incrementing by 1 per page.
	 *
	 * @return int
	 */
	public function id()
	{
		return $this->id;
	}
	
	/**
	 * HTML escaped name of the comment author.
	 *
	 * @return string
	 */
	public function name()
	{
		if ($this->name === null) {
			return '';
		}
		return htmlentities($this->name);
	}
	
	/**
	 * Unescaped name of the comment author.
	 *
	 * @return string
	 */
	public function rawName()
	{
		return $this->name;
	}
	
	/**
	 * HTML escaped email address of the comment author.
	 *
	 * @return string
	 */
	public function email()
	{
		if ($this->email_address === null) {
			return '';
		}
		return htmlentities($this->email_address);
	}
	
	/**
	 * Unescaped email address on the comment author.
	 *
	 * @return string|null
	 */
	public function rawEmail()
	{
		return $this->email_address;
	}
	
	/**
	 * HTML escaped website address of the comment author.
	 *
	 * @return string
	 */
	public function website()
	{
		if ($this->website === null) {
			return '';
		}
		return htmlentities($this->website);
	}
	
	/**
	 * Unescaped website address of the comment author.
	 *
	 * @return null|string
	 */
	public function rawWebsite()
	{
		return $this->website;
	}
	
	/**
	 * Formatted message which is processed using Markdown and optionally
	 * SmartyPants. Only HTML tags allowed by the `form.message.allowed_tags`
	 * option are kept. HTML tags and HTML entities included in the message are
	 * escaped before applying the Markdown formatter.
	 *
	 * @return string
	 */
	public function message()
	{
		$message = markdown(htmlentities($this->message));
		
		if (Comments::option('form.message.smartypants')) {
			$message = smartypants($message);
		}
		
		return strip_tags($message, Comments::option('form.message.allowed_tags'));
	}
	
	/**
	 * Unprocessed message.
	 *
	 * @return string
	 */
	public function rawMessage()
	{
		return $this->message;
	}
	
	/**
	 * List of custom fields.
	 *
	 * @return CommentsField[]
	 */
	public function customFields()
	{
		return $this->custom_fields;
	}
	
	/**
	 * Unescaped value of the custom field with the name `$field_name`. `null` if
	 * no custom field with the name `$field_name` exists.
	 *
	 * @param string $field_name Name of the custom field.
	 * @return mixed|null
	 */
	public function customField($field_name)
	{
		foreach ($this->custom_fields as $field) {
			if ($field->name() === $field_name) {
				return $field->value();
			}
		}
		return null;
	}
	
	/**
	 * Formatted date and time of the publication of the comment.
	 *
	 * @param string $format
	 * @return string
	 */
	public function date($format='Y-m-d')
	{
		return $this->datetime->format($format);
	}
	
	/**
	 * Date and time of the publication of the comment.
	 *
	 * @return DateTime
	 */
	public function datetime()
	{
		return $this->datetime;
	}
	
	/**
	 * Page on which the comment was posted or if the comment is in preview mode,
	 * the page on which the comment is previewed.
	 *
	 * @return Page
	 */
	public function page()
	{
		return $this->content_page;
	}
	
	/**
	 * `true` iff the comment is in preview mode and not stored as Kirby page.
	 *
	 * @return bool
	 */
	public function isPreview()
	{
		return $this->is_preview === true;
	}
	
	/**
	 * `true` iff the website address of the comment author is not `null`.
	 *
	 * @return bool
	 */
	public function isLinkable()
	{
		return $this->website !== null;
	}
}
