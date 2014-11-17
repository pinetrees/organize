<?php
	$tsqli = new tsqli("localhost", "root", "root", "laravel_organize");
	echo json_encode($tsqli->fetchContents(1, 'object'));
?>
