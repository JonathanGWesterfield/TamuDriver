program_LIBRARY_DIRS := $(mysql_config --libs) mysqlclient
CFLAGS += $(mysql_config --cflags)

connect: Connect.c
	gcc -o connect $(mysql_config --cflags) Connect.c $(mysql_config --libs)
	
clean:
	rm *.o