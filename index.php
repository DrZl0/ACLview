<!DOCTYPE html>
<html>
<head>
    <title>DMZ Control</title>

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

</body>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.12.15
 * Time: 10:37
 */
	//Параметры коннекта к БД

	$srvname="localhost";
	$username="root";
	$passwd="P@ssw0rd@$";
	$dbname="testdb";

	// Соединиться с сервером БД

	mysql_connect($srvname, $username, $passwd) or die (mysql_error ());

	// Выбрать БД

	mysql_select_db($dbname) or die(mysql_error());

	// SQL-запрос

	$strSQL = "SELECT ip_addr,user,file_rules FROM acl_view";

	// Выполнить запрос (набор данных $rs содержит результат)

	$rs = mysql_query($strSQL);
    print_r($rs);

	echo "<table id='example' class='display' cellspacing='0' width='100$'>";
	echo "<caption>Контроль доступа в DMZ-зону</caption>";

	echo "<thead>";
		echo "<tr>";
			echo "<th>IP address</th>";
			echo "<th>User Name</th>";
			echo "<th>ACL Name</th>";
		echo "</tr>";
	echo "</thead>";

	echo "<tfoot>";
		echo "<tr>";
			echo "<th>IP address</th>";
			echo "<th>User Name</th>";
			echo "<th>ACL Name</th>";
		echo "</tr>";
	echo "</tfoot>";

	echo "<tbody>";

	while($row = mysql_fetch_array($rs))
    {

        // Записать значение столбца value (который является теперь массивом $row)

        echo "<tr>";
        echo "<td>".$row['ip_addr'] ."</td>";
        echo "<td>".$row['user'] ."</td>";
        echo "<td>".$row['file_rules'] ."</td>";
        echo "</tr>";

    }
	echo "</tbody>";
	// Закрыть соединение с БД

	mysql_close();

?>