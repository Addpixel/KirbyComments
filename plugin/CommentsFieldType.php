<?php

/**
 * CommentsFieldType
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
	 * The name of the field.
	 *
	 * @var string
	 */
	private $name;
	/**
	 * The title of the field.
	 *
	 * @var string
	 */
	private $title;
	/**
	 * The name used to identify the field over HTTP POST.
	 *
	 * @var string
	 */
	private $http_post_name;
	/**
	 * Whether the field is required.
	 *
	 * @var bool
	 */
	private $is_required;
	/**
	 * Validates the value of a field. This closure receives the field’s value
	 * as its first argument. Returns `true` for valid values, throws exceptions
	 * with a code in the range of 400-499 for known validation errors and
	 * returns `false` for unknown validation errors.
	 * 
	 * Signature: function validate($value: mixed, $page: Page)
	 *
	 * @var \Closure
	 */
	private $validate;
	/**
	 * Sanitizes the value of the field. This closure receives the field’s value
	 *  as its first argument and must return a value. Note that this closure is
	 * called after Kirby Comments’s validation and after `$this->validate`.
	 * 
	 * Signature: function validate($value: mixed, $page: Page)
	 *
	 * @var \Closure
	 */
	private $sanitize;
	
	static public function named($name) {
		foreach (CommentsFieldType::$instances as $field_type) {
			if ($field_type->name() === $name) {
				return $field_type;
			}
		}
		return null;
	}
	
	/**
	 * Creates a `CommentsFieldType` from an associative array. The array must
	 * include a `name` key pointing to a string and can additionally contain any
	 * of the following keys:
	 *
	 * - `httpPostName`: string: If unset, `name` is used.
	 * - `required`: bool: Defaults to `false`.
	 * - `validate`: \Closure: Defaults to `null`.
	 * - `sanitize`: \Closure: Defaults to `null`.
	 *
	 * @param array $array
	 * @return CommentsFieldType
	 * @throws Exception if `$array` does not have a `name` key. 
	 */
	static function from_array($array) {
		if (!isset($array['name'])) {
			throw new Exception('Custom field without name attribute.', 204);
		}
		$name = $array['name'];
		$title = isset($array['title']) ? $array['title'] : $name;
		$http_post_name = isset($array['httpPostName']) ? $array['httpPostName'] : $name;
		$is_required = isset($array['required']) ? $array['required'] : false;
		$validate = isset($array['validate']) ? $array['validate'] : null;
		$sanitize = isset($array['sanitize']) ? $array['sanitize'] : null;
		
		return new CommentsFieldType($name, $title, $http_post_name, $is_required, $validate, $sanitize);
	}
	
	function __construct($name, $title, $http_post_name, $is_required, $validate, $sanitize) {
		$this->name = $name;
		$this->title = $title;
		$this->http_post_name = $http_post_name;
		$this->is_required = $is_required;
		$this->validate = $validate;
		$this->sanitize = $sanitize;
	}
	
	public function name() {
		return $this->name;
	}
	
	public function title() {
		return $this->title;
	}
	
	public function httpPostName() {
		return $this->http_post_name;
	}
	
	public function isRequired() {
		return $this->is_required;
	}
	
	public function validateValue($value, $page) {
		$validate = $this->validate;
		
		if ($validate === null) {
			return true;
		}
		
		return $validate($value, $page);
	}
	
	public function sanitizeValue($value, $page) {
		$sanitize = $this->sanitize;
		
		if ($sanitize === null) {
			return $value;
		}
		
		return $sanitize($value, $page);
	}
}
