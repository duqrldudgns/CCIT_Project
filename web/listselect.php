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
$conn = mysqli_connect("116.35.231.186", "duqrljyh", "CCITgnsl12!@","Project");
mysqli_query('SET NAMES utf8');
if (!$conn) {
echo "Unable to connect to DB: " . mysqli_error();
exit;
}

$sql = "select lecture_name from Subject;";

$result = mysqli_query($conn,$sql);

if (!$result) {
echo "Could not successfully run query ($sql) from DB: " . mysqli_error();
exit;
}

if (mysqli_num_rows($result) == 0) {
echo "No rows found, nothing to print so am exiting";
exit;
}

?>

<form action="listselect.php" method="POST">
          <h1>과목을 선택하세요</h1>
            <select name="Subject_num">
                <option value="1">hacking</option>
                <option value="2">gilgil</option>
            </select>
            <input type="submit">
         </form>
       
<?php
while ($row = mysqli_fetch_assoc($result)) {
echo "{$row['lecture_name']}";
}
mysqli_free_result($result);
?>
</body>
</html>
