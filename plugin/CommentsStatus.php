<?php

/**
 * CommentsStatus
 * 
 * A status describes the state of an object after an operation and can either
 * be a success status or an error status. While only one success code (status
 * code 0) exists, multiple errors codes (split into multiple domains) help to
 * describe the exact nature of the problem.
 *
 * @package   Kirby Comments
 * @author    Florian Pircher <florian@addpixel.net>
 * @link      https://addpixel.net/kirby-comments/
 * @copyright Florian Pircher
 * @license   https://addpixel.net/kirby-comments/LICENSE
 */
class CommentsStatus
{
	/**
	 * Status code indicating the type of status. Have a look at the documentation
	 * for a complete table of status codes and their meaning.
	 *
	 * @var integer
	 */
	private $code;
	
	/**
	 * Size of a status code domain. All status code domains span a range of this
	 * size starting at a specific value.
	 *
	 * @var integer
	 */
	static private $domain_size = 100;
	
	/**
	 * Starting values of all valid status code domains referenced by their
	 * domain name.
	 *
	 * @var integer[string]
	 */
	static private $domains = array(
		'success'         => 0,
		'system_error'    => 100,
		'developer_error' => 200,
		'user_error'      => 300,
		'custom_error'    => 400
	);
	
	/**
	 * Exception that has caused this status. `null` iff the status is not based
	 * upon an exception.
	 *
	 * @var Exception|null
	 */
	private $exception;
	
	/**
	 * CommentsStatus constructor.
	 *
	 * @param integer $code
	 * @param Exception|null $exception
	 */
	function __construct($code, $exception=null)
	{
		$this->code = $code;
		$this->exception = $exception;
	}
	
	/**
	 * Returns `true` iff the status code is in a certain domain.
	 *
	 * @param string $domain_name
	 * @return bool
	 */
	private function is_in_domain($domain_name)
	{
		$code = $this->code;
		$domain = CommentsStatus::$domains[$domain_name];
		$size = CommentsStatus::$domain_size;
		
		return ($code >= $domain) && ($code < $domain + $size);
	}
	
	/**
	 * Status code indicating the type of status. Have a look at the documentation
	 * for a complete table of status codes and their meaning.
	 *
	 * @return integer
	 */
	public function getCode()
	{
		return $this->code;
	}
	
	/**
	 * Message of the exception that caused this status iff `$this->exception` is
	 * not `null`; string describing the status code as such otherwise.
	 *
	 * @return string
	 */
	public function getMessage()
	{
		if ($this->exception !== null) {
			return $this->getException()->getMessage();
		}
		
		return 'Status with code '.$this->code.'.';
	}
	
	/**
	 * Exception that has caused the status. `null` iff the status is not based
	 * upon an exception.
	 *
	 * @return Exception|null
	 */
	public function getException()
	{
		return $this->exception;
	}
	
	/**
	 * `true` iff the status is in the User or Custom domain.
	 *
	 * @return bool
	 */
	public function isUserError()
	{
		return $this->is_in_domain('user_error') || $this->is_in_domain('custom_error');
	}
	
	/**
	 * `true` iff the status is not in the Success domain.
	 *
	 * @return bool
	 */
	public function isError()
	{
		return !$this->is_in_domain('success');
	}
}
