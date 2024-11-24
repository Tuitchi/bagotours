<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();


$pageRole = "admin";
require_once '../php/accValidation.php';

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

if (isset($_GET['event'])) {
    $event_code_raw = base64_decode($_GET['event']);
    $event_code = preg_replace(sprintf('/%s/', $salt), '', $event_code_raw);
    
    $stmt = $conn->prepare('SELECT * FROM events WHERE event_code =' . $event_code);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header("Location: event?status=404");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <!-- Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours - View Tour</title>
    <style>
        .tour-container {
            margin-top: 20px;
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .tour-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .tour-container img {
            width: 100%;
            border-radius: 10px;
        }

        .tour-container p {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .tour-container .btn-edit,
        .tour-container .btn-delete {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            text-align: center;
            border-radius: 5px;
            color: white;
            text-decoration: none;
        }

        .tour-container .btn-edit {
            background-color: #007bff;
        }

        .tour-container .btn-delete {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main id="main">
            <div class="head-title">
                <div class="left">
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>
            <div class="tour-container">
                <?php if (!empty($event)) { ?>
                    <h1><?php echo htmlspecialchars($event['event_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <img src="../upload/Event/<?php echo htmlspecialchars($event['event_image'], ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Tour Image">
                    <p><strong>Address:</strong>
                        <?php echo htmlspecialchars($event['event_location'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($event['event_type'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Description:</strong>
                        <?php echo nl2br(htmlspecialchars($event['event_description'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <h5><?php echo $event['status'] ?></h5>
                    <a class="btn-edit" href="#">Edit</a>
                    <a href="#" class="btn-delete" data-event-id="<?php echo $event['event_code']; ?>">Delete</a>

                <?php } else { ?>
                    <p>Event not found.</p>
                <?php } ?>
            </div>
        </main>
    </section>
    <script src="../assets/js/script.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelector('.btn-delete').addEventListener('click', function (e) {
            e.preventDefault();

            const tourId = this.getAttribute('data-tour-id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../php/delete_tour.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            'tour_id': tourId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if (data.success) {
                                Swal.fire('Deleted!', data.message, 'success').then(() => {
                                    window.location.href = 'tours.php';
                                });
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while deleting the tour.', 'error');
                        });
                }
            });
        });
    </script>
</body>

</html>