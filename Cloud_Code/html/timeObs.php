<?php
    include_once 'dbconn.php';

    /*Shows all records for today*/

?>

<header>
	<title>Today - Local Weather Station</title>
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
                <a  class="active" href="timeObs.php">Today</a>
                <a href="customDate.php">Select Date</a>
                <a href="rain.php">Rain</a>
                <a href="month.php">Monthly Statistics</a>
        </ul>
    </div>	


<?php
    /*Header with today's date*/
    echo "<h3>Observations for " . date("d M Y") . "</h3>";

    /*Day summary*/

$sql = "select AVG(temperature) AS AVGTEMP, AVG(humidity) AS AVGHUM, AVG(pressure) AS AVGPRESS from timeObs WHERE dateTim = CURDATE()";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
    echo "<p>Average Temp: " . round($row['AVGTEMP'], 2) . "°C </p>";
    echo "<p>Average Humidity: " . round($row['AVGHUM'], 2) . "%</p>";
    echo "<p>Average Pressure: " . round($row['AVGPRESS'], 2) . "hPa</td></p>";
    }
}

/*Mins and maxes temperature*/
$sql = "select MIN(temperature) AS MINTEMP, MAX(temperature) AS MAXTEMP from timeObs WHERE dateTim = CURDATE()";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
    echo "<p>Minimum Temp: " . round($row['MINTEMP'], 2) . "°C </p>";
    echo "<p>Maximum Temp: " . round($row['MAXTEMP'], 2) . "°C </p>";
    }
}

/*Max wind speed and gusts*/
$sql = "select MAX(windspeed) AS MAXWINDSPEED, AVG(windspeed) AS AVGWIND, MAX(gusts) AS MAXGUST from timeObs WHERE dateTim = CURDATE()";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result))
    {
    echo "<p>Maximum wind speed: " . round($row['MAXWINDSPEED'], 2) . "km/h </p>";
    echo "<p>Average wind speed: " . round($row['AVGWIND'], 2) . "km/h </p>";
    echo "<p>Maximum gust: " . round($row['MAXGUST'], 2) . "km/h </p>";
    }
}





    /*Diaplay 15 minute interval readings results in a table*/

$sql = "SELECT * FROM timeObs WHERE dateTim = CURDATE() ORDER BY tim DESC;";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
	echo "<table>
<tr>
<th>Time</th>
<th>Temperature  (°C)</th>
<th>Humidity (%)</th>
<th>Pressure  (hPa)</th>
<th>Wind Speed (Km/h)</th>
<th>Wind Direction</th>
<th>Gust speed (Km/h)</th>
</tr>";

while($row = mysqli_fetch_array($result))
{
echo "<tr>";
echo "<td>" . $row['tim'] . "</td>";
echo "<td>" . round($row['temperature'], 1) . "</td>";
echo "<td>" . round($row['humidity'], 1) . "</td>";
echo "<td>" . round($row['pressure'], 1) . "</td>";
echo "<td>" . round($row['windspeed'], 1) . "</td>";
echo "<td>" . $row['winddirection'] . "</td>";
echo "<td>" . round($row['gusts'], 1) . "</td>";
echo "</tr>";
}
echo "</table>";
	
} else {
    echo "<h2>Weather station is currently offline</h2>";
}
$conn->close();
?> 

<br>

</body>
<?php
    include_once 'footer.php';
?>