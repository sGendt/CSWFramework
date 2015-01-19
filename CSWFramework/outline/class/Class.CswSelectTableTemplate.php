<?php

class CswSelectTableTemplate
{
	protected $tables = array();
	protected $returnTemplate = '';
	protected $distinct = null; 
	protected $alias = array();
	protected $className = null;
	protected $joints = array();
	protected $clause = array();
	protected $limit = array();


	public function __construct($distinct = null)
	{
		$this->distinct = $distinct;
		$this->className = get_class($this);
	}


	/**
	 *
	 */

	public function setTable($table = array())
	{
		$defaultOptions = array
		(
			'table' => null,
			'properties' => null,
			'groupBy' => null,
			'orderBy' => null,
			'operation' => null,
			'functions' => null,
			'alias' => null,
			'limit' => null
		);

		$table = array_merge($defaultOptions, $table);

		$this->tables[] = $table; 
	}

	public function setLimit($start, $end = null)
	{
		if($end == null)
		{
			array_push($this->limit, $start);
		}
		else
		{
			array_push($this->limit, $start, $end);
		}
	}

	public function generateAlias($tableName)
	{
		$nbLetter = strlen($tableName);

		$firstLetter = 0;
		$middleLetter = floor($nbLetter / 2);
		$lastLetter = $nbLetter - 1;

		$keepLetters = array($firstLetter, $middleLetter, $lastLetter);
		$upperCases = array_keys(CswString::getUpperCase($tableName));

		for ($i = 0; $i < $nbLetter; $i++)
		{
			if 
			(
				(in_array($i, $keepLetters) == true) ||
				(in_array($i, $upperCases) == true)
			)
			{
				$letters[] = $tableName[$i];
			}
		}

		$alias = strtolower(implode('', $letters));

		return $alias;
	}

	public function setClause($clause = array())
	{
		// col5 NOT NULL and col6 IN('list') and ((col1 = 1 and col2 = 3) or (col3 = 2 or col4 = 4))

		$this->clause = $clause;
	}

	protected function formateClause()
	{
		$return = ''; 
		$cols = array();
		$comparisonOperators = array();
		$logicalOperators = array();
		$groups = array();

		foreach($this->clause['cols'] as $infoCols)
		{
			$cols[] = $this->generateAlias($infoCols[3]) . '.' .$infoCols[0];
			
			if(is_object($infoCols[2]) == false)
			{
				$comparisonOperators[] = $infoCols[1] . ' ' . $infoCols[2];
			}
			else
			{
				$comparisonOperators[] = $infoCols[1] . ' ' . $infoCols[2]->getTemplate();
			}
		}

		$logicalOperators = $this->clause['operators'];
		$logicalOperators[] = '';


		foreach($this->clause['groups'] as $group)
		{
			$grp = array();

			foreach($group as $col => $table)
			{
				$column = $this->generateAlias($table) . '.' . $col;
				$grp[] = $column;
			}

			$groups[] = $grp;
		}

		$bornGroup = $this->defineGroup($cols, $groups);

		if
		(
			(count($cols) == count($comparisonOperators)) && 
			(count($comparisonOperators) == count($logicalOperators))
		)
		{

			foreach($cols as $i => $col)
			{
				$return .= (isset($bornGroup['open'][$i]) == true) ? $bornGroup['open'][$i] : '';
				$return .= $col . ' ' . $comparisonOperators[$i] . ' ';
				$return .= (isset($bornGroup['close'][$i]) == true) ? $bornGroup['close'][$i] : '';
				$return .= ' ' . $logicalOperators[$i] . ' ';
			}
		}
		else
		{
			echo count($cols).'<br />'. count($tblCols) .'<br />'. count($tblCols).'<br />'. count($comparisonOperators).'<br />'. count($comparisonOperators).'<br />'. count($logicalOperators);
		}

		return $return;
	}


	protected function defineGroup($cols, $groups)
	{
		$return = array();

		foreach($groups as $group)
		{
			$keySkip = array();

			foreach($group as $keyColGroup => $colGroup)
			{
				$keySkip[] = array_search($colGroup, $cols);
			}

			$i = $keySkip[0];
			$state = false;

			foreach($keySkip as $key)
			{
				if($i == $key)
				{
					$state = true;
				}
				else
				{
					$state = false;
				}
				$i++;
			}

			if($state == true)
			{
				$firstKey = 0;
				$lastKey = count($keySkip) - 1;

				$return['open'][$keySkip[$firstKey]] = (isset($return['open'][$keySkip[$firstKey]]) == true) ? $return['open'][$keySkip[$firstKey]] . '(' : '(';
				$return['close'][$keySkip[$lastKey]] = (isset($return['close'][$keySkip[$lastKey]]) == true) ? $return['close'][$keySkip[$lastKey]] . ')' : ')';
			}
		}

		return $return;
	}

		/*	
			'function' => array
			(
				'functionName' => array
				(
					'columnName' => '',
					'allias' => ''
				)
			)
		*/


	/**
	 *
	 */

	public function setJoint($joint = array())
	{
		$defaultOptions = array
		(
			'tables' => array(),
			'type' => null,
			'on' => array()
		);

		$joint = array_merge($defaultOptions, $joint);

		$this->joints[] = $joint;

		/*array
		(
			'tables' => array
			(
				'maPremiereTable',
				'maDeuxiemeTable'
			),
			'type' => 'INNER',
			'on' => array
			(
				array('id', 'maPremiereTableId'),
				array('name', 'maPremiereTableName')
			)
		)*/
	}

	//vérifier si une sous-requête avec un alais dans une clausure fonctionne le SGDB mysql

