#include <mysql/mysql.h>
#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <pcap.h>
#include <unistd.h>
#include <thread>
#include <mutex>

#include "radiotap_header.h"

// adapter : AWUS036NH
// For include mysql header file
// to install libmysqlclient-dev or default-libmysqlclient-dev

using namespace std;

static MYSQL *conn;                     //database connect
static MYSQL_RES *res;                  //data save
static MYSQL_ROW row;                   //data arr sort
static MYSQL_FIELD *fields;             //like arr
static u_int num_fields;                //fields count
static int cnt = 0;
static int ch = 1;

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

    printf("DB connect soccess\n");
    return 0;
}

int runQuery(char pwr,uint8_t mac_addr[6], std::mutex& mutex){
    cnt++;
    char *insert_query = (char *)malloc(sizeof(char) * 70);

    sprintf(insert_query, "insert INTO Log VALUES(NULL,'");

    for(int i=0 ; i<6; i++){
    sprintf(insert_query + 29 +(i*2), "%02x", mac_addr[i]);
    }

    sprintf(insert_query + 41,"','%d', CURRENT_TIMESTAMP);", pwr);

    printf("%s",insert_query);
    printf("\n");

    mutex.lock();
    if(mysql_query(conn, insert_query)) {
        errorMsg("MySQL insert_Query Excute fail");
        return -1;
    }
     mutex.unlock();
     printf("-------cnt : %d, insert query soccess!!!-------\n\n", cnt);
    return 0;
}

void setting(uint8_t subtype, char pwr, uint8_t *station){
    printf("----------------%02x Data catch !!----------------\n", subtype);

    std::mutex mutex;
    runQuery(pwr,station,ref(mutex));
}

int savedata(char* argv){
    char* dev = argv;
    char errbuf[PCAP_ERRBUF_SIZE];
    pcap_t* handle = pcap_open_live(dev, BUFSIZ, 1, 1000, errbuf);
    if (handle == nullptr) {
        fprintf(stderr, "couldn't open device %s: %s\n", dev, errbuf);
        return -1;
    }

    while (true) {
        struct pcap_pkthdr* header;
        const u_char* packet;
        int res = pcap_next_ex(handle, &header, &packet);
        if (res == 0) continue;
        if (res == -1 || res == -2) break;
        struct radiotap_header *rh = (struct radiotap_header *)packet;
        struct ieee80211_header *ih = (struct ieee80211_header *)(packet + rh->it_length);
        //printf("%u bytes captured \n", header->caplen);
        if(rh->it_length == 13 || rh->it_length == 14) continue;

        //Data frame
        if(ih->type_subtype == QOS_DATA){
            switch (ih->flags & 0x03) {
            case 1:{    //flag = T, 0001
                setting(ih->type_subtype, rh->it_antenna_signal, ih->add2);
                break;
            }
            case 2:{    //flag = F, 0010
                setting(ih->type_subtype, rh->it_antenna_signal, ih->add2);
                break;
            }
            }
        }
        else if(ih->type_subtype == PROBE_REQUEST|| ih->type_subtype == NULL_FUNCTION
                || ih->type_subtype == QOS_NULL_FUNCTION || ih->type_subtype == AUTHENTICATION || ih->type_subtype == ACTION){
            setting(ih->type_subtype, rh->it_antenna_signal, ih->add2);
        }


    }
    pcap_close(handle);
    return 0;
}

void ch_hopping(char* dev){
    while (true) {
        char cmd[32] = "iwconfig ";
        strcat(cmd, dev);
        strcat(cmd, " channel ");
        sprintf(cmd + 23, "%d", ch);
        system(cmd);
        sleep(1);
        ch += 6;
        ch %= 13;
        if(!ch) ch = 13;
    }
}

void closeDB(){
    if(res) mysql_free_result(res);
    mysql_close(conn);
}

void usage() {
    printf("syntax: mysql_insert <interface>\n");
    printf("sample: mysql_insert wlan0\n");
}

int main(int argc, char* argv[]) {
    if (argc != 2) {
        usage();
        return -1;
    }
    char* dev = argv[1];

    const char *server = "10.100.111.52";
    const char *db_id = "dbadmin";
    const char *db_pw = "CCITdudgns23!@";
    const char *database = "Project";        // DB name

    if (connectDB(server, db_id, db_pw, database)<0) {
        closeDB();
        return 0;
    }

    thread t1(savedata,dev);
    thread t2(ch_hopping,dev);

    t1.join();
    t2.join();

    closeDB();
    return 0;
}
