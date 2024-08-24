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

include 'includes/dashboard_query.php';
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
	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<title>BaGoTours. Dashboard</title>
</head>

<body>


	<!-- SIDEBAR -->
	<?php include 'includes/sidebar.php'; ?>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->

		<?php include 'includes/navbar.php'; ?>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
					<?php include 'includes/breadcrumb.php'; ?>
				</div>
			</div>

			<ul class="box-info">
				<li>
					<i class='bx bxs-calendar-check'></i>
					<span class="text">
						<h3><?php echo $total_pending ?></h3>
						<p>Pending</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-group'></i>
					<span class="text">
						<h3><?php echo $total_users ?></h3>
						<p>Users</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-map-pin'></i>
					<span class="text">
						<h3><?php echo $total_tours ?></h3>
						<p>Tours</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Tours</h3>
					</div>
					<div id="myChart" style="max-width:100%; height:400px"></div>
				</div>
				<div class="todo">
					<div class="head">
						<h3>Notifcations</h3>
						<i class='bx bx-plus'></i>
						<i class='bx bx-filter'></i>
					</div>
					<ul class="todo-list">
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded'></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded'></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded'></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded'></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded'></i>
						</li>
					</ul>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->


	<script src="../assets/js/script.js"></script>
	<script>
		google.charts.load('current', {
			'packages': ['corechart']
		});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			fetch('assets/chart.php')
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok: ' + response.statusText);
					}
					return response.json();
				})
				.then(tourData => {
					console.log('Tour Data:', tourData);

					if (Array.isArray(tourData) && tourData.length > 0) {
						const data = google.visualization.arrayToDataTable([
							['Tour Type', 'Count'],
							...tourData
						]);

						const options = {
							title: 'Tours by Type',
							is3D: true
						};

						const chart = new google.visualization.PieChart(document.getElementById('myChart'));
						chart.draw(data, options);
					} else {
						console.error('No data or invalid data format:', tourData);
					}
				})
				.catch(error => console.error('Error fetching data:', error));
		}
	</script>
</body>

</html>