<?php
    include_once 'dbconn.php';

echo "
<!DOCTYPE html>
<head>
    <link rel='stylesheet' href='style.css'>
    <title>Local Weather Station</title>
</head>";

/*SQL query finds most recent reading and saves to variables to customise home page
DATE_FORMAT(dateTim, '%d %M %Y') AS dateTim, to have better format for date*/
$sql = "SELECT DATE_FORMAT(dateTim, '%d %M %Y') AS dateTim, DATE_FORMAT(tim, '%H:%i') AS tim, temperature, humidity, pressure, windspeed, winddirection FROM timeObs WHERE dateTim = CURDATE() ORDER BY tim DESC LIMIT 1;";
$result = $conn->query($sql);

/*If the number of results is more than 0*/
if ($result->num_rows > 0) {
    /*Loop for every returned result, still required even for LIMIT 1 SQL as data is stored in array*/
    while($row = mysqli_fetch_array($result))
    {
        /*Change data from array to integer*/
        $rowtemperature = $row['temperature'];
        $rowhumidity = $row['humidity'];
        $rowpressure = $row['pressure'];
        $rowdateTim = $row['dateTim'];
        $rowtim = $row['tim'];
        $rowWindSpeed = $row['windspeed'];
        $rowWindDirection = $row['winddirection'];
    }
    $result = "Success";
/*If there is no results*/
} else {
    $result = "Failed";
}

$intTemp = (int) $rowtemperature;

/*Calculates the background image - Change image based off current temp*/
if ($intTemp < 10) {
    echo "<body style='background-color:#B6D0E2'>";
}
else if ($intTemp < 20) {
    echo "<body style='background-color:#C1E1C1'>";
}
else if ($intTemp < 30) {
    echo "<body style='background-color:#ffcc99'>";
}
else if ($result == "Failed") {
    echo "<body id='backgroundImage'>";
}
else {
    echo "<body style='background-color:#f08080'>";
}

/*Heading Table with 2 images and title*/
echo "
    <div class='StdHead'>
        <table class='headerTable'>
            <tr>
                <td class='nb'>";
                imageCalc($result, $intTemp);
                echo "</td>
                <td class='nb'>
                    <h1>Home Weather</h1>
                    <h2>Is local weather</h2>
                </td>
                <td class='nb'>";
                imageCalc($result, $intTemp);
                echo "</td>
            </tr>
        </table>
    </div>";

