<?php
include '../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id <> ?";
$stmt = $conn->prepare($query) or die('Error preparing statement');
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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
                    <table id="userTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Date Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $counter = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($counter++) . "</td>
                                            <td>" . htmlspecialchars($row['name']) . "</td>
                                            <td>" . htmlspecialchars($row['email']) . "</td>
                                            <td>" . htmlspecialchars($row['role']) . "</td>
                                            <td>" . date('M. d, Y', strtotime($row['date_created'])) . "</td>

                                            <td>
                                                <a href='#' class='btn-view' data-id='" . htmlspecialchars($row['id']) . "'>View</a> |
                                                <a href='#' class='btn-edit' data-id='" . htmlspecialchars($row['id']) . "'>Edit</a> |
                                                <a href='#' class='btn-delete' data-id='" . htmlspecialchars($row['id']) . "'>Delete</a>
                                            </td>
                                          </tr>";
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

    <div id="viewUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>User Information</h2>
            <div id="userInfoContent"></div>
        </div>
    </div>
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit User Information</h2>
            <div id="editUserContent"></div>
        </div>
    </div>

    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add User</h2>
            <form id="addUserForm">
                <div class="form-group">
                    <label for="userName">Name:</label>
                    <input type="text" name="userName" id="userName" required>
                </div>
                <div class="form-group">
                    <label for="userEmail">Email:</label>
                    <input type="email" name="userEmail" id="userEmail" required>
                </div>
                <div class="form-group">
                    <label for="userPassword">Password:</label>
                    <input type="password" name="userPassword" id="userPassword" required>
                </div>
                <div class="form-group">
                    <label for="userRole">Role:</label>
                    <select name="userRole" id="userRole" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-submit">Add User</button>
                </div>
            </form>
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
            $('.btn-view').click(function(event) {
                event.preventDefault();
                const userId = $(this).data('id');
                viewUser(userId);
            });
            $('.btn-edit').click(function(event) {
                event.preventDefault();
                const userId = $(this).data('id');
                editUser(userId);
            });
            $('.btn-delete').click(function(event) {
                event.preventDefault();
                const userId = $(this).data('id');
                deleteUser(userId);
            });

            function viewUser(userId) {
                $.getJSON(`../php/get_user_info.php?id=${userId}`, function(data) {
                    if (data.success) {
                        $('#userInfoContent').html(`
                            <p style="text-align: center;">
                                <img src="../upload/Profile Pictures/${data.user.profile_picture}" alt="Profile Picture" width="100">
                            </p>
                            <p><strong>Name:</strong> ${data.user.name || 'N/A'}</p>
                            <p><strong>Username:</strong> ${data.user.username || 'N/A'}</p>
                            <p><strong>Email:</strong> ${data.user.email || 'N/A'}</p>
                            <p><strong>Phone Number:</strong> ${data.user.phone_number || 'N/A'}</p>
                            <p><strong>Role:</strong> ${data.user.role || 'N/A'}</p>
                            <p><strong>Date Created:</strong> ${data.user.date_created || 'N/A'}</p>
                        `);
                        $('#viewUserModal').show();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Unable to fetch user information.'
                        });
                    }
                }).fail(function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'There was an error fetching the user information.'
                    });
                });
            }

            function editUser(userId) {
                $.getJSON(`../php/get_user_info.php?id=${userId}`, function(data) {
                    if (data.success) {
                        $('#editUserContent').html(`
                        <form id="editUserForm">
                            <input type="hidden" name="editUserId" value="${userId}">
                            <div class="form-group">
                                <label for="editName">Name</label>
                                <input type="text" name="editName" id="editName" value="${data.user.name || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editEmail">Email</label>
                                <input type="email" name="editEmail" id="editEmail" value="${data.user.email || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editPhoneNumber">Phone Number</label>
                                <input type="text" name="editPhoneNumber" id="editPhoneNumber" value="${data.user.phone_number || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editUsername">Username</label>
                                <input type="text" name="editUsername" id="editUsername" value="${data.user.username || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editRole">Role</label>
                                <select name="editRole" id="editRole" required>
                                    <option value="admin" ${data.user.role === 'admin' ? 'selected' : ''}>Admin</option>
                                    <option value="owner" ${data.user.role === 'owner' ? 'selected' : ''}>Owner</option>
                                    <option value="user" ${data.user.role === 'user' ? 'selected' : ''}>User</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-submit">Save Changes</button>
                        </form>
                    `);
                        $('#editUserModal').show();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Unable to fetch user information.'
                        });
                    }
                }).fail(function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'There was an error fetching the user information.'
                    });
                });
            }

            function deleteUser(userId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `../php/delete_user.php?id=${userId}`,
                            type: 'POST',
                            data: $(this).serialize(),
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
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
                        });
                    }
                });
            }

            // Close modal
            $('.close').click(function() {
                $(this).closest('.modal').hide();
            });
            $('#addUserForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: '../php/admin/addUser.php',
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
            $(document).on('submit', '#editUserForm', function(event) {
                event.preventDefault();
                $.ajax({
                    url: '../php/admin/editUser.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editUserModal').hide();
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
            $(window).click(function(event) {
                if ($(event.target).hasClass('modal')) {
                    $(event.target).hide();
                }
            });
        });
    </script>
</body>

</html>