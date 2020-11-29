#include <mysql/mysql.h>
#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <pcap.h>
#include <map>
#include <unistd.h>
#include <thread>
#include <mutex>

#include "radiotap_header.h"

// adapter : AWUS036NH
// For include mysql header file
// to install libmysqlclient-dev or default-libmysqlclient-dev

using namespace std;

static map<mac_key, data_info> d;
static map<mac_key, data_info>::iterator d_iter;
static MYSQL *conn;                     //database connect
static MYSQL_RES *res;                  //data save
static MYSQL_ROW row;                   //data arr sort
static MYSQL_FIELD *fields;             //like arr
static u_int num_fields;                //fields count
static int ch = 1;
static char *insert_query = (char *)malloc(sizeof(char));
static int query_len=22;

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

int runQuery(){
    sprintf(insert_query + query_len-2, "; ");

    if(mysql_query(conn, insert_query)) {
        errorMsg("MySQL insert_Query empty");
    }
    else{
        printf("\n\n-------query_len : %d, insert query soccess!!!-------\n\n", query_len);
        printf("%s",insert_query);
        printf("\n\n---------------------------------------------------------------\n\n");
    }

    d.clear();
    memset(insert_query, 0, sizeof(char));
    sprintf(insert_query, "insert INTO Log VALUES");
    query_len=22;

    return 0;
}

void setting(char* lecture_room, std::mutex& mutex){
    while(true){
        sleep(30);
        mutex.lock();
        for( d_iter= d.begin(); d_iter != d.end(); d_iter++){

            sprintf(insert_query + query_len, "(NULL,'");
            query_len+=7;

            mac_key mac;
            mac = d_iter->first;
            uint8_t *ptr = reinterpret_cast<uint8_t*>(&mac);

            for(int i=0 ; i<6; i++){
                sprintf(insert_query + query_len +(i*2), "%02x", ptr[i]);
            }
            query_len+=12;

            sprintf(insert_query + query_len,"','%s','%d','%d', CURRENT_TIMESTAMP),\n",lecture_room, (*d_iter).second.pwr, (*d_iter).second.frames);

            query_len=query_len+strlen(lecture_room)+3;

            if ((*d_iter).second.pwr > char(0xf6)) query_len=query_len+26+2;
            else query_len=query_len+26+3;

            if ((*d_iter).second.frames < 10) query_len+=4;
            else if ((*d_iter).second.frames < 100) query_len+=5;
            else query_len+=6;

        }
        runQuery();
        mutex.unlock();
    }
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

	char limit = 0xba; // 0xba = -70
        if(rh->it_antenna_signal < limit || rh->it_length == 13 || rh->it_length == 14) continue;
        std::mutex mutex;

    int rssi = 0;
        if (rh->it_antenna_signal<127)
            rssi= rh->it_antenna_signal -1;
        else
            rssi= rh->it_antenna_signal -255 -1;

        //Data frame
        if(/*ih->type_subtype == QOS_DATA ||*/ ih->type_subtype == PROBE_REQUEST|| ih->type_subtype == NULL_FUNCTION
                || ih->type_subtype == QOS_NULL_FUNCTION || ih->type_subtype == AUTHENTICATION || ih->type_subtype == ACTION){

            mutex.lock();
	    
            auto d_iter = d.find(ih->add2);             //station
            if(d_iter ==d.end()){
                data_info d_info;
                d_info.pwr = rssi;                      //PWR
                d_info.frames=1;                        //count
                d_info.flags= ih->flags;

                d[ih->add2] = d_info;
            }
            else{
                (*d_iter).second.frames ++;
                if((*d_iter).second.pwr < rh->it_antenna_signal) (*d_iter).second.pwr = rssi;
            }
            mutex.unlock();
        }

        if(ih->type_subtype == DATA){

            mutex.lock();
            auto d_iter = d.find(ih->add3);             //station
            if(d_iter ==d.end()){
                data_info d_info;
                d_info.pwr = rssi;                      //PWR
                d_info.frames=1;                        //count
                d_info.flags= ih->flags;

                d[ih->add2] = d_info;
            }
            else{
                (*d_iter).second.frames ++;
                if((*d_iter).second.pwr < rh->it_antenna_signal) (*d_iter).second.pwr = rssi;
            }
            mutex.unlock();
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
    printf("syntax: mysql_insert <interface> server_ip db_id db_pw db_name lecture_room\n");
    printf("sample: mysql_insert wlan0 192.168.0.5 root toor project 831\n");
}

int main(int argc, char* argv[]) {
    if (argc < 7) {                //ch check add
        usage();
        return -1;
    }
    char* dev = argv[1];

    const char *server = argv[2];
    const char *db_id = argv[3];
    const char *db_pw = argv[4];
    const char *database = argv[5];        // DB name
    memset(insert_query, 0, sizeof(char));
    sprintf(insert_query, "insert INTO Log VALUES");

    if (connectDB(server, db_id, db_pw, database)<0) {
        closeDB();
        return 0;
    }

    std::mutex mutex;
    thread t1(savedata,dev);
    thread t2(setting,argv[6],ref(mutex));
    thread t3(ch_hopping,dev);

    t1.join();
    t2.join();
    t3.join();

    closeDB();
    return 0;
}
