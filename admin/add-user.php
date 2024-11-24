<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['location'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $bookable = isset($_POST['bookable']) ? $_POST['bookable'] : 0;
    if (tourAlreadyExists($conn, $title)) {
        $errorMessage = "Tour already exist, please fill up a new tour.";
    } else {
        $uploaded_images = [];
        if (isset($_FILES['tour-images']) && !empty($_FILES['tour-images']['name'][0])) {
            $images = $_FILES['tour-images'];
            $image_error = false;

            foreach ($images['name'] as $key => $image_name) {
                $image_tmp_name = $images['tmp_name'][$key];
                $image_type = $images['type'][$key];
                $image_size = $images['size'][$key];
                $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);

                // Validate image (Check for allowed types and size)
                if (!in_array($image_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $image_error = true;
                    $error_message = "Invalid image type. Only jpg, jpeg, png, and gif are allowed.";
                    break;
                }
                if ($image_size > 5000000) { // Max size of 5MB
                    $image_error = true;
                    $error_message = "Image size exceeds 5MB.";
                    break;
                }

                // Generate a unique name for the image
                $new_image_name = uniqid() . '.' . $image_ext;
                $target_path = "../upload/Tour Images/" . $new_image_name;

                if (move_uploaded_file($image_tmp_name, $target_path)) {
                    $uploaded_images[] = $new_image_name; // Store the image name for DB insertion
                } else {
                    $image_error = true;
                    $error_message = "Error uploading image.";
                    break;
                }
            }

            if ($image_error) {
                echo '<script>alert("' . $error_message . '");</script>';
            }
        }

        // If no errors, insert the tour into the database
        if (!$image_error) {
            // Convert the array of uploaded images to a comma-separated string for the database
            $image_paths = implode(',', $uploaded_images);

            // Insert tour details into the database
            $query = "INSERT INTO tours (title, type, description, address, latitude, longitude, img, bookable, status, user_id) 
                  VALUES (:title, :type, :description, :address, :latitude, :longitude, :img, :bookable, 1, :user_id)";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':latitude', $latitude);
            $stmt->bindParam(':longitude', $longitude);
            $stmt->bindParam(':img', $image_paths);
            $stmt->bindParam(':bookable', $bookable);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $successMessage = "Tour added successfully!";
            } else {
                $errorMessage = "Failed to add the tour. Please try again.";
            }
        }
    }
}

function tourAlreadyExists($conn, $title)
{
    $query = "SELECT COUNT(*) as count FROM tours WHERE title = :title AND status = 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
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
            width: 100%;
            height: auto;
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

                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="pp">

                            <div class="section-header">
                                <hr class="section-divider">
                                <h3 class="section-title">Profile Picture</h3>
                                <hr class="section-divider">
                            </div>
                            <div class="image-preview-container">
                                <img src="../upload/Profile Pictures/default.png" alt="">
                            </div>
                            <input type="file" id="profile-picture" name="profile-picture" accept="image/*" multiple>
                        </div>

                        <div class="info-group">
                            <div class="section-header">
                                <hr class="section-divider">
                                <h3 class="section-title">User Information</h3>
                                <hr class="section-divider">
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="firstname">First Name <span>required</span></label>
                                    <input type="text" id="firstname" name="firstname" required>
                                </div>
                                <div class="input-group">
                                    <label for="middlename">Middle Name<span class="opt">Optional</span></label>
                                    <input type="text" id="middlename" name="middlename" required>
                                </div>
                                <div class="input-group">
                                    <label for="lastname">Last Name <span>required</span></label>
                                    <input type="text" id="lastname" name="lastname" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="gender">Gender<span>required</span></label>
                                    <select name="gender" id="gender" required style="width:50%">
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
                                <div class="input-group">
                                    <label for="province">Province</label>
                                    <select name="province" id="province" required>
                                        <option value="" selected disabled>Select Province</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                </div>
                                <div class="input-group">
                                    <label for="city">City or Municipality</label>
                                    <select name="city" id="city" required>
                                        <option value="" selected disabled>Select City or Municipality</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="address">Address<span>required</span></label>
                                    <input type="text" id="address" name="address">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="username">Username<span>required</span></label>
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
                            </div>
                            <div class="form-group">
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
            $('#country, #province, #city').select2();
        });
        // Populate the country select dropdown with countries
        const countries = [
            "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina",
            "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh",
            "Barbados", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina",
            "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia",
            "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China",
            "Colombia", "Comoros", "Congo", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic",
            "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador",
            "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France",
            "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea",
            "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia",
            "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya",
            "Kiribati", "Korea, North", "Korea, South", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia",
            "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
            "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania",
            "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco",
            "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua",
            "Niger", "Nigeria", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea",
            "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda",
            "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino",
            "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore",
            "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka",
            "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand",
            "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine",
            "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City",
            "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
        ];

        const selectElement = document.getElementById('country');

        // Loop through the array and create an option for each country
        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country;
            option.textContent = country;
            selectElement.appendChild(option);

        });
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
    </script>
</body>

</html>