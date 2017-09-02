<?php

/**
 * Status
 */
class CommentsStatus
{
	/**
	 * The status code.
	 *
	 * @var integer
	 */
	private $code;
	/**
	 * The size of a individual status-domain.
	 *
	 * @var integer
	 */
	static private $domain_size = 100;
	/**
	 * Defines the individual status-domains.
	 *
	 * @var array
	 */
	static private $domains = array(
		'no_error'     => 0,
		'system_error' => 100,
		'dev_error'    => 200,
		'user_error'   => 300,
		'custom_error' => 400
	);
	/**
	 * The exception the status is based on.
	 *
	 * @var Exception
	 */
	private $exception;
	
	function __construct($code, $exception = null)
	{
		$this->code = $code;
		$this->exception = $exception;
	}
	
	private function is_in_domain($domain_name) {
		$code = $this->code;
		$domain = CommentsStatus::$domains[$domain_name];
		$size = CommentsStatus::$domain_size;
		
		return ($code >= $domain) && ($code < $domain + $size);
	}
	
	public function getCode()
	{
		return $this->code;
	}
	
	public function getMessage()
	{
		if ($this->exception != null) {
			return $this->getException()->getMessage();
		}
		
		return "Status with code {$this->code}.";
	}
	
	public function getException()
	{
		return $this->exception;
	}
	
	public function isUserError()
	{
		return $this->is_in_domain('user_error') || $this->is_in_domain('custom_error');
	}
	
	public function isError()
	{
		return !$this->is_in_domain('no_error');
	}
}
