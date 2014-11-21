<?php

class tsqli {

	public $mysqli;

	public $database;

	public $query;

	public $result;

	public $style;

	public function __construct( $host, $user, $password, $database ) {

		$this->mysqli = new mysqli( $host, $user, $password, $database );

		$this->database = $database;

	}

	public function query( $query ) {

		return $this->result = $this->mysqli->query( $query );

	}

	public function query_($query) {

		return $this->mysqli->query( $query );

	}

	public function _query( $query ) {

		$this->mysqli->query( $query );
		return $this;

	}

	public function fetch_assoc( $result = NULL ) {

		if( empty( $this->result ) ) return false;
		return $this->result->fetch_assoc();

	}

	public function fetch_array( $result = NULL ) {

		if( empty( $this->result ) ) return false;
		return $this->result->fetch_array();

	}

	public function fetch_object( ) {

		if( empty( $this->result ) ) return false;
		return $this->result->fetch_object();

	}

	public function get_cell( $query ) {

		$result = $this->query( $query )->fetch_row();
		return array_shift( $result );

	}

	public function get_row( $result = NULL ) {

		if( $result == NULL ) $result = $this->result;
		return $this->row = $this->$result->fetch_row();

	}

	public function set_var( $key, $value ) {

		$value = $this->mysqli->real_escape_string($value);
		$this->mysqli->query("REPLACE INTO variables SET k='$key', v='$value'");
		return $this;

	}

	public function get_var( $key ) {

		return $this->get_cell( "SELECT v FROM variables WHERE k='$key'" );

	}

	public function get_var_( $key ) {

		return ($this->is_serialized( $value = $this->get_var( $key ) )) ? unserialize( $value ) : $value;

	}

	public function possibly_unserialize( $value ) {

		return ($this->is_serialized( $value )) ? unserialize( $value ) : $value;

	}

	public function getVariable_( $name ) {

		$name = $this->mysqli->real_escape_string($name);
		return $this->get_col("SELECT a.name FROM objects as a INNER JOIN objects as b ON a.parent=b.ID WHERE b.name='$name'");

	}

	public function getVariables_( $scalars = true ) {

		$this->query("SELECT a.name as v, a.ID, a.position, b.name as k FROM objects as a INNER JOIN objects as b ON a.parent=b.ID WHERE b.type='Variable' ORDER BY b.name, a.position");
		while( $row = $this->fetch_object() ) $array[strtolower($row->k)][] = strtolower($row->v);
		if( $scalars == true ) foreach( $array as $key => $value ) if( count($array[$key]) == 1 ) $array[$key] = $array[$key][0];
		return $array;

	}

	public function getVariables() {

		$this->query("SELECT * FROM variables");
		while( $row = $this->fetch_object() ) $array[$row->k] = $this->possibly_unserialize( $row->v );
		return $array;

	}

