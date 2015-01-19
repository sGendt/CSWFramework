<?php

/*ALTER [IGNORE] TABLE tbl_name
    alter_specification [, alter_specification] ...

alter_specification:
    ADD [COLUMN] column_definition [FIRST | AFTER col_name ] // par default en fin de table
  | ADD [COLUMN] (column_definition,...)
  | ADD INDEX [index_name] [index_type] (index_col_name,...)
  | ADD [CONSTRAINT [symbol]]
        PRIMARY KEY [index_type] (index_col_name,...)
  | ADD [CONSTRAINT [symbol]]
        UNIQUE [index_name] [index_type] (index_col_name,...)
  | ADD [FULLTEXT|SPATIAL] [index_name] (index_col_name,...)
  | ADD [CONSTRAINT [symbol]]
        FOREIGN KEY [index_name] (index_col_name,...)
        [reference_definition]
  | ALTER [COLUMN] col_name {SET DEFAULT literal | DROP DEFAULT}
  | CHANGE [COLUMN] old_col_name column_definition
        [FIRST|AFTER col_name]
  | MODIFY [COLUMN] column_definition [FIRST | AFTER col_name]
  | DROP [COLUMN] col_name
  | DROP PRIMARY KEY
  | DROP INDEX index_name
  | DROP FOREIGN KEY fk_symbol

  | DISABLE KEYS
  | ENABLE KEYS
  | RENAME [TO] new_tbl_name
  | ORDER BY col_name
  | CONVERT TO CHARACTER SET charset_name [COLLATE collation_name]
  | [DEFAULT] CHARACTER SET charset_name [COLLATE collation_name]
  | DISCARD TABLESPACE
  | IMPORT TABLESPACE
  | table_options*/

 /*supprimer une colonne ok
    supprimer une contrainte ok
    ajouter une colonne ok avec extension
    ajouter une contrainte ok avec extension
    ajouter une contrainte de ligne DEFAULT ok*/


 // Il y a une similarité avec CswCreateTableTemplate pensé à optimiser et supprimer les doublons de code
 // en revoyant l'architecture

 class CswAlterTableTemplate
 {
 	protected $returnTemplate = null;
 	
 	protected $dropedColumns = array();
 	protected $dropedPrimarykey = array();
	protected $dropedIndexes = array();
	protected $dropedForeignKeys = array();

 	protected $addedColumns = array();
 	protected $addedIndexes = array();
 	protected $addedPrimaryKeys = array();
 	protected $addedForeignKeys = array();

 	protected $defaultValueOfColumns = array();

 	protected $changedColumns = array();
 	protected $modifiedColumns = array();

	protected $newTableName = null;
	protected $convertedCharset = null;
	protected $convertedCollate = null;

 	public function __construct($sgdb = MYSQL, $object = TABLE)
 	{
 		$this->returnTemplate = 'ALTER ' . $object . ' `%1`.`%2`';
 	}


 	public function formateTemplate()
 	{
 		foreach($this->dropedColumns as $column)
 		{
 			$this->returnTemplate .= '<br /> DROP ' . $column;
 		}

 		if($this->dropedPrimarykey == true) $this->returnTemplate .= '<br /> DROP PRIMARY KEY';

 		foreach($this->dropedIndexes as $dropedIndex)
 		{
 			$this->returnTemplate .= '<br /> DROP INDEX ' . $dropedIndex;
 		}

 		foreach($this->dropedForeignKeys as $dropedForeignKey)
 		{
 			$this->returnTemplate .= '<br /> DROP FOREIGN KEY ' . $dropedForeignKey;
 		}

 		$this->returnTemplate .= '<br /> ' . $this->formateCols('ADD');
 		$this->returnTemplate .= '<br /> ' . $this->formateIndexes();
 		$this->returnTemplate .= '<br /> ' . $this->formatePrimaryKeys();
 		$this->returnTemplate .= '<br /> ' . $this->formateColumnToChangeDefaultValue();
 		$this->returnTemplate .= '<br /> ' . $this->formateChangeCols();
 		$this->returnTemplate .= '<br /> ' . $this->formateCols('MODIFY');
 		$this->returnTemplate .= '<br /> ' . $this->formateNewTableName();
 		$this->returnTemplate .= '<br /> ' . $this->formateCharsetCollate();
 	}


 	/**
	 *
	 */
	
	protected function formateCharsetCollate()
	{
		$returnTemplate = '';

		if($this->convertedCharset != null) 
			$returnTemplate .= 'CONVERT TO CHARACTER SET ' . $this->convertedCharset;
		if($this->convertedCollate != null)
			$returnTemplate .= 'COLLATE ' . $this->convertedCollate;

		return $returnTemplate;
	}


 	/**
	 *
	 */
	
	protected function formateNewTableName()
	{
		if($this->newTableName != null) return 'RENAME `' . $this->newTableName . '`';
	}


 	/**
	 *
	 */
	
	protected function formateCols($action = 'ADD')
	{
		$cols = array();

		foreach($this->addedColumns as $column)
		{
			$col = $action . ' `' . $column['name'] . '` ';
			$col .= $column['type'] . ' ';

			if($column['null'] == false) 
				$col .= 'NOT NULL ';

			if($column['default'] != null) 
				$col .= 'DEFAULT \'' . $column['default'] . '\'';

			if($column['position'] != null) 
				$col .= ' ' . $column['position'] ;

			$cols[] = $col;
		}

		return implode(' ', $cols);
	}


	/**
	 *
	 */

	protected function formateChangeCols()
	{
		$cols = array();

		foreach($this->changedColumns as $column)
		{
			$col = 'CHANGE `' . $column['oldName'] . '` `' . $column['name'] . '` ';
			$col .= $column['type'] . ' ';

			if($column['null'] == false) 
				$col .= 'NOT NULL ';

			if($column['default'] != null) 
				$col .= 'DEFAULT \'' . $column['default'] . '\'';

			if($column['position'] != null) 
				$col .= ' ' . $column['position'] ;

			$cols[] = $col;
		}

		return implode(' ', $cols);
	}


	/**
	 * [UNIQUE|FULLTEXT|SPATIAL]
	 */

	protected function formateIndexes()
	{
		$nbIndex = count($this->addedIndexes);

		$indexes = array();

		for($i = 0; $i < $nbIndex; $i++)
		{
			$arrayKey = array_keys($this->addedIndexes[$i]);

			$indexType = (is_string($arrayKey[0])) ? $arrayKey[0] : '';
			$indexName = 'index_' . implode('_', $this->addedIndexes[$i]);
			$indexColumns = implode('`, `', $this->addedIndexes[$i]);

			$indexes[] = 'ADD INDEX ' . $indexName . ' ' . $indexType . ' (`' . $indexColumns . '`)';
		}

		return implode(' ', $indexes);
	}


	protected function formatePrimaryKeys()
	{
		$nbPrimaryKey = count($this->addedPrimaryKeys);

		$primaryKeys = array();

		for($i = 0; $i < $nbPrimaryKey; $i++)
		{
			$primaryKeyName = 'pk_' . implode('_', $this->addedPrimaryKeys[$i]);
			$primaryKeyColumns = implode('`, `', $this->addedPrimaryKeys[$i]);

			$primaryKeys[] = 'ADD CONSTRAINT ' . $primaryKeyName . ' PRIMARY KEY (`' . $primaryKeyColumns . '`)';
		}

		return implode(' ', $primaryKeys);
	}


	protected function formateForeignKeys()
	{
		$foreignKeys = array();

		foreach($this->addedForeignKeys as $foreignKey)
		{
			$returnTemplate = 'ADD CONSTRAINT ';

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
			$returnTemplate .= '(`' . implode('`, `', $foreignKey['columnsExt']) . '`) ';

			$foreignKeys[] = $returnTemplate;
		}

		return implode(' ', $foreignKeys);
	}


	protected function formateColumnToChangeDefaultValue()
	{
		$defaultValueOfColumns = array();

		$nbDefaultValueOfColumn = count($this->defaultValueOfColumns);

		for($i = 0; $i < $nbDefaultValueOfColumn; $i++)
		{
			$returnTemplate = 'ALTER COLUMN `' . $this->defaultValueOfColumns[$i]['name'] . '`';

			if($this->defaultValueOfColumns[$i]['value'] == null)
			{
				$returnTemplate .= ' DROP DEFAULT';
			}
			else
			{
				$returnTemplate .= ' SET DEFAULT \'' . $this->defaultValueOfColumns[$i]['value'] . '\'';
			}

			$defaultValueOfColumns[] = $returnTemplate;
		}

		return implode(' ', $defaultValueOfColumns);
	}


 	public function getTemplate()
 	{
 		return $this->returnTemplate;
 	}


    public function setColumnToChangeDefaultValue($defaultValueOfColumn = array())
 	{
 		$defaultOptions = array
 		(
 			'name' => null, 
 			'value' => null // SET | DROP
 		);

 		$defaultValueOfColumn = array_merge($defaultOptions, $defaultValueOfColumn);

 		$this->defaultValueOfColumns[] = $defaultValueOfColumn;
 	}


 	public function setColumnToDrop($dropedColumn = array())
 	{
 		$defaultOptions = array
 		(
 			'name' => null
 		);

 		$alteration = array_merge($defaultOptions, $dropedColumn);

 		$this->dropedColumns[] = $dropedColumn;
 	}

	/**
	 *
	 */

	public function setColumnToAdd($addedColumn = array())
	{
		$defaultOptions = array
		(
			'name' => null,
			'type' => null,
			'null' => null,
			'default' => null,
			'position' => null //[FIRST | AFTER col_name ]
		);

		$addedColumn = array_merge($defaultOptions, $addedColumn);

		$this->addedColumns[] = $addedColumn;
	}

	/**
	 *
	 */

	public function setindexToAdd($columns = array())
	{
		$this->addedIndexes[] = $columns;
	}

	/**
	 *
	 */

	public function setPrimaryKeyToAdd($columns = array())
	{
		$this->addedPrimaryKeys[] = $columns;
	}


	/**
	 *
	 */

	public function setForeignKeyToAdd($columns = array())
	{
		$defaultOptions = array
		(
			'columns' => array(),
			'table' => null,
			'columnsExt' => array()
		);

		$foreignKey = array_merge($defaultOptions, $columns);

		$this->addedForeignKeys[] = $foreignKey;
	}

	/**
	 *
	 */

	public function setColumnToChange($column = array())
	{
		$defaultOptions = array
		(
			'oldName' => null,
			'name' => null,
			'type' => null,
			'null' => null,
			'default' => null,
			'position' => null //[FIRST | AFTER col_name ]
		);

		$changedColumn = array_merge($defaultOptions, $column);

		$this->changedColumns[] = $changedColumn;
	}

	/**
	 *
	 */

	public function setColumnToModify($column = array())
	{
		$defaultOptions = array
		(
			'name' => null,
			'type' => null,
			'null' => null,
			'default' => null,
			'position' => null //[FIRST | AFTER col_name ]
		);

		$modifiedColumn = array_merge($defaultOptions, $column);

		$this->modifiedColumns[] = $modifiedColumn;
	}


  	/**
	 *
	 */

	public function setPrimaryKeyToDrop($isDroped = true)
	{
		$this->dropedPrimarykey = $isDroped;
	}


	/**
	 *
	 */

	public function setIndexToDrop($indexNames = array())
	{
		$this->dropedIndexes = $indexNames;
	}


	/**
	 *
	 */

	public function setForeignKeyToDrop($foreignKeyNames = array())
	{
		$this->dropedForeignKeys = $foreignKeyNames;
	}

	/**
	 *
	 */

	public function setNewTableName($name)
	{
		$this->newTableName = $name;
	}

	public function convertCaracter($charset)
	{
		 $this->convertedCharset = $charset;
	}

	public function convertCollate($collate)
	{
		 $this->convertedCollate = $collate;
	}
 }

?>
