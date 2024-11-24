<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM events ORDER BY event_code DESC");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon"
        href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">

    <title>BaGoTours || Event</title>
    <style>
        .order {
            overflow: auto;
            scrollbar-width: none;
        }

        .order::-webkit-scrollbar {
            display: none;
        }

        .data {
            margin-top: 10px;
            flex-direction: column;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            border-bottom: 1px solid #ccc;
        }

        .data .img {
            width: 100%;
            height: 200px;
        }

        .data .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .data .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .data .content h4 {
            margin: 0 0 10px;
            font-size: 1.5em;
        }

        .data .content p {
            color: gray;
            margin: 0 0 5px;
        }

        .content {
            position: relative;
            flex-grow: 1;
            width: 100%;
            padding-right: 60px;
        }

        .title {
            padding: 0px 25px 0px 0px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown {
            position: absolute;
            bottom: 0;
            right: 25px;
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
            z-index: 5;
            display: none;
        }

        .actions a {
            text-align: start;
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
                <a class="btn-download" id="btn-download" href="add-event">
                    <i class='bx bx-plus'></i>Add event
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Event List</h3>
                        <div class="search-container">
                            <i class='bx bx-search' id="search-icon"></i>
                            <input type="text" id="search-input" placeholder="Search...">
                        </div>
                        <i class='bx bx-filter'></i>
                    </div>
                    <?php if (!empty($events)) {
                        foreach ($events as $event) { ?>
                            <div class="data">
                                <div class="img">
                                    <img src="../upload/Event/<?php echo htmlspecialchars($event['event_image'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="Tour Image">
                                </div>
                                <div class="content">
                                    <div class="title">
                                        <h4><?php echo htmlspecialchars($event['event_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                        <span style="font-size:13px">
                                            <?php echo htmlspecialchars($event['event_type'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>
                                    <p>📍 <?php echo htmlspecialchars($event['event_location'], ENT_QUOTES, 'UTF-8'); ?></p>


                                    <p>📅 <?php echo htmlspecialchars($event['event_date_start'], ENT_QUOTES, 'UTF-8'); ?>
                                        - <?php echo htmlspecialchars($event['event_date_end'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p id="stats"
                                        style="color:<?php echo ($event['status'] == 'upcoming') ? 'blue' : (($event['status'] == 'completed') ? 'green' : 'red'); ?>;">
                                        <?php echo htmlspecialchars($event['status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <div class="dropdown">
                                        <button id="drop">Manage<i class="bx bx-caret-down"></i></button>
                                        <div class="actions" style="display:none">
                                            <a href="#" class="btn" id="view"
                                                data-id="<?php echo htmlspecialchars($event['event_code'], ENT_QUOTES, 'UTF-8'); ?>"><i
                                                    class="bx bx-folder"></i>View</a>
                                            <a href="edit-event?id=<?php echo htmlspecialchars($event['event_code'], ENT_QUOTES, 'UTF-8'); ?>" class="btn" id="edit"><i
                                                    class="bx bx-edit-alt"></i>Edit</a>
                                            <a href="#" class="btn" id="delete"
                                                data-id="<?php echo htmlspecialchars($event['event_code'], ENT_QUOTES, 'UTF-8'); ?>"><i
                                                    class="bx bx-trash"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        <?php }
                    } else {
                        echo "<p class='empty-message'>No event found.</p>";
                    } ?>
                </div>
            </div>
        </main>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        $(document).ready(function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            $(document).on('click', '#delete', function (event) {
                event.preventDefault();
                const event_code = $(this).data('id');
                deleteEvent(event_code);
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

            function deleteEvent(eventId) {
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
                            url: `../php/delete_event.php?id=${eventId}`,
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
                            error: function (xhr, status, error) {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'An error occurred. Please try again.'
                                });
                                console.error('AJAX Error:', status, error);
                            }
                        });
                    }
                });
            }

        });
    </script>
</body>

</html>