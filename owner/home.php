<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}

require_once '../include/db_conn.php';
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
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
	<!-- My CSS -->
	<link rel="stylesheet" href="../assets/css/admin.css">

	<title>BaGoTours. Home</title>
	<style>
		#map {
			width: 100%;
			height: 500px;
		}

		.marker {
			background-size: cover;
			width: 30px;
			height: 30px;
			border-radius: 50%;
			cursor: pointer;
		}

		.mapboxgl-popup {
			max-width: 200px;
			font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
		}

		.mapboxgl-popup-content {
			text-align: center;
			font-weight: bold;
		}

		.marker:hover {
			transform: scale(1.1);
		}

		.marker:active {
			transform: scale(1.2);
		}

		.popup-content {
			background-color: #fff;
			padding: 20px;
			border-radius: 5px;
		}

		.popup-content img {
			width: 180px;
		}

		.popup-close {
			color: black;
			font-size: 20px;
			cursor: pointer;
		}
	</style>
</head>

<body>
	<?php include 'includes/sidebar.php'; ?>
	<section id="content">
		<?php include 'includes/navbar.php'; ?>
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Home</h1>
					<?php include 'includes/breadcrumb.php'; ?>
				</div>
			</div>
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Home</h3>
						<i class='bx bx-search'></i>
						<i class='bx bx-filter'></i>
					</div>
				</div>
			</div>
		</main>
	</section>

	<script src="../assets/js/script.js"></script>
</body>

</html>