<?php
/*
 * b2evolution - http://b2evolution.net/
 *
 * Copyright (c) 2003-2004 by Francois PLANQUE - http://fplanque.net/
 * Released under GNU GPL License - http://b2evolution.net/about/license.html
 *
 * This file implements "data objects by fplanque" :P
 */

/*
 * This is typically an abstract class, useful only when derived
 */
class DataObject
{
	var	$dbtablename;
	var $dbprefix;
	var $dbchanges = array();
	var	$ID = 0;		// This will be the ID in the DB

	/* 
	 * DataObject::DataObject(-)
	 *
	 * Constructor
	 */
	function DataObject( $tablename, $prefix = '' )
	{
		$this->dbtablename = $tablename;
		$this->dbprefix = $prefix;
	}	
	
	/* 
	 * DataObject::get(-)
	 *
	 * Get a param
	 */
	function get( $parname )
	{
		return $this->$parname;
	}

	/* 
	 * DataObject::disp(-)
	 *
	 * Display a param
	 */
	function disp( $parname, $format = 'htmlbody' )
	{
		// Note: we call get again because of derived objects specific handlers !
		echo format_to_output( $this->get($parname), $format );
	}


	/* 
	 * User::set(-)
	 *
	 * Set param value
	 */
	function set( $parname, $fieldtype, $parvalue )
	{
		// Set value:
		$this->$parname = $parvalue;
		// Remmeber change for later db update:
		$this->dbchange( $this->dbprefix.$parname , $fieldtype, $parname );
	}

	/* 
	 * DataObject::dbchange(-)
	 *
	 * Records a change that will need to be updated in the db
	 */
	function dbchange( $dbfieldname, $dbfieldtype, $valuepointer )
	{
		$this->dbchanges[$dbfieldname]['type'] = $dbfieldtype;
		$this->dbchanges[$dbfieldname]['value'] = $valuepointer ;
	}


	/* 
	 * DataObject::dbupdate(-)
	 *
	 * Update the DB based on previously recorded changes
	 */
	function dbupdate( )
	{
		global $querycount;
	
		if( $this->ID == 0 ) die( 'New object cannot be updated!' );
	
		if( count( $this->dbchanges ) == 0 )
			return;	// No changes!
			
		$sql_changes = array();
		foreach( $this->dbchanges as $loop_dbfieldname => $loop_dbchange )
		{
			// Get changed value:
			eval('$loop_value = $this->'.$loop_dbchange['value'].';');
			// Prepare matching statement:
			switch( $loop_dbchange['type'] )
			{
				case 'string':
					$sql_changes[] = $loop_dbfieldname." = '".addslashes( $loop_value )."' ";
					break;
					
				default:
					$sql_changes[] = $loop_dbfieldname." = $loop_value ";
			}				
		}

		// Prepare full statement:
		$sql = "UPDATE $this->dbtablename SET ".implode( ', ', $sql_changes )." WHERE ID = $this->ID";
		//echo $sql;

		$querycount++;
		$result = mysql_query($sql) or mysql_oops( $query );
		
		// Reset changes in object:
		$this->dbchanges = array();
		
		return $result;
	}
	

	/* 
	 * DataObject::dbinsert(-)
	 *
	 * Insert object into DB based on previously recorded changes
	 */
	function dbinsert( )
	{
		global $querycount;
	
		if( $this->ID != 0 ) die( 'Existing object cannot be inserted!' );
	
		$sql_fields = array();
		$sql_values = array();
		foreach( $this->dbchanges as $loop_dbfieldname => $loop_dbchange )
		{
			// Get changed value:
			eval('$loop_value = $this->'.$loop_dbchange['value'].';');
			// Prepare matching statement:
			$sql_fields[] = $loop_dbfieldname;
			switch( $loop_dbchange['type'] )
			{
				case 'string':
					$sql_values[] = "'".addslashes( $loop_value )."' ";
					break;
					
				default:
					$sql_values[] = $loop_value;
			}				
		}

		// Prepare full statement:
		$sql = "INSERT INTO $this->dbtablename (".implode( ', ', $sql_fields ).") VALUES (".implode( ', ', $sql_values ).")";
		// echo $sql;

		$querycount++;
		$result = mysql_query($sql) or mysql_oops( $query );

		// store ID for newly created db record
		$this->ID = mysql_insert_id();
		
		// Reset changes in object:
		$this->dbchanges = array();
		
		return $result;
	}
	
}
?>
