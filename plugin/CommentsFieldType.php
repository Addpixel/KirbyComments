<?php

/**
 * CommentsFieldType
 * 
 * Defines the name, title, HTTP POST name, properties of custom fields, and
 * provides validation and sanitization mechanisms.
 *
 * @package   Kirby Comments
 * @author    Florian Pircher <florian@addpixel.net>
 * @link      https://addpixel.net/kirby-comments/
 * @copyright Florian Pircher
 * @license   https://addpixel.net/kirby-comments/LICENSE
 */
class CommentsFieldType
{
	/**
	 * Registered custom field types.
	 *
	 * @var CommentsFieldType[]
	 */
	public static $instances = array();
	
	/**
	 * Name of the field type. Must be usable as a YAML object key.
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * Title of the field type. Describes the field type with one or two words.
	 *
	 * @var string
	 */
	private $title;
	
	/**
	 * Name used to identify fields of this type over HTTP POST.
	 *
	 * @var string
	 */
	private $http_post_name;
	
	/**
	 * `true` iff the value of fields of this type may not be an empty string
	 * or missing from the HTTP POST data.
	 *
	 * @var bool
	 */
	private $is_required;
	
	/**
	 * Maximum allowed number of characters in the field.
	 *
	 * @var integer
	 */
	private $max_length;
	
	/**
	 * Validates the value of a field of this type. This closure receives the
	 * field’s value as its first argument. Returns `true` for valid values,
	 * throws exceptions with a code in the range of 400-499 for known validation
	 * errors and returns `false` for unknown validation errors. Note that this
	 * closure is called after Kirby Comments’s validation (which checks
	 * `required` and `max-length`).
	 * 
	 * If `null`, a return value of `true` is assumed.
	 * 
	 * Signature: function validate($value: string, $page: Page)
	 *
	 * @var \Closure
	 */
	private $validate;
	
	/**
	 * Sanitizes the value of a field of this type. This closure receives the
	 * field’s value as its first argument and must return a value. Note that this
	 * closure is called after Kirby Comments’s validation and after
	 * `$this->validate`.
	 * 
	 * If `null`, a return value of `$value` is assumed.
	 * 
	 * Signature: function sanitize($value: string, $page: Page)
	 *
	 * @var \Closure
	 */
	private $sanitize;
	
	/**
	 * Returns a comments field type by its name. `null` iff no field type by the
	 * name `$name` can be found.
	 *
	 * @param string $name
	 * @return CommentsFieldType|null
	 */
	static public function named($name)
	{
		foreach (CommentsFieldType::$instances as $field_type) {
			if ($field_type->name() === $name) {
				return $field_type;
			}
		}
		return null;
	}
	
	/**
	 * Constructs a `CommentsFieldType` from an associative array. The array must
	 * include a `name` key pointing to a string and can additionally contain any
	 * of the following key-value pairs:
	 *
	 * - `title : string`: If unset, `name` is used.
	 * - `httpPostName : string`: If unset, `name` is used.
	 * - `required : bool`: Defaults to `false`.
	 * - `max-length : integer`: Defaults to 128.
	 * - `validate : \Closure`: Defaults to `null`.
	 * - `sanitize : \Closure`: Defaults to `null`.
	 *
	 * @param array $array
	 * @return CommentsFieldType
	 * @throws Exception Throws if `$array` does not have a `name` key.
	 */
	static function from_array($array)
	{
		if (!isset($array['name'])) {
			throw new Exception('Custom field without name attribute.', 204);
		}
		$name = $array['name'];
		$title = isset($array['title']) ? $array['title'] : $name;
		$http_post_name = isset($array['httpPostName']) ? $array['httpPostName'] : $name;
		$is_required = isset($array['required']) ? $array['required'] : false;
		$max_length = isset($array['max-length']) ? $array['max-length'] : 128;
		$validate = isset($array['validate']) ? $array['validate'] : null;
		$sanitize = isset($array['sanitize']) ? $array['sanitize'] : null;
		
		return new CommentsFieldType($name, $title, $http_post_name, $is_required, $validate, $sanitize);
	}
	
	/**
	 * CommentsFieldType constructor.
	 *
	 * @param string $name
	 * @param string $title
	 * @param string $http_post_name
	 * @param bool $is_required
	 * @param integer $max_length
	 * @param Closure|null $validate
	 * @param Closure|null $sanitize
	 */
	function __construct($name, $title, $http_post_name, $is_required, $max_length, $validate, $sanitize)
	{
		$this->name = $name;
		$this->title = $title;
		$this->http_post_name = $http_post_name;
		$this->is_required = $is_required;
		$this->max_length = $max_length;
		$this->validate = $validate;
		$this->sanitize = $sanitize;
	}
	
	/**
	 * Name of the field type.
	 *
	 * @return string
	 */
	public function name()
	{
		return $this->name;
	}
	
	/**
	 * Title of the field type. Describes the field type with one or two words.
	 *
	 * @return string
	 */
	public function title()
	{
		return $this->title;
	}
	
	/**
	 * Name used to identify fields of this type over HTTP POST.
	 *
	 * @return string
	 */
	public function httpPostName()
	{
		return $this->http_post_name;
	}
	
	/**
	 * `true` iff the value of fields of this type may not be an empty string
	 * or missing from the HTTP POST data.
	 *
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->is_required;
	}
	
	/**
	 * Maximum allowed number of characters in the field.
	 *
	 * @return integer
	 */
	public function maxLength()
	{
		return $this->max_length;
	}
	
	/**
	 * Validates a string value using `$this->validate`.
	 *
	 * @param string $value
	 * @param Page $page
	 * @return bool
	 * @throws Exception
	 */
	public function validateValue($value, $page)
	{
		$validate = $this->validate;
		
		if ($validate === null) {
			return true;
		}
		
		return $validate($value, $page);
	}
	
	/**
	 * Sanitizes a string value using `$this->sanitize`.
	 * 
	 * @param string $value
	 * @param Page $page
	 * @return mixed|null
	 */
	public function sanitizeValue($value, $page)
	{
		$sanitize = $this->sanitize;
		
		if ($sanitize === null) {
			return $value;
		}
		
		return $sanitize($value, $page);
	}
}
