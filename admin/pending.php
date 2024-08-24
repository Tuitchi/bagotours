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

$query = "SELECT users.*, tours.* FROM tours RIGHT JOIN users ON users.id = tours.user_id WHERE tours.status = 0;";
$result = mysqli_query($conn, $query);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


	<title>BaGoTours. Pending</title>
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
					<h1>Tours</h1>
                    <?php include 'includes/breadcrumb.php';?>
				</div>
				
			</div>
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Pending Tours</h3>
						<i class='bx bx-search' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<table>
						<thead>
							<tr>
								<th>#</th>
								<th>Full Name</th>
								<th>Email</th>
								<th>Title</th>
								<th>Address</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0) {
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status = $row['status'];
                                    echo "<tr>";
                                    echo "<td>".$counter++."</td>";
                                    echo "<td>".$row['firstname']." " .$row['lastname']."</td>";
                                    echo "<td>".$row['email']."</td>";
                                    echo "<td>".$row['title']."</td>";
                                    echo "<td>".$row['address']."</td>";
                                    echo "<td>";
                                        if ($status == 0) {
                                            echo "Pending";
                                        } elseif ($status == 1) {
                                            echo "Accepted";
                                        } else {
                                            echo "Declined";
                                        }
                                    echo "</td>";
                                    echo "<td><button class='view-btn' data-id='".$row['id']."' data-name='".$row['firstname']." ".$row['lastname']."' data-email='".$row['email']."' data-title='".$row['title']."' data-address='".$row['address']."' data-status='".$row['status']."'>View</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No users found.</td></tr>";
                            }
                            ?>
                        </tbody>
					</table>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

	<script src="../assets/js/script.js"></script>
    <script>
document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.getAttribute('data-id');
        const userName = this.getAttribute('data-name');
        const userEmail = this.getAttribute('data-email');
        const userTitle = this.getAttribute('data-title');
        const userAddress = this.getAttribute('data-address');
        const userStatus = this.getAttribute('data-status');

        Swal.fire({
            title: `User: ${userName}`,
            html: `
                <p>Email: ${userEmail}</p>
                <p>Title: ${userTitle}</p>
                <p>Address: ${userAddress}</p>
                <label for="status">Change Status:</label>
                <select id="status" class="swal2-input">
                    <option value="0" ${userStatus == 0 ? 'selected' : ''}>Pending</option>
                    <option value="1" ${userStatus == 1 ? 'selected' : ''}>Accepted</option>
                    <option value="2" ${userStatus == 2 ? 'selected' : ''}>Declined</option>
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update Status',
            preConfirm: () => {
                const newStatus = Swal.getPopup().querySelector('#status').value;
                return { userId, newStatus };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../php/update_status.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        Swal.fire('Updated!', 'User status has been updated.', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', 'There was a problem updating the status.', 'error');
                    }
                };
                xhr.send("id=" + result.value.userId + "&status=" + result.value.newStatus);
            }
        });
    });
});
</script>

</body>
</html>