	public function getClasses( $name ) { 

		return $this->get_col("SELECT c.name FROM objects as a INNER JOIN objects as b INNER JOIN objects as c ON a.ID=b.parent AND c.parent=b.ID WHERE b.name='Classes';
");

	}

	public function getClasses_( $ID ) {

		return unserialize($this->get_cell("SELECT classes FROM objects WHERE ID=$ID"));

	}

	public function getFields_( $ID ) {

		return unserialize($this->get_cell("SELECT fields FROM objects WHERE ID=$ID"));

	}

	public function getTypes_() {

		$this->query("SELECT name, fields FROM objects WHERE type='type'");
		while( $row = $this->fetch_object() ) $types[strtolower($row->name)]['fields'] = unserialize($row->fields);
		return $types;

	}

	public function get_col( $query ) {

		$array = array();
		$this->query($query);
		while( $row = $this->fetch_array() ) {

			$array[] = $row[0];

		}

		return $array;

	}

	public function countIndex( $table, $ID, $name ) {

		$name = $this->mysqli->real_escape_string($name);
		return $this->get_cell("SELECT COUNT(*) FROM $table WHERE ID=$ID AND name='$name'");

	}

	public function deleteIndex( $table, $ID ) {

		$position = $this->get_cell("SELECT position FROM $table WHERE ID=$ID");
		$this->query("DELETE FROM $table WHERE ID=$ID");
		$this->query("UPDATE $table SET position=position-1 WHERE position>$position");
		$this->deleteChildren( $table, $ID );
		

	}

	public function deleteChildren( $table, $parent ) {

		$this->query("DELETE FROM style WHERE ID=$parent");
		$children = $this->get_col("SELECT ID FROM $table WHERE parent=$parent");
		$this->query("DELETE FROM $table WHERE parent=$parent");
		foreach( $children as $child ) $this->deleteChildren( $table, $child );

	}

	public function deleteIndices( $table, $IDs ) {

		if( empty($IDs) ) return;
		$IDs = implode(',',$IDs);
		$this->query("DELETE FROM $table WHERE ID in ($IDs)");
		$children = $this->get_col("SELECT ID FROM $table WHERE parent IN ($IDs)");
		$this->deleteIndices( $table, $children );
		//foreach( $IDs as $ID ) $this->deleteIndex( $table, $ID );

	}

	public function renameIndex( $table, $ID, $name ) {

		$name = $this->mysqli->real_escape_string($name);
		return $this->query("UPDATE $table SET name='$name' WHERE ID=$ID");

	}

	public function getPosition( $table, $ID ) {

		return $this->get_cell("SELECT position FROM $table WHERE ID=$ID");

	}
	
	public function getPositionAndParent( $table, $ID ) {

		return $this->query_("SELECT position, parent FROM $table WHERE ID=$ID")->fetch_object();

	}

	public function getParent( $table, $ID ) {

		return $this->get_cell("SELECT parent FROM $table WHERE ID=$ID");

	}

	public function setPosition( $table, $ID, $position ) {

		return $this->query("UPDATE $table SET position=$position WHERE ID=$ID");
	
	}

	public function setPosition_( $table, $ID, $position, $parent ) {

		$prev = $this->getPosition( $table, $ID );
		if( $position > $prev ) $this->query("UPDATE $table SET position=position-1 WHERE parent=$parent AND position <= $position AND position > $prev");
		if( $position < $prev ) $this->query("UPDATE $table SET position=position+1 WHERE parent=$parent AND position >= $position AND position < $prev");
		$this->setPosition( $table, $ID, $position );

	}

	public function fixPositions( $table ) {

		foreach( $this->get_col("SELECT ID FROM $table ORDER BY position") as $index => $value ) $this->setPosition( $table, $value, $index );

	}

	public function fixPositions_( $table ) {

		$parents = $this->getUnique( "objects", "parent" );
		foreach( $parents as $parent ) foreach( $this->get_col("SELECT ID FROM $table WHERE parent=$parent ORDER BY position") as $index => $value ) $this->setPosition( $table, $value, $index );

	}

	public function fetchContents( $ID, $type, $table = 'objects', $order = 'position' ) {

		if( $type == 'reference' ) : $d = $this->getDestination( $ID ); return $this->fetchContents( $d->ID, $d->type, $table, $order ); endif;
		$this->query("SELECT * FROM $table WHERE parent=$ID ORDER BY $order");
		//while( $row = $this->fetch_object() ) $array[] = array( "ID" => $row->ID, "name" => $row->name );
		while( $row = $this->fetch_assoc() ): 
			$row['classes'] = unserialize($row['classes']); 
			$row['fields'] = unserialize($row['fields']); 
			$array[] = $row; 
		endwhile;
		return $array;

	}

	public function getDestination( $ID ) {

		$fields = $this->getFields_($ID);
		if( array_key_exists( 'destination', $fields ) ) return $this->fetchObject($fields['destination']);
		return false;

	}

	public function getUnique( $table, $column ) {

		return $this->get_col( "SELECT DISTINCT $column FROM $table" );

	}

	public function transfer( $table, $ID, $position, $parent ) {

		$prev = $this->getPositionAndParent( $table, $ID );
		$this->query("UPDATE $table SET position=position+1 WHERE position>=$position AND parent=$parent");
		$this->query("UPDATE $table SET parent=$parent, position=$position WHERE ID=$ID");
		$this->query("UPDATE $table SET position=position-1 WHERE parent=$prev->parent AND position>$prev->position");

	}

	public function multitransfer( $table, $IDs, $position, $parent ) {

		$prev = $this->getPositionAndParent( $table, $IDs[0] );
		$count = count($IDs);
		$this->query("UPDATE $table SET position=position+$count WHERE position>=$position AND parent=$parent");
		foreach( $IDs as $i => $ID ) $this->query("UPDATE $table SET parent=$parent, position=$position+$i WHERE ID=$ID");
		$this->query("UPDATE $table SET position=position-$count WHERE parent=$prev->parent AND position>$prev->position");

	}

	public function fetchStyle() { 

		$this->query("SELECT * FROM style");
		while( $object = $this->fetch_object() ) $ID[$object->ID][$object->attribute] = $object->value;
		$this->style = $ID;
		return $this;

	}

	public function renderStyle( $tags = true ) {

		if( $tags == true ) echo "<style>\n";
		foreach( $this->style as $ID => $_ ) $this->renderStyleBlock( $ID, $_ );
		if( $tags == true ) echo "</style>";

	}

	public function renderStyleBlock( $ID, $atts ) {

		echo "#_$ID {\n";
		foreach( $atts as $att => $val ) echo "  $att: $val;\n";
		echo "}\n";

	}

	public function setStyleAttribute( $ID, $attribute, $value ) {

		$attribute = $this->mysqli->real_escape_string($attribute);
		if( $value == 'none' ) return $this->query("DELETE FROM style WHERE ID=$ID AND attribute='$attribute'");
		$value = $this->mysqli->real_escape_string($value);
		echo "REPLACE INTO style SET ID=$ID, attribute='$attribute', value='$value'";
		$this->query("REPLACE INTO style SET ID=$ID, attribute='$attribute', value='$value'");

	}

	public function obComplete( $ID ) {

		$this->setStyleAttribute( $ID, 'background', 'lightgreen' );
		echo $ID;

	}

	public function setType( $ID, $type, $table = 'objects' ) {

		$this->query("UPDATE $table SET type='$type' WHERE ID=$ID");

	}

	public function updateType( $parent, $type ) {

		if( $type == 'Variable' ) $this->set_var( strtolower($this->get_cell("SELECT name FROM objects WHERE ID=$parent")), serialize($this->get_col("SELECT name FROM objects WHERE parent=$parent")) );

	}

	public function classify( $ID, $class, $table = 'objects' ) {

		$classes = $this->getClasses_( $ID ); 
		$classes = ( in_array( $class, $classes ) ) ? array_values(array_diff($classes, array( $class ))) : array_merge($classes, array( $class ));
		$classes = $this->mysqli->real_escape_string(serialize($classes));
		$this->query("UPDATE $table SET classes='$classes' WHERE ID=$ID");

	}

	public function addField( $ID, $field, $table = 'objects' ) {

		$fields = $this->getFields_( $ID ); 
		$fields = ( in_array( $field, $fields ) ) ? array_values(array_diff($fields, array( $field ))) : array_merge($fields, array( $field ));
		$fields = $this->mysqli->real_escape_string(serialize($fields));
		$this->query("UPDATE $table SET fields='$fields' WHERE ID=$ID");

	}

	public function setField( $ID, $key, $value, $table = 'objects' ) {

		$fields = $this->getFields_( $ID ); 
		$fields[$key] = $value;
		$fields = $this->mysqli->real_escape_string(serialize($fields));
		$this->query("UPDATE $table SET fields='$fields' WHERE ID=$ID");

	}

	public function setFields( $ID, $fields, $table = 'objects' ) {

		$fields = $this->mysqli->real_escape_string(serialize($fields));
		$this->query("UPDATE $table SET fields='$fields' WHERE ID=$ID");

	}

	public function incrementTime( $ID, $time, $table = 'objects' ) {

		return $this->query("UPDATE $table SET time=time+$time WHERE ID=$ID");

	}

	public function sumTimeTree( $ID ) {

		return $this->sumChildrenTimes( array( $ID ), $this->get_cell("SELECT time FROM objects WHERE ID=$ID") );

	}

	public function sumChildrenTimes( $IDs, $time = 0 ) {

		if( !is_array($IDs) ) return 'false!';
		if( empty($IDs) ) return $time;
		$IDs = implode(',',$IDs);
		$time += $this->get_cell("SELECT SUM(time) FROM objects WHERE parent IN ($IDs)");
		$children = $this->get_col("SELECT ID FROM objects WHERE parent IN ($IDs)");
		return $this->sumChildrenTimes( $children, $time );

	}

	public function renderTime( $time, $format ) {

		$hours = floor($time/3600);
		if( $hours < 10 ) $hours = '0'.$hours;
		$time -= 3600*$hours;
		$minutes = floor($time/60);
		if( $minutes < 10 ) $minutes = '0'.$minutes;
		$time -= 60*$minutes;
		$seconds = $time;
		if( $seconds < 10 ) $seconds = '0'.$seconds;
		return "$hours:$minutes:$seconds";	

	}

	public function fetchObject( $ID ) {

		return $this->query("SELECT * FROM objects WHERE ID=$ID")->fetch_object();

	}

	public function getChildType( $ID ) {

		$type = 'object';
		$object = $this->fetchObject( $ID );
		$fields = unserialize( $object->fields );
		if( array_key_exists( 'type', $fields ) ) $type = $fields['type'];
		return $type;

	}

	public function is_serialized( $data ) {
		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
		return true;
		if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
			return false;
		switch ( $badions[1] ) {
			case 'a' :
			case 'O' :
			case 's' :
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
					return true;
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
				break;
		}
		return false;
	}

}
