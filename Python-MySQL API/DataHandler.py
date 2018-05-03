#!/usr/bin/env python #LINUX?

import serial

from DBHandler import dbConnect, insert, fileToDB

## @file DataHandler.py

dataMode = 0  # 0 = File (offline), 1 = DB (online)
ser = serial.Serial('COM3', 9600)  # Change port for Linux
location = "lot35"
if dbConnect():
    print 'Database connection established. Will add data from file if applicable.'
    dataMode = 1
    fileToDB()
else:
    print 'DB connection could not be established. Will write data to file.'
    dataMode = 0


while True:
    """
    This loop reads from the Arduino serial port and inserts data when necessary.
    It also checks for the parking lot number being updated so that the location
    in the database is updated accurately.
    """
    data = ser.readline()[:-2]  # Truncate trailing newline character
    print data
    # For inOrOut, 1 = entering, 0 = exiting
    if data == "Entered":
        print("Inserting: Entered")
        insert(dataMode, location, 1)
    if data == "Exited":
        print("Inserting: Exited")
        insert(dataMode, location, 0)
    if "lot" in data:
        location = data
        print("Location updated: " + location)
