CC= gcc
MYSQLCFLAGS= $(mysql_config --cflags)
MYSQLLIBS= $(mysql_config --libs) -lmysqlclient 

connect: Connect.c
	gcc Connect.c -o connect $(MYSQLCFLAGS) $(MYSQLLIBS)

clean:
	rm -f connect


#program_LIBRARY_DIRS := $(mysql_config --libs) mysqlclient
#CFLAGS += $(mysql_config --cflags)

#connect: Connect.c
#	gcc -o connect $(mysql_config --cflags) Connect.c $(mysql_config --libs)
	
#clean:
#	rm *.oœ