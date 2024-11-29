<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>BaGoTours || QR Code</title>
    <style>
        .tourQR {
            width: 300px;
            height: 350px;
            margin: 0 9px;
            display: inline-block;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        #add {
            position: relative;
            top: 35%;
            left: 32%;
            font-size: 100px;
            color: gray;
            transition: transform 0.3s;
        }

        #add-table:hover {
            box-shadow: 0 0 20px rgba(0, 255, 0, 3);
        }

        .tourQR:hover #add {
            color: green;
            transform: scale(1.1);
        }

        .qr-code-info {
            padding: 0 15px;
        }

        #qr-image {
            margin: 5px 5px;
            width: 98%;
            height: 250px;
            object-fit: contain;
        }

        .btn-form {
            margin-left: 25px;
            color: white;
            width: auto;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #52aa6f;
        }

        .btn-form:hover {
            background-color: green;
        }

        #tour {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 55%;
            border: gray 1px solid;
        }

        .btn {
            float: right;
            margin-top: 10px;
            margin-right: 10px;
            color: white;
            width: 90px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #btn-print {
            background-color: #3498db;
        }

        #btn-print:hover {
            background-color: #1c70b1;
        }

        #btn-delete {
            background-color: #e55f62;
        }

        #btn-delete:hover {
            background-color: red;
        }

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
            width: 40%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .form-group {
            margin: 50px 0;
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
                        <h3>Generated QR Code</h3>
                    </div>
                    <?php require_once '../func/func.php';
                    $qrcodes = getAllQR($conn, $user_id);
                    foreach ($qrcodes as $qr) { ?>
                        <div class="tourQR">
                            <img id="qr-image" src="<?php echo $qr['qr_code_path'] ?>">
                            <div class="qr-code-info">
                                <h4><?php echo $qr['title'] ?></h4>
                                <button class="btn" id="btn-delete" data-id="<?php echo $qr['id']; ?>">Delete</button>
                                <button class="btn" id="btn-print" data-image="<?php echo $qr['qr_code_path']; ?>" data-title="<?php echo $qr['title']; ?>">Print</button>
                            </div>
                        </div>

                    <?php } ?>

                    <div class="tourQR" id="add-table">
                        <i class='bx bx-plus' id="add"></i>
                    </div>
                </div>
            </div>
            </div>
        </main>
    </section>
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Generate QR Code</h2>
            <form id="addQR">
                <div class="form-group">
                    <label for="tour">Tours:</label>
                    <select name="tour" id="tour" required>
                        <option value="none" selected disabled hidden>Select an Option</option>
                        <?php $tours = getTouristSpots($conn, $user_id);
                        foreach ($tours as $tour) {
                        ?>
                            <option value="<?php echo $tour['title'] ?>|<?php echo $tour['id'] ?>"><?php echo $tour['title'] ?></option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="btn-form">Generate</button>
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
            $(document).on('click', '#btn-delete', function(event) {
                event.preventDefault();
                const QRid = $(this).data('id');
                deleteQR(QRid);
                console.log(QRid);
            });

            function deleteQR(QRid) {
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
                            url: `../php/deleteQR.php?id=${QRid}`,
                            type: 'POST',
                            data: $(this).serialize(),
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Toast.fire({
                                        icon: 'success',
                                        title: response.message
                                    });
                                    $('main').load(location.href + ' main > *');
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
            $(document).on('click', '#add-table', function() {
                $('#addModal').show();
            });
            $('.close').click(function() {
                $(this).closest('.modal').hide();
            });
            $(window).click(function(event) {
                if ($(event.target).hasClass('modal')) {
                    $(event.target).hide();
                }
            });

            $('#addQR').submit(function(event) {

                event.preventDefault();
                $.ajax({
                    url: '../php/generateQR.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            $('#addModal').hide();
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            $('main').load(location.href + ' main > *');
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText)
                        Toast.fire({
                            icon: 'error',
                            title: 'There was an error processing the request.'
                        });
                    }
                });
            });
            $(document).on('click', '#btn-print', function() {
                const qrImage = $(this).data('image');
                const qrTitle = $(this).data('title');

                const printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print QR Code</title>');
                printWindow.document.write('<style>body { text-align: center; font-family: Arial, sans-serif; }</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<h4>' + qrTitle + '</h4>');
                printWindow.document.write('<img src="' + qrImage + '" style="width: 100%; max-width: 500px;">');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
            });
        });
    </script>
</body>

</html>