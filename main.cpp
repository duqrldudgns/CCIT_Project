#include <mysql/mysql.h>
#include <istream>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
// For include mysql header file
// to install libmysqlclient-dev or default-libmysqlclient-dev

static MYSQL *conn;                     //database connect
static MYSQL_RES *res;                  //data save
static MYSQL_ROW row;                   //data arr sort
static MYSQL_FIELD *fields;             //like arr
static u_int num_fields;                //fields count

void errorMsg(const char *errMsg){
    printf("lately Error Meassage : %s \n", mysql_error(conn));
    printf("this error : %s \n",errMsg);
}

int connectDB(const char* server,const char* user, const char* password, const char* database){
    conn = mysql_init(nullptr);
    if (conn == nullptr) {
        errorMsg("MySQL_init fail");
        return -1;
    }


    if(!mysql_real_connect(conn, server, user, password, database, 3306, nullptr, 0)) {
        errorMsg("MySQL_real_connect fail");
        return -2;
    }
    return 0;
}

int runQuery(const char *query, const char* mac_data){
    if(mysql_query(conn, query)) {
        errorMsg("MySQL Query Excute fail");
        return -1;
    }


    if(!(res = mysql_store_result(conn))){             //쿼리에 대한 결과를 row에 저장
        errorMsg("MySQL res fail\n");
        return -2;
    }


    if(!(num_fields=mysql_num_fields(res))){
        errorMsg("MysQL fields fail\n");
        return -3;
    }
    //select field_name
    num_fields=mysql_num_fields(res);
    fields = mysql_fetch_fields(res);
    printf("fields success : %d \n", num_fields);
    const char *name = "mac_addr";
    printf("%12s \n", name);

    int cnt = 0;
    for(int i=0; i < num_fields;i++) {
        if(!memcmp(fields[i].name, name, 9)) {
        cnt = i;
        break;
        }
    }

    while( (row=mysql_fetch_row(res))!= nullptr){         //one line read
        printf("%12s \n",row[cnt]);
        if(!memcmp(row[cnt], mac_data, 17)){
            printf("catch \n");


            break;
        }
    }

    //show all pick
    //    num_fields=mysql_num_fields(res);
    //    while( (row=mysql_fetch_row(res))!= nullptr){
    //        printf("-");
    //        for (u_int i=0; i< num_fields; i++) {
    //            printf("%12s",row[i]);
    //        }
    //        printf("\n");
    //    }

    return 0;
}

void closeDB(){
    if(res) mysql_free_result(res);
    mysql_close(conn);
}

int main() {
    const char *server = "10.100.111.52";
    const char *db_id = "dbadmin";
    const char *db_pw = "CCITdudgns23!@";
    const char *database = "Project";        // DB name
    const char *select_query = "select * from Device";
    const char *insert_query = "insert";

    const char *mac_data = "cc:cc:cc:cc:cc:cc";
    const char *time_data;

    if (connectDB(server, db_id, db_pw, database)<0) {
        closeDB();
        return 0;
    }

    if (runQuery(select_query, mac_data) < 0) {
        closeDB();
        return 0;
    }

    closeDB();
    return 0;
}
