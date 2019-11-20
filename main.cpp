#include <mysql/mysql.h>
#include <istream>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
// For include mysql header file
// to install libmysqlclient-dev or default-libmysqlclient-dev

MYSQL_RES *res;
MYSQL_ROW row;
MYSQL *conn;

 void errorMsg(char *errMsg)
{
        printf("t%s",errMsg);
        printf("nntError Meassage : %sn", mysql_error(conn));
}

int runQuery(const char *query)
{
        if(mysql_query(conn, query)) {
                printf("MySQL Query Excute failed");
                return -1;
        }

        res = mysql_store_result(conn);
        return 0;
}

int fetchRow(void)
{
        if(res) {
                row = mysql_fetch_row(res);
                if(!row) {
                        printf("MySQL Fetch failed");
                        return -1;
                }
                return 0;
        }
        printf("MySQL Query Result Null");
        return -2;
}

void fetchfieldbyID(int id, char *buffer, int len)
{
        if(row[id]) {
                strncpy(buffer, row[id], len);
        } else {
                printf("MySQL Query Result Null");
        }
}

void fetchfieldbyName(const char *name, char *buffer, int len)
{
        MYSQL_FIELD *fields;
        int num_fields;
        int i;

        if(res) {
                num_fields = mysql_num_fields(res);
                fields = mysql_fetch_fields(res);

                for(i = 0; i < num_fields; i++) {
                        if(!strcmp(fields[i].name, name)) {
                                if(row[i]) {
                                    strncpy(buffer, row[i], len);
                                    return;
                                }
                        }
                }

        } else {
                printf("MySQL Query Result Null");
        }
}

int connectDB(const char* server,const char* user, const char* password, const char* database)
{
        conn = mysql_init(NULL);
        if (conn == NULL) {
                printf("MySQL init fail");
                return -1;
        }
        if(!mysql_real_connect(conn, server,
           user, password, database, 3306, (char *)NULL, 0)) {
                printf("MySQL connect fail");
                return -2;
        }
        return 0;

}

int connectDB2(void)
{
        const char *server = "10.100.111.52";
        const char *user = "root";
        const char *password = "ccit2019";
        const char *database = "custom";

        conn = mysql_init(NULL);
        if (conn == NULL) {
                printf("MySQL init fail");
                return -1;
        }
        if(!mysql_real_connect(conn, server,
           user, password, database, 3306, (char *)NULL, 0)) {
                printf("MySQL connect fail");
                return -2;
        }
        return 0;

}

void closeDB(void)
{
        if (res)
                mysql_free_result(res);
        mysql_close(conn);
}

int main() {
    printf("Start Login process!!\n");

    const char *db_id = "root";
    const char *db_pw = "ccit2019";
    const char *server = "10.100.111.52";
    const char *database = "custom"; // DB name

    const char* id = "dd";
    const char* passwd = "bb";

    int rt = 0;

    if (connectDB(server, db_id, db_pw, database) < 0) {
        return rt;
    }
    const char *Query = "select user_id, user_pw from test";
    if (runQuery(Query) < 0) {
        return rt;
    }

    while((row = mysql_fetch_row(res)) != NULL) {
        printf("%s %s\n", row[0], row[1]);
    }
    closeDB();

    return 0;
}
