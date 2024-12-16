<?php
include '../include/db_conn.php';
session_start();
require '../func/func.php';

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$id = $_POST['id'];
	$status = (int) $_POST['status']; // Ensure the status is treated as an integer
	$people = $_POST['people'];

	try {
		$stmt = $conn->prepare("SELECT user_id, BID FROM booking WHERE id = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$booking = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$booking) {
			header('Location: booking?status=not_found');
			exit();
		}

		$client_id = $booking['user_id']; // This is the client who made the booking.

		// Map the integer status to a user-friendly string
		$statusText = match ($status) {
			1 => "Confirmed",
			2 => "Cancelled",
			3 => "Completed",
			default => "Unknown",
		};

		// Update the booking record
		$stmt = $conn->prepare("UPDATE booking SET status = :status, people = :people WHERE id = :id");
		$stmt->bindParam(':status', $status, PDO::PARAM_INT);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->bindParam(':people', $people, PDO::PARAM_INT);

		if ($stmt->execute()) {
			// Create a notification for the client
			$message = "Your booking (ID: " . $booking['BID'] . ") has been updated. Status: $statusText.";
			$url = "booking";
			$type = "booking_update";
			$notificationResult = createNotification($conn, $client_id, $id, $message, $url, $type);

			if ($notificationResult === "success") {
				header('Location: booking?status=success');
			} else {
				header('Location: booking?status=notif_error');
			}
		} else {
			header('Location: booking?status=err');
		}
	} catch (Exception $e) {
		error_log("Error updating booking: " . $e->getMessage());
		header('Location: booking?status=err');
	}
	exit();
}


$result = setBookingBtn($conn, $user_id);

$statusOrder = [0, 1, 3, 4, 2];

