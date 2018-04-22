#include <stdio.h>
#include <stdlib.h>
#include <mysql.h>
#include <unistd.h>
#include "Connect.h"

/* // Main method for testing
int main(int argc, char const *argv[])
{
	connect();

	for(int i = 0; i < 8; i++)
    {
        for(int j = 0; j < 8; j++)
        {
            sleep(1);
            if(i % 2 == 0)
            {
                if(j % 2 == 0)
                    useInsert(true, true);
                else
                    useInsert(false, true);
            }
            else
            {
                if(j % 2 == 0)
                    useInsert(true, false);
                else
                    useInsert(false, false);
            }

        }
    }

	return EXIT_SUCCESS;
}*/

/**
 * @brief Closes the connection
 * @return Boolean. Returns true if connection has been closed successfully and false if the connection
 * could not be closed for some reason.
 */
bool connect()
{
	conn = mysql_init(NULL);

	if (!conn)
	{
    	fprintf(stderr, "Init failed, out of memory?\n%s[%d]", mysql_error(conn), mysql_errno(conn));
        return false;
	}

    // will try to recursively connect to the database 4 more times to see if it will connect
	if(!(mysql_real_connect(conn, host, user, pass, dbname, port, unix_socket, flag)))
	{
		fprintf(stderr, "\nERROR: %s[%d]\n", mysql_error(conn), mysql_errno(conn));
		recursionCounter += 1;
		if(recursionCounter < 5)
		{
            sleep(1);
			connect();
		}
		else
		{
			fprintf(stderr, "\nERROR: COULD NOT CONNECT TO DATABASE AFTER 5 TRIES\n");
			printf("\n\nERROR: COULD NOT CONNECT TO DATABASE AFTER 5 TRIES\n");
            recursionCounter = 0; // reset the recursion counter
            return false;
		}
	}
    else
    {
        printf("Connection Successful!\n\n");
        recursionCounter = 0; // reset the recursion counter
        connected = true; // specifies if we are connected or not

        return true;
    }
}

/**
 * @brief Closes the connection
 * @return Boolean. Returns true if connection has been closed successfully and false if the connection
 * could not be closed for some reason.
 */
bool closeConnection()
{
	mysql_close(conn); // close the connection
    connected = false; // set connected flag to false
    printf("MySQL Connection closed\n\n");

    if(!conn)
        return true;
    else
        return false;
}

/**
 * @brief Calls the insert statement, allows for use to insert into either lot 35 or
 * lot 54 to provide consistency of naming conventions in the database
 * @param inOrOut - A boolean value. Therefore, 1 is true and 0 is false. 1 Means entering & 0 means exiting.
 * @param lot35or54 - A boolean value. True = lot 35, False = lot 54
 * @return Boolean. Whether or not the function worked
 */
bool useInsert(bool inOrOut, bool lot35or54)
{
    if(lot35or54)
    {
        insert(inOrOut, "lot35");
    }
    else
    {
        insert(inOrOut, "lot54");
    }
}

/**
 * @brief Inserts into the database. It inserts the location, whether or not it car entered or exited,
 * the day of the week (numerical representation), and the datetime
 * @param inOrOut - A boolean value. Therefore, 1 is true and 0 is false. 1 Means entering & 0 means exiting.
 * @param location - Specifies which parking lot we are located at
 * @return Boolean. Will return true if successfully inserted into the database. Returns false if inserting
 * into the database was unsuccessful. Will return false if database connection is broken.
 */
bool insert(bool inOrOut, char* location)
{
    if(connected)
    {
        // printf("Attempting to format SQL String\n");
        char query[500];
        sprintf(query, "INSERT INTO DriverData (location, InOrOut, weekDay, entryTime) VALUES (\"%s\", %d, dayofweek(now()), now())", location, inOrOut);

        // printf("The SQL string: \n%s\n", query);

        if (mysql_query(conn, query))
        {
            fprintf(stderr, "\nERROR: %s[%d]\n", mysql_error(conn), mysql_errno(conn));
            return false;
        }
        else
        {
            printf("Sucessfully Inserted to database: %s\n", query);
        }

        return true;
    }
    printf("\n\nERROR!! CANNOT INSERT!!! NOT CONNECTED TO DATABASE!!!!!!!!!!!!!!!!!!!!!\n");
    return false;

}

/**
	Working local compile command
	gcc -o connect -I /usr/local/Cellar/mysql-connector-c/6.1.11/include Connect.c -L/usr/local/Cellar/mysql-connector-c/6.1.11/lib -lmysqlclient


	Compile Command
	gcc -o connect $(mysql_config --cflags) Connect.c $(mysql_config --libs) mysqlclient
*/