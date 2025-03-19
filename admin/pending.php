<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];

$query = "SELECT users.*, tours.* FROM tours RIGHT JOIN users ON users.id = tours.user_id WHERE tours.status = 'Pending' OR tours.status = 'Rejected' Order BY tours.status";
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

    <title>BaGoTours || Pending Tour</title>
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
            z-index: 9999;
        }

        /* Main Modal Styling */
        #viewModal {
            display: none;
            z-index: 1;
        }

        textarea {
            resize: none;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 0;
            width: 100%;
            overflow-y: auto;
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

        .view-btn {
            background-color: #45a049;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s ease;
        }

        button {
            border: none;
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
                                    echo '<td style="color: ' . ($row['status'] == 'Pending' ? "green" : "red") . '">' . $row['status'] . '</td>';
                                    if ($row['status'] == 'Pending') {
                                        echo "<td><button class='view-btn' data-id='" . $row['id'] . "'>View</button></td>";
                                    } else {
                                        echo "<td><button class='view-btn' disabled>Expiry Date : " . $row['expiry'] . "</button></td>";
                                    }
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

            <div id="rejectModal" class="modal">
                <div class="modal-content">
                    <span class="close reject">&times;</span>
                    <h2>Reason for Rejection</h2>
                    <form id="rejectForm">
                        <textarea name="reason" id="reason" required></textarea>
                        <input type="hidden" name="tour_id" id="rejectTourId">
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="accept-btn decline reject-btn">Submit</button>
                    </form>
                </div>
            </div>
            <div id="approvalModal" class="modal">
                <div class="modal-content">
                    <span class="close accept-close">&times;</span>
                    <h2>Are you sure?</h2>
                    <form id="acceptForm">
                        <input type="hidden" name="tour_id" id="acceptTourID">
                        <input type="hidden" name="user_id" id="acceptUserID">
                        <input type="hidden" name="status" value="Confirmed">
                        <button type="submit" class="accept-btn accept">Yes</button>
                        <button type="button" class="accept-btn decline accept-close">No</button>
                    </form>
                </div>
            </div>
        </main>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    F
    <script>
        $(document).ready(function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            $(document).on("submit", "#acceptForm", function (e) {
                e.preventDefault(); // Prevent default form submission behavior
                console.log("Running AJAX request");

                const formData = new FormData(this);
                const submitButton = $(this).find(".accept"); // Select the submit button    for (let [key, value] of formData.entries()) {
                for (let [key, value] of formData.entries()) {
                    console.log(key + ": " + value); // Logs each key-value pair in FormData
                };
                submitButton.prop("disabled", true).text("Accepting...");

                // Check if the form data is being collected properly
                console.log("Form data:", formData);

                // Perform the AJAX request
                $.ajax({
                    url: "../php/updatePending.php",
                    type: "POST",
                    data: formData,
                    processData: false, // Prevent jQuery from processing the FormData object
                    contentType: false, // Prevent jQuery from setting the content type header
                    success: function (data) {
                        console.log("Response:", data); // Check the response in the console
                        if (data.success) {
                            submitButton.text("Accepted"); // Change text to "Accepted"
                            Toast.fire({
                                icon: "success",
                                title: "Accepted successfully!",
                            }).then(() => {
                                location.reload(); // Reload the page after the toast
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "Approval Error: " + data.message,
                            });
                            // Re-enable the button if submission fails
                            submitButton.prop("disabled", false).text("Accept");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", error);
                        Toast.fire({
                            icon: "error",
                            title: "An error occurred while submitting the rejection.",
                        });
                        // Re-enable the button if an error occurs
                        submitButton.prop("disabled", false).text("Accept");
                    },
                });
            });


            $("#rejectForm").on("submit", function (e) {
                e.preventDefault(); // Prevent the default form submission

                const formData = new FormData(this);
                const submitButton = $(this).find(".reject-btn"); // Select the submit button

                // Disable the button while processing
                submitButton.prop("disabled", true).text("Submitting...");

                $.ajax({
                    url: "../php/updatePending.php",
                    type: "POST",
                    data: formData,
                    processData: false, // Prevent jQuery from processing the FormData object
                    contentType: false, // Prevent jQuery from setting the content type header
                    success: function (data) {
                        if (data.success) {
                            submitButton.text("Submitted"); // Change text to "Submitted"
                            Toast.fire({
                                icon: "success",
                                title: "Rejection submitted successfully!",
                            })
                            location.reload(); // Reload the page after the toast
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "Failed to submit rejection: " + data.message,
                            });
                            // Re-enable the button if submission fails
                            submitButton.prop("disabled", false).text("Submit");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", error);
                        Toast.fire({
                            icon: "error",
                            title: "An error occurred while submitting the rejection.",
                        });
                        // Re-enable the button if an error occurs
                        submitButton.prop("disabled", false).text("Submit");
                    },
                });
            });
            function toggleModal(modalId, action = "show") {
                if (action === "show") {
                    $(`#${modalId}`).fadeIn();
                } else {
                    $(`#${modalId}`).fadeOut();
                }
            }

            function displayError(message) {
                Toast.fire({
                    icon: 'error',
                    title: message || 'An error occurred. Please try again.'
                });
            }

            $(document).on('click', '.acceptOpenModal', function () {
                const tourId = $(this).data('id');
                const userId = $(this).data('user');

                // Debugging
                console.log('Tour ID:', tourId); // Should log the tour ID
                console.log('User ID:', userId); // Should log the user ID

                if (tourId && userId) {
                    $('#acceptTourID').val(tourId);
                    $('#acceptUserID').val(userId);
                    $('#approvalModal').show(); // Show the modal
                } else {
                    console.error('Tour ID or User ID is missing');
                }
            });
            $(document).on('click', '.reject-btn', function () {
                const tourId = $(this).data('id');
                const userId = $(this).data('user');

                // Debugging
                console.log('Tour ID:', tourId); // Should log the tour ID
                console.log('User ID:', userId); // Should log the user ID

                if (tourId && userId) {
                    $('#rejectTourId').val(tourId);
                    $('#user_id').val(userId);
                    $('#rejectModal').show(); // Show the modal
                } else {
                    console.error('Tour ID or User ID is missing');
                }
            });

            $('.close').click(function () {
                toggleModal('viewModal', 'hide');
            });

            $('.close.reject').click(function () {
                toggleModal('rejectModal', 'hide');
            });
            $('.accept-close').click(function () {
                toggleModal('approvalModal', 'hide');
            });

            $('.close-zoom').click(function () {
                toggleModal('zoomModal', 'hide');
            });

            $(document).on('click', '.view-btn', function (event) {
                event.preventDefault();
                const id = $(this).data('id');
                ViewPendingTour(id);
            });

            function ViewPendingTour(id) {
                $.getJSON(`../php/get_pending.php?id=${id}`, function (data) {
                    if (data.success) {
                        const formattedDate = new Date(data.pending.date_created).toLocaleString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });

                        const images = data.pending.img ? data.pending.img.split(',') : [];
                        const proofTitles = data.pending.proof_title ? data.pending.proof_title.split(',') : [];
                        const proofImages = data.pending.proof_image ? data.pending.proof_image.split(',') : [];

                        $('#applicationInfoContent').html(`
                        <h1 style="text-align: center;">${data.pending.title}</h1>
                        <div class="tour-images">
                            ${images.map(image => `
                                <img src="../upload/Tour Images/${image}" alt="Tour Picture" width="100" class="zoomable-img">
                            `).join('')}
                        </div>
                        <p><strong>Email:</strong> ${data.pending.email}</p>
                        <p><strong>Phone Number:</strong> ${data.pending.phone_number}</p>
                        <p><strong>Address:</strong> ${data.pending.address}</p>
                        <p><strong>Description:</strong> ${data.pending.description}</p>
                        <p><strong>Proof:</strong></p>
                        <ul>
                            ${proofTitles.map(title => `<li>${title}</li>`).join('')}
                        </ul>
                        <div class="proof-images">
                            ${proofImages.map(image => `
                                <img src="../upload/Permits/${image}" alt="Proof Picture" width="100" class="zoomable-img">
                            `).join('')}
                        </div>
                        <p><strong>Date:</strong> ${formattedDate}</p>
                        <div class="btn-group">
                            <button class="accept-btn accept acceptOpenModal" data-id="${data.pending.id}" data-user="${data.pending.user_id}">Accept</button>
                            <button class="accept-btn decline reject-btn" data-id="${data.pending.id}" data-user="${data.pending.user_id}">Decline</button>
                        </div>
                    `);
                        toggleModal('viewModal', 'show');
                    } else {
                        displayError('Unable to fetch pending information.');
                    }
                }).fail(function () {
                    displayError('There was an error fetching the pending information.');
                });
            }

            $(document).on('click', '.zoomable-img', function () {
                const imgSrc = $(this).attr('src');
                $('#zoomImage').attr('src', imgSrc);
                toggleModal('zoomModal', 'show');
            });

            $(window).click(function (event) {
                if ($(event.target).is('#zoomModal')) toggleModal('zoomModal', 'hide');
                if ($(event.target).is('#rejectModal')) toggleModal('rejectModal', 'hide');
                if ($(event.target).is('#viewModal')) toggleModal('viewModal', 'hide');
            });

        });
    </script>


</body>

</html>