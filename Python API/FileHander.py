import datetime

def insertToFile(location, inOrOut):
    """
    This function inserts data read from the Arduino into a text file.
    Each data entry is a line in the text file that contains the entry
    time, location, and type.
    :param location:
    :param inOrOut:
    :return: void
    """
    time = datetime.datetime.now().replace(microsecond=0)
    time.strftime('%Y-%m-%d %H:%M:%S')
    dataFile = open("data.txt", "a+")
    dataFile.write("%s %s %s\n" % (str(time), str(location), str(inOrOut)))
    dataFile.close()


def fileIsEmpty():
    """
    This function checks the data file to see if it is empty.
    It is used by the fileToDB function to send file contents to
    the DB if the file is not empty.
    :return: Boolean - specifies whether or not the file has more to read from
    """
    dataFile = open("data.txt")
    # Move to first character in file
    dataFile.seek(0)
    firstChar = dataFile.read(1)
    if not firstChar:
        print ("File is empty!")
        dataFile.close()
        return True
    else:
        print("File not empty!")
        dataFile.close()
        return False

