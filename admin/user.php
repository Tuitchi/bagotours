<?php
include '../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

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
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>BaGoTours. Users</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
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
                    <h1>User List</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
                <a href="#" class="btn-download" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class='bx bx-plus'></i>
                    <span class="text">Add user</span>
                </a>
            </div>
            <div class="table-data" id="userTableContainer">
                <div class="order">
                    <div class="head">
                        <h3>User List</h3>
                        <div class="search-container">
                            <i class='bx bx-search' id="search-icon"></i>
                            <input type="text" id="search-input" placeholder="Search...">
                        </div>
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

    <!-- Modal for viewing user information -->
    <div id="viewUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>User Information</h2>
            <div id="userInfoContent"></div>
        </div>
    </div>

    <!-- Modal for adding new user -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add User</h2>
            <form id="addUserForm">
                <div id="userInfoContent">
                    <div class="form-group">
                        <label for="userName">Name:</label>
                        <input type="text" name="userName" id="userName" >
                    </div>
                    <div class="form-group">
                        <label for="userEmail">Email:</label>
                        <input type="email" name="userEmail" id="userEmail" >
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Password:</label>
                        <input type="password" name="userPassword" id="userPassword" >
                    </div>
                    <div class="form-group">
                        <label for="userRole">Role:</label>
                        <select name="userRole" id="userRole" >
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-submit">Add User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-view').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const userId = this.getAttribute('data-id');
                    fetchUserInfo(userId);
                });
            });

            const modal = document.getElementById('viewUserModal');
            const closeModalButton = modal.querySelector('.close');

            function fetchUserInfo(userId) {
                fetch(`../php/get_user_info.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.user;
                            const userInfoContent = document.getElementById('userInfoContent');
                            userInfoContent.innerHTML = `
                                <p style="text-align: center;">
                                    <img src="../upload/Profile Pictures/${user.profile_picture}" alt="Profile Picture" width="100">
                                </p>
                                <p><strong>Name:</strong> ${user.name ? user.name : 'N/A'}</p>
                                <p><strong>Username:</strong> ${user.username ? user.username : 'N/A'}</p>
                                <p><strong>Email:</strong> ${user.email ? user.email : 'N/A'}</p>
                                <p><strong>Phone Number:</strong> ${user.phone_number ? user.phone_number : 'N/A'}</p>
                                <p><strong>Role:</strong> ${user.role ? user.role : 'N/A'}</p>
                                <p><strong>Date Created:</strong> ${user.date_created ? user.date_created : 'N/A'}</p>
                            `;
                            modal.style.display = 'block';
                        } else {
                            alert('Unable to fetch user information.');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('There was an error fetching the user information.');
                    });
            }

            closeModalButton.onclick = function() {
                modal.style.display = 'none';
            };

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
        });

        $(document).ready(function() {
            $('#addUserForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: '../php/admin/usercode.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#addUserModal').hide();

                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            $('#userTableContainer').load(location.href + ' #userTableContainer > *');
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'There was an error processing the request.'
                        });
                    }
                });
            });

            $('.close').click(function() {
                $(this).closest('.modal').hide();
            });

            $(window).click(function(event) {
                if ($(event.target).hasClass('modal')) {
                    $(event.target).hide();
                }
            });
        });
    </script>
</body>

</html>