import mysql.connector

from mysql.connector import errorcode
from FileHander import insertToFile, fileIsEmpty


def dbConnect():
    """
    This function attempts to establish a database connection using the specified parameters
    and throws an exception if needed.
    :return: Boolean - specifies whether the connection was successful or not
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
    This functions inserts the data entry that is read in from the Arduino
    into the MySQL database. The entry contains whether the car is entering
    or exiting as well as the lot number.
    :param inOrOut: Boolean - whether the car entered or exited
    :param location: String - specifies which lot we are recording for
    :return: Boolean - specifies whether or not the insert was successful or not
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
    This function reads in the data from the data file that is used to store
    data if a DB connection could not be established and inserts it into the database.
    :return: void
    """
    # Exit function if file is empty
    if fileIsEmpty():
        return

    dataFile = open("data.txt", "r+")
    # Split the data file into lines
    entries = dataFile.readlines()
    for entry in entries:
        # Divide the current line into multiple values
        values = entry.split()
        # Concatenate date and time
        entryTime = values[0] + " " + values[1]
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
    # Erase file contents once all data has been inserted
    dataFile.seek(0)
    dataFile.truncate()
    dataFile.close()


def insert(dataMode, location, inOrOut):
    """
    This function handles inserting the data read from the Arduino into either
    the file or the database depending on whether a network connection can be established.
    :param dataMode: Boolean/integer - Whether there is a network connection or not
    :param location: String - specifies which lot we are recording for
    :param inOrOut: Boolean - whether the car entered or exited
    :return: void
    """
    if dataMode == 1:
        dbInsert(location, inOrOut)
    elif dataMode == 0:
        insertToFile(location, inOrOut)