// Sort the bookings array by custom status order
usort($result, function ($a, $b) use ($statusOrder) {
	$aOrder = array_search($a['status'], $statusOrder);
	$bOrder = array_search($b['status'], $statusOrder);
	return $aOrder - $bOrder;
});
function setBookingBtn($conn, $user_id)
{
	$stmt = $conn->prepare("SELECT b.*, t.title as tour_title, u.firstname, u.lastname, u.email, u.phone_number FROM booking b
          JOIN tours t ON b.tour_id = t.id
          JOIN users u ON b.user_id = u.id WHERE t.user_id = :user_id
          ORDER BY b.start_date DESC");
	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getStatusButton($row)
{
	switch ($row['status']) {
		case "0":
			return "<button class='btn view' data-id='{$row['id']}'><i class='bx bxs-edit-alt'></i>Pending Approval</button>";
		case "1":
			return "<button class='btn arrival' data-id='{$row['id']}'><i class='bx bxs-edit-alt'></i>Awaiting Arrival</button>";
		case "2":
			return "<button class='btn-drop'>Cancelled/Drop</button>";
		case "3":
			return "<button class='btn-waiting'>User Rating...</button>";
		case "4":
			return "<button class='btn-success'>Completed</button>";
		default:
			return "Error status";
	}
}
// PHP section

?>

<!-- HTML and JavaScript section -->
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">
	<link rel="stylesheet" href="assets/css/admin.css">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<title>BaGoTours || Booking</title>
	<style>
		/* CSS adjustments */
		.table-container {
			overflow-x: auto;
		}

		.btn {
			padding: 5px 10px;
			border-radius: 5px;
			font-size: 14px;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}

		.btn.arrival {
			background-color: #f8312f;
			color: white;
			border: none;
			cursor: pointer;
			margin-right: 5px;
		}

		.btn.view {
			background-color: #71a3c1;
			color: white;
			border: none;
			cursor: pointer;
		}

		.btn.arrival:hover {
			background-color: #f3817e;
		}

		.btn.view:hover {
			background-color: #a4ccec;
		}

		/* for modal */
		/* Container styling */
		.modal-content {
			position: relative;
			background-color: #fff;
			margin: auto;
			border-radius: 4px;
			width: 80vw;
		}

		@media screen and (max-width: 768px) {
			.modal-content {
				padding: 0;
			}
		}

		.booking-card {
			max-width: 80vw;
			padding: 20px;
			border-radius: 8px;
			background-color: #ffffff;
		}

		/* Title styling */
		.booking-title {
			text-align: center;
			font-size: 1.5em;
			color: #333;
			margin-bottom: 15px;
		}

		.booking-date {
			font-size: 0.9em;
			color: #5bc0de;
			background-color: #e9f7fc;
			padding: 2px 8px;
			border-radius: 4px;
		}

		/* Content layout styling */
		.booking-content {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 15px;
		}

		/* User Information styling */
		.user-info {
			display: flex;
			align-items: flex-start;
		}

		.user-image {
			max-width: 100px;
			height: auto;
			border-radius: 6px;
			margin-right: 15px;
			box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
		}

		.user-details {
			font-size: 0.9em;
		}

		.user-name {
			font-weight: bold;
			color: #333;
		}

		.user-address {
			color: #777;
			font-size: 0.85em;
		}

		.icon {
			margin-right: 5px;
		}

		/* Contact Information styling */
		.contact-info h6 {
			font-size: 1em;
			font-weight: bold;
			color: #555;
			margin-bottom: 8px;
		}

		.contact-info p {
			margin: 0;
			color: #666;
			font-size: 0.9em;
		}

		.icon-location:before {
			content: "\f3c5";
		}

		.icon-email:before {
			content: "\f0e0";
		}

		.icon-phone:before {
			content: "\f095";
		}

		/* Footer styling */
		.booking-footer {
			display: flex;
			justify-content: space-between;
			align-items: center;
			font-size: 0.85em;
			color: #888;
		}

		.booking-created {
			color: #666;
		}

		/* Action buttons styling */
		/* Base style for action buttons */
		.action-buttons button {
			padding: 8px 16px;
			font-size: 1em;
			font-weight: bold;
			color: #fff;
			border-radius: 4px;
			border: none;
			cursor: pointer;
			margin-left: 5px;
			transition: background-color 0.3s ease-in-out, transform 0.2s;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
		}

		/* Hover effect for buttons */
		.action-buttons button:hover {
			background-color: rgba(0, 0, 0, 0.8);
			transform: translateY(-2px);
		}

		/* Approve button style */
		.action-buttons .approve {
			background-color: #28a745;
		}

		.action-buttons .approve:hover {
			background-color: #218838;
		}

		/* Decline button style */
		.action-buttons .decline {
			background-color: #dc3545;
		}

		.action-buttons .decline:hover {
			background-color: #c82333;
		}

		/* Arrived button style */
		.action-buttons .arrived {
			background-color: #f5af22;
			/* Darker text color for better contrast */
		}

		.action-buttons .arrived:hover {
			background-color: #d88e08;
		}

		form label {
			margin-bottom: 5px;
			font-weight: bold;
		}

		form input[type="number"] {
			padding: 8px;
			font-size: 1em;
			border-radius: 4px;
			border: 1px solid #ccc;
			margin-bottom: 10px;
			width: 80px;
		}

		form input[type="number"]:focus {
			border-color: #28a745;
			outline: none;
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
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Booking List</h3>
					</div>
					<div class="table-container">
						<table>
							<thead>
								<tr>
									<th>#</th>
									<th>User</th>
									<th>Tour</th>
									<th>Date Scheduled</th>
									<th>People</th>
									<th>Phone # / Email</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($result):
									$counter = 1 ?>
									<?php foreach ($result as $row): ?>
										<tr>
											<td><?php echo $counter++; ?></td>
											<td><?php echo htmlspecialchars($row['firstname'], ENT_QUOTES, 'UTF-8') . " " . $row['lastname']; ?>
											</td>
											<td><?php echo htmlspecialchars($row['tour_title'], ENT_QUOTES, 'UTF-8'); ?></td>
											<td><?php echo htmlspecialchars($row['start_date'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($row['end_date'], ENT_QUOTES, 'UTF-8') ?>
											</td>
											<td><?php echo ($row['people'] == 0) ? 'N/A' : htmlspecialchars($row['people'], ENT_QUOTES, 'UTF-8'); ?>
											</td>
											<td><?php echo htmlspecialchars($row['phone_number'], ENT_QUOTES, 'UTF-8'); ?> -
												<?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?>
											</td>
											<td><?php echo getStatusButton($row); ?></td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="7" style="text-align: center;">No bookings found.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Modals for Viewing and Completion -->
			<div id="viewModal" class="modal fade">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-header">
							<h5 class="modal-title">Pending Approval</h5>
						</div>
						<div id="applicationInfoContent" class="modal-body"></div>
					</div>
				</div>
			</div>

			<div id="completeModal" class="modal fade">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-header">
							<h5 class="modal-title">Waiting for arrival</h5>
						</div>
						<div id="completeContent" class="modal-body"></div>
					</div>
				</div>
			</div>
		</main>
	</section>

	<!-- JavaScript -->
	<script src="../assets/js/script.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		$(document).ready(function () {
			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000,
				timerProgressBar: true
			});
			<?php if (isset($_GET['status']) == 'success') {
				echo "Toast.fire({
                    icon: 'success',
                    title: 'Booking status updated.'
                });";
			} elseif (isset($_GET['status']) == 'err') {
				echo "Toast.fire({
                    icon: 'error',
                    title: 'Failed to load booking data.'
                });";
			} ?>

			function fetchBookingData(id, modalContent, modalId) {
				$.getJSON(`../php/getBooking.php?id=${id}`)
					.done(data => {
						if (data.success && data.book) {
							const book = data.book;
							const startDate = formatDate(book.start_date);
							const endDate = formatDate(book.end_date);
							const createdDate = formatDate(book.date_created);
							const actionButtons = (modalId === '#viewModal') ? getActionButtons('view', id, book.people) : getActionButtons('complete', id, book.people);
							const trusted = book.is_trusted === 1 ? 'Trusted' : '';
							const content = `<div class="booking-card">
								<h3 class="booking-title">${book.tour_title} <span class="booking-date">${startDate} - ${endDate}</span></h3>
								<div class="booking-content">
									<div class="user-info">
										<img class="user-image" src="../${book.profile_picture}" alt="User Image">
											<div class="user-details">
												<h4 class="user-name">${book.name} <span>${trusted}</span></h4>
												<p class="user-address"><i class='bx bxs-map'></i>${book.home_address}</p>
												<p class="user-address"><i class='bx bxs-key'></i>${book.bookingId}</p>
											</div>
									</div>
									<div class="contact-info">
										<h6>Contact Information</h6>
										<p><i class='bx bxs-envelope'></i> ${book.email}</p>
										<p><i class='bx bxs-phone' ></i> ${book.phone_number}</p>
									</div>
								</div>
								<div class="booking-footer">
									<span class="booking-created">Book Created: ${createdDate}</span>
									<form method="post" action="">
										<input type="hidden" name="id" value="${id}">
										<div class="action-buttons">${actionButtons}</div>
									</form>
								</div>	
							</div>`
							modalContent.html(content);
							$(modalId).modal('show');
						} else {
							showErrorToast('Unable to fetch booking information.');
						}
					})
					.fail(() => showErrorToast('Error fetching booking information.'));
			}

			function getActionButtons(action, bookId, people) {
				if (action === 'view') {
					return `
					<input type="hidden" name="people" value="${people}">
					<button type="submit" class="action-button approve" name="status" value="1">Approve</button>
				<button type="submit" class="action-button decline" name="status" value="2">Decline</button>`;
				} else if (action === 'complete') {
					return `
			<label for="people">Number of People:</label>
			<input type="number" name="people" min="1" max="99" value="${people}" required>
			<button type="submit" class="action-button arrived" name="status" value="3">Arrived</button>
		`;
				}
				return '';
			}

			function setStatus(button, status) {
				const form = button.closest('form');
				const statusInput = form.querySelector('input[name="status"]');
				statusInput.value = status;
			}

			function formatDate(dateStr) {
				return new Date(dateStr).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
			}


			function showErrorToast(message) {
				Toast.fire({ icon: 'error', title: message });
			}
			$('.view').click(function () {
				const id = $(this).data('id');
				fetchBookingData(id, $('#applicationInfoContent'), '#viewModal');
			});
			$('.arrival').click(function () {
				const id = $(this).data('id');
				fetchBookingData(id, $('#completeContent'), '#completeModal');
			});
			$('.close').click(function () {
				$('#viewModal').modal('hide');
				$('#completeModal').modal('hide');
			});

		});
	</script>
</body>

</html>