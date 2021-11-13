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
                <a class="active" href="customDate.php">Select Date</a>
                <a href="rain.php">Rain</a>
                <a href="month.php">Monthly Statistics</a>
        </ul>
    </div>	
<br>

<!-- Back button -->
    <div>
        <a href="customDate.php"> < Back</a>
    </div>



<?php

$search = $_POST['search'];

include_once 'dbconn.php';


/*Checks if any results*/

$sql = "select * from timeObs where dateTim = '$search'";
$result = $conn->query($sql);

if ($result->num_rows <= 0) {
    echo "<h2>No data for $search</h2>";
    die();  
} 

/*Date and time in proper format
DATE_FORMAT(dateTim, '%d %M %Y') AS dateTim*/

$sql = "select DATE_FORMAT(dateTim, '%W %d %M %Y') AS dateTim from timeObs where dateTim = '$search' LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
        echo "<h3>Observations for " . $row['dateTim'] . "</h3>";
    }
}


/*Summary section for the day

Averages*/

$sql = "select AVG(temperature) AS AVGTEMP, AVG(humidity) AS AVGHUM, AVG(pressure) AS AVGPRESS from timeObs where dateTim = '$search'";
$result = $conn->query($sql);
/*If there is observations for selected day*/
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {   
        /*Echo or print every average, followed by unit of measurement*/
        echo "<p>Average Temp: " . round($row['AVGTEMP'], 2) . "°C </p>";
        echo "<p>Average Humidity: " . round($row['AVGHUM'], 2) . "%</p>";
        echo "<p>Average Pressure: " . round($row['AVGPRESS'], 2) . "hPa</td></p>";
    }
}



/*Mins and maxes*/
$sql = "select MIN(temperature) AS MINTEMP, MAX(temperature) AS MAXTEMP 
        from timeObs where dateTim = '$search'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
    echo "<p>Minimum Temp: " . round($row['MINTEMP'], 1) . "°C </p>";
    echo "<p>Maximum Temp: " . round($row['MAXTEMP'], 1) . "°C </p>";
    }
}

/*Wind*/
/*Max wind speed and gusts*/
$sql = "select MAX(windspeed) AS MAXWINDSPEED, AVG(windspeed) AS AVGWIND, MAX(gusts) AS MAXGUST from timeObs WHERE dateTim = '$search'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
    echo "<p>Maximum wind speed: " . round($row['MAXWINDSPEED'], 2) . "km/h </p>";
    echo "<p>Average wind speed: " . round($row['AVGWIND'], 2) . "km/h </p>";
    echo "<p>Maximum gust: " . round($row['MAXGUST'], 2) . "km/h </p>";
    }
}



/*Rainfall - Displays the sum rainfall for the selected date*/
$sql = "SELECT SUM(rain) AS total
    FROM rain 
    WHERE dateTim = '$search';";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
        echo "<p>There was " . round($row['total'], 2) . "mm of rain (Midnight to midnight)</p>";
    }
} 
else 
{
    echo "<h4>No results avalible</h4>";
}

/*Displays every 15 minute results*/
$sql = "select * from timeObs where dateTim = '$search'";
$result = $conn->query($sql);

/*If there is observations for the day then create the heading (th = table heading)*/
if ($result->num_rows > 0) {

	echo "<table class='data'>
    <tr>
        <th class='data'>Time</th>
        <th class='data'>Temperature  (°C)</th>
        <th class='data'>Humidity (%)</th>
        <th class='data'>Pressure  (hPa)</th>
        <th>Wind Speed (Km/h)</th>
        <th>Wind Direction</th>
        <th>Gust speed (Km/h)</th>
    </tr>";

while($row = mysqli_fetch_array($result))
{
    /*While there is more observations create a new row*/
    echo "<tr>";
        /*If there is only partial observations then there may be empty cells in the table*/
        echo "<td class='data'>" . $row['tim'] . "</td>";
        echo "<td class='data'>" . round($row['temperature'], 1) . "</td>";
        echo "<td class='data'>" . round($row['humidity'], 1) . "</td>";
        echo "<td class='data'>" . round($row['pressure'], 1) . "</td>";
        echo "<td>" . round($row['windspeed'], 1) . "</td>";
        echo "<td>" . $row['winddirection'] . "</td>";
        echo "<td>" . round($row['gusts'], 1) . "</td>";    
    echo "</tr>";
}
/*Once finished then close the table*/
echo "</table>";
} 

$conn->close();


    include_once 'footer.php';
?>

</html>