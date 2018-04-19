#ifndef CONNECT_H
#define CONNECT_H

// define a boolean value since C does not have a built in boolean
typedef int bool;
#define true 1
#define false 0

static char *host = "database.cse.tamu.edu";
static char *user = "jgwesterfield";
static char *pass = "Whoop19!";
static char *dbname = "jgwesterfield-TamuDriver";
static unsigned int port = 3306;
static char *unix_socket = NULL;
static unsigned int flag = 0;
bool connected = false;

MYSQL *conn;
int recursionCounter = 0; // used to count number of times we try to connect after a failed connection

/**
 * @brief MUST BE CALLED FIRST! Will open a connection to the database using the host, user,
 * pass, dbname, port, unix_socket, and flag variables.
 *
 * If a connection is not made immediately, the function will try to connect 5 more times.
 * @return Boolean true or false. Returns true if a connection is made successfully.
 * Will return false if a connection isn't made after trying to reconnect 5 times.
 */
bool connect();

/**
 * @brief Closes the connection
 * @return Boolean. Returns true if connection has been closed successfully and false if the connection
 * could not be closed for some reason.
 */
bool closeConnection();

/**
 * @brief Inserts into the database. It inserts the location, whether or not it car entered or exited,
 * the day of the week (numerical representation), and the datetime
 * @param inOrOut - A boolean value. Therefore, 1 is true and 0 is false. 1 Means entering & 0 means exiting.
 * @param location - Specifies which parking lot we are located at
 * @return Boolean. Will return true if successfully inserted into the database. Returns false if inserting
 * into the database was unsuccessful. Will return false if database connection is broken.
 */
bool insert(bool inOrOut, char* location);

/*
 * Better Working compile option
	gcc -o connect $(mysql_config --cflags) Connect.c $(mysql_config --libs) mysqlclient
 */

#endif