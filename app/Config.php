<?php

namespace app;

class Config
{
    //------------   Настройки  DB -----------------------------------

    const DB_HOST = '127.0.0.1';
    const DB_NAME = 'tm_db';
    const DB_USERNAME = 'mysql';
    const DB_PASSWD = 'mysql';

    //------------ Статус приложения ----------------------------------
    const PROJECT_DEVELOPMENT = 'dev';
    const PROJECT_RELEASE = 'rel';
    public static $PROJECT_STATUS = self::PROJECT_RELEASE;
}