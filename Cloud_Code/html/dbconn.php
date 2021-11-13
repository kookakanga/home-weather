<?php
/*Header file
* This file is common to every page in the website
* This page contains database connection
* Cange username and myPassword to match your database */


<!-- Icon -->
echo "<link rel='icon' type='image/x-icon' href='favicon.ico' />";



/*Database connection information*/

$dbServername = "localhost"; /*Change this if using a remote database*/
$dbUsername = "username"; /*Change this*/
$dbPassword = "myPassword"; /*Change this*/
$dbName = "weather";

$conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);


if ($conn->connect_error) {
	echo "<h1>The weatherstation is currently offline</h1>
	<p>You can report this error to weather [at] bryce.id.au</p>";
    die("Connection failed: " . $conn->connect_error);
    
}


/*Connection to Analytics if desired*/


?>

