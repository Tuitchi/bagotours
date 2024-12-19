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
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
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
						<h3><?php echo totalVisitors($conn, $user_id); ?></h3>
						<p>Total Visitors</p>
						<a href="visitor">view all visitors.</a>
					</span>
				</li>
				<li>
					<i class='bx bxs-star'></i>
					<span class="text">
						<h3><?php echo averageStars($conn, $user_id); ?> / 5</h3>
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
								<?php require_once '../func/func.php';
								$tours = getTouristSpots($conn, $user_id);
								foreach ($tours as $tour) { ?>
									<option value="<?php echo $tour['id'] ?>"><?php echo $tour['title'] ?></option>
								<?php } ?>
							</select>
							<select id="timeFilter">
								<option value="" selected>all</option>
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
						<h3>Notifcations</h3>
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
		google.charts.load('current', {
			'packages': ['corechart']
		});
		google.charts.setOnLoadCallback(tourChart);
		google.charts.setOnLoadCallback(visitorChart);

		function tourChart() {
			const loader = document.querySelector('#ToursChart + .loader');
			loader.style.display = 'block';

			fetch('assets/tourChart.php')
				.then(response => response.json())
				.then(tourData => {
					loader.style.display = 'none';
					if (Array.isArray(tourData) && tourData.length > 0) {
						const data = google.visualization.arrayToDataTable([
							['Tour Type', 'Count'],
							...tourData
						]);

						const options = {
							title: 'Tours by Type',
							is3D: true
						};

						const chart = new google.visualization.PieChart(document.getElementById('ToursChart'));
						chart.draw(data, options);
					} else {
						console.error('No data or invalid data format:', tourData);
						document.getElementById('ToursChart').innerHTML = '<p>No data available to display.</p>';
					}
				})
				.catch(error => {
					loader.style.display = 'none';
					console.error('Error fetching data:', error);
					document.getElementById('ToursChart').innerHTML = '<p>Failed to load chart data.</p>';
				});
		}


		document.getElementById('tour').addEventListener('change', applyFilters);
		document.getElementById('timeFilter').addEventListener('change', applyFilters);

		function applyFilters() {
			const tourId = document.getElementById('tour').value;
			const timeFilter = document.getElementById('timeFilter').value;

			visitorChart(tourId, timeFilter);
		}

		function visitorChart(tourId = null, timeFilter) {
			const loader = $('#visitorChart + .loader');

			loader.show();
			const origin = window.location.origin;
			let basePath;

			if (origin === 'http://localhost') {
				basePath = origin + '/bagotours/admin/';
			} else {
				basePath = origin + '/admin/';
			}

			const url = new URL('assets/visitorChart.php', basePath);

			console.log(url.toString()); // Output the resolved URL to verify

			alert('url: ' + url);
			if (tourId) {
				url.searchParams.append('tour', tourId);
			}
			url.searchParams.append('id', <?php echo $user_id; ?>); // Inject PHP user ID dynamically
			url.searchParams.append('time', timeFilter);

			$.ajax({
				url: url,
				method: 'GET',
				dataType: 'json',
				success: function (visitorData) {
					loader.hide();
					if (Array.isArray(visitorData) && visitorData.length > 0) {
						const data = google.visualization.arrayToDataTable([
							['City Residence', 'Count'],
							...visitorData
						]);

						const options = {
							title: 'Visitor Report',
							is3D: true,
							colors: ['#FF5722', '#4CAF50', '#2196F3', '#FFC107'],
							pieSliceText: 'value',
							legend: {
								position: 'right',
								textStyle: {
									color: '#333',
									fontSize: 14
								}
							},
							chartArea: {
								left: 10,
								top: 20,
								width: '80%',
								height: '70%'
							}
						};

						const chart = new google.visualization.PieChart($('#visitorChart')[0]);
						chart.draw(data, options);
					} else {
						$('#visitorChart').html('<p>No data available to display.</p>');
					}
				},
				error: function (error) {
					loader.hide(); // Hide the loader even on error
					console.error('Error fetching data:', error);
					$('#visitorChart').html('<p>Failed to load chart data.</p>');
				}
			});
		}

	</script>
</body>

</html>