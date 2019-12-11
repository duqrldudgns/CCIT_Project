<!DOCTYPE html>
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

if ($_POST['mac_addr']){
 $insertQuery = "
    INSERT INTO Member VALUES ( 
	NULL, 
        '".$_POST['student_id']."',
        '".$_POST['name']."',
	'".$_POST['grade']."',
        '".$_POST['department']."'
    )";
        $result = mysqli_query($conn,$insertQuery);

if (!$result) {
echo "Could not successfully run query from DB:" ;
exit;
}
        //데이터가 전달되었으면 새로고침하기
        //if (($_POST['custom_id'] != NULL)&($_POST['custom_name'] != NULL)&($_POST['custom_age'] != NULL)){
        // echo "<meta http-equiv='refresh' content='1;form2.php' />";
        //}

$sql = "
    INSERT INTO Device VALUES (
	NULL,
	'".$_POST['mac_addr']."',
	'".$_POST['device_kind']."',
	'".$_POST['student_id']."'
    )";

$result = mysqli_query($conn,$sql);

if (!$result) {
echo "Could not successfully run query from DB:" ;
exit;
}
}

$sql = "SELECT Device.num, mac_addr,device_kind, name, student_id, grade, department FROM Device LEFT JOIN Member ON Member_student_id = Member.student_id;
";

$result = mysqli_query($conn,$sql);

if (!$result) {
echo "Could not successfully run query from DB:" ;
exit;
}

if (mysqli_num_rows($result) == 0) {
echo "No rows found, nothing to print so am exiting";
exit;
}

echo "<table>";
echo "<tr><td>num</td><td>mac_addr</td><td>device_kind</td><td>name</td><td>student_id</td><td>grade</td><td>department</td></tr>";
while ($row = mysqli_fetch_assoc($result)) {
	echo "<tr><td>{$row['num']}</td><td>{$row['mac_addr']}</td><td>{$row['device_kind']}</td><td>{$row['name']}</td><td>{$row['student_id']}</td><td>{$row['grade']}</td><td>{$row['department']}</td></tr>";
}
echo "</table>";

mysqli_free_result($result);
?>
