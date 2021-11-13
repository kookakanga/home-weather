#Code is called by cron every 15 minutes --> Runs from start to finish then end and wait to be called again

#Code to run on the Pi --> This code will periodically take sensor readings from the BME280 and wind data then send to database
#Author kookakanga (Bryce)
#Last updated Nov 2021

#Will be using the BME280 library from pimoroni (Link in log book) as library.

#Configure rasberry pi to enable I2C bus
	# sudo raspi-config nonint do_i2c 0

#To install library to pi
	# sudo pip install pimoroni-bme280 smbus

#To install SQL client on Pi
	#pip install mysqlclient

#crontab -e
    #0,15,30,45 * * * * python ~/storeCron.py

#!/usr/bin/python

print("Taking time observation")

#Import libraries required for sensors
from datetime import datetime
import time
import mysql.connector

#Import BME280 sensors
try:
    from smbus2 import SMBus
except ImportError:
    from smbus import SMBus
from bme280 import BME280
import sys

#File reading libraries 
import csv
import os
PATH = "/tmp/speeds.csv"

#Import libraries required for wind direction
from gpiozero import MCP3008

#Set default values for database
databaseURL = "localhost" #Change this if using a remote web/database server
databaseUsername = "username" #Change this
databasePassword = "myPassword" #Change this
databaseTable = "weather"

#Voltage to degrees values
#Set the directions for the wind sensor
#Ref https://projects.raspberrypi.org/en/projects/build-your-own-weather-station/7 
volDeg = {
    2.7:"N",
    2.9:"NNE",
    2.3:"NE",
    2.5:"ENE",
    1.8:"E",
    2.0:"ESE",
    0.7:"SE",
    0.8:"SSE",
    0.1:"S",
    0.3:"SSW",
    0.4:"W",
    0.6:"WSW",
    1.4:"WNW",
    1.2:"NW",
    2.2: "NE",
    2.8:"NNW",
    0.2:"SW"
}

#Initialise BME sensors
bus = SMBus(1)
bme280 = BME280(i2c_dev=bus)

#initialise wind sensor
adc = MCP3008(channel=0)

#Initial reading of sensors - This is required as the first reading when starting the script is always inaccurate. 
try:
    temperature = bme280.get_temperature()
    pressure = bme280.get_pressure()
    humidity = bme280.get_humidity()
except Exception as e:
    print("Unable to connect to sensors for initial test")

#Get wind readings from temp directory
#Ref https://www.codegrepper.com/code-examples/python/how+to+convert+text+file+to+array+in+python 
#If file does not exist then there has been no wind recorded in the last 15 minutes --> Set the array to 0
#Run a maximum of 5 times to ensure does not get stuck --> If stuck set the array to 0 and sleep for 1 sec incase locked by runCron
complete = 1
if not os.path.exists(PATH):
    windArray = [0]
else:
    while(complete <= 5):
        try:
            f = open(PATH, "r")
            windArray = f.read().splitlines()
            f.close()
            #Delete file to start fresh
            if os.path.exists(PATH):
                os.remove(PATH)
            complete = 6
        except Exception as e:
            print("Unable to open wind file for reading " + str(e) + "Run " + str(complete) + " times")
            complete = complete + 1
            windArray = [0]
            time.sleep(1)

#Convert wind values to an array of float values
#ref https://stackoverflow.com/questions/2424412/what-is-the-easiest-way-to-convert-list-with-str-into-list-with-int
windArray = map(float, windArray)

#Find the largest number, this is gusts 
#Ref https://www.geeksforgeeks.org/python-program-to-find-largest-number-in-a-list/ 
gusts = max(windArray)

#find the average - This is wind speed = sum / length
#Ref https://www.geeksforgeeks.org/find-average-list-python/ 
windSpeed = sum(windArray) / len(windArray)

#Get direction of the wind if there is a windspeed, else set to null as direction is of last known wind.
compl = 0
if(windSpeed > 0.1):
    while(compl <= 5):
        #Get direction of the wind 
        windDir = round(adc.value*3.3, 1)
        #Checks voltage is valid and if so prepare to be stored in database
        if not windDir in volDeg:
            print("Unknown value of " + str(windDir))
            windDir = "Err"
            compl = compl + 1
        else:
            windDir = str(volDeg[windDir])
            compl = 6
else:
    windDir = "Err"

time.sleep(10)

#Get temperature readings 
try:
    temperature = bme280.get_temperature()
    pressure = bme280.get_pressure()
    humidity = bme280.get_humidity()
    nowT = datetime.now()
except Exception as e:
    temperature = None
    pressure = None
    humidity = None
    nowT = datetime.now()
    print("Unable to read sensors at " + str(nowT) + " because " + str(e))

#Connect to database
try:
    cnx = mysql.connector.connect(host=databaseURL, user=databaseUsername, passwd=databasePassword, db=databaseTable)
    cur = cnx.cursor()
    #Write these values to database
    sql = ("INSERT INTO timeObs "
                    " (dateTim, tim, temperature, pressure, humidity, gusts, windspeed, winddirection) "
                    "VALUES (%(currDate)s, %(currTime)s, %(tempFormat)s, %(presFormat)s, %(humFormat)s, %(gustformat)s, %(windspeedformat)s, %(windDirFormat)s)")
    val = {
            'currDate': (nowT.strftime("%Y-%m-%d")),
            'currTime': (nowT.strftime("%H:%M:%S")),
            'tempFormat': temperature,
            'presFormat': pressure,
            'humFormat': humidity,
            'gustformat': gusts,
            'windspeedformat': windSpeed,
            'windDirFormat': windDir
            }
    cur.execute(sql, (val))
    cnx.commit()
    cnx.close()
    print("Write successful at " + nowT.strftime("%Y-%m-%d %H:%M:%S"))
except Exception as e:
    print("Unable to store values in database because " + str(e))

