<?php
include '../include/db_conn.php';
session_start();


$pageRole = "admin";
require_once '../php/accValidation.php';

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

$query = "SELECT users.*, tours.* FROM tours RIGHT JOIN users ON users.id = tours.user_id WHERE tours.status = 0 OR tours.status = 2";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>BaGoTours. Pending Tour</title>
    <style>
        /* General Body Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
            padding-top: 60px;
            transition: all 0.3s ease;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 900px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .close,
        .close-zoom {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            z-index: 1000;
            transition: color 0.3s ease;
        }

        .close:hover,
        .close-zoom:hover {
            color: #ff5722;
        }

        /* Zoom Modal Styling */
        #zoomModal {
            display: none;
            position: fixed;
            z-index: 9999;
            /* Set a higher z-index for the zoom modal */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
            padding-top: 60px;
            transition: all 0.3s ease;
        }

        /* Main Modal Styling */
        #viewModal {
            display: none;
            position: fixed;
            z-index: 9998;
            /* Set a lower z-index for the main modal */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent black background */
            padding-top: 60px;
            transition: all 0.3s ease;
        }

        .zoom-modal-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            max-width: 900px;
            max-height: 90%;
        }

        .zoom-modal-content img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Close Button for Zoom */
        .close-zoom {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            z-index: 10000;
            /* Higher z-index to ensure it's clickable */
        }

        /* Close Button for Main Modal */
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            z-index: 1000;
        }

        .zoom-modal-content img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }


        /* Tour Image Section */
        .tour-images,
        .proof-images {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .tour-images img,
        .proof-images img {
            max-width: 100px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .tour-images img:hover,
        .proof-images img:hover {
            transform: scale(1.1);
        }

        /* Text Styling */
        h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5rem;
            color: #333;
            font-weight: bold;
        }

        p {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
        }

        strong {
            font-weight: 600;
        }

        /* Button Styling */
        .btn-group {
            position: absolute;
            bottom: 20px;
            right: 20px;
        }

        .accept-btn {
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .accept-btn.accept {
            background-color: #45a049;
        }
        .accept-btn.decline {
            background-color: #ff5722;
        }

        .accept-btn.accept:hover {
            background-color: #45a049;
        }
        .accept-btn.accept:active {
            background-color: #388e3c;
        }

        .accept-btn.decline:active {
            background-color: #388e3c;
        }
        .accept-btn.decline:hover {
            background-color: red;
        }


        /* Overflow Description */
        p[style*="overflow"] {
            white-space: normal;
            height: auto;
            overflow: visible;
            text-overflow: unset;
        }

        /* Zoom Image */
        .zoomable-img {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .zoomable-img:hover {
            transform: scale(1.05);
        }

        /* Toast Styling */
        .toast {
            position: fixed;
            top: 10px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .toast.show {
            display: block;
            animation: toast-slide-in 0.5s ease-out;
        }

        @keyframes toast-slide-in {
            0% {
                top: -50px;
            }

            100% {
                top: 10px;
            }
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
                        <h3>All Pending Tour</h3>
                        <i class='bx bx-search'></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th>Tour Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result) {
                                $counter = 1;
                                foreach ($result as $row) {
                                    $status = $row['status'];
                                    echo "<tr>";
                                    echo "<td>" . $counter++ . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "<td>" . $row['title'] . "</td>";
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
                                echo "<tr><td colspan='7'>No pending tours found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="zoomModal" class="modal">
                <span class="close-zoom">&times;</span>
                <div class="zoom-modal-content">
                    <img id="zoomImage" src="" alt="Zoomed Image">
                </div>
            </div>

            <div id="viewModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Pending Tours</h2>
                    <div id="applicationInfoContent"></div>
                </div>
            </div>
        </main>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        console.log(typeof Swal !== 'undefined' ? 'SweetAlert2 Loaded' : 'SweetAlert2 Not Loaded');

        $(document).ready(function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            $('.close').click(function () {
                $('#viewModal').fadeOut();
            });

            $('.view-btn').click(function (event) {
                event.preventDefault();
                const id = $(this).data('id');
                View(id);
            });

            function View(id) {
                let url = new URL(window.location.href);
                url.searchParams.set('view', 'true');
                url.searchParams.set('id', id);
                window.history.pushState({}, '', url);

                $.getJSON(`../php/get_pending.php?id=${id}`, function (data) {
                    if (data.success) {
                        const originalDate = new Date(data.pending.date_created);
                        const formattedDate = originalDate.toLocaleString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });

                        // Ensure img, proof_title, and proof_image are arrays
                        const images = data.pending.img ? data.pending.img.split(',') : [];
                        const proofTitles = data.pending.proof_title ? data.pending.proof_title.split(',') : [];
                        const proofImages = data.pending.proof_image ? data.pending.proof_image.split(',') : [];

                        $('#applicationInfoContent').html(`
            <h1 style="text-align: center;">${data.pending.title}</h1>
            
            <!-- Loop through 'img' array and display all images -->
            <div class="tour-images">
                ${images.map(image => `
                    <img src="../upload/Tour Images/${image}" alt="Tour Picture" width="100" class="zoomable-img">
                `).join('')}
            </div>
            
            <p><strong>Name:</strong> ${data.pending.name}</p>
            <p><strong>Email:</strong> ${data.pending.email}</p>
            <p><strong>Phone Number:</strong> ${data.pending.phone_number}</p>
            <p><strong>Address:</strong> ${data.pending.address}</p>
            
            <p style="overflow: hidden; white-space: normal; height: 5em; text-overflow: ellipsis;">
                <strong>Description:</strong> ${data.pending.description}
            </p>
            
            <!-- Loop through 'proof_title' array and display all titles -->
            <p><strong>Proof:</strong></p>
            <ul>
                ${proofTitles.map(title => `
                    <li>${title}</li>
                `).join('')}
            </ul>
            
            <!-- Loop through 'proof_image' array and display all images -->
            <div class="proof-images">
                ${proofImages.map(image => `
                    <img src="../upload/Permits/${image}" alt="Proof Picture" width="100" class="zoomable-img">
                `).join('')}
            </div>

            <p><strong>Date:</strong> ${formattedDate}</p>
            <div class="btn-group">
            <a class="accept-btn accept" href="../php/updatePending.php?status=1&tour_id=${data.pending.id}&user_id=${data.pending.user_id}">Accept</a>
            <a class="accept-btn decline" href="../php/updatePending.php?status=2&tour_id=${data.pending.id}&user_id=${data.pending.user_id}">Decline</a>
        </div>
        `);

                        // Show the modal
                        $('#viewModal').fadeIn();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Unable to fetch pending information.'
                        });
                    }
                }).fail(function () {
                    Toast.fire({
                        icon: 'error',
                        title: 'There was an error fetching the pending information.'
                    });
                });

            }
            const urlParams = new URLSearchParams(window.location.search);
            const view = urlParams.get('view');
            const id = urlParams.get('id');

            if (view === 'true' && id) {
                View(id);
            }

            $(document).on('click', '.zoomable-img', function () {
                const imgSrc = $(this).attr('src');
                $('#zoomImage').attr('src', imgSrc);
                $('#zoomModal').fadeIn();
            });
            $('.close-zoom').click(function () {
                $('#zoomModal').fadeOut();
            });
            $(window).click(function (event) {
                if ($(event.target).is('#zoomModal')) {
                    $('#zoomModal').fadeOut();
                }
            });
        });
    </script>

</body>

</html>