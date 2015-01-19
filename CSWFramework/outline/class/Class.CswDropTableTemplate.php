<?php

class CswDropTableTemplate
{
	protected $returnTemplate = null;
	/**
	 * TABLE | DOMAIN | ASSERTION | VIEW
	 */

	public function __CONSTRUCT($sgdb = MYSQL, $object = TABLE)
	{
		$this->returnTemplate = 'DROP ' . $object . ' `%1`.`%2`' ;
	}

	public function getTemplate()
	{
		return $this->returnTemplate;
	}
}

?>
