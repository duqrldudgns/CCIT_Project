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
$conn = mysqli_connect("localhost", "dbadmin", "CCITdudgns23!@","custom");
mysqli_query('SET NAMES utf8');
if (!$conn) {
echo "Unable to connect to DB: " . mysqli_error();
exit;
}

 $insertQuery = "
    INSERT INTO info (
        custom_id,
        custom_name,
        custom_age
    ) VALUES (
        '".$_POST['custom_id']."',
        '".$_POST['custom_name']."',
        '".$_POST['custom_age']."'
    )";
        $result = mysqli_query($conn,$insertQuery);
        //데이터가 전달되었으면 새로고침하기
        //if (($_POST['custom_id'] != NULL)&($_POST['custom_name'] != NULL)&($_POST['custom_age'] != NULL)){
        // echo "<meta http-equiv='refresh' content='1;form2.php' />";
        //}
?>

<?php
$sql = "SELECT * FROM info";

$result = mysqli_query($conn,$sql);

if (!$result) {
echo "Could not successfully run query ($sql) from DB: " . mysqli_error();
exit;
}

if (mysqli_num_rows($result) == 0) {
echo "No rows found, nothing to print so am exiting";
exit;
}

echo "<table>";
echo "<tr><td>id</td><td>name</td><td>age</td></tr>";
while ($row = mysqli_fetch_assoc($result)) {
	echo "<tr><td>{$row['custom_id']}</td><td>{$row['custom_name']}</td><td>{$row['custom_age']}</td></tr>";
}
echo "</table>";
mysqli_free_result($result);
?>