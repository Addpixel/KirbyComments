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
	protected $id;
	
	/**
	 * Name of the comment author. `null` iff no email address was provided by
	 * the comment author.This value does not contain HTML tags but may still
	 * contain HTML entities.
	 *
	 * @var string|null
	 */
	protected $name;
	
	/**
	 * Email address of the comment author. `null` iff no email address was
	 * provided by the comment author. This value does not contain HTML tags but
	 * may still contain HTML entities.
	 *
	 * @var string|null
	 */
	protected $email_address;
	
	/**
	 * Absolute website address (including scheme) of the comment author. `null`,
	 * iff no website address was provided by the comment author. This value does
	 * not contain HTML tags but may still contain HTML entities.
	 *
	 * @var string|null
	 */
	protected $website;
	
	/**
	 * Message of the comment. This value is unescaped and may therefore contain
	 * Markdown syntax, HTML tags, or HTML entities.
	 *
	 * @var string
	 */
	protected $message;
	
	/**
	 * List of `CommentsField` objects used as custom fields.
	 *
	 * @var CommentsField[]
	 */
	protected $custom_fields;
	
	/**
	 * Date and time of the publication of the comment.
	 *
	 * @var \DateTime
	 */
	protected $datetime;
	
	/**
	 * `true` iff the comment is in preview mode and not stored as Kirby page.
	 *
	 * @var bool
	 */
	protected $is_preview;
	
	/**
	 * Page on which the comment was posted or iff the comment is in preview mode,
	 * the page on which the comment is previewed.
	 *
	 * @var Page
	 */
	protected $content_page;
	
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
	 * Returns `null` for a value of `null` or a whitespace only string. Other
	 * values are escaped using `strip_tags(trim($value))`.
	 *
	 * @param string|null $value
	 * @return string|null
	 */
	private static function null_empty_escape($value) {
		if ($value === null || trim($value) === '') {
			return null;
		}
		return strip_tags(trim($value));
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
				throw new Exception('The comment must be written by a human being.', 310);
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
			
			if (strlen($value) > $type->maxLength()) {
				throw new Exception('The '.$type->title().' is too long.', 313);
			}
			
			return new CommentsField($type, $value, $content_page, true);
		}, CommentsFieldType::$instances);
		
		// Validate fields
		if (gettype($id) !== 'integer') {
			throw new Exception('Expected comment ID of type integer.', 100);
		} elseif ($id <= 0) {
			throw new Exception('Expected comment ID of value greater than 0.', 101);
		} elseif (Comments::option('form.name.required') && $name == '') {
			throw new Exception('The name field is required.', 301);
		} elseif (strlen($name) > Comments::option('form.name.max-length')) {
			throw new Exception('The name is too long.', 302);
		} elseif (Comments::option('form.email.required') && $email_address == '') {
			throw new Exception('The email address field is required.', 303);
		} elseif ($email_address != '' && !v::email($email_address)) {
			throw new Exception('The email address must be valid.', 304);
		} elseif (strlen($email_address) > Comments::option('form.email.max-length')) {
			throw new Exception('The email address is too long.', 305);
		} elseif (preg_match('/^\s*javascript:/i', $website)) {
			throw new Exception('The website address field must not contain JavaScript code.', 306);
		} elseif (strlen($website) > Comments::option('form.website.max-length')) {
			throw new Exception('The website address is too long.', 307);
		} elseif ($message == '') {
			throw new Exception('The message field is required.', 308);
		} elseif (strlen($message) > Comments::option('form.message.max-length')) {
			throw new Exception('The message is too long. (A maximum of '.Comments::option('form.message.max-length').' characters is allowed.)', 309);
		}
		
		return new Comment($content_page, $id, $name, $email_address, $website, $message, $custom_fields, $datetime, $is_preview);
	}
	
	/**
	 * Constructs a `Comment` from a comment page.
	 *
	 * @param Page $page A comment page.
	 * @return Comment
	 * @throws Exception if no comment could be constructed from `$page`.
	 */
	public static function form_page($page) {
		try {
			$content_page = $page->parent()->parent();
			
			// Read custom fields
			$custom_fields = array();
			
			if ($page->customfields()->exists()) {
				$custom_fields_data = $page->customfields()->yaml();
				
				foreach ($custom_fields_data as $field_name => $value) {
					// Construct and add custom field
					$type = CommentsFieldType::named($field_name);
					// Ignore undefined custom fields
					if ($type === null) { continue; }
					
					$field = new CommentsField($type, $value, $content_page, false);
					$custom_fields[] = $field;
				}
			}
			
			$name = $page->name()->exists() ? $page->name()->value() : null;
			$email_address = $page->email()->exists() ? $page->email()->value() : null;
			$website = $page->website()->exists() ? $page->website()->value() : null;
			
			return new Comment(
				$content_page,
				$page->cid()->int(),
				$name,
				$email_address,
				$website,
				$page->text()->value(),
				$custom_fields,
				new DateTime(date('c', $page->date()))
			);
		} catch (Exception $e) {
			throw new Exception('Could not construct `Comment` from page.', 102, $e);
		}
	}
	
	/**
	 * Comment constructor. Trims the `$name`, `$email_address`, `$website` and
	 * `$message` values and strips HTML tags from the name, email address and
	 * website.
	 *
	 * @param Page $content_page
	 * @param integer $id
	 * @param string|null $name
	 * @param string|null $email_address
	 * @param string|null $website
	 * @param string $message
	 * @param CommentsField[string] $custom_fields
	 * @param \DateTime $datetime
	 * @param bool $is_preview
	 */
	function __construct($content_page, $id, $name, $email_address, $website, $message, $custom_fields, $datetime, $is_preview=false)
	{
		$this->content_page  = $content_page;
		$this->id            = $id;
		$this->name          = Comment::null_empty_escape($name);
		$this->email_address = Comment::null_empty_escape($email_address);
		$this->website       = Comment::null_empty_escape($website);
		$this->message       = trim($message);
		$this->custom_fields = $custom_fields;
		$this->datetime      = $datetime;
		$this->is_preview    = $is_preview === true;
		
		if ($this->website !== null && !preg_match('/^https?:/', $this->website)) {
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
	 * HTML escaped name of the comment author. `""` iff no name was specified.
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
	 * Unescaped name of the comment author. `null` iff no name was specified.
	 *
	 * @return string|null
	 */
	public function rawName()
	{
		return $this->name;
	}
	
	/**
	 * HTML escaped email address of the comment author. `""` iff no email
	 * address was specified.
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
	 * Unescaped email address on the comment author. `null` iff no email address
	 * was specified.
	 *
	 * @return string|null
	 */
	public function rawEmail()
	{
		return $this->email_address;
	}
	
	/**
	 * HTML escaped website address of the comment author. `""` iff no website
	 * address was specified.
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
	 * Unescaped website address of the comment author. `null` iff no website
	 * address was specified.
	 *
	 * @return string|null
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
		$message = $this->message;
		
		if (Comments::option('form.message.htmlentities')) {
			$message = htmlentities($message);
		}
		
		$message = markdown($message);
		
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
	 * Formatted date and/or time of the publication of the comment. The value of
	 * `$format` must match a pattern for PHP’s `DateTime::format` method
	 * (http://php.net/manual/de/datetime.format.php).
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
	 * @return \DateTime
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
