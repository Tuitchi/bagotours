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
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
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
                    <h1>Pending</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>

            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>All Pendings</h3>
                        <i class='bx bx-search'></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
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
                                    echo "<td>" . $counter++ . "</td>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "<td>" . $row['title'] . "</td>";
                                    echo "<td>" . $row['address'] . "</td>";
                                    echo "<td>";
                                    if ($status == 0) {
                                        echo "Pending";
                                    } elseif ($status == 1) {
                                        echo "Accepted";
                                    } else {
                                        echo "Declined";
                                    }
                                    echo "</td>";
                                    echo "<td><button class='view-btn' data-id='" . $row['id'] . "'>View</button></td>";
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
    </section>
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Pending Tours</h2>
            <div id="applicationInfoContent"></div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            $('.close').click(function() {
                $('#viewModal').hide();
            });

            $('.view-btn').click(function(event) {
                event.preventDefault();
                const id = $(this).data('id');
                View(id);
            });

            function View(id) {
                $.getJSON(`../php/get_pending.php?id=${id}`, function(data) {
                    if (data.success) {
                        const originalDate = new Date(data.pending.date_created);
                        const formattedDate = originalDate.toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit',hour12: true});
                        $('#applicationInfoContent').html(`
                    <p style="text-align: center;">
                    <h1>${data.pending.title}</h1>
                    <img src="../upload/Tour Images/${data.pending.img}" alt="Tour Picture" width="100">
                    </p>
                    <p><strong>Name:</strong> ${data.pending.name}</p>
                    <p><strong>Email:</strong> ${data.pending.email}</p>
                    <p><strong>Phone Number:</strong> ${data.pending.phone_number}</p>
                    <p><strong>Address:</strong> ${data.pending.address}</p>
                    <p style="overflow: hidden; white-space: normal;height: 5em;text-overflow: -o-ellipsis-lastline; "><strong>Description:</strong> ${data.pending.description}</p>
                    <p><strong>Proof:</strong> ${data.pending.proof}</p>
                    <img src="../upload/Permits/${data.pending.proofImage} alt="Proof Picture" width="100">
                    <p><strong>Date:</strong> ${formattedDate}</p>
                    <a class="accept-btn" href="../php/updatePending.php?status=1&tour_id=${data.pending.tours.id}&user_id=${data.pending.users.id}">Accept</a>
                    <a class="accept-btn" href="../php/updatePending.php?status=2&tour_id=${data.pending.tours.id}">Decline</a>
                `);
                        $('#viewModal').show();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Unable to fetch pending information.'
                        });
                    }
                }).fail(function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'There was an error fetching the pending information.'
                    });
                });
            }
        });
    </script>

</body>

</html>