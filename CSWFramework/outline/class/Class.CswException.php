<?php

class CswException extends Exception
{
	protected $time;
	protected $trace;
	protected $traceString;

	public function __construct($type = STANDARD, $message = null, $code = 0)
	{
		parent::__construct($message, $code);
		$this->time = time();
	}

	public function getException()
	{
		$this->trace = $this->getTrace();
		$this->traceString = $this->getTraceAsString();
	}

	public static function catchOrphanException()
	{

	}

	/*
		final function getMessage();
		final function getCode();
		final function getFile();
		final function getLine();
	*/
}

set_exception_handler('CswException::catchOrphanException');

?>
