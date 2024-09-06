<?php
session_start();

?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .title-button-wrapper {
            display: block;
            width: 100%;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0 auto;
            text-align: center;
            flex-grow: 1;
        }
        table {
            width: 100%;
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
        tr:hover {background-color: #f5f5f5;}

        @media screen and (max-width: 768px) {
            table {
                display: block;
                width: 100%;
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
                color: white;
                border-radius: 4px;
                transition: background-color 0.3s ease;
            }
        }

        .table-responsive {
            width: 100%;
        }

        .btn btn-primary {
            float: right;
            right: 0;
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
                    <tr>
                        <td>1</td>
                        <td>Sample Tour</td>
                        <td>2024-09-15</td>
                        <td>5</td>
                        <td>Active</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning">Edit</a>
                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
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
        <?php if ($status === 'success'): ?>
            Toast.fire({
                icon: 'success',
                title: 'Your booking has been added successfully!'
            });
        <?php endif; ?>
        <?php if ($status === 'error'): ?>
            Toast.fire({
                icon: 'error',
                title: 'Error occured while adding booking!'
            });
        <?php endif; ?>
        <?php if ($status === 'edit'): ?>
            Toast.fire({
                icon: 'success',
                title: 'Your booking has been updated successfully!'
            });
        <?php endif; ?>
        <?php if ($status === 'delete'): ?>
            Toast.fire({
                icon: 'success',
                title: 'Your booking has been deleted successfully!'
            });
        <?php endif; ?>
    </script>

    <?php include('inc/footer.php'); ?>
</body>

</html>