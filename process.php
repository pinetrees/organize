<?php

	require_once('conn.php');
	extract( $_POST );

	switch($action) {

		case 'register':
			$name = $tsqli->mysqli->real_escape_string($name);
			$next = $tsqli->get_cell("SELECT MAX(position)+1 FROM objects WHERE parent=$parent");
			if( ! $next ) $next = 0;
			$type = $tsqli->getChildType( $parent );
			$tsqli->query("INSERT INTO objects SET name='$name', position=$next, parent=$parent, type='$type'");
			//$tsqli->updateType($parent, $type);
			echo json_encode($tsqli->mysqli->insert_id);
			break;
		case 'delete':
			if($tsqli->countIndex("objects", $ID, $name) == 1 ) $tsqli->deleteIndex("objects", $ID);
			//$tsqli->updateType($parent, $type);
			break;
		case 'multidelete':
			$tsqli->deleteIndices("objects", $IDs);
			break;
		case 'rename':
			echo $tsqli->renameIndex("objects", $ID, $name);
			//$tsqli->updateType($parent, $type);
			break;
		case 'order':
			$tsqli->setPosition_("objects", $ID, $position, $parent);
			break;
		case 'transfer':
			$tsqli->transfer("objects", $ID, $position, $parent);
			break;
		case 'multitransfer':
			$tsqli->multitransfer("objects", $IDs, $position, $parent);
			break;
		case 'populate':
			echo json_encode($tsqli->fetchContents($ID, $type));
			break;
		case 'complete':
			$tsqli->obComplete( $ID );
			break;
		case 'style':
			$tsqli->setStyleAttribute( $ID, $attribute, $value );
			break;
		case 'type':
			$tsqli->setType( $ID, $type );
			break;
		case 'class':
			print_r( $_POST );
			$tsqli->classify( $ID, $classification );
			break;
		case 'addfield':
			$tsqli->addField( $ID, $field );
			break;
		case 'setfield':
			print_r( $_POST );
			$tsqli->setField( $ID, $key, $value );
			break;
		case 'time':
			$tsqli->incrementTime( $ID, $time );
			break;
		case 'sumtime':
			echo $tsqli->renderTime( $tsqli->sumTimeTree( $ID ) );
			break;
		case 'scalartime':
			echo $tsqli->get_cell("SELECT time FROM objects WHERE ID=$ID");
			break;

	}


?>