	public function formateTemplate()
	{
		if(empty($this->tables[0]['alias']) == false)
		{
			$this->returnTemplate .= '(';
		}

		$this->returnTemplate .= ' SELECT';
		$this->returnTemplate .= ($this->distinct != null) ? ' ' . $this->distinct  : '';

		$this->returnTemplate .= ' ' . $this->formateProperties();
		$this->returnTemplate .= ' FROM';
		$this->returnTemplate .= ' ' . $this->formateTable();
		
		if(empty($this->clause) == false)
		{
			$this->returnTemplate .= ' WHERE';
			$this->returnTemplate .= ' ' . $this->formateClause();
		}

		$this->returnTemplate .= $this->formateGroupBy();
		$this->returnTemplate .= $this->formateOrderBy();
		$this->returnTemplate .= $this->formateLimit();

		if(empty($this->tables[0]['alias']) == false)
		{
			$this->returnTemplate .= ' ) as ' . $this->tables[0]['alias'];
		}
	}

	protected function formateLimit()
	{
		$return = '';

		if(empty($this->limit) == false)
		{
			$return = ' LIMIT ' . implode(', ', $this->limit);
		}

		return $return;
	}


	protected function formateGroupBy()
	{
		$return = '';
		$properties = array(); 


		$nbTable = count($this->tables);
		$tableNames = $this->getTablesInfo();
		$groupBys = $this->getTablesInfo('groupBy');


		for($i = 0; $i < $nbTable; $i++)
		{
			$nbProperty = count($groupBys[$i]);
			
			for($j = 0; $j < $nbProperty; $j++)
			{
				if(empty($groupBys[$i][$j]) == false)
				{
					$properties[] = $this->generateAlias($tableNames[$i]) . '.' . $groupBys[$i][$j];
				}
			}
		}

		if(empty($properties) == false)
		{
			$return = ' GROUP BY ' . implode(', ', $properties);
		}

		return $return;
	}


	protected function formateOrderBy()
	{
		$return = '';
		$properties = array();
		$orderByDirection = '';

		$nbTable = count($this->tables);
		$tableNames = $this->getTablesInfo();
		$orderBys = $this->getTablesInfo('orderBy');

		for($i = 0; $i < $nbTable; $i++)
		{
			$nbOrderBy = count($orderBys[$i]);

			if(empty($orderBys[$i]) == false)
			{
				foreach($orderBys[$i] as $direction => $properties)
				{
					$orderByDirection = $direction;
					
					$nbProperty = count($properties);

					for($j = 0; $j < $nbProperty; $j++)
					{
						$orderByProperties[] = $this->generateAlias($tableNames[$i]) . '.' . $properties[$j];
					}
				}
			}
		}

		if(empty($orderByProperties) == false)
		{
			$return = ' ORDER BY ' . implode(', ', $orderByProperties) . ' ' . $direction;
		}

		return $return;
	}



	protected function formateTable()
	{
		$return = '';

		$nbTable = count($this->tables);
		$tableNames = $this->getTablesInfo();

		if(count($this->joints) > 0)
		{
			$joints = $this->getJointsInfo();
			$jointTypes = $this->getJointsInfo('type');
			$jointOns = $this->getJointsInfo('on');

			for($i = 0; $i < $nbTable; $i++)
			{
				$readedTables[] = $tableNames[$i];

				if($i == 0)
				{
					$return .= $tableNames[$i] . ' as ' . $this->generateAlias($tableNames[$i]);
					continue;
				}

				if
				(
					(in_array($joints[$i - 1][0], $readedTables) == true) &&
					($joints[$i - 1][1] == $tableNames[$i])
				)
				{
					$return .= ' ' . $jointTypes[$i - 1];
					$return .= ' ' . $tableNames[$i]  . ' as ' . $this->generateAlias($tableNames[$i]);
					$return .= ' ON';

					$joint = array();

					foreach($jointOns[$i - 1] as $on)
					{
						$joint[] = $this->generateAlias($joints[$i - 1][0]) . '.' . $on[0] . ' = ' . $this->generateAlias($joints[$i - 1][1]) . '.' . $on[1];
					}

					$return .= ' ' . implode(' AND ', $joint);
				}
				else
				{
					// error 
				}	
			}
		}
		else
		{
			for($i = 0; $i < $nbTable; $i++)
			{
				$tableNames[$i] = $tableNames[$i] . ' as ' . $this->generateAlias($tableNames[$i]);
			}

			$return = implode(', ', $tableNames);
		} 

		return $return;
	}

	protected function getTablesInfo($info = 'table')
	{
		$return = array();

		foreach($this->tables as $table)
		{
			$return[] = $table[$info];
		}

		return $return;
	}

	protected function getJointsInfo($info = 'tables')
	{
		$return = array();

		foreach($this->joints as $joint)
		{
			$return[] = $joint[$info];
		}

		return $return;
	}


	protected function formateProperties()
	{
		$return = '';

		foreach($this->tables as $table)
		{
			$alias = $this->generateAlias($table['table']);

			foreach($table['properties'] as  $property)
			{
				if(is_object($property) == false)
				{
					$properties[] = $alias . '.' . $property;
				}
				else
				{
					$properties[] = $property->getTemplate();
				}
			}

			if(empty($table['functions']) == false)
			{
				$functions = $this->formateFunction($table, $alias);
				$properties = array_merge($properties, $functions);
			}

			$return = implode(', ', $properties);
		}

		return $return;
	}

	/**
	 *
	 */

	protected function formateFunction($table, $alias)
	{
		foreach($table['functions'] as $functionName => $properties)
		{
			foreach($properties as $property)
			{
				$functions[] = $functionName . '(' . $alias . '.' . $property['property'] . ') as ' . $property['alias']; 
			}
		}

		return $functions;
	}


	/**
	 *
	 */

	public function getTemplate()
	{
		return $this->returnTemplate;
	}
}

?>
