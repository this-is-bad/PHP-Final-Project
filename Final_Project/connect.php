<?php
function doDB() {
	global $mysqli;

	//connect to server and select database; you may need it
	$mysqli = mysqli_connect("localhost", "root", "", "forumdb");
	// $mysqli = mysqli_connect("localhost", "lisabalbach_joness", "CIT19020001", "lisabalbach_Jones");

	//if connection fails, stop script execution
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
}
?>