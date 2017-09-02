<?php

/**
 * CommentsField
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
	 * Value of the field.
	 *
	 * @var mixed
	 */
	private $value;
	
	function __construct($type, $value, $page, $process_value) {
		$this->type = $type;
		
		if ($process_value && !$this->type->validateValue($value, $page)) {
			throw new Exception('The value of the '.$this->type->title().' field is invalid.', 311);
		}
		
		$this->value = $process_value ? $this->type->sanitizeValue($value, $page) : $value;
	}
	
	public function type() {
		return $this->type;
	}
	
	public function name() {
		return $this->type->name();
	}
	
	public function value() {
		return $this->value;
	}
}
