<?php
include '../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: ../login.php?action=Invalid");
	exit();
}

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];
$tour_id = $_SESSION['tour_id'];

$query = "SELECT b.*, t.title as tour_title, u.username FROM booking b
          JOIN tours t ON b.tours_id = t.id
          JOIN users u ON b.user_id = u.id WHERE t.id = '$tour_id'
          ORDER BY b.date_sched DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
	die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
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
					<?php include 'includes/breadcrumb.php'; ?>
				</div>
			</div>

			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Booking List</h3>
						<i class='bx bx-search'></i>
						<i class='bx bx-filter'></i>
					</div>
					<table>
						<thead>
							<tr>
								<th>User</th>
								<th>Tour</th>
								<th>Date Ordered</th>
								<th>People</th>
								<th>Phone</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (mysqli_num_rows($result) > 0): ?>
								<?php while ($row = mysqli_fetch_assoc($result)): ?>
									<tr>
										<td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['tour_title'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['date_sched'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['people'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['phone_number'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td>
											<select class="status-select" data-booking-id="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
												<option value="0" <?php if ($row['status'] == '0') echo 'selected'; ?>>Pending</option>
												<option value="1" <?php if ($row['status'] == '1') echo 'selected'; ?>>Confirmed</option>
												<option value="2" <?php if ($row['status'] == '2') echo 'selected'; ?>>Cancelled</option>
											</select>
										</td>
										<td>
											<button class="btn-delete" data-booking-id="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">🗑️</button>
											<button class="btn-view" data-user-id="<?php echo htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8'); ?>" data-booking-id="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">View</button>
										</td>
									</tr>
								<?php endwhile; ?>
							<?php else: ?>
								<tr>
									<td colspan="7" style="text-align: center;">No bookings found.</td>
								</tr>
							<?php endif; ?>
						</tbody>

					</table>
				</div>
			</div>
		</main>
	</section>
	<script src="../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		const Toast = Swal.mixin({
			toast: true,
			position: "top-end",
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true,
			didOpen: (toast) => {
				toast.onmouseenter = Swal.stopTimer;
				toast.onmouseleave = Swal.resumeTimer;
			}
		});
		document.querySelectorAll('.btn-view').forEach(button => {
			button.addEventListener('click', function() {
				var userId = this.getAttribute('data-user-id');
				var bookingId = this.getAttribute('data-booking-id');

				window.location.href = '../admin/view_booking.php?user_id=' + userId + '&booking_id=' + bookingId;
			});
		});

		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.status-select').forEach(select => {
				select.addEventListener('change', function() {
					var bookingId = this.getAttribute('data-booking-id');
					var newStatus = this.value;

					fetch('../php/update_status.php', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/x-www-form-urlencoded'
							},
							body: new URLSearchParams({
								'booking_id': bookingId,
								'status': newStatus
							})
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								Toast.fire({
                                    icon:'success',
                                    title: 'Status updated successfully!'
                                });
							} else {
								console.error('Failed to update status:', data.message);
							}
						})
						.catch(error => {
							console.error('Error:', error);
						});
				});
			});
			document.querySelectorAll('.btn-delete').forEach(button => {
				button.addEventListener('click', function() {
					var bookingId = this.getAttribute('data-booking-id');

					fetch('../php/delete_booking.php', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/x-www-form-urlencoded'
							},
							body: new URLSearchParams({
								'booking_id': bookingId
							})
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								location.reload();
							} else {
								console.error('Failed to delete booking:', data.message);
							}
						})
						.catch(error => {
							console.error('Error:', error);
						});
				});
			});
		});
	</script>
</body>

</html>