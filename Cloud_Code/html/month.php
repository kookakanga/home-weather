<!DOCTYPE html>
<?php 
    include_once 'dbconn.php';

    echo "
    <body id='backgroundImage'>
    <div class='StdHead'>
        <h1>Home Weather</h1>
        <h2>Is local weather</h2>
    </div>

    <head>
        <link rel='stylesheet' href='style.css'>
        <title>Local Weather Station</title>
    </head>";


    echo "<div class='mainmenu'>
        <ul class='menu'>
                <a href='index.php'>Home</a>
                <a href='about.php'>About</a>
                <a href='timeObs.php'>Today</a>
                <a href='customDate.php'>Select Date</a>
                <a href='rain.php'>Rain</a>
                <a class='active' href='month.php'>Monthly Statistics</a>
        </ul>
    </div>";



    echo "<p>Coming soon</p> 
    
    
    </body>";


    include_once 'footer.php';
?>