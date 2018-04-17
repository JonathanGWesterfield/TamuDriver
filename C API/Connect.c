#include <stdio.h>
#include <stdlib.h>
#include <mysql.h>
#include <string.h>

static char *host = "database.cse.tamu.edu";
static char *user = "jgwesterfield";
static char *pass = "Whoop19!";
static char *dbname = "jgwesterfield-TamuDriver";

typedef enum { false, true } bool;

unsigned int port = 3306;
static char *unix_socket = NULL;
unsigned int flag = 0;

MYSQL *conn;
int recursionCounter = 0; // used to count number of times we try to connect after a failed connection

void connect();
void insert(bool inOrOut, char location[]);

int main(int argc, char const *argv[])
{
	connect();

	insert(true, "test");

	return EXIT_SUCCESS;
}

/**
 * @brief Creates the connection to the mysql database using the host, user, pass, and dbname pointers
 */
void connect()
{
	conn = mysql_init(NULL);

	if (!conn)
	{
    	fprintf(stderr, "Init faild, out of memory?\n%s[%d]", mysql_error(conn), mysql_errno(conn));
    	exit(1);
	}

	if(!(mysql_real_connect(conn, host, user, pass, dbname, port, unix_socket, flag)))
	{
		fprintf(stderr, "\nERROR: %s[%d]\n", mysql_error(conn), mysql_errno(conn));
		recursionCounter += 1;
		if(recursionCounter < 5)
		{
			connect();
		}
		else
		{
			fprintf(stderr, "\nERROR: COULD NOT CONNECT TO DATABASE AFTER 5 TRIES\n");
			printf("\n\nERROR: COULD NOT CONNECT TO DATABASE AFTER 5 TRIES\n");
			exit(1);
		}
	}
	printf("Connection Successful!\n\n");
	recursionCounter = 0; // reset the recursion counter

	return;
}

/**
 * @brief Closes the connection if needed
 */
void closeConnection()
{
	mysql_close(conn);
}

/**
 * @brief Inserts into the database. It inserts the location, whether or not it car entered or exited,
 * the day of the week (numerical representation), and the datetime
 * @param inOrOut - A boolean value. Therefore, 1 is true and 0 is false. 1 Means entering & 0 means exiting.
 * @param location - Whether we are at one parking lot or a different one
 */
void insert(bool inOrOut, char location[])
{
	MYSQL_STMT *stmt;
    char query[] = "INSERT INTO WalkerData (Location, InOrOut, WeekDay, DateTime) VALUES (\"";
    strncat(query, location, 1);
    strncat(query, "\", ", 1);
    strncat(query, inOrOut, 1);
    strncat(query, ", dayofweek(now()), now())", 1);

    stmt = mysql_stmt_init(conn);

    if (stmt) 
    {
      	printf("Statement init OK!");
    } 
    else 
    {
      	fprintf(stderr, "Statement init failed: %s[%d]\n", mysql_error(conn), mysql_errno(conn));
    }

    if (stmt) 
    {                   
		if (mysql_stmt_prepare(stmt, query, sizeof(query))) 
		{
			printf("Statement prepare failed: %s[%d]\n", mysql_stmt_error(stmt), mysql_errno(conn));
      	} 
      	else 
      	{
			puts("Statement prepare OK!");
      	}
                        
      	mysql_stmt_close(stmt);
    }
}
/**
	Working compile command
	gcc -o connect -I /usr/local/Cellar/mysql-connector-c/6.1.11/include Connect.c -L/usr/local/Cellar/mysql-connector-c/6.1.11/lib -lmysqlclient   


	Better Working compile option
	gcc -o secondConnect $(mysql_config --cflags) Connect.c $(mysql_config --libs) mysqlclient
*/