'''
File: DBInfo.py
Author: XXXXXXXXXXXXX
Date: 3/19/18
Section: 505
E-mail:  XXXXXXXXXXXXX

This file contains the DatabaseInformation class for use in our Project 1
This class holds all attributes of the database necessary for connection, insertion,
display, and checking entries inserted.
'''

import pymysql
import time
# Database information class
class DatabaseInformation:

    # __init__
    # Constructor for DatabaseInformation object
    def __init__(self, hostName, port, userName, password, databaseName, tableName):
        self.m_hostName = "database.cse.tamu.edu"
        self.m_portNumber = 3306
        self.m_userName = "jgwesterfield"
        self.m_password = "Whoop19!"
        self.m_databaseName = "jgwesterfield-TamuDriver"
        self.m_tableName = "DriverData"
        self.m_connection = -1
        self.m_cursor = -1

    # DatabaseConnect
    # connects to the Database and returns either 0 or 1 based on unsuccessful or successful
    # connection and creation of a cursor    
    def DatabaseConnect(self):
        try:  
            self.m_connection = pymysql.connect(self.m_hostName, user = self.m_userName,
                                     port = self.m_portNumber, passwd = self.m_password,
                                     db = self.m_databaseName)
            connectionSuccess = 1
        except:
            connectionSuccess = 0
        try:
            self.m_cursor = self.m_connection.cursor()
            cursorSuccess = 1
        except:
            cursorSuccess = 0
            if(connectionSuccess == 1):
                while(cursorSuccess == 0):
                    cursorSuccess = self.RetryCursor()
        return connectionSuccess, cursorSuccess

    # RetryCursor
    # Attempts to create a cursor to the database that is already connected. This is called if
    # database connection is successful but cursor creation is not
    def RetryCursor(self):
        if(self.m_connection is -1):
            print("you must establish a connection to the database before using this function")
            return
        try:
            self.m_cursor = self.m_connection.cursor()
            cursorSuccess = 1
        except:
            cursorSuccess = 0
            
        return cursorSuccess    

    # InsertNewEntry        
    # Places new entry into the database   
    def InsertNewEntry(self, inOrOut, location):
        #Execute INSERT query and commit changes to database
        try:
            queryString = "INSERT INTO `" + self.m_databaseName + "`.`" + self.m_tableName
            queryString += " (location, inOrOut, weekDay, entryTime) VALUES ("
            queryString += InOrOut + ", \"" + location + "\", dayofweek(now()), now())"
            self.m_cursor.execute(queryString)
            self.m_connection.commit()
        except:
            print("you must establish a connection to a database before entering a new entry")

    # CheckLastInsert
    # Returns and prints out the last entry added to the database for use in debugging
    def CheckLastInsert(self):
        try:
            queryString = "Select * from `" + self.m_databaseName + "`.`" + self.m_tableName + "`;"
            numberRows = self.m_cursor.execute(queryString)
            lastRowQuery = "SELECT * from `"+ self.m_databaseName +"`.`" + self.m_tableName + "` Limit " + str(numberRows-1) + ",1;"
            self.m_cursor.execute(lastRowQuery)
            #check if the last row matches the row inserted
            for entryNumber, entryTime in self.m_cursor.fetchall():
                print (entryNumber, entryTime)
        except:
            print("you must establish a connection to a database before entering a new entry")
            
    # __DisplayTable
    # Displays the entire table, only for debugging use       
    def __DisplayTable(self):
        if(self.m_cursor is -1 or self.m_connection is -1):
            print("you must establish a cursor and connection to use this function")
            return        
        queryString = "SELECT entryNumber, entryTime FROM " + self.m_tableName
        self.m_cursor.execute(queryString)
        for entryNumber, entryTime in self.m_cursor.fetchall():
            print (entryNumber, entryTime)
