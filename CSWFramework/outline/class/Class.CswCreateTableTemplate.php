<?php

//modif pour voir le fonctionnement du versionning 2
// faire un héritage multiple à la fin

/**
 *
 */

class CswCreateTableTemplate
{
	protected $engine = null;
	protected $charset = null;
	protected $collate = null;
	protected $primaryKey = null;
	protected $foreignKeys = array();
	protected $columns = array();
	protected $autoIncrementation = null;
	protected $database = null;
	protected $table = null;
	protected $uniqueKeys = array();
	protected $indexKeys = array();
	protected $fulltextKeys = array();
	protected $returnTemplate = null;
	protected $returnTemplateIndex = null;
	protected $sgdb = null;
	protected $templateIndexes = array();
	protected $templateForeignKeys = array();

	// template ici les templates

	private $creationTemplate = 'CREATE  TABLE IF NOT EXISTS `%1`.`%2`';

	public function __CONSTRUCT($sgdb = MYSQL)
	{
		$this->sgdb = $sgdb;
		$this->setEngine();
		$this->setCharset();
		$this->setCollate();
		$this->setAutoIncrementation();
	}

	
	/**
	 * CHanger car il peut y avoir plusieurs primary key ou du moins un jeu de colonne mais qu'un seul auto incre
	 */
	
	protected function formateCols()
	{
		$cols = array(); 

		foreach($this->columns as $column)
		{
			$col = '`' . $column['name'] . '` ';
			$col .= $column['type'] . ' ';

			if($this->primaryKey == $column['name'])
			{
				$col .= 'PRIMARY KEY ';

				if($this->autoIncrementation == true)
				{
					$col .= 'AUTO_INCREMENT ';
				}
			}

			if(in_array($column['name'], $this->uniqueKeys))
			{
				$col .= 'UNIQUE ';
			}

			if($column['null'] == false) 
				$col .= 'NOT NULL ';

			if($column['default'] != null) 
				$col .= 'DEFAULT \'' . $column['default'] . '\'';

			$cols[] = $col;
		}

		return implode(', ', $cols);
	}


	/**
	 *
	 */

	protected function formateForeinKeys()
	{
		foreach($this->foreignKeys as $foreignKey)
		{
			$returnTemplate = ' CONSTRAINT ';

			if
			(
				is_array($foreignKey['columns']) == true && 
				empty($foreignKey['columns']) == false
			)
			{
				$returnTemplate .= 'fk_' . implode('_', $foreignKey['columns']) . ' '; 
			}

			$returnTemplate .= 'FOREIGN KEY '; 
			$returnTemplate .= '(`' . implode('`, `', $foreignKey['columns']) . '`) '; 
			$returnTemplate .= 'REFERENCES ';
			$returnTemplate .= '`' . $foreignKey['table']  . '` ';
			$returnTemplate .= '(`' . implode('`, `', $foreignKey['columnsExt']) . '`) '; ;

			$foreignKeys[] = $returnTemplate;
		}

		$this->templateForeignKeys = implode(', ', $foreignKeys);

		// CONSTRAINT 'nom de la contrainte' FOREIGN KEY ('col', 'cols') REFERENCES 'table extérieur' ('col ext', 'cols ext'))
	}

	
	/**
	 *
	 */

	public function formateTemplate()
	{
		if(empty($this->foreignKeys) == false)
		{
			$this->formateForeinKeys();
		}

		$returnTemplate = $this->creationTemplate;
		$returnTemplate .= '(';
		$returnTemplate .= $this->formateCols();
		$returnTemplate .= ', ' . $this->templateForeignKeys;
		$returnTemplate .= ') ';
		$returnTemplate .= 'CHARACTER SET ' . $this->charset . ' ';
		$returnTemplate .= 'COLLATE ' . $this->collate . ' ';
		$returnTemplate .= 'ENGINE=' . $this->engine . ' ';

		if(empty($this->indexKeys) == false)
		{
			$this->formateTemplateIndex();
		}

		$this->returnTemplate['table'] = $returnTemplate;
		$this->returnTemplate['indexes'] = $this->templateIndexes;
		$this->returnTemplate['foreignKey'] = $this->templateForeignKeys;
		
		//ADD CONTRAINTE uc_PersonID UNIQUE (P_ID,LastName)
	}


	/**
	 * [UNIQUE|FULLTEXT|SPATIAL]
	 */

	protected function formateTemplateIndex()
	{
		$nbIndex = count($this->indexKeys);

		for($i = 0; $i < $nbIndex; $i++)
		{
			$arrayKey = array_keys($this->indexKeys[$i]);

			$indexType = (is_string($arrayKey[0])) ? $arrayKey[0] : '';
			$indexName = 'index_' . implode('_', $this->indexKeys[$i]);
			$indexColumns = implode(', ', $this->indexKeys[$i]);

			$this->templateIndexes[] = 'CREATE ' . $indexType . ' INDEX ' . $indexName . ' ON `%1`.`%2` (' . $indexColumns . ')';
		}
	}

	/**
	 *
	 */

	public function getTemplate()
	{
		return $this->returnTemplate;
	}

	/**
	 *
	 */

	public function setAutoIncrementation($autoIncrementation = false)
	{
		$this->autoIncrementation = $autoIncrementation;
	}


	/**
	 *
	 */

	public function setCharset($charset = null)
	{
		$this->charset = (empty($charset)) ? constant($this->sgdb . '_CHARSET_UTF8') : $charset;
	}


	/**
	 *
	 */

	public function setCollate($collate = null)
	{
		$this->collate = (empty($collate)) ? constant($this->sgdb . '_COLLATE_UTF8_GENERAL_CI') : $collate;
	}


	/**
	 *
	 */

	public function setColumn($column = array())
	{
		$defaultOptions = array
		(
			'name' => null,
			'type' => null,
			'null' => null,
			'default' => null
		);

		$column = array_merge($defaultOptions, $column);

		$this->columns[] = $column;
	}


	/**
	 *
	 */

	public function setEngine($engine = null)
	{
		$this->engine = (empty($engine)) ? constant($this->sgdb . '_INNODB') : $engine;
	}


	/**
	 *
	 */

	public function setForeignKey($columns = array())
	{
		$defaultOptions = array
		(
			'columns' => array(),
			'table' => null,
			'columnsExt' => array()
		);

		$foreignKey = array_merge($defaultOptions, $columns);

		$this->foreignKeys[] = $foreignKey;
	}


	/**
	 *
	 */

	public function setFulltextKey($column = array())
	{
		$this->fulltextKeys = $columns;
	}


	/**
	 *
	 */

	public function setIndexKey($columns = array())
	{
		$this->indexKeys[] = $columns;
	}


	/**
	 *
	 */

	public function setPrimaryKey($column = null)
	{
		$this->primaryKey = $column;
	}


	/**
	 *
	 */

	public function setUniqueKey($columns = array())
	{
		$this->uniqueKeys = $columns;
	}
}

?>
