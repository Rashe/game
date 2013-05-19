<?php
    /*

    *** Configuration file ***

    Vitaly Shot: 15.5.2011

    */
    
    $config['db']['host'] = 'localhost';
    $config['db']['user'] = 'root';
    $config['db']['pass'] = '';
    $config['db']['name'] = 'creatrio';
    $config['debug'] = false;
    
    $config['site']['name'] = 'CreaTrio';

//    define('HOST', $_SERVER['SERVER_NAME']);                    // Host
//    define('BASE_URL', "http://".$_SERVER['SERVER_NAME']."/");  // Base URL
//    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']."/");         // Base PATH
//    $SITE_NAME = '-= Test =-';                                  // Site name

//    define('DB_HOST', "localhost");
//    define('DB_USER', "root");
//    define('DB_PASS', "");
//    define('DB_NAME', "test");

    include_once($_SERVER['DOCUMENT_ROOT'].$config['db']['name'].'/lib/functions.php');
    include_once($_SERVER['DOCUMENT_ROOT'].$config['db']['name'].'/lib/class.sql.php');
?>