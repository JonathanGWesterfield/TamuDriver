import mysql.connector

from mysql.connector import errorcode
from FileHander import insertToFile, fileIsEmpty


def dbConnect():
    """
    This function attempts to establish a database connection using the specified parameters
    and throws an exception if needed.
    :return:
    """
    global cnx
    global cursor

    try:
        config = {
            'user': 'jgwesterfield',
            'password': 'Whoop19!',
            'host': 'database.cse.tamu.edu',
            'database': 'jgwesterfield-TamuDriver',
            'raise_on_warnings': True,
        }

        cnx = mysql.connector.connect(**config)
        cursor = cnx.cursor()
        print ("DB Connected")
        return True
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
            cnx.close()
            return False
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
            cnx.close()
            return False
        else:
            print(err)
            return False


def dbInsert(location, inOrOut):
    """
    This functions inserts the data entry that is read in from the Arduino into the MySQL database. The entry contains
    whether the car is entering or exiting as well as the lot number.
    :param inOrOut:
    :param location:
    :return:
    """
    try:
        sql = "INSERT INTO DriverData (location, InOrOut, weekDay, entryTime) VALUES (\""
        sql += str(location) + "\", " + str(inOrOut) + ", dayofweek(now()), now())"

        cursor.execute(sql)
        cnx.commit()
        return False

    except mysql.connector.Error as err:
        print("Something went wrong: {}".format(err))
        return True


def fileToDB():
    """
    This function reads in the data from the data file that is used to store data if a DB connection
    could not be established and reads it in to the database.
    :return:
    """
    # Exit function if file is empty
    if fileIsEmpty():
        return

    dataFile = open("data.txt","r+")
    entries = dataFile.readlines()  # Split the data file into lines
    for entry in entries:
        values = entry.split()  # Divide the current line into multiple values
        entryTime = values[0] + " " + values[1]  # Concatenate date and time
        entryLocation = values[2]
        entryInOrOut = values[3]
        try:
            sql = "INSERT INTO DriverData (location, InOrOut, weekDay, entryTime) VALUES (\""
            sql += str(entryLocation) + "\", " + str(entryInOrOut) + ", dayofweek(\"" + str(entryTime) + "\"), \"" + str(entryTime) + "\")"

            print("Attempting to Insert into the Database: ")
            print(sql)
            print("\n")
            cursor.execute(sql)
            cnx.commit()
        except mysql.connector.Error as err:
            print("Something went wrong: {}".format(err))

    dataFile.truncate()  # Erase file contents once all data has been inserted
    dataFile.close()


def insert(dataMode, location, inOrOut):
    if dataMode == 1:
        dbInsert(location, inOrOut)
    elif dataMode == 0:
        insertToFile(location, inOrOut)



