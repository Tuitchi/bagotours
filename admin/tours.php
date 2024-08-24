<?php
include '../include/db_conn.php';
session_start();

session_regenerate_id();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <title>BaGoTours. Tours</title>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Tours</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
                <a href="#" class="btn-download" id="btn-download">
                    <i class='bx bx-plus'></i>
                    <span class="text">Add tours</span>
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Tourist Spot List</h3>
                        <i class='bx bx-search'></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $images = explode(',', $row['images']);
                            echo '<div class="data">';
                            foreach ($images as $image) {
                                echo '<img src="../upload/Tour Images/' . $image . '" alt="Tour Image">';
                            }
                            echo '<div class="content">';
                            echo '<h4>' . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . '</h4>';
                            echo '<p>Address: ' . htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p>Type: ' . htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p>Description: ' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p>Status: ' . htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<a href="edit-tour?id=' . urlencode($row['id']) . '" class="btn-edit">Edit</a>';
                            echo '<a href="#" class="btn-delete" data-tour-id="' . urlencode($row['id']) . '">Delete</a>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No tours found.</p>';
                    }
                    ?>
                </div>
            </div>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
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
                                        location.reload();
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
        });
    </script>
</body>

</html>