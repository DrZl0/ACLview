<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.12.15
 * Time: 10:29
 */

//Параметры коннекта к БД

$srvname="localhost";
$username="root";
$passwd="P@ssw0rd@$";
$dbname="testdb";

//Массив с именами файлов таблиц ACL, совпадают с именами таблиц в БД

$acl_tables = array();
$i=0;
if ($handle = opendir('/home/andrew/acl')) {

    /* Именно этот способ чтения элементов каталога является правильным. */
    while (false !== ($file = readdir($handle))) {

        if((stristr($file, 'acl_')) and (stristr($file, 'rules'))) {
            $i++;
            $acl_tables[$i] = $file;
            //echo "acl_tables[".$i."] = ".$acl_tables[$i]."<br>";
        }

    }

    closedir($handle);
}

//$acl_tables = array(1 => 'acl_esx_rules','acl_exim_rules','acl_ftp_rules','acl_loader_rules','acl_motorsich_6116_rules','acl_motorsich_oracle_rules','acl_motorsich_rdp_rules','acl_pib_rules','acl_printera_rules','acl_prohod_rules','acl_sharepoint_user_rules','acl_spfserv_rules','acl_squid_rules','acl_tender_rules','acl_terminet_rules');


//Очистка таблиц БД перед обновлением данных

mysql_connect($srvname, $username, $passwd) or die (mysql_error ());
mysql_select_db($dbname) or die(mysql_error());
$trunk = "TRUNCATE TABLE acl_rules";
mysql_query($trunk) or die(mysql_error());

  for ($i = 1; $i <= count($acl_tables); $i++)
  {
      $name_rules = strstr($acl_tables[$i], 'acl_');
      $name_rules = strstr($name_rules, '_rules', true);
      $insert = "INSERT INTO acl_rules (name_rules) VALUE $name_rules";
      mysql_query($insert) or die(mysql_error());
  }


mysql_close();

//Подключение и запись данных из файлов в БД

mysql_connect($srvname, $username, $passwd) or die (mysql_error ());
mysql_select_db($dbname) or die(mysql_error());


  for ($i = 1; $i <= count($acl_tables); $i++)
  {
      $lp = "/home/andrew/acl/$acl_tables[$i]";
      $fp = fopen($lp, 'rt');

      if ($fp)
      {
          while (!feof($fp))
          {

              $mytext = fgets($fp, 999);
              list($ip_addr, $username) = explode("#", $mytext);
              echo "acl_tables[".$i."] = ".$acl_tables[$i]." ".$ip_addr."<br>";
              if ($ip_addr !="" and $username !="")
              {
                  $strSQL = "INSERT INTO $acl_tables[$i](ip_addr,username) VALUES('$ip_addr','$username')";
                  mysql_query($strSQL) or die (mysql_error());
              }
          }

          echo "Table $acl_tables[$i] updated</br />";

      }

      else echo "Ошибка при открытии файла ".$acl_tables[$i]."<br />";
  }

mysql_close();
?>