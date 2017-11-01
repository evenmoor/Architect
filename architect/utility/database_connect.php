<?
/*
	Architect Database Connector Version 1.0
	
	Development by: Joshua Moor
	Last Modified: 02/27/13
	
	This file provides a connection to a MYSQL database.
	Requires full site configuration to be included before it.
*/

//server hosting the database
$database_host = constant("ARCH_DB_LOCATION");
//username to connect under
$user = constant("ARCH_DB_USERNAME");
//password to connect with
$password = constant("ARCH_DB_PASSWORD");

//opening the connection to the server
$database_connection = mysql_connect($database_host, $user, $password)
	or die("<p>I am unable to connect to the server. Please contact us and let us know.</p>");
	
//selecting the database
$database_name = constant("ARCH_DB_NAME");

//opening connection to the database
mysql_select_db($database_name) 
	or die("<p>I am unable to connect to the database. Please contact us and let us know.</p>");
?>