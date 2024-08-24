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

// Using prepared statements for security
$query = "SELECT * FROM users WHERE id <> ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die('Error preparing statement');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">

    <title>BaGoTours. Users</title>
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
                    <h1>User List</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
                <a href="#" class="btn-download" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class='bx bx-plus'></i>
                    <span class="text">Add user</span>
                </a>
            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>User List</h3>
                        <i class='bx bx-search'></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $counter = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($counter++) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>
                                            <a href='#' class='btn-view' data-id='" . htmlspecialchars($row['id']) . "'>View</a> |
                                            <a href='#' class='btn-edit'>Edit</a> |
                                            <a href='#' class='btn-delete' data-id='" . htmlspecialchars($row['id']) . "'>Delete</a>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No users found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </section>

    <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userInfoModalLabel">User Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userInfoContent">
                    <!-- Dynamic content goes here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-view').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const userId = this.getAttribute('data-id');
                    fetchUserInfo(userId);
                });
            });
        });

        function fetchUserInfo(userId) {
            fetch(`../php/get_user_info.php?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Add this line to inspect the response

                    if (data.success) {
                        const user = data.user;
                        // Update modal content
                        const userInfoContent = document.getElementById('userInfoContent');

                        userInfoContent.innerHTML = `
                    <p><strong>Name:</strong> ${user.name ? user.name : 'N/A'}</p>
                    <p><strong>Username:</strong> ${user.username ? user.username : 'N/A'}</p>
                    <p><strong>Email:</strong> ${user.email ? user.email : 'N/A'}</p>
                    <p><strong>Phone Number:</strong> ${user.phone_number ? user.phone_number : 'N/A'}</p>
                    <p><strong>Role:</strong> ${user.role ? user.role : 'N/A'}</p>
                    <p><strong>Date Created:</strong> ${user.date_created ? user.date_created : 'N/A'}</p>
                    <p><strong>Profile Picture:</strong> 
                        <img src="../uploads/${user.profile_picture}" alt="Profile Picture" width="100">
                    </p>
                `;

                        // Show the modal
                        const userInfoModal = new bootstrap.Modal(document.getElementById('userInfoModal'));
                        userInfoModal.show();
                    } else {
                        Swal.fire('Error!', 'Unable to fetch user information.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire('Error!', 'There was an error fetching the user information.', 'error');
                });
        }
    </script>
</body>

</html>