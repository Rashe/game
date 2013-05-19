<!DOCTYPE html>
<html>
<head>
    <title>Newrtonomica | Admin</title>
<!--    <link href="../style/style.css" rel="stylesheet" type="text/css"/>-->
<!--    <script type="text/javascript" src="../scripts/jquery-1.8.3.js"></script>-->
<!--    <script type="text/javascript" src="../scripts/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js"></script>-->
<!--    <script type="text/javascript" src="js/scripts.js"></script>-->
</head>
<body>
<?php
include_once("../lib/ez_sql_core.php");
include_once("../lib/ez_sql_mysql.php");

//                     db_user / db_password / db_name / db_host
$db = new ezSQL_mysql('sql29156','aL2*kZ8*','sql29156','sql2.freemysqlhosting.net');
//$db = new ezSQL_mysql('puwwacom_admin','1Qaz2Wsx','puwwacom_game','box586.bluehost.com');
//$db = new ezSQL_mysql('root','','creatrio','localhost');
$my_tables = $db->get_results("SHOW TABLES",ARRAY_N);
$db->debug();
?>

</body>
</html>





