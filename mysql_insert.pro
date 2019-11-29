TEMPLATE = app
CONFIG += console c++11
CONFIG -= app_bundle
CONFIG -= qt

SOURCES += \
        main.cpp

HEADERS += \
    radiotap_header.h

LIBS += -lmysqlclient -lpcap -lpthread
