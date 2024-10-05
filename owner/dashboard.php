<?php
include '../include/db_conn.php';
require_once __DIR__ . '/../func/dashboardFunc.php';
session_start();


$pageRole = "owner";
require_once '../php/accValidation.php';

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];
$tour = $_SESSION['tour_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="assets/css/owner.css">
	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<title>BaGoTours. Dashboard</title>
</head>

<body>
	<?php include 'includes/sidebar.php'; ?>
	<section id="content">
		<?php include 'includes/navbar.php'; ?>
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
					<?php include 'includes/breadcrumb.php'; ?>
				</div>
			</div>

			<ul class="box-info">
				<li>
					<i class='bx bxs-map-pin'></i>
					<span class="text">
						<h3><?php echo totalVisitors($conn, $user_id)?></h3>
						<p>Visitors</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-group'></i>
					<span class="text">
						<h3><?php echo totalBooking($conn, $user_id) == "" ? 0 : totalBooking($conn, $user_id)?></h3>
						<p>Books</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-map-pin'></i>
					<span class="text">
						<h3><?php echo totalStars($conn, $user_id)?></h3>
						<p>Stars</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Reports</h3>
						<i class='bx bx-filter'></i>
					</div>
					<div id="myChart" style="max-width:100%; height:400px"></div>
				</div>
				<div class="todo">
					<div class="head">
						<h3>Notifcations</h3>
						<i class='bx bx-plus'></i>
					</div>
				</div>
			</div>
		</main>
	</section>


	<script src="../assets/js/script.js"></script>
	<script>
	</script>
</body>

</html>