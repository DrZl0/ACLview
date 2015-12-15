<!DOCTYPE html>
<html>
<head>
    <title>Test Table</title>

    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>

    <script>
$(document).ready(function() {
    $('#example').DataTable();
} );
    </script>

</head>
</body>

<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.12.15
 * Time: 10:44
 */

//Параметры коннекта к БД

$srvname="localhost";
$username="root";
$passwd="P@ssw0rd@$";
$dbname="testdb";
$path="/home/andrew/acl";

//Массив с именами файлов таблиц ACL, совпадают с именами таблиц в БД

$acl_tables = array();
$i=0;
if ($handle = opendir($path)) {

    /* Читаем файлы, и нужные записываем в массив */

    while (false !== ($file = readdir($handle))) {

        if((stristr($file, 'acl_')) and (stristr($file, 'rules'))) {
            $i++;
            $acl_tables[$i] = $file;
            //echo "acl_tables[".$i."] = ".$acl_tables[$i]."<br>";
        }

    }

    closedir($handle);
}


//Очистка таблиц БД перед обновлением данных

mysql_connect($srvname, $username, $passwd) or die (mysql_error ());
mysql_select_db($dbname) or die(mysql_error());

mysql_query("TRUNCATE TABLE acl_rules") or die(mysql_error());
//mysql_query("TRUNCATE TABLE acl_users") or die(mysql_error());
mysql_query("TRUNCATE TABLE acl_matrix") or die(mysql_error());

//Заполняем таблицу acl_rules из массива

for ($i = 1; $i <= count($acl_tables); $i++)
{
    $name_rules = substr($acl_tables[$i], 4, strlen($acl_tables[$i]));
    $name_rules = strstr($name_rules, '_rules', true);
    $insert = "INSERT INTO acl_rules (file_rules) VALUE ('$name_rules')";
    //echo $insert."<br>";
    mysql_query($insert) or die(mysql_error());
}

//Заполняем таблицу acl_users и acl_matrix

for ($i = 1; $i <= count($acl_tables); $i++)
{
    $lp = "$path/$acl_tables[$i]";
    $fp = fopen($lp, 'rt');

    if ($fp)
    {
        while (!feof($fp))
        {

            $mytext = fgets($fp, 999);
            list($ip_addr, $username) = explode("#", $mytext);

            if ($ip_addr !="" and $username !="")
            {
                $name_rules2 = substr($acl_tables[$i], 4, strlen($acl_tables[$i]));
                $name_rules2 = strstr($name_rules2, '_rules', true);
                $q_select="SELECT id_rules FROM acl_rules WHERE file_rules = '$name_rules2'";
                $res_q_select = mysql_query($q_select) or die (mysql_error());
                $row = mysql_fetch_array($res_q_select);
                echo $row['id_rules']."<br />";
                $id_rules = $row['id_rules'];

                /*Проверка существования записи в таблице acl_users
                Пишем в табюлицу $ip_addr $username если не существует
                ЗАПОЛНЕНИЕ ТАБЛ acl_users */

                $exists = mysql_num_rows(mysql_query("SELECT id_users FROM acl_users WHERE ip_addr = '$ip_addr'"));
                if ($exists !=1)
                {
                    $insert = "INSERT INTO acl_users (ip_addr, user) VALUE ('$ip_addr','$username')";
                    mysql_query($insert) or die(mysql_error());
                }
                //echo "exists = ".$exists."<br />";

                $q_select="SELECT id_users FROM acl_users WHERE ip_addr = '$ip_addr'";
                $res_q_select = mysql_query($q_select) or die (mysql_error());
                $row = mysql_fetch_array($res_q_select);
                //echo $row['id_users']."<br />";
                $id_users = $row['id_users'];

                $exists = mysql_num_rows(mysql_query("SELECT id_rules,id_users FROM acl_matrix WHERE (id_rules = '$id_rules' AND id_users = '$id_users'"));
                if ($exists !=1 )
                {
                    $insert = "INSERT INTO acl_matrix (id_rules, id_users) VALUE ('$id_rules','$id_users')";
                    mysql_query($insert) or die(mysql_error());
                }

            }

        }


    }

    else echo "Ошибка при открытии файла ".$acl_tables[$i]."<br />";
}

mysql_close();