<?php
class CswSearchEngine
{
	protected $elements = array();
	protected $clause = null;
	protected $rows;
	public $feedback = array();
	protected $nbr;
	protected $exchangeBdd 	= null;
	protected $keywords = array();
	protected $words = array();
	
	public function __CONSTRUCT($from, $elements)
	{
		global $exchangeBdd;
		$this->exchangeBdd = $exchangeBdd;

		$this->from = $from;
		$this->elements = $elements;
		
		$this->checkElements();
	}
	
	private function checkElements()
	{
		if(!empty($this->elements))
		{
			$i=0;

			foreach($this->elements as $elements)
			{
				$clause = array();

				foreach($elements as $key => $chars)
				{
					$words =  explode(" ", $chars);

					foreach($words as $word)
					{
						$this->keywords[] = '%' . $word . '%';
						$this->words[] = $word ;
						$clause[] = $key .' LIKE ?';
						$i++;
					}
				}

				$this->clause[] = '(' . implode(' OR ', $clause) . ')';
			}
		}
		

		$this->request();
	}	
	
	private function request()
	{

		$request = 'SELECT * FROM ' . $this->from . ' WHERE ' . implode(' AND ', $this->clause);


		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute($this->keywords);
		$this->rows = $query->fetchAll();
	}
	
	
	public function searchToStrong()
	{
		$columns = array_keys($this->elements);
		foreach($this->rows as &$row)
		{
			foreach($columns as $column)
			{
				$value = $row->$column;

				foreach($this->words as $word)
					$value = preg_replace('('.$word.')i', '<b>${0}</b>', $value, -1);

				$row->$column = $value;
			}
		}
	}
	
	public function getRows()
	{
		return $this->rows;
	}

}
?>