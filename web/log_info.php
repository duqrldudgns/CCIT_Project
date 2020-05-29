<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>ccit-young-project</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">

</head>

<body id="page-top">

<?php
$conn = mysqli_connect("116.35.231.186", "duqrljyh", "CCITgnsl12!@","Project");
mysqli_query('SET NAMES utf8');
if (!$conn) {
echo "Unable to connect to DB: " . mysqli_error();
exit;
}
?>

  <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <a class="navbar-brand mr-1" href="index.php"> SAC(Section Attendance Check)</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
      <i class="fas fa-bars"></i>
    </button>

  </nav>

  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>대시보드</span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-fw fa-folder"></i>
          <span>회원정보입력 및 확인</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <h6 class="dropdown-header">Login Screens:</h6>
          <a class="dropdown-item" href="register.html">정보입력</a>
          <a class="dropdown-item" href="login_info.php">정보확인</a>
        </div>
      </li>
      <li class="nav-item dropdown">
	<a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-fw fa-table"></i>
          <span>로그보기</span>
	</a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <h6 class="dropdown-header">show Log :</h6>
          <a class="dropdown-item" href="log_info.php">자세한 정보</a>
          <a class="dropdown-item" href="subject_select.php">과목 선택</a>
          <a class="dropdown-item" href="log_one_time.php">원하는 시간대에 존재여부</a>
        </div>
      </li>
    </ul>






    <div id="content-wrapper">

      <div class="container-fluid">

        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Dashboard</a>
          </li>
          <li class="breadcrumb-item active">Tables</li>
        </ol>

        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-table"></i>
            Log Data</div>
          <div class="card-body">
            <div class="table-responsive">

              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                   <th>순서</th>
		   <th>mac주소</th>
		   <th>pwr</th>
		   <th>거리(미터)</th>
		   <th>시간</th>
		   <th>기기</th>
		   <th>학번</th>
		   <th>학년</th>
		   <th>이름</th>
		   <th>학과</th>
		</tr>
                </thead>
                <tfoot>
                  <tr>
                   <th>순서</th>
		   <th>mac주소</th>
		   <th>pwr</th>
		   <th>거리(미터)</th>
		   <th>시간</th>
		   <th>기기</th>
		   <th>학번</th>
		   <th>학년</th>
		   <th>이름</th>
		   <th>학과</th>
		  </tr>
                </tfoot>
                <tbody>

<?php
if($_GET['start_time'] && $_GET['finish_time']){
$sql = "SELECT Log.num, Log.mac_addr, pwr, time, device_kind, student_id, grade, name,  department FROM Log 
	LEFT JOIN Device ON Log.mac_addr = Device.mac_addr 
	LEFT JOIN Member ON Device.Member_student_id = Member.student_id 
where time >= '".$_GET['start_time']."' 
and time <= '".$_GET['finish_time']."'";
}

else{
$sql = "SELECT Log.num, Log.mac_addr, pwr, time, device_kind, student_id, grade, name,department FROM Log 
	LEFT JOIN Device ON Log.mac_addr = Device.mac_addr 
	LEFT JOIN Member ON Device.Member_student_id = Member.student_id";
}

$result = mysqli_query($conn,$sql);

if (!$result) {
echo "Could not successfully run query from DB: ";
exit;
}

if (mysqli_num_rows($result) == 0) {
echo "No rows found, nothing to print so am exiting";
exit;
}

while ($row = mysqli_fetch_assoc($result)) {
$mod = $row['pwr'] / -5;
echo "<tr>
<td>{$row['num']}</td>
<td>{$row['mac_addr']}</td>
<td>{$row['pwr']}</td>
<td>$mod m</td>
<td>{$row['time']}</td>
<td>{$row['device_kind']}</td>
<td>{$row['student_id']}</td>
<td>{$row['grade']}</td>
<td>{$row['name']}</td>
<td>{$row['department']}</td>
</tr>";
}

mysqli_free_result($result);
?>

		</tbody>
              </table>
            </div>
          </div>
          <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
        </div>

        <p class="small text-center text-muted my-5">
          <em>More table examples coming soon...</em>
        </p>

      </div>
      <!-- /.container-fluid -->

      <!-- Sticky Footer -->
      <footer class="sticky-footer">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright © Your Website 2019</span>
          </div>
        </div>
      </footer>

    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login.html">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Page level plugin JavaScript-->
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin.min.js"></script>

  <!-- Demo scripts for this page-->
  <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
