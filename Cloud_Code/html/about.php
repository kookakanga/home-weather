<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Local Weather Station</title>
</head>

<body id="backgroundImage">
    <div class="StdHead">
        <h1>Home Weather</h1>
        <h2>Is local weather</h2>
    </div>

    <?php header("Location: https://bryce.id.au/weather.html"); ?>

<!-- Menu -->

    <div class="mainmenu">
        <ul class="menu">
                <a href="index.php">Home</a>
                <a class="active" href="about.php">About</a>
                <a href="timeObs.php">Today</a>
                <a href="yesterday.php">Yesterday</a>
                <a href="rain.php">Rain</a>
                <a href="other.php">Coming soon</a>
        </ul>
    </div>	

    <p>If you are not automatically redirected click <a href="https://bryce.id.au/weather.html">here </a></p>


</body>

<?php
    include_once 'footer.php';
?>
