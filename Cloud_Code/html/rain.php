<?php
    include_once 'dbconn.php';
?>

<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Rain - Local Weather Station</title>
</head>

<body id="backgroundImage">
    <div class="StdHead">
        <h1>Home Weather</h1>
        <h2>Is local weather</h2>
    </div>

    <div class="mainmenu">
        <ul class="menu">
                <a href="index.php">Home</a>
                <a href='about.php'>About</a>
                <a href="timeObs.php">Today</a>
                <a href="customDate.php">Select Date</a>
                <a class="active" href="rain.php">Rain</a>
                <a href="month.php">Monthly Statistics</a>
        </ul>
    </div>	

    <div>
<?php
    /*Display today results*/
    echo "<h3>Today</h3>";

    $sql = "SELECT SUM(rain) AS total
        FROM rain 
        WHERE dateTim = CURDATE();";
    $result = $conn->query($sql);


    if ($result->num_rows > 0) {
        while($row = mysqli_fetch_array($result))
        {
            echo "<p>There has been " . round($row['total'], 2) . "mm since midnight </p>";
        }
    } 
    else 
    {
        echo "<h4>No results avalible</h4>";
    }


    /*9am rain calculation*/
    echo "<h3>Since 9am</h3>";
    if((int)date("Hi") <= 900)
    {
        /*If the current time is earlier than 9am today then find since 9am yesterday*/
        $sql = "SELECT SUM(rain) AS total
        FROM rain 
        WHERE dateTim = CURDATE() OR (dateTim = SUBDATE(current_date, 1) AND tim > '9:00');";
        $result = $conn->query($sql);


        if ($result->num_rows > 0) 
        {
            while($row = mysqli_fetch_array($result))
            {
                echo "<p>There has been " . round($row['total'], 2) . "mm since 9am yesterday morning </p>";
            }
        } 
        else 
        {
            echo "<h4>No results avalible</h4>";
        }
    }
    else if ((int)date("Hi") > 900)
    {
        /*If the current time is later than 9am today*/
            $sql = "SELECT SUM(rain) AS total
                    FROM rain 
                    WHERE dateTim = CURDATE() 
                    AND tim > '9:00';";
            $result = $conn->query($sql);


            if ($result->num_rows > 0) {

                while($row = mysqli_fetch_array($result))
                {
                    echo "<p>There has been " . round($row['total'], 2) . "mm since 9am this morning </p>";
                }
        
            } else {
                echo "<h4>No results avalible</h4>";
            }
    }
    else
    {
        echo "Date Time Error";
    }

    /*Select date to view 9am results --> Must exit php section first*/
?>

<h3>Select date</h3>
<p>Display all rain for date where 9am concludes the reading</p>
<p>Eg listing 9 June will show rain for 9am 8 June - 9am 9 June</p>
<FORM ACTION="rainSearch.php" METHOD="post">
    <PRE>
    <p>Date: <INPUT TYPE="date" NAME="search"> </p>
    <INPUT TYPE="Submit" VALUE="Search">
    </PRE>
</FORM>

<?php


        /*Display total rainfall in the last 24 hours*/
    echo "<h3>Last 24 hours</h3>";
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



    /*Yesterday*/
    echo "<h3>Yesterday</h3>";
    $sql = "SELECT SUM(rain) AS total
        FROM rain 
        WHERE dateTim = SUBDATE(current_date, 1);";
    $result = $conn->query($sql);


    if ($result->num_rows > 0) {
        while($row = mysqli_fetch_array($result))
        {
            echo "<p>There was " . round($row['total'], 2) . "mm yesterday (Midnight to midnight)</p>";
        }
    } 
    else 
    {
        echo "<h4>No results avalible</h4>";
    }









        $conn->close();
?>
    </div>
</body>

<?php
    include_once 'footer.php';
?>
