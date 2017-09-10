<?php

/**
 * CommentsField
 * 
 * Comments fields hold the value of a custom field and associate it with a
 * comments field type. Upon construction, a comments field validates its value
 * using the associated type and, if the validation was successful, sanitizes
 * the value, which may transform it into a value of any type.
 *
 * @package   Kirby Comments
 * @author    Florian Pircher <florian@addpixel.net>
 * @link      https://addpixel.net/kirby-comments/
 * @copyright Florian Pircher
 * @license   https://addpixel.net/kirby-comments/LICENSE
 */
class CommentsField
{
	/**
	 * Type of the field.
	 *
	 * @var CommentsFieldType
	 */
	private $type;
	/**
	 * Value of the field. This value is always valid and sanitized.
	 *
	 * @var mixed|null
	 */
	private $value;
	
	/**
	 * CommentsField constructor.
	 *
	 * @param CommentsFieldType $type
	 * @param mixed|null $value
	 * @param Page $page
	 * @param bool $process_value Whether to validate and sanitize the value.
	 * @throws Exception Throws on value validation errors.
	 */
	function __construct($type, $value, $page, $process_value) {
		$this->type = $type;
		
		if ($process_value && !$this->type->validateValue($value, $page)) {
			throw new Exception('The value of the '.$this->type->title().' field is invalid.', 311);
		}
		
		$this->value = $process_value ? $this->type->sanitizeValue($value, $page) : $value;
	}
	
	/**
	 * Type of the field.
	 *
	 * @return CommentsFieldType
	 */
	public function type() {
		return $this->type;
	}
	
	/**
	 * Name of the field.
	 *
	 * @return string
	 */
	public function name() {
		return $this->type->name();
	}
	
	/**
	 * Value of the field. This value is always valid and sanitized.
	 *
	 * @return mixed|null
	 */
	public function value() {
		return $this->value;
	}
}
