<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();
$user_id = $_SESSION['user_id'];

$status = isset($_GET["status"]) ? $_GET["status"] : '';
$query = isset($_GET['search']) ? $_GET['search'] : null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 100; // Number of items per page
$data = getAllToursforAdmin($conn, $query, $page, $limit);

$tours = $data['tours'];
$total = $data['total'];
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/tour.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours || Tours</title>
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
                <a class="btn-download" id="btn-download" href="add-tour">
                    <i class='bx bx-plus'></i>Add Tours
                </a>
            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Tourist Spot List</h3>
                        <div class="search-container">
                            <form method="GET" class="search-container">
                                <i class='bx bx-search' id="search-icon"></i>
                                <input type="text" name="search" id="search-input" placeholder="Search..."
                                    value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>">
                            </form>
                        </div>
                        <i class='bx bx-filter'></i>
                    </div>
                    <?php if (!empty($tours)): ?>
                        <?php foreach ($tours as $row): ?>
                            <?php
                            $images = explode(',', $row['img']);
                            $mainImage = htmlspecialchars($images[0], ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="data">
                                <div class="img">
                                    <a href="edit-tour?id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <img src="../upload/Tour Images/<?php echo $mainImage; ?>" alt="Main Tour Image"></a>
                                </div>

                                <div class="content">
                                    <h4><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                                        <p style="font-size:13px">
                                            <?php echo htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8'); ?>
                                        </p>
                                    </h4>

                                    <p>📍 <?php echo htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8'); ?></p>


                                    <p id="stats" style="color: <?php echo ($row['status'] == 'Active') ? 'green' : 'red'; ?>;">
                                        <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>

                                    <div class="dropdown">
                                        <button id="drop">Manage<i class="bx bx-caret-down"></i></button>
                                        <div class="actions" style="display:none">
                                            <a href="#" class="btn" id="view"
                                                data-id="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>"><i
                                                    class="bx bx-folder"></i>View</a>
                                            <a href="edit-tour?id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="btn" id="edit">
                                                <i class="bx bx-edit-alt"></i>Edit</a>
                                            <a href="accommodation-fees-management?id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="btn" id="edit">
                                                <i class='bx bx-dollar-circle'></i>Pricing</a>
                                            <a href="#" class="btn" id="delete"
                                                data-id="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>"><i
                                                    class="bx bx-trash"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No tours found.</p>
                    <?php endif; ?>
                </div>

            </div>
            <div id="viewModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="Close()">&times;</span>
                    <div id="tour-info-card"></div>
                </div>
            </div>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#search-input").on("keypress", function (event) {
                if (event.which === 13) {
                    $(this).closest("form").submit();
                }
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
                const actions = $(this).next('.actions');
                $('.actions').not(actions).hide();
                actions.toggle();
            });

            $(document).click(function (event) {
                if (!$(event.target).closest('.dropdown').length) {
                    $('.actions').hide();
                }
            });

            $(document).on('click', '#delete', function (event) {
                event.preventDefault();
                const id = $(this).data('id');
                Delete(id);
            });

            $(document).on('click', '#view', function (event) {
                event.preventDefault();
                const id = $(this).data('id');
                View(id);
            });

            function View(tourId) {
                $.getJSON(`../php/get_tour_info.php?id=${tourId}`, function (data) {
                    if (data.success) {
                        renderTourInfo(data.tour);
                    } else {
                        Toast.fire({ icon: 'error', title: 'Unable to fetch tour information.' });
                    }
                }).fail(function () {
                    Toast.fire({ icon: 'error', title: 'Error fetching tour information.' });
                });
            }

            function renderTourInfo(tour) {
                const images = tour.img ? tour.img.split(',') : [];
                let carouselItems = '', carouselIndicators = '';

                const placeholderImage = '../upload/Tour Images/placeholder.jpg';

                images.forEach((path, index) => {
                    const trimmedPath = path.trim();
                    const isActive = index === 0 ? 'active' : '';
                    const imageSrc = trimmedPath ? `../upload/Tour Images/${trimmedPath}` : placeholderImage;

                    carouselItems += `
            <div class="carousel-slide ${isActive}">
                <img src="${imageSrc}" alt="Tour Image ${index + 1}" class="tour-img" onerror="this.src='${placeholderImage}';">
            </div>`;

                    carouselIndicators += `
            <span class="indicator ${isActive}" data-slide="${index}"></span>`;
                });

                $('#tour-info-card').html(`
        <div class="tour-carousel">
            <div class="carousel-slides">${carouselItems}</div>
            <div class="carousel-indicators">${carouselIndicators}</div>
            <button class="carousel-control prev">&#10094;</button>
            <button class="carousel-control next">&#10095;</button>
        </div>
        <div class="tour-info-details">
            <h3 class="tour-title">${tour.title || 'N/A'}</h3><span>${tour.type || 'N/A'}</span>
            <div class="tour-details">
                <div><i class="bx bxs-map"></i> ${tour.address || 'N/A'}</div>
                <div><strong>About:</strong> ${tour.description || 'N/A'}</div>
                <div>
                    <span><strong>Status:</strong> ${tour.status || 'N/A'}</span>
                    <span><strong>Date Created:</strong> ${tour.date_created || 'N/A'}</span>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" onclick="Close()">Close</button>`);

                $('#viewModal').css('display', 'flex');
                initCarousel();
            }

            let currentSlide = 0;

            function initCarousel() {
                const $slides = $('.carousel-slide');
                const $indicators = $('.indicator');

                $('.carousel-control.next').off('click').on('click', function () {
                    currentSlide = (currentSlide + 1) % $slides.length;
                    showSlide();
                });

                $('.carousel-control.prev').off('click').on('click', function () {
                    currentSlide = (currentSlide - 1 + $slides.length) % $slides.length;
                    showSlide();
                });

                $indicators.off('click').on('click', function () {
                    currentSlide = parseInt($(this).data('slide'));
                    showSlide();
                });

                function showSlide() {
                    $slides.removeClass('active').eq(currentSlide).addClass('active');
                    $indicators.removeClass('active').eq(currentSlide).addClass('active');
                }

                showSlide();
            }

            function Delete(tourId) {
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
                            url: `../php/delete_tour.php?id=${tourId}`,
                            type: 'POST',
                            dataType: 'json',
                            success: function (response) {
                                Toast.fire({ icon: response.success ? 'success' : 'error', title: response.message });
                                if (response.success) $('main').load(location.href + ' main > ');
                            },
                            error: function () {
                                Toast.fire({ icon: 'error', title: 'An error occurred. Please try again.' });
                            }
                        });
                    }
                });
            }

            // Function: Close Modal
            window.Close = function () {
                $('#viewModal').css('display', 'none');
            };
        });
    </script>

</body>

</html>