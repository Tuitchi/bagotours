<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];
require_once __DIR__ . '/../func/dashboardFunc.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
	<link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/admin.css">
	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<title>BaGoTours || Dashboard</title>
	<style>
		.box-info li span a {
			font-size: .7em;
			bottom: 0;
		}

		.box-info li span a:hover {
			color: #443396;
			text-decoration: underline;
		}

		.order {
			position: relative;
		}

		.loader {
			position: absolute;
			top: 50%;
			left: 50%;
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 9999;
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
					<?php include 'includes/breadcrumb.php'; ?>
				</div>
			</div>

			<ul class="box-info">
				<li>
					<i class='bx bxs-user'></i>
					<span class="text">
						<h3 id="totalVisitors"><?php echo totalVisitors($conn, $user_id); ?></h3>
						<p>Total Visitors</p>
						<a href="visitor">view all visitors.</a>
					</span>
				</li>
				<li>
					<i class='bx bxs-star'></i>
					<span class="text">
						<h3 id="averageStars"><?php echo averageStars($conn, $user_id); ?> / 5</h3>
						<p>Total Average Stars</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-map-pin'></i>
					<span class="text">
						<h3><?php echo totalTours($conn, $user_id); ?></h3>
						<p>Overall Tours</p>
					</span>
				</li>
			</ul>

			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Visitors</h3>
						<div class="filter">
							<select name="tour" id="tour" required>
								<option value="" selected>All</option>
								<?php
								require_once '../func/func.php';
								$tours = getTouristSpots($conn, $user_id);
								foreach ($tours as $tour) { ?>
									<option value="<?php echo $tour['id'] ?>"><?php echo $tour['title'] ?></option>
								<?php } ?>
							</select>
							<select id="timeFilter">
								<option value="" selected>All</option>
								<option value="daily">Daily</option>
								<option value="monthly">Monthly</option>
								<option value="yearly">Yearly</option>
							</select>
						</div>
					</div>
					<div id="visitorChart" style="max-width:100%; height:400px"></div>
					<div class="loader" style="display: none;"></div>
				</div>
			</div>

			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Tours</h3>
					</div>
					<div id="ToursChart" style="max-width:100%; height:400px"></div>
					<div class="loader" style="display: none;"></div>
				</div>
				<div class="todo">
					<div class="head">
						<h3>Notifications</h3>
						<i class='bx bx-plus'></i>
						<i class='bx bx-filter'></i>
					</div>
				</div>
			</div>
		</main>
	</section>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="../assets/js/script.js"></script>
	<script>
		// Reusable function to construct the base URL
		function getBasePath() {
			const origin = window.location.origin;
			return origin.includes('localhost') ? origin + '/bagotours/admin/' : origin + '/admin/';
		}

		// Event listener for both filters
		document.getElementById('tour').addEventListener('change', applyFilters);
		document.getElementById('timeFilter').addEventListener('change', applyFilters);

		// Apply filters when either dropdown is changed
		function applyFilters() {
			const tourId = document.getElementById('tour').value;
			const timeFilter = document.getElementById('timeFilter').value;

			updateBoxInfo(tourId, timeFilter); // Update the box-info dynamically
			visitorChart(tourId, timeFilter); // Update the visitor chart
		}

		// Update the box-info section dynamically based on selected filters
		function updateBoxInfo(tourId, timeFilter) {
			const loader = $('.box-info .loader');
			loader.show();

			const url = new URL('../php/updateBoxInfo.php', getBasePath());
			if (tourId) url.searchParams.append('tour', tourId);
			url.searchParams.append('id', <?php echo $user_id; ?>);
			url.searchParams.append('time', timeFilter);

			$.ajax({
				url: url,
				method: 'GET',
				dataType: 'json',
				success: function (data) {
					loader.hide();
					if (data) {
						document.getElementById('totalVisitors').textContent = data.total_visitors;
						document.getElementById('averageStars').textContent = data.average_stars + ' / 5';
					}
				},
				error: function (error) {
					loader.hide();
					console.error('Error fetching data:', error);
					alert('Failed to update box info');
				}
			});
		}

		// Google Charts loading and display
		google.charts.load('current', { 'packages': ['corechart'] });
		google.charts.setOnLoadCallback(tourChart);
		google.charts.setOnLoadCallback(visitorChart);

		// Generic chart rendering function
		function drawChart(elementId, url, chartOptions) {
			const loader = document.querySelector(`#${elementId} + .loader`);
			loader.style.display = 'block';

			fetch(url)
				.then(response => response.json())
				.then(data => {
					loader.style.display = 'none';
					if (Array.isArray(data) && data.length > 0) {
						const chartData = google.visualization.arrayToDataTable([['Label', 'Count'], ...data]);
						const chart = new google.visualization.PieChart(document.getElementById(elementId));
						chart.draw(chartData, chartOptions);
					} else {
						document.getElementById(elementId).innerHTML = '<p>No data available to display.</p>';
					}
				})
				.catch(error => {
					loader.style.display = 'none';
					console.error('Error fetching chart data:', error);
					document.getElementById(elementId).innerHTML = '<p>Failed to load chart data.</p>';
				});
		}

		// Display Tour Chart
		function tourChart() {
			const url = 'assets/tourChart.php';
			const chartOptions = { title: 'Tours by Type', is3D: true };
			drawChart('ToursChart', url, chartOptions);
		}

		// Display Visitor Chart
		function visitorChart(tourId = null, timeFilter) {
			const url = new URL('assets/visitorChart.php', getBasePath());
			if (tourId) url.searchParams.append('tour', tourId);
			url.searchParams.append('id', <?php echo $user_id; ?>);
			url.searchParams.append('time', timeFilter);

			const chartOptions = {
				title: 'Visitor Report',
				is3D: true,
				colors: ['#FF5722', '#4CAF50', '#2196F3', '#FFC107'],
				pieSliceText: 'value',
				legend: { position: 'right', textStyle: { color: '#333', fontSize: 14 } },
				chartArea: { left: 10, top: 20, width: '80%', height: '70%' }
			};
			drawChart('visitorChart', url, chartOptions);
		}
	</script>
</body>

</html>
