<?php
include '../include/db_conn.php';
session_start();

$pageRole = "admin";
require_once '../php/accValidation.php';
require_once '../func/func.php';

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

$result = getBooking($conn, $user_id);

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="assets/css/admin.css">

	<title>BaGoTours. Booking</title>
	<style>
		.media img {
			max-width: 150px;
		}

		.media {
			width: 60%;
			display: flex;
			align-items: center;
		}

		.btn-group {
			display: flex;
			justify-content: flex-end;
		}

		.btn-group a {
			padding: 10px 15px;
			margin-right: 10px;
			border: none;
			background-color: #4CAF50;
			color: white;
			cursor: pointer;
			border-radius: 5px;
		}

		.btn-group .btn-success:hover {
			background-color: #45a049;
		}

		.btn-group .btn-danger {
			background-color: red;
		}

		.btn-group .btn-danger:hover {
			background-color: darkred;
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
							<?php if ($result): ?>
								<?php foreach ($result as $row): ?>
									<tr>
										<td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['tour_title'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['date_sched'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['people'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row['phone_number'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td>
											<?php
											switch ($row['status']) {
												case "0":
													echo "Pending";
													break;
												case "1":
													echo "Confirmed";
													break;
												case "2":
													echo "Cancelled";
													break;
												case "3":
													echo "Ongoing";
													break;
												case "4":
													echo "Completed";
													break;
												default:
													echo "Error status";
											} ?>
										</td>
										<td><?php
											switch ($row['status']) {
												case "0":
													echo "<button class='btn-view' data-id=". $row['id']."><i class='bx bx-edit-alt'></i>Edit</button>";
													break;
												case "1":
													echo "<button class='btn-waiting'>Waiting...</button>";
													break;
												case "2":
													echo "<button class='btn-drop'>Cancelled</button>";
													break;
												case "3":
													echo "<button class='btn-ready' data-id=". $row['id'].">Approval</button>";
													break;
												case "4":
													echo "<button class='btn-success'>Completed</button>";
													break;
												default:
													echo "Error status";
											} ?>
										</td>
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
		</main>
	</section>
	<!-- MODAL -->
	<div id="viewModal" class="modal fade">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Booking Details</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div id="applicationInfoContent" class="modal-body">
				</div>
			</div>
		</div>
	</div>
	<div id="completeModal" class="modal fade">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Booking Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div id="completeContent" class="modal-body">
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="../assets/js/script.js"></script>
	<script>
		$(document).ready(function() {
			console.log(typeof Swal !== 'undefined' ? 'SweetAlert2 Loaded' : 'SweetAlert2 Not Loaded');

			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000,
				timerProgressBar: true
			});

			// Function to fetch and display booking details
			function fetchBookingData(id, modalContent, modalId) {
				$.getJSON(`../php/getBooking.php?id=${id}`)
					.done(function(data) {
						if (data.success && data.book) {
							const formattedDate = new Date(data.book.date_sched).toLocaleString('en-US', {
								month: 'long',
								day: 'numeric',
								year: 'numeric',
							});
							const createdDate = new Date(data.book.date_created).toLocaleString('en-US', {
								month: 'long',
								day: 'numeric',
								year: 'numeric',
							});
							let people = data.book.people ? `${data.book.people} Persons` : 'Not Specified';

							// Create a reusable function for generating the content
							const generateContent = (action) => `
                        <h3 class="mx-auto">${data.book.tour_title} <span class="badge badge-info">${formattedDate}</span></h3>
                        <div class="content">
                            <div class="media">
                                <img class="align-self-start mr-3 img-thumbnail" src="../upload/Profile Pictures/${data.book.profile_picture}" alt="Image" style="max-width: 150px;">
                                <div class="media-body">
                                    <h4 class="mt-0">${data.book.name}</h4>
                                    <ul class="list-unstyled">
                                        <li><p><i class='bx bx-pin'></i> ${data.book.home_address}</p></li>
                                        <li><i class='bx bx-male'></i> ${people}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="rightInfo">
                                <div class="contact">
                                    <h6>Contact Information</h6>
                                    <p><i class='bx bx-envelope'></i> ${data.book.email}</p>
                                    <p><i class='bx bx-phone'></i> ${data.book.phone_number}</p>
                                </div>
                            </div>
                        </div>
                        <span>Book Created: ${createdDate}</span>
                        <div class="btn-group">
                            ${action === 'view' ? `
                                <a class="btn-success" href="../php/updateBooking.php?status=1&id=${data.book.booking_id}&user=${data.book.user_id}&tour=${data.book.tour_id}">Approve</a>
                                <a class="btn-danger" href="../php/updateBooking.php?status=2&id=${data.book.booking_id}&user=${data.book.user_id}&tour=${data.book.tour_id}">Decline</a>
                            ` : `
                                <a class="btn-success" href="../php/updateBooking.php?status=4&id=${data.book.booking_id}&user=${data.book.user_id}&tour=${data.book.tour_id}">Complete</a>
                                <a class="btn-danger" href="../php/updateBooking.php?status=2&id=${data.book.booking_id}&user=${data.book.user_id}&tour=${data.book.tour_id}">Drop</a>
                            `}
                        </div>
                    `;

							modalContent.html(generateContent(modalId === '#viewModal' ? 'view' : 'complete'));
							$(modalId).modal('show');
						} else {
							Toast.fire({
								icon: 'error',
								title: 'Unable to fetch booking information.'
							});
						}
					})
					.fail(function(jqXHR, textStatus, errorThrown) {
						console.error(`Error fetching booking data: ${textStatus}`, errorThrown);
						Toast.fire({
							icon: 'error',
							title: 'There was an error fetching the booking information.'
						});
					});
			}

			$('.btn-view').click(function(event) {
				event.preventDefault();
				const id = $(this).data('id');
				fetchBookingData(id, $('#applicationInfoContent'), '#viewModal');
			});
			$('.btn-ready').click(function(event) {
				event.preventDefault();
				const id = $(this).data('id');
				fetchBookingData(id, $('#completeContent'), '#completeModal');
			});

			const urlParams = new URLSearchParams(window.location.search);
			const id = urlParams.get('id');

			if (urlParams.has('id') && urlParams.get('view') === 'true') {
				fetchBookingData(id, $('#applicationInfoContent'), '#viewModal');
			}

			function completeBooking(id) {
				const url = new URL(window.location.href);
				url.searchParams.set('complete', 'true');
				url.searchParams.set('id', id);
				window.history.pushState({}, '', url);
				fetchBookingData(id, $('#completeContent'), '#completeModal');
			}

			if (urlParams.get('complete') === 'true' && id) {
				completeBooking(id);
			}
		});
	</script>
</body>

</html>