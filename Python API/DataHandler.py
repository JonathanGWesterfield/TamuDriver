#!/usr/bin/env python #LINUX?

import serial

from DBHandler import dbConnect, insert, fileToDB

dataMode = 0  # 0 = File (offline), 1 = DB (online)

if dbConnect():
    print 'Database connection established. Will add data from file if applicable.'
    dataMode = 1
    fileToDB()
else:
    print 'DB connection could not be established. Will write data to file.'
    dataMode = 0

ser = serial.Serial('COM3', 9600)  # Change port for Linux

i = 0
location = "lot35"

while True:
    print ser.readline()
    data = ser.readline()[:-2]  # Truncate trailing newline character
    # For inOrOut, 1 = entering, 0 = exiting
    if data == "Entered":
        print("Inserting: Entered")
        insert(dataMode, location, 1)
    if data == "Exited":
        print("Inserting: Exited")
        insert(dataMode, location, 0)

