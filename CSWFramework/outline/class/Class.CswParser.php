<?php

class CswParser
{
	protected $file = null;
	protected $datas = array();
	protected $columns = array();


	public function __construct($file = null)
	{
		$this->file = $file;
	}

	public function getDatasByTxt()
	{
		$this->formateDatasByTxt();
		return $this->datas;
	}

	/**
	 * 	European format \t separator
	 */

	protected function formateDatasByTxt()
	{
		$lines = file($this->file);

		foreach ($lines as $i => $line)
		{
			$line = str_replace("\t", '[tabulation]', $line);
			$values = explode('[tabulation]', $line);

			if($i == 0)
			{
				$this->columns = $values;
				continue;
			}

			$obj = new stdClass();

			foreach($values as $j => $value)
			{
				$property = trim($this->columns[$j]);
				$obj->$property = trim($value);
			}

			$this->datas[] = $obj;
		}
	}

	public function getDatasByCsv()
	{
		$this->formateDatasByCsv();
		return $this->datas;
	}

	/**
	 * 	European format \t separator
	 */

	protected function formateDatasByCsv()
	{
		$lines = file($this->file);

		foreach ($lines as $i => $line)
		{
			$values = explode(';', $line);

			if($i == 0)
			{
				$this->columns = $values;
				continue;
			}

			$obj = new stdClass();

			foreach($values as $j => $value)
			{
				$property = trim($this->columns[$j]);
				$obj->$property = trim($value);
			}

			$this->datas[] = $obj;
		}
	}

	public function formateDatasToCsv($datas, $pathSave)
	{
		$fields = array();
		$values = array();
		$keys = array_keys($datas);

		foreach ($keys as $key)
			$fields[] = $key;

		foreach ($datas as $data)
			$values[] = $data;

		$lines = implode('; ', $fields) . "\r\n" . implode('; ', $values);

		return CswDirectory::write($pathSave, utf8_decode($lines));
	}
}

?>