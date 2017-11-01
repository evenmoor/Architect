<?
/*
	Architect Database Disconnector Version 1.0
	
	Development by: Joshua Moor
	Last Modified: 02/27/13
	
	This file closes a connection to a MYSQL database.
	Requires database_connect to be included before it.
*/

//closes connection to database
mysql_close($database_connection);
?>