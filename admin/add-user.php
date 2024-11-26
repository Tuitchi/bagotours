<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['firstname'] . " " . $_POST['lastname'];
    $gender = $_POST['gender'];
    $country = $_POST['country'];
    $province = isset($_POST['province']) ? $_POST['province'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $address = $city
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['confirm-password'];
    $profile_picture = $_FILES['profile-picture'];
    $errors[] = '';

    if (empty($gender) || empty($name) || empty($country) || empty($username) || empty($email) || empty($password) || empty($passwordConfirmation)) {
        echo "All fields are required.";
    }


}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/add.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <title>BaGoTours. Tours</title>
    <style>
        form {
            display: flex;
            flex-direction: row;
            gap: 25px;
            justify-content: space-between;
        }

        .image-preview-container {
            display: block;
            padding: 10px
        }

        .image-preview-container img {
            width: auto;
            height: 50vh;
            object-fit: cover;
        }

        .form-group {
            gap: 4px;
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
                    <div class="title">
                        <h2>Create a New User</h2>
                        <p>Fill in the required information below to create a new user account. Please make sure all
                            details are correct.</p>
                    </div>

                    <form action="" method="POST">

                        <div class="pp">

                            <div class="section-header">
                                <hr class="section-divider">
                                <h3 class="section-title">Profile Picture</h3>
                                <hr class="section-divider">
                            </div>
                            <div class="image-preview-container">
                                <img src="../upload/Profile Pictures/default.png" alt="">
                            </div>
                            <input type="file" id="profile-picture" name="profile-picture" accept="image/*">
                        </div>

                        <div class="info-group">
                            <div class="section-header">
                                <hr class="section-divider">
                                <h3 class="section-title">User Information</h3>
                                <hr class="section-divider">
                            </div>
                            <label for="firstname">Name <span>required</span></label>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" id="firstname" name="firstname" placeholder="First Name"
                                        required>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="gender">Gender<span>required</span></label>
                                    <select name="gender" id="gender" required style="width:80%">
                                        <option value="" selected disabled>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <label for="country">Country<span>required</span></label>
                                    <select name="country" id="country" required>
                                        <option value="" selected disabled>Select Country</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                </div>
                                <div class="input-group province" style="display:none;">
                                    <label for="province">Province</label>
                                    <select name="province" id="province" required disabled>
                                        <option value="" selected disabled>Select Province</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                </div>
                                <div class="input-group city" style="display:none;">
                                    <label for="city">City/Municipality</label>
                                    <select name="city" id="city" required disabled>
                                        <option value="" selected disabled>Select City/Municipality</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group" style="width: 50%;">
                                    <label for="username">Username<span>requed</span></label>
                                    <input type="text" id="username" name="username">
                                </div>
                                <div class="input-group">
                                    <label for="email">Email<span>required</span></label>
                                    <input type="text" id="email" name="email">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="password">Password<span>required</span></label>
                                    <input type="password" id="password" name="password">
                                </div>
                                <div class="input-group">
                                    <label for="confirm-password">Confirm Password<span>required</span></label>
                                    <input type="password" id="confirm-password" name="confirm-password">
                                </div>
                            </div>
                            <button type="submit" class="btn-submit">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </section>



    <!-- jQuery (required by Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script>
        $(document).ready(function () {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            // Populate the country select dropdown with countries
            const countries = ["Philippines"
            ];
            const $countryDropdown = $('#country');
            const $provinceDropdown = $('#province');
            const $cityDropdown = $('#city');

            countries.forEach(country => {
                $countryDropdown.append(`<option value="${country}">${country}</option>`);
            });
            $countryDropdown.on('change', function () {
                const selectedCountry = $(this).val();

                if (selectedCountry === "Philippines") {
                    $provinceDropdown.prop('disabled', false);
                    $cityDropdown.prop('disabled', true);

                    $.ajax({
                        url: '../php/getProvinces.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function (provinces) {
                            $('.input-group.province').css('display', 'block');
                            $provinceDropdown.html('<option value="" selected disabled>Select Province</option>');
                            provinces.forEach(province => {
                                $provinceDropdown.append(`<option value="${province.code}">${province.name}</option>`);
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error("Error fetching provinces:", error);
                        }
                    });

                } else {
                    $provinceDropdown.prop('disabled', true).html('<option value="" selected disabled>Select Province</option>');
                    $cityDropdown.prop('disabled', true).html('<option value="" selected disabled>Select City/Municipality</option>');
                }
            });


            $provinceDropdown.on('change', function () {
                const provinceId = $(this).val();
                console.log('Selected provinceId:', provinceId); // Debugging log

                if (provinceId) {
                    $cityDropdown.prop('disabled', false); // Enable city dropdown

                    // Fetch cities for the selected province
                    $.ajax({
                        url: '../php/getCities.php',
                        method: 'GET',
                        data: { provinceId: provinceId },
                        dataType: 'json',
                        success: function (cities) {
                            $('.input-group.city').css('display', 'block');

                            $cityDropdown.html('<option value="" selected disabled>Select City/Municipality</option>');
                            cities.forEach(city => {
                                $cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                            });
                        },
                        error: function (xhr, status, error) {
                            let errorMessage = xhr.responseText ? xhr.responseText : error;
                            Toast.fire({
                                icon: 'error',
                                title: `Error: ${errorMessage}`
                            });
                        }
                    });

                } else {
                    // If no province is selected, disable the city dropdown and reset it
                    $cityDropdown.prop('disabled', true).html('<option value="" selected disabled>Select City/Municipality</option>');
                }
            });




            <?php if (!empty($successMessage)): ?>
                Toast.fire({
                    icon: "success",
                    title: "<?php echo $successMessage ?>"
                });
            <?php elseif (!empty($errorMessage)): ?>
                Toast.fire({
                    icon: "error",
                    title: "<?php echo $errorMessage ?>"
                });
            <?php endif; ?>


            $('#tour-images').on('change', function (event) {
                const files = event.target.files;
                const $imagesPreview = $('.image-preview-container');
                const $mainImagePreview = $('#main-image-preview');
                const $thumbnailContainer = $('.thumbnail-images');

                // Clear existing image previews and thumbnails
                $imagesPreview.toggle();
                $thumbnailContainer.empty();
                $mainImagePreview.attr('src', '');

                $.each(files, function (index, file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const $img = $('<img>', {
                            src: e.target.result,
                            alt: `Image ${index + 1}`,
                        });

                        $img.on('click', function () {
                            $mainImagePreview.attr('src', e.target.result);
                            $('.thumbnail-images img').removeClass('selected');
                            $img.addClass('selected');
                        });

                        $thumbnailContainer.append($img);

                        if (index === 0) {
                            $mainImagePreview.attr('src', e.target.result);
                            $img.addClass('selected');
                        }
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
</body>

</html>