<!DOCTYPE html>
<script language='javascript'>
//  window.setTimeout('window.location.reload()',3000); //3초마다 리플리쉬 시킨다 1000이 1초가 된다.
 </script>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<style>
table{border:1px solid gray; border-collapse:collapse;}
td{border:1px solid gray;padding:5px;}
</style>
</head>
<body>
<?php


$conn = mysqli_connect("localhost", "dbadmin", "CCITdudgns23!@","Project");
mysqli_query('SET NAMES utf8');
if (!$conn) {
echo "Unable to connect to DB: " . mysqli_error();
exit;
}

$sql = "SELECT Log.num, Log.mac_addr, pwr, time, device_kind, student_id, grade, name,  department FROM Log LEFT JOIN Device ON Log.mac_addr = Device.mac_addr LEFT JOIN Member ON Device.Member_student_id = Member.student_id where time >= '".$_POST['start_time']."' and time <= '".$_POST['finish_time']."'";

$result = mysqli_query($conn,$sql);

if (!$result) {
echo "Could not successfully run query ($sql) from DB: " . mysqli_error();
exit;
}

if (mysqli_num_rows($result) == 0) {
echo "No rows found, nothing to print so am exiting";
exit;
}

// While a row of data exists, put that row in $row as an associative array
// Note: If you're expecting just one row, no need to use a loop
// Note: If you put extract($row); inside the following loop, you'll
// then create $userid, $fullname, and $userstatus

echo "<table>";
echo "<tr><td>num</td><td>mac_addr</td><td>pwr</td><td>time</td><td>device_kind</td><td>student_id</td><td>grade</td><td>name</td><td>department</td></tr>";
while ($row = mysqli_fetch_assoc($result)) {
echo "<tr><td>{$row['num']}</td><td>{$row['mac_addr']}</td><td>{$row['pwr']}</td><td>{$row['time']}</td><td>{$row['device_kind']}</td><td>{$row['student_id']}</td><td>{$row['grade']}</td><<td>{$row['name']}</td><td>{$row['department']}</td></tr>";
}
echo "</table>";
mysqli_free_result($result);

?>
</body>
</html> 
