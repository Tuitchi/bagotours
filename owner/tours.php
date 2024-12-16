<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();
$user_id = $_SESSION['user_id'];

$status = isset($_GET["status"]) ? $_GET["status"] : '';
$query = isset($_GET['search']) ? $_GET['search'] : null;
$tour = getAllToursforOwners($conn, $user_id,$query);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours || Tours</title>
    <style>
        .data {
            display: flex;
            flex-wrap: nowrap;
            gap: 15px;
            align-items: flex-start;
            justify-content: flex-start;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .data .img {
            width: 200px;
            height: 200px;
            overflow: hidden;
            border-radius: 15%;
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
            margin: 0 0 5px;
        }

        .data .content .btn-edit,
        .data .content .btn-delete {
            margin-top: 10px;
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .data .content .btn-delete {
            background-color: #dc3545;
        }

        .content {
            position: relative;
            flex-grow: 1;
            padding-right: 60px;
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

        /* General Styles */
        .tour-info-card {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            gap: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 12px;
            background-color: #f9f9f9;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin: 15px 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .tour-info-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .tour-image {
            flex-shrink: 0;
        }

        .tour-img {
            width: 100%;
            height: 50vh;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #ddd;
        }

        .tour-info-details {
            flex-grow: 1;
            padding-left: 15px;
            display: flex;
            flex-direction: column;
        }

        .tour-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .tour-details {
            font-size: 16px;
            color: #555;
        }

        .detail-item {
            margin-bottom: 12px;
        }

        .detail-item.meta {
            display: flex;
            justify-content: space-between;
        }

        .detail-item strong {
            color: #444;
            font-weight: bold;
        }

        .info-text {
            color: #777;
            font-style: italic;
        }

        @media screen and (max-width: 768px) {
            .tour-info-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .tour-image {
                margin-bottom: 15px;
            }

            .tour-info-details {
                padding-left: 0;
            }
        }

        .tour-carousel {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;
        }

        .carousel-slides {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-slide {
            min-width: 100%;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .tour-img {
            width: 100%;
            height: 50vh;
            object-fit: cover;
        }

        .carousel-control {
            position: absolute;
            top: 50%;
            width: auto;
            padding: 0.5rem;
            color: #fff;
            font-size: 1.5rem;
            background: rgba(0, 0, 0, 0.5);
            cursor: pointer;
            border: none;
            transform: translateY(-50%);
            z-index: 2;
        }

        .carousel-control.prev {
            left: 10px;
        }

        .carousel-control.next {
            right: 10px;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 5px;
        }

        .indicator {
            height: 10px;
            width: 10px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
        }

        .indicator.active {
            background-color: #717171;
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
                    <?php if (!empty($tour)): ?>
                        <?php foreach ($tour as $row): ?>
                            <?php
                            $images = explode(',', $row['img']);
                            $mainImage = htmlspecialchars($images[0], ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="data">
                                <div class="img">
                                    <img src="../upload/Tour Images/<?php echo $mainImage; ?>" alt="Main Tour Image">
                                </div>

                                <div class="content">
                                    <h4><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p style="font-size:13px"><?php echo htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p><?php echo htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8'); ?></p>

                                    <p><strong>About</strong></p>
                                    <p style="font-size:13px;">
                                        <?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle the search input submission
            $("#search-input").on("keypress", function(event) {
                if (event.which === 13) { // Check for Enter key
                    $(this).closest("form").submit(); // Submit the form
                }
            });
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            $(document).on('click', '#delete', function(event) {
                event.preventDefault();
                const id = $(this).data('id');
                Delete(id);
            });
            $(document).on('click', '#drop', function(event) {
                event.preventDefault();

                // Close any open dropdowns first (optional, to hide others)
                $('.actions').not($(this).next('.actions')).hide();

                // Toggle the display of the clicked dropdown
                const actions = $(this).next('.actions'); // This targets the .actions that comes after the clicked button

                // Check the current state and toggle it
                if (actions.css('display') === 'none') {
                    actions.css('display', 'block'); // Show the dropdown
                } else {
                    actions.css('display', 'none'); // Hide the dropdown
                }
            });

            // Close the dropdown when clicking outside of it
            $(document).click(function(event) {
                if (!$(event.target).closest('.dropdown').length) {
                    $('.actions').hide();
                }
            });
            $(document).on('click', '#view', function(event) {
                event.preventDefault();
                const id = $(this).data('id');
                View(id);
            });

            function View(tourId) {
                $.getJSON(`../php/get_tour_info.php?id=${tourId}`, function(data) {
                    if (data.success) {
                        const images = data.tour.img.split(',');
                        let carouselItems = '';
                        let carouselIndicators = '';

                        images.forEach((imagePath, index) => {
                            const isActive = index === 0 ? 'active' : ''; // Make the first image active
                            carouselItems += `
                    <div class="carousel-slide ${isActive}">
                        <img src="../upload/Tour Images/${imagePath.trim()}" alt="Tour Image ${index + 1}" class="tour-img">
                    </div>
                `;
                            carouselIndicators += `
                    <span class="indicator ${isActive}" data-slide="${index}"></span>
                `;
                        });

                        // Render the tour details with the custom carousel for images
                        $('#tour-info-card').html(`
                <div class="tour-carousel">
                    <div class="carousel-slides">${carouselItems}</div>
                    <div class="carousel-indicators">${carouselIndicators}</div>
                    <button class="carousel-control prev" onclick="prevSlide()">&#10094;</button>
                    <button class="carousel-control next" onclick="nextSlide()">&#10095;</button>
                </div>
                <div class="tour-info-details">
                    <h3 class="tour-title">${data.tour.title || 'N/A'}</h3><span class="info-text">${data.tour.type || 'N/A'}</span>
                    <div class="tour-details">
                        <div class="detail-item">
                            <i class="bx bxs-map"></i><span class="info-text">${data.tour.address || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <strong>About</strong><br> <span class="info-text">${data.tour.description || 'N/A'}</span>
                        </div>
                        <div class="detail-item meta">
                            <span class="info-text"><strong>Status:</strong> ${data.tour.status || 'N/A'}</span>
                            <span class="info-text"><strong>Date Created:</strong> ${data.tour.date_created || 'N/A'}</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" onclick=Close()>Close</button>
            `);
                        $('#viewModal').css('display', 'flex');
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Unable to fetch tour information.'
                        });
                    }
                }).fail(function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'There was an error fetching the tour information.'
                    });
                });
            }

            let currentSlide = 0;

            function showSlide(index) {
                const slides = document.querySelectorAll('.carousel-slide');
                const indicators = document.querySelectorAll('.indicator');
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                    indicators[i].classList.toggle('active', i === index);
                });
                currentSlide = index;
            }

            function nextSlide() {
                const slides = document.querySelectorAll('.carousel-slide');
                showSlide((currentSlide + 1) % slides.length);
            }

            function prevSlide() {
                const slides = document.querySelectorAll('.carousel-slide');
                showSlide((currentSlide - 1 + slides.length) % slides.length);
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('indicator')) {
                    showSlide(parseInt(e.target.getAttribute('data-slide')));
                }
            });

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
                            success: function(response) {
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
                            error: function(xhr, status, error) {
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
            window.Close = function() {
                document.querySelector('.modal').style.display = 'none';
            };

        });
    </script>
</body>

</html>