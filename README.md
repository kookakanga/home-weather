# Home-weather
Ever wanted to know weather observations at your location? Now you can.

Home weather is based on a tutorial from https://projects.raspberrypi.org/en/projects/build-your-own-weather-station/ with a twist

There are 2 components
- Raspberry Pi to take observations
- Web server with database to store observations

To assemble the raspberry Pi a tutorial is available <a href="https://projects.raspberrypi.org/en/projects/build-your-own-weather-station/">here</a>
Some understanding of electronics may be required.

load the SQL_Code files into your database of choice

To create the web server install php and mysqli
This was tested using Apache2 as the web server
Change the dbconn.php file with your database credentials then it should work.

Diffent configurations
    - All 3 services on a Raspberry Pi
    - Raspberry Pi to take observations then send to seperate Database/Web server

An example is at https://weather.bryce.id.au

Any issues open a fork.
