<header>
	<title>Results - Local Weather Station</title>
	<link rel="stylesheet" href="style.css">
    <style>
        table, th, td {
        border: 2px solid black;
        border-collapse: collapse;
        }       
    </style>
</header>

<body id="backgroundImage">
    <div class="StdHead">
      <h1>Home Weather</h1>
      <h2>Is local weather</h2>
    </div>

    <div class="mainmenu">
        <ul class="menu">
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="timeObs.php">Today</a>
                <a href="customDate.php">Select Date</a>
                <a class="active" href="rain.php">Rain</a>
                <a href="month.php">Monthly Statistics</a>
        </ul>
    </div>	
<br>

<!-- Back button -->
    <div>
        <a href="rain.php"> < Back</a>
    </div>



<?php

$search = $_POST['search'];

include_once 'dbconn.php';

/*Date and time in proper format
DATE_FORMAT(dateTim, '%d %M %Y') AS dateTim*/

$sql = "select DATE_FORMAT(dateTim, '%W %d %M %Y') AS dateTim from timeObs where dateTim = '$search' LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
        echo "<h3>Observations until 9am on " . $row['dateTim'] . "</h3>";
    }
}

/*Rainfall <readings></readings>*/
$sql = "select SUM(rain) AS tot from rain 
    WHERE dateTim = '$search' 
    AND tim < 900
    OR dateTim = SUBDATE('$search', 1)
    AND tim > 900;";

if ($result->num_rows <= 0) {
	echo "No records error";
}
else if($row['tot'] == NULL) {
	echo "No rain recorded";
}
else {
    while($row = mysqli_fetch_array($result))
    {
        echo "<p>Redorded: " . $row['tot'] . "mm</h3>";
    }
}


echo "<p>To view based on calendar date use select date from main menu</p>";

$conn->close();


    include_once 'footer.php';
?>

</html>
