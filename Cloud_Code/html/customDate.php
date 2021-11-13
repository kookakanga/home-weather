<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Local Weather Station</title>
    <style>
        FORM {
            font-size: 20px;
            margin-left: 30px;
        }



    </style>
</head>

<body id="backgroundImage">
    <div class="StdHead">
        <h1>Home Weather</h1>
        <h2>Is local weather</h2>
    </div>

<!-- Menu -->

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
    <h3>Select date</h3>
    <p>Display all recordings for the selected date</p>

<FORM ACTION="daySearch.php" METHOD="post">
    <PRE>
    <p>Date: <INPUT TYPE="date" NAME="search"> </p>
    <INPUT TYPE="Submit" VALUE="Search">
    </PRE>
</FORM>

</BODY>

<?php 
    include_once 'footer.php';
?>
</HTML>