/*Echo main menu*/

    echo "<div class='mainmenu'>
        <ul class='menu'>
                <a class='active' href='index.php'>Home</a>
                <a href='about.php'>About</a>
                <a href='timeObs.php'>Today</a>
                <a href='customDate.php'>Select Date</a>
                <a href='rain.php'>Rain</a>
                <a href='month.php'>Monthly Statistics</a>
        </ul>
    </div>	";
    echo "<div class=currDisplay id=currentObs>";

    /*If the weatherstation is offline then load nothing using die function*/
    if ($result == "Failed") {
        echo $result;
        echo "<h2 style='color:red'>***Weather station is currently offline***</h2>";
        die();                
    }


        /*Display most recent observation*/
        echo "<h3>Current Observations</h3>";
        echo "<p>Last reading at " . $rowtim . " on the " . $rowdateTim . "</p>";
        echo "<p>It is currently " . round($rowtemperature, 1) . "°C </p>";
        echo "<p>There is " . round($rowhumidity, 1) . "% humidity </p>";
        echo "<p>Barometric pressure: " . round($rowpressure, 1) . "hPa</p>";
        if($rowWindSpeed > 0)
        {
            echo "<p>Windspeed of " . $rowWindSpeed . "km/h from " . $rowWindDirection . " direction</p>";
        }

        /*Display minimum temperature from the last 24 hours*/
        $sql = "SELECT SUM(rain) AS total_rain FROM rain WHERE dateTim = CURDATE();";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = mysqli_fetch_array($result))
            {
                echo "<p>There has been " . round($row['total_rain'], 1) . "mm of rain since midnight </p>";
            }
        }


        /******************************************************************************** */

        /*Results from the last 24 hours*/
        echo "<br><h3>Last 24 hours</h3><br>";


        /*Display minimum temperature from the last 24 hours*/
        $sql = "SELECT DATE_FORMAT(dateTim, '%d %M %Y') AS dateTim, tim, temperature 
            FROM timeObs 
            WHERE temperature  = (SELECT MIN(temperature) 
                FROM timeObs 
                WHERE dateTim = CURDATE() 
                OR (dateTim = SUBDATE(current_date, 1) 
                AND tim >= CURRENT_TIME())
            )
            AND (dateTim = CURDATE() 
            OR (dateTim = SUBDATE(current_date, 1) 
            AND tim >= CURRENT_TIME()));";
        $result = $conn->query($sql);


        if ($result->num_rows > 0) {
	        

            while($row = mysqli_fetch_array($result))
            {
                echo "<p>A minimum of " . round($row['temperature'], 1) . "°C recorded at " . $row['tim'] . " on " . $row['dateTim'] . "</p>";
            }
	
        } else {
            echo "<h2>Weather station is currently offline</h2>";
        }

        /*Display maximum temperature from the last 24 hours*/
        $sql = "SELECT DATE_FORMAT(dateTim, '%d %M %Y') AS dateTim, tim, temperature 
            FROM timeObs 
            WHERE temperature  = (SELECT MAX(temperature) 
                FROM timeObs 
                WHERE dateTim = CURDATE() 
                OR (dateTim = SUBDATE(current_date, 1) 
                AND tim >= CURRENT_TIME())
            )
            AND (dateTim = CURDATE() 
            OR (dateTim = SUBDATE(current_date, 1) 
            AND tim >= CURRENT_TIME()));";
        $result = $conn->query($sql);


        if ($result->num_rows > 0) {
	        

            while($row = mysqli_fetch_array($result))
            {
                echo "<p>A maximum of " . round($row['temperature'], 1) . "°C recorded at " . $row['tim'] . " on " . $row['dateTim'] . "</p>";
            }
	
        } else {
            echo "<h2>Weather station is currently offline</h2>";
        }


        /*Average temperature 24 <hours></hours*/
        $sql = "SELECT AVG(temperature) AS avg_temp             
            FROM timeObs 
            WHERE (dateTim = CURDATE() 
            OR (dateTim = SUBDATE(current_date, 1) 
            AND tim >= CURRENT_TIME()));";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = mysqli_fetch_array($result))
            {
                echo "<p>Average temperature of " . round($row['avg_temp'], 1) . "°C</p>";
            }
        }

        /*Average wind 24 <hours></hours*/
        $sql = "SELECT AVG(windspeed) AS avg_wind             
            FROM timeObs 
            WHERE (dateTim = CURDATE() 
            OR (dateTim = SUBDATE(current_date, 1) 
            AND tim >= CURRENT_TIME()));";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = mysqli_fetch_array($result))
            {
                echo "<p>Average windspeed of " . round($row['avg_wind'], 1) . "km/h</p>";
            }
        }

        /*Total rain last 24 <hours></hours>*/
        $sql = "SELECT SUM(rain) AS total
            FROM rain 
            WHERE dateTim = CURDATE() 
            OR (dateTim = SUBDATE(current_date, 1) 
            AND tim >= CURRENT_TIME());";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
	        while($row = mysqli_fetch_array($result))
            {
                echo "<p>There has been " . round($row['total'], 2) . "mm in the last 24 hours </p>";
            }
        } else {
            echo "<h4>No results avalible</h4>";
        }



        /*Close down and footer*/
        $conn->close();


echo "<br>
</body>";

/*Standard footer*/
include_once 'footer.php';


function imageCalc($inResult, $intTemp) {
    /*Function calculates images on each side of title based on current temperature
    Input: inResult (String), inTemp (Integer)
    Output: None (HTML print to screen)*/
    if ($inResult == "Failed") {
        /*Offline, do nothing*/
    }
    else if ($intTemp < 10) {
        echo "<img src='cold.png'>";
    }
    else if ($intTemp < 20)
    {   
        echo "<img src='cloud.png'>";
    }
    else if ($intTemp < 30) {
        echo "<img src='sun.png'>";
    }
    else {
        echo "<img src='verhot.png'>";
    }
}

?>
