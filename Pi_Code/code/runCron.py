#This python script runs in an infinate loop

#Pressing the rain gauge button causes an interrupt which stores the data in the database

#Half rotation of the wind device causes an interrupt to store speed in /tmp/speeds.csv or as indicated by PATH

#Author kookakanga (Bryce)
#Last updated Nov 2021

#Configure rasberry pi to enable I2C bus
	# sudo raspi-config nonint do_i2c 0

#To install SQL client on Pi
	#pip install mysqlclient

#Crontab -e
    #@reboot * * * * python ~/runcron.py

#!/usr/bin/python

print("Hello World! We're running")

#Import libraries required for system
#datetime for storing the date, time for sleep function
from datetime import datetime
import time
from typing import Counter
import mysql.connector
import sys

#Rain libraries
from gpiozero import Button

#Wind libraries
import math
import os 

#Set default values database
databaseURL = "localhost" #Change this if using a remote web/database server
databaseUsername = "username"
databasePassword = "myPassword"
databaseTable = "weather"

#default values rain
rain_sensor = Button(6)
BUCKET_SIZE = 0.2794

#Button plug
wind_speed_sensor = Button(5)
#Set global counter to 0 for initialisation
wind_count = 0
#Number of seconds between readings
interval = 5
#Radius of anemometer
radius_cm = 9.0
#File path
PATH = "/tmp/speeds.csv"
#Reset the windspeed file on startup
if os.path.exists(PATH):
    os.remove(PATH)

#Constants for calculating wind speed
CM_KM = 100000.0
ADJUSTMENT = 1.18
SEC_HOUR = 3600



#attempt to access and log into database
connection = 0
while(connection == 0):
    try:
        cnx = mysql.connector.connect(host=databaseURL, user=databaseUsername, passwd=databasePassword, db=databaseTable)
        cur = cnx.cursor()
        connection = 1
        print("Database connection success")
        cnx.close()
    except:
        print("No database connection exists, trying again in 5 seconds")
        connection = 0
        time.sleep(5)

#Rain function, if the bucket tips then record in database rain table
def rain_tip():
    nowT = datetime.now()
    try:
        cnx = mysql.connector.connect(host=databaseURL, user=databaseUsername, passwd=databasePassword, db=databaseTable)
        cur = cnx.cursor()
	#Write these values to database
        sql = ("INSERT INTO rain "
                        " (dateTim, tim, rain) "
                        "VALUES (%(currDate)s, %(currTime)s, %(amtFormat)s)")
        val = {
                'currDate': (nowT.strftime("%Y-%m-%d")),
                'currTime': (nowT.strftime("%H:%M:%S")),
                'amtFormat': BUCKET_SIZE
                }
        cur.execute(sql, (val))

        cnx.commit()
        cnx.close()
        print("Success writing to database")
    except Exception as e:
        print("Database writing error " + nowT.strftime("%Y-%m-%d %H:%M:%S") + e)
    return ()

#Function imports none, returns none, uses global variable wind_count to incerment with interrupt
def spin():
    global wind_count
    wind_count = wind_count + 1

#function imports interval (number of seconds between readings), returns the wind speed, converts to KM per hour
def calculate(timesec):
    global wind_count
    try:
        circumference_cm = (2 * math.pi) * radius_cm
        #rotations = wind_count / 2
        rotaltions = wind_count

        dist_cm = (circumference_cm * rotations) / CM_KM
        speed_cm = dist_cm / timesec
        speed = speed_cm * SEC_HOUR
    except Exception as e:
        print("Error calculating speed at " + datetime.now.strftime("%Y-%m-%d %H:%M:%S") + e)
        speed = 0
    #Adjustment is to take into acount any resistance, this value is approximatly 1.8.
    print(speed * ADJUSTMENT)
    return speed * ADJUSTMENT

#function imports wind speed in km per hour then writes to the file for reading by timeobs, returns nothing
#Repeat several times incase running at the same time as storeCron
def fileWrite(speed):
    attempts = 1
    while attempts < 5:
        try:
            f = open(PATH, "a")
            f.write(str(speed) + "\n")
            f.close()
            attempts = 6
        except Exception as e:
            print("File writing error at " + datetime.now.strftime("%Y-%m-%d %H:%M:%S") + "\nNumber of attempts: " + str(attempts) + "\n" + e)
            attempts = attempts + 1
            time.sleep(1)

#Button press interrupts 
rain_sensor.when_pressed = rain_tip
wind_speed_sensor.when_pressed = spin

#Main interval is set to 5 seconds
while True:
    wind_count = 0
    time.sleep(interval)
    fileWrite(calculate(interval))
