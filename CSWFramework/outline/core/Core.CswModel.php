<?php

class CswModel
{
	public function __call($method, $arguments)
	{
		if(preg_match("/get/", $method))
		{
			$property = lcfirst(str_replace('get', '', $method));
			$return = $this->$property;

			return $return['value'];
		}

		if(preg_match("/set/", $method))
		{
			$property = lcfirst(str_replace('set', '', $method));
			
			$value = $this->$property;
			$value['value'] = $arguments[0];
			
			$this->$property = $value;
		}
	}

	protected function returnState($type, $message)
	{
		if($message == 'null')
			return;

		$return = new stdClass;
		$return->type = $type;
		$return->message = $message;

		$_SESSION[ALERTSESSIONNAME] = $return;
	}

	public function getReturnState()
	{
		return $_SESSION[ALERTSESSIONNAME];
	}

	public function publicSave()
	{
		$this->save();
	}

	protected function save()
	{
		if($this->getId() == null)
		{
			$properties = $this->getProperties();

			if($this->emptyProperties())
			{
				$this->returnState
				(
					ALERTWARNING, 
					NOTALLFIELD
				);

				return false;
			}

			if(!$this->validateProperties())
			{
				$this->returnState
				(
					ALERTWARNING, 
					WRONGDATA
				);

				return false;
			}

			foreach($properties as $property => $values)
			{
				if(in_array($property, $this->escapedProperties))
					continue;

				$columns[] = $property;
				$inserts[$property] = $values['value'];
			}


			$request = 'INSERT INTO ' . strtolower(get_class($this)) .' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')';
			
			try
			{

				$query = $this->exchangeBdd->getConnection()->prepare($request);

				$query->execute($inserts);

				$this->setId($this->exchangeBdd->getConnection()->lastInsertId());
				
				$this->returnState
				(
					ALERTSUCCES, 
					constant('SUCCESSAVE' . strtoupper(get_class($this)))
				);

				return true;
			}
			catch(Exception $e)
			{
				//die($e->getMessage());
				$this->returnState
				(
					ALERTWARNING, 
					constant('ERRORSAVE' . strtoupper(get_class($this)))
				);

				return false;
			}
		}
		else
		{
			 return $this->update();
		}
	}

	protected function update()
	{
		if(!$this->validateProperties())
		{
			$this->returnState
			(
				ALERTWARNING, 
				WRONGDATA
			);

			return false;
		}

		$properties = $this->getProperties();

		foreach($properties as $property => $values)
		{
			if(in_array($property, $this->escapedProperties))
				continue;

			$columns[] = $property . ' = :' . $property ;
			$inserts[$property] = $values['value'];
		}

		$request = 'UPDATE ' . strtolower(get_class($this)) . ' SET ' . implode(', ', $columns). ' WHERE id = :id';
		
		try
		{

			$query = $this->exchangeBdd->getConnection()->prepare($request);

			$query->execute($inserts);
			
			$this->returnState
			(
				ALERTSUCCES, 
				constant('SUCCESMAJ' . strtoupper(get_class($this)))
			);

			return true;
		}
		catch(Exception $e)
		{
			//die($e->getMessage());
			$this->returnState
			(
				ALERTWARNING, 
				constant('ERRORMAJ' . strtoupper(get_class($this)))
			);

			return false;
		}
	}

	public function loadSecure($id, $uid)
	{
		$values = array
		(
			'id' => $id,
			'uid' => $uid
		);

		$request = 'SELECT * FROM '. strtolower(get_class($this)) .' WHERE id = :id AND uid = :uid';

		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute($values);

		$rows = $query->fetchAll();
		$nbrows = count($rows);

		if($nbrows == 1)
		{
			foreach($rows[0] as $property => $value)
			{
				$values = $this->$property;
				$values['value'] = $value;
				$this->$property = $values;
			}
		}
	}

	public function getAll()
	{
		$request = 'SELECT * FROM ' . strtolower(get_class($this));

		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute();
		$rows = $query->fetchAll();

		return $rows;
	}

	public function load($id)
	{
		$values = array
		(
			'id' => $id
		);

		$request = 'SELECT * FROM '. strtolower(get_class($this)) .' WHERE id = :id';

		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute($values);

		$rows = $query->fetchAll();
		$nbrows = count($rows);

		if($nbrows == 1)
		{
			foreach($rows[0] as $property => $value)
			{
					$values = $this->$property;
					$values['value'] = $value;
					$this->$property = $values;
			}
		}
	}

	protected function emptyProperties()
	{
		$properties = $this->getProperties();

		foreach($properties as $property => $values)
		{
			if(in_array($property, $this->escapedProperties))
				continue;

			if($values['required'] == true && empty($values['value']))
			{
				//die($property . ' -> ' . $values['value']);
				return true;
			}
		}

		return false;
	}

	protected function validateProperties()
	{
		$properties = $this->getProperties();

		foreach($properties as $property => $values)
		{
			if(in_array($property, $this->escapedProperties))
				continue;

			if(!empty($values['type']) && !empty($values['value']))
			{
				$function = 'is' . ucfirst($values['type']);

				if(!CswRegex::$function($values['value']))
				{
					//die($property . ' -> ' . $values['value']);
					return false;
				}
			}
		}

		return true;
	}

	protected function getProperties($object = null)
	{
		$object = (empty($object)) ? $this : $object;
		return get_object_vars($object);
	}

	public function getThread($id, $recursive = false, &$thread = array())
	{
		if(!$recursive)
		{
			$values = array('id' => $id);

			$request = 'SELECT * FROM ' . strtolower(get_class($this)) . ' WHERE id = :id';

			$query = $this->exchangeBdd->getConnection()->prepare($request);

			$query->execute($values);
			$rows = $query->fetchAll();


			$nbrows = count($rows);


			if($nbrows == 1)
			{
				$row = $rows[0];
				
				$values = array('parentId' => $row->id);

				$thread[] = $row;
				$request = 'SELECT * FROM ' . strtolower(get_class($this)) . ' WHERE parentId = :parentId';

				$query = $this->exchangeBdd->getConnection()->prepare($request);

				$query->execute($values);
				$rows = $query->fetchAll();

				$nbrows = count($rows);

				if($nbrows == 1)
				{

					$row = $rows[0];
					$thread[] = $row;
					$this->getThread($row->id, true, $thread);
				}
			}
		}
		else
		{
			$values = array('parentId' => $id);


			$request = 'SELECT * FROM ' . strtolower(get_class($this)) . ' WHERE parentId = :parentId';

			$query = $this->exchangeBdd->getConnection()->prepare($request);

			$query->execute($values);
			$rows = $query->fetchAll();

			$nbrows = count($rows);

			if($nbrows == 1)
			{
				$row = $rows[0];
				$thread[] = $row;
				$this->getThread($row->id, true, $thread);
			}
		}
	}

	protected function loadByXml($nodes)
	{
		$properties = $this->getProperties();

		foreach($properties as $propertyName => $value)
		{

		    foreach($nodes as $node)
		    {
		    	if($node->nodeName != $propertyName)
		    		continue;

		    	$function = 'set' . ucfirst($node->nodeName);
		    	if($node->nodeName == 'price' || $node->nodeName == 'deliveryCost')
		    		$this->$function(str_replace(',', '.', $node->nodeValue));
		    	else
		    		$this->$function($node->nodeValue);
			}
		}

		$this->setId(null);
	}

	protected function loadByTxt($nodes)
	{
		$properties = $this->getProperties();

		foreach($properties as $propertyName => $value)
		{

		    foreach($nodes as $nodeName => $nodeValue)
		    {
		    	if($nodeName != $propertyName)
		    		continue;

		    	$function = 'set' . ucfirst($nodeName);

		    	if($nodeName == 'price'  || $nodeName == 'deliveryCost')
		    		$this->$function(str_replace(',', '.', $nodeValue));
		    	else
		    		$this->$function($nodeValue);
			}
		}

		$this->setId(null);
	}

	protected function delete()
	{
		$values = array
		(
			'id' => $this->getId()
		);

		$request = 'DELETE FROM ' . strtolower(get_class($this)) . ' WHERE id = :id';

		try
		{

			$query = $this->exchangeBdd->getConnection()->prepare($request);

			$query->execute($values);
			
			$this->returnState
			(
				ALERTSUCCES, 
				constant('SUCCESDELETE' . strtoupper(get_class($this)))
			);
		}
		catch(Exception $e)
		{
			//die($e->getMessage());
			$this->returnState
			(
				ALERTWARNING, 
				constant('ERRORDELETE' . strtoupper(get_class($this)))
			);
		}
	}

	public function __wakeup()
	{
		global $exchangeBdd;
		$this->exchangeBdd = $exchangeBdd;
	}
}