all: connect

connect: Connect.c
	gcc -o output $(mysql_config --cflags) Connect.c $(mysql_config --libs)
	
clean:
	rm *.o