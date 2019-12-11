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

// $insertQuery = "SELECT ".$_POST['lecture_name']." FROM Subject";
//        $result = mysqli_query($conn,$insertQuery);
        //데이터가 전달되었으면 새로고침하기
        //if (($_POST['custom_id'] != NULL)&($_POST['custom_name'] != NULL)&($_POST['custom_age'] != NULL)){
        // echo "<meta http-equiv='refresh' content='1;form2.php' />";
        //}
?>

<?php
$sql = "SELECT lecture_name, Subject_time.num, year, day, start_time, finish_time FROM Subject_time LEFT JOIN Subject ON Subject_num = Subject.num WHERE Subject_num = ".$_POST['Subject_num']."";

$result = mysqli_query($conn,$sql);

if (!$result) {
echo "Could not successfully run query from DB: ";
exit;
}

if (mysqli_num_rows($result) == 0) {
echo "No rows found, nothing to print so am exiting";
exit;
}


echo "<table>";
echo "<tr><td>start time</td><td>finish time</td></tr>";
while ($row = mysqli_fetch_assoc($result)) {
	echo "<tr><td>{$row['year']}-{$row['day']} {$row['start_time']}</td> <td>{$row['year']}-{$row['day']} {$row['finish_time']}</td></tr>";
}
echo "</table>";
mysqli_free_result($result);
?>


<form action="list_student_id.php" method="post">
      <p>  시작 시간 : <input type="text" name="start_time"></p>
      <p>끝나는 시간 : <input type="text" name="finish_time"></p>
      <p>  찾을 학번 : <input type="text" name="student_id"></p>
      <p>  찾을 mac : <input type="text" name="mac_addr"></p>
      <p><input type="submit"></p>
    </form>

<form action="list_onetime.php" method="post">
<h1> 있기라도한가 체크하기(패킷한번이라도잡혔나) </h1>     
<p>  시작 시간 : <input type="text" name="start_time"></p>
      <p>끝나는 시간 : <input type="text" name="finish_time"></p>
      <p><input type="submit"></p>
    </form>

</body>
</html>
