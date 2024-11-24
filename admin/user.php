<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Pagination settings
$results_per_page = 8;
$counter = ($page - 1) * $results_per_page + 1;
$start_from = ($page - 1) * $results_per_page;

// Query for total count with filters
$count_query = "SELECT COUNT(*) as total FROM users WHERE role != 'admin'";
$params = [];

if (!empty($search)) {
    $count_query .= " AND (name LIKE :search OR username LIKE :search OR email LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
$stmt = $conn->prepare($count_query);
$stmt->execute($params);
$count_row = $stmt->fetch();
$total_records = $count_row['total'];

$total_pages = ceil($total_records / $results_per_page);
$data_query = "SELECT * FROM users WHERE role != 'admin'";
$params = [];

if (!empty($search)) {
    $data_query .= " AND (name LIKE :search OR username LIKE :search OR email LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$data_query .= " LIMIT $start_from, $results_per_page";

// Prepare and execute the data query
$stmt = $conn->prepare($data_query);
$stmt->execute($params);

// Fetch students data
$users = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">

<tl>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>BaGoTours. Users</title>
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown button {
            color: black;
            padding: 7px 10px;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            border-radius: 5px;
            width: 120px;
        }

        .dropdown button:hover {
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        }

        .actions {
            position: absolute;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            display: none;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            color: black;
            font-size: 1.1em;
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
                        <?php include 'includes/breadcrumb.php'; ?>
                    </div>
                    <a href="add-user" class="btn-download">
                        <i class='bx bx-plus'></i>
                        <span class="text">Add User</span>
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
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Date Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($users) > 0): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $counter++; ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td><?php echo date('M. d, Y', strtotime($user['date_created'])); ?></td>
                                            <td>
                                                <div class='dropdown'>
                                                    <button id='drop'>Manage<i class='bx bx-caret-down'></i></button>
                                                    <div class='actions' style='display:none'>
                                                        <a href='#' class='btn' id='view' data-id='<?php echo htmlspecialchars($row['
                                                            id']) ?>'><i class='bx bx-folder'></i>View</a>
                                                        <a href='#' class='btn' id='edit' data-id='<?php echo htmlspecialchars($row['
                                                            id']) ?>'><i class='bx bx-edit-alt'></i>Edit</a>
                                                        <a href='#' class='btn' id='delete' data-id='<?php echo htmlspecialchars($row['
                                                            id']) ?>'><i class='bx bx-trash'></i>Delete</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan=" 6">No user found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <?php
                            // Ensure $page is an integer
                            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($page > 1): ?>
                                <a class="btn" href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
                            <?php endif; ?>

                            <?php if ($start_page > 1): ?>
                                <a href="?page=1">1</a>
                                <?php if ($start_page > 2): ?>
                                    <span>...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <span>...</span>
                                <?php endif; ?>
                                <a href="?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                            <?php endif; ?>

                            <?php if ($page < $total_pages): ?>
                                <a class="btn" href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                            <?php endif; ?>
                        </div>
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

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../assets/js/script.js"></script>

        <script>
            $(document).ready(function () {
                $(document).on('click', '#drop', function (event) {
                    event.preventDefault();

                    // Close any open dropdowns first (optional, to hide others)
                    $('.actions').not($(this).next('.actions')).hide();

                    // Toggle the display of the clicked dropdown
                    const actions = $(this).next('.actions'); // This targets the .actions that comes after the clicked button

                    // Check the current state and toggle it
                    if (actions.css('display') === 'none') {
                        actions.css('display', 'block');  // Show the dropdown
                    } else {
                        actions.css('display', 'none');   // Hide the dropdown
                    }
                });
                const $searchInput = $('#search-input');
                const $studentTableBody = $('#userTable tbody');
                const $paginationContainer = $('.pagination');

                // Function to fetch filtered data from the server
                function fetchFilteredData() {
                    const search = $searchInput.val().trim();
                    const page = new URLSearchParams(window.location.search).get('page') || 1;

                    const params = new URLSearchParams({
                        search,
                        page
                    });

                    // Fetch filtered data via AJAX
                    $.ajax({
                        url: `?${params.toString()}`,
                        type: 'GET',
                        success: function (html) {
                            const $doc = $(html);

                            // Update the table body
                            const $newTableBody = $doc.find('#userTable tbody');
                            $studentTableBody.html($newTableBody.html());

                            // Update the pagination
                            const $newPagination = $doc.find('.pagination');
                            $paginationContainer.html($newPagination.html());
                        },
                        error: function (error) {
                            console.error('Error fetching data:', error);
                        }
                    });
                }

                // Add event listeners for search and filters
                $searchInput.on('input', fetchFilteredData);
                $departmentFilter.on('change', fetchFilteredData);
                $yearFilter.on('change', fetchFilteredData);

                // Pagination links event listener
                $paginationContainer.on('click', 'a', function (e) {
                    e.preventDefault();
                    const url = new URL($(this).attr('href'));
                    const page = url.searchParams.get('page');
                    const params = new URLSearchParams(window.location.search);

                    params.set('page', page);
                    history.pushState(null, '', `?${params.toString()}`);
                    fetchFilteredData();
                });
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                $(document).on('click', '#drop', function (event) {
                    event.preventDefault();

                    // Close any open dropdowns first (optional, to hide others)
                    $('.actions').not($(this).next('.actions')).hide();

                    // Toggle the display of the clicked dropdown
                    const actions = $(this).next('.actions'); // This targets the .actions that comes after the clicked button

                    // Check the current state and toggle it
                    if (actions.css('display') === 'none') {
                        actions.css('display', 'block');  // Show the dropdown
                    } else {
                        actions.css('display', 'none');   // Hide the dropdown
                    }
                });

                // Close the dropdown when clicking outside of it
                $(document).click(function (event) {
                    if (!$(event.target).closest('.dropdown').length) {
                        $('.actions').hide(); // Hide all dropdowns if the click is outside of the dropdown
                    }
                });
                $(document).on('click', '#view', function (event) {
                    event.preventDefault();
                    const userId = $(this).data('id');
                    viewUser(userId);
                });
                $(document).on('click', '#delete', function (event) {
                    event.preventDefault();
                    const userId = $(this).data('id');
                    deleteUser(userId);
                });

                function viewUser(userId) {
                    $.getJSON(`../php/get_user_info.php?id=${userId}`, function (data) {
                        if (data.success) {
                            $('#userInfoContent').html(`
                            <p style="text-align: center;">
                                <img src="../upload/Profile Pictures/${data.user.profile_picture}" alt="Profile Picture" width="100" height="100">
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
                    }).fail(function () {
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
                                success: function (response) {
                                    if (response.success) {
                                        Toast.fire({
                                            icon: 'success',
                                            title: response.message
                                        });
                                        $('main').load(location.href + ' main > ');
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
                $('#addUserForm').submit(function (event) {
                    event.preventDefault();
                    $.ajax({
                        url: '../php/admin/addUser.php',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function (response) {
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
                        error: function () {
                            Toast.fire({
                                icon: 'error',
                                title: 'There was an error processing the request.'
                            });
                        }
                    });
                });
                $(document).on('submit', '#editUserForm', function (event) {
                    event.preventDefault();
                    $.ajax({
                        url: '../php/admin/editUser.php',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function (response) {
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
                        error: function () {
                            Toast.fire({
                                icon: 'error',
                                title: 'There was an error processing the request.'
                            });
                        }
                    });
                });
                $(window).click(function (event) {
                    if ($(event.target).hasClass('modal')) {
                        $(event.target).hide();
                    }
                });

                $('.close').click(function () {
                    $(this).closest('.modal').hide();
                });
            });
        </script>
    </body>

</html>