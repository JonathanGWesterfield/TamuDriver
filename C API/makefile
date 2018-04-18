all: connect

connect: Connect.c
	gcc -o secondConnect $(mysql_config --cflags) Connect.c $(mysql_config --libs) mysqlclient
	
clean:
	rm *.o