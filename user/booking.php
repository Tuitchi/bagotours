<?php
include_once '../include/db_conn.php';
include_once '../func/user_func.php';
session_start();

$user_id = $_SESSION["user_id"];

$booking = getBookingById($conn, $user_id);
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main {
            overflow-x: auto;
            display: block;
            height: 675px;
        }

        .title-button-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
            text-align: center;
            flex-grow: 1;
        }

        table {
            margin: auto;
            width: 80%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .table-responsive {
            width: 100%;
        }

        .btn-primary {
            float: right;
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 768px) {
            table {
                display: block;
                width: 100%;
                overflow-x: auto;
            }

            thead {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                border: none;
            }

            tr:nth-child(odd) {
                background-color: #f9f9f9;
            }

            th {
                position: sticky;
                top: 0;
                background-color: #f4f4f4;
                z-index: 2;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
            }

            td:before {
                position: absolute;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: right;
                font-weight: bold;
                background-color: #f4f4f4;
                color: #333;
                border-radius: 4px;
                content: attr(data-label);
                display: inline-block;
            }
        }

        @media screen and (max-width: 600px) {
            .title-button-wrapper {
                flex-direction: column;
            }
        }

        @media screen and (max-width: 480px) {
            .title-button-wrapper {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include('inc/topnav.php'); ?>
    <main class="main">
        <div class="title-button-wrapper">
            <h1>Bookings</h1>
            <a href="add_booking.php" class="btn btn-primary">Add Booking</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tour Title</th>
                        <th scope="col">Date Sched</th>
                        <th scope="col">No. People</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    foreach ($booking as $book) { ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($book['tour_title']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($book['date_scheduled']))); ?></td>
                            <td><?php echo htmlspecialchars($book['number_of_people']); ?></td>
                            <td><?php echo htmlspecialchars($book['status']); ?></td>
                            <td>
                                <a href="delete_booking.php?id=<?php echo $book['booking_id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php include('inc/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        <?php if (isset($status)): ?>
            Toast.fire({
                icon: '<?php echo $status; ?>',
                title: 'Your booking has been <?php echo $status === 'success' ? 'added' : ($status === 'edit' ? 'updated' : 'deleted'); ?> successfully!'
            });
        <?php endif; ?>
    </script>

</body>

</html>