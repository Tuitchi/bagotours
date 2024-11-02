<?php
include '../include/db_conn.php';
session_start();

$pageRole = "admin";
require_once '../php/accValidation.php';
require_once '../func/func.php';

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];
$result = getBooking($conn, $user_id);

$statusOrder = [0, 1, 3, 4, 2];

// Sort the bookings array by custom status order
usort($result, function ($a, $b) use ($statusOrder) {
	$aOrder = array_search($a['status'], $statusOrder);
	$bOrder = array_search($b['status'], $statusOrder);
	return $aOrder - $bOrder;
});

function getStatusButton($row)
{
	switch ($row['status']) {
		case "0":
			return "<button class='btn-view' data-id='{$row['id']}'><i class='bx bx-edit-alt'></i>Edit</button>";
		case "1":
			return "<button class='btn-ready' data-id='{$row['id']}'>Approval</button>";
		case "2":
			return "<button class='btn-drop'>Cancelled</button>";
		case "3":
			return "<button class='btn-waiting'>User Rating</button>";
		case "4":
			return "<button class='btn-success'>Completed</button>";
		default:
			return "Error status";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">
	<link rel="stylesheet" href="assets/css/admin.css">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<title>BaGoTours | Bookings</title>
	<style>
		.media img {
			max-width: 150px;
		}

		.media {
			width: 60%;
			display: flex;
			align-items: center;
		}

		.table-container {
			overflow-x: auto;
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
						<i class='bx bx-search'></i>
						<i class='bx bx-filter'></i>
					</div>
					<div class="table-container">
						<table>
							<thead>
								<tr>
									<th>User</th>
									<th>Tour</th>
									<th>Date Ordered</th>
									<th>People</th>
									<th>Phone</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($result): ?>
									<?php foreach ($result as $row): ?>
										<tr>
											<td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
											<td><?php echo htmlspecialchars($row['tour_title'], ENT_QUOTES, 'UTF-8'); ?></td>
											<td><?php echo htmlspecialchars($row['date_sched'], ENT_QUOTES, 'UTF-8'); ?></td>
											<td><?php echo htmlspecialchars($row['people'], ENT_QUOTES, 'UTF-8'); ?></td>
											<td><?php echo htmlspecialchars($row['phone_number'], ENT_QUOTES, 'UTF-8'); ?></td>
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

			<!-- View Modal -->
			<div id="viewModal" class="modal fade">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Booking Details</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div id="applicationInfoContent" class="modal-body"></div>
					</div>
				</div>
			</div>

			<!-- Complete Modal -->
			<div id="completeModal" class="modal fade">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Booking Details</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div id="completeContent" class="modal-body"></div>
					</div>
				</div>
			</div>
		</main>
	</section>
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

			function fetchBookingData(id, modalContent, modalId) {
				$.getJSON(`../php/getBooking.php?id=${id}`)
					.done(data => {
						if (data.success && data.book) {
							const formattedDate = formatDate(data.book.date_sched);
							const createdDate = formatDate(data.book.date_created);
							const content = generateContent(data.book, modalId === '#viewModal' ? 'view' : 'complete', formattedDate, createdDate);
							modalContent.html(content);
							$(modalId).modal('show');
						} else {
							showErrorToast('Unable to fetch booking information.');
						}
					})
					.fail(() => showErrorToast('Error fetching booking information.'));
			}

			function formatDate(dateStr) {
				return new Date(dateStr).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
			}

			function generateContent(book, action, formattedDate, createdDate) {
				return `
					<div class="booking-card mx-auto p-3 border rounded shadow-sm">
						<h3 class="text-center mb-3">${book.tour_title} <span class="badge badge-info">${formattedDate}</span></h3>
						<div class="d-flex justify-content-between">
							<div class="user-details d-flex align-items-start">
								<img class="img-thumbnail mr-3" src="../upload/Profile Pictures/${book.profile_picture}" alt="User Image" style="max-width: 150px;">
								<div>
									<h4 class="font-weight-bold">${book.name}</h4>
									<p><i class="bx bx-pin"></i> ${book.home_address}</p>
								</div>
							</div>
							<div class="contact-info">
								<h6>Contact Information</h6>
								<p><i class="bx bx-envelope"></i> ${book.email}</p>
								<p><i class="bx bx-phone"></i> ${book.phone_number}</p>
							</div>
						</div>
						<div class="footer mt-3 d-flex justify-content-between align-items-center">
							<span>Book Created: ${createdDate}</span>
							<div class="btn-group">
								${getActionButtons(action, book)}
							</div>
						</div>
					</div>
				`;
			}

			function getActionButtons(action, book) {
				if (action === 'view') {
					return `<a class="btn btn-success" href="../php/updateBooking.php?status=1&id=${book.booking_id}&user=${book.user_id}&tour=${book.tour_id}">Approve</a>
							<a class="btn btn-danger" href="../php/updateBooking.php?status=2&id=${book.booking_id}&user=${book.user_id}&tour=${book.tour_id}">Decline</a>`;
				} else {
					return `<a class="btn btn-success" href="../php/updateBooking.php?status=3&id=${book.booking_id}&user=${book.user_id}&tour=${book.tour_id}">Complete</a>
							<a class="btn btn-danger" href="../php/updateBooking.php?status=2&id=${book.booking_id}&user=${book.user_id}&tour=${book.tour_id}">Drop</a>`;
				}
			}

			function showErrorToast(message) {
				Toast.fire({ icon: 'error', title: message });
			}

			$('.btn-view').click(function () {
				const id = $(this).data('id');
				fetchBookingData(id, $('#applicationInfoContent'), '#viewModal');
			});
			$('.btn-ready').click(function () {
				const id = $(this).data('id');
				fetchBookingData(id, $('#completeContent'), '#completeModal');
			});
		});
	</script>
</body>

</html>