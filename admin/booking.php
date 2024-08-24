<?php 
include '../include/db_conn.php';
session_start();

session_regenerate_id();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="../assets/css/admin.css">

	<title>BaGoTours. Booking</title>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
	<section id="content">
		<?php include 'includes/navbar.php'; ?>
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Booking</h1>
                    <?php include 'includes/breadcrumb.php';?>
				</div>
			</div>

			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Booking</h3>
						<i class='bx bx-search' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<table>
						<thead>
							<tr>
								<th>User</th>
								<th>Date Order</th>
								<th>Status</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</main>
	</section>
	<script src="../assets/js/script.js"></script>
</body>
</html>