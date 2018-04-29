import datetime


def insertToFile(location, inOrOut):
    time = datetime.datetime.now().replace(microsecond=0)
    time.strftime('%Y-%m-%d %H:%M:%S')
    dataFile = open("data.txt","a+")
    dataFile.write("%s %s %s\n" % (str(time), str(location), str(inOrOut)))
    dataFile.close()


def fileIsEmpty():
    dataFile = open("data.txt")
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

