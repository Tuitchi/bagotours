<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['firstname'] . " " . $_POST['lastname'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $active = $_POST['active'];

    include '../func/user_func.php';
    $emailAlreadyUsed = emailAlreadyUsed($conn, $email);
    $usernameAlreadyUsed = usernameAlreadyUsed($conn, $username);

    if (empty($gender) || empty($name) || empty($username) || empty($email) || empty($role)) {
        $errorMessage = "All fields are required.";
    } elseif ($emailAlreadyUsed) {
        $errorMessage = "Email already in use.";
    } elseif ($usernameAlreadyUsed) {
        $errorMessage = "Username already in use.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    }
    if (empty($errorMessage)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, gender, home_address, role, username, email, password, profile_picture)
                    VALUES (:name, :gender, :home_address, :role, :username, :email, :password, :profile_picture)";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':home_address', $home_address);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':profile_picture', $profile_picture);

            if ($stmt->execute()) {
                $successMessage = "User successfully created!";
            } else {
                $errorMessage = "Database errorMessage: Unable to insert record.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Database errorMessage: " . $e->getMessage();
        }
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
                        <h2>Edit User - <?php echo $user['name']?></h2>
                        <p>Fill in the required information below to create a new user account. Please make sure all
                            details are correct.</p>
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data">



                        <div class="info-group" style="width: 100%;">
                            <div class="section-header">
                                <hr class="section-divider">
                                <h3 class="section-title">User Information</h3>
                                <hr class="section-divider">
                            </div>
                            <label for="firstname">Name <span>required</span></label>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" id="firstname" name="firstname" placeholder="First Name" value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>"
                                        required>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="lastname" name="lastname" placeholder="Last Name" value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group" style="width: 60%;">
                                    <label for="gender">Gender<span>required</span></label>
                                    <select name="gender" id="gender" required>
                                        <option value="" selected disabled>Select Gender</option>
                                        <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
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
                                <div class="input-group" style="width: 100%;">
                                    <label for="username">Username<span>required</span></label>
                                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                </div>
                                <div class="input-group" style="width: 30%;">
                                    <label for="role">Role<span>required</span></label>
                                    <select name="role" id="role" required>
                                        <option value="" selected disabled>Select a Role</option>
                                        <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                        <option value="owner" <?php echo (isset($_POST['role']) && $_POST['role'] == 'owner') ? 'selected' : ''; ?>>Tourist Spot Owner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="email">Email<span>required</span></label>
                                    <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="password">Password<span>required</span></label>
                                    <input type="password" id="password" name="password" required>
                                </div>
                                <div class="input-group">
                                    <label for="confirm-password">Confirm Password<span>required</span></label>
                                    <input type="password" id="confirm-password" name="confirm-password" required>
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
        $(document).ready(function() {
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
            const countries = [
                "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria",
                "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia",
                "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia",
                "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)",
                "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica",
                "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia",
                "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea",
                "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel",
                "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea (North)", "Korea (South)", "Kuwait", "Kyrgyzstan",
                "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi",
                "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova",
                "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand",
                "Nicaragua", "Niger", "Nigeria", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea",
                "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia",
                "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles",
                "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka",
                "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga",
                "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom",
                "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
            ];
            const $countryDropdown = $('#country');
            const $provinceDropdown = $('#province');
            const $cityDropdown = $('#city');

            countries.forEach(country => {
                $countryDropdown.append(`<option value="${country}">${country}</option>`);
            });
            $countryDropdown.on('change', function() {
                const selectedCountry = $(this).val();

                if (selectedCountry === "Philippines") {
                    $provinceDropdown.prop('disabled', false);
                    $cityDropdown.prop('disabled', true);

                    $.ajax({
                        url: '../php/getProvinces.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function(provinces) {
                            $('.input-group.province').css('display', 'block');
                            $provinceDropdown.html('<option value="" selected disabled>Select Province</option>');
                            provinces.forEach(province => {
                                $provinceDropdown.append(`<option value="${province.name}" data-code="${province.code}">${province.name}</option>`);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching provinces:", error);
                        }
                    });

                } else {
                    $provinceDropdown.prop('disabled', true).html('<option value="" selected disabled>Select Province</option>');
                    $cityDropdown.prop('disabled', true).html('<option value="" selected disabled>Select City/Municipality</option>');
                }
            });


            $provinceDropdown.on('change', function() {
                const provinceName = $(this).val();
                const provinceId = $(this).find(':selected').data('code');
                console.log('Selected provinceName:', provinceName);
                console.log('Selected Province Id:', provinceId);

                if (provinceId) {
                    $cityDropdown.prop('disabled', false); // Enable city dropdown

                    // Fetch cities for the selected province
                    $.ajax({
                        url: '../php/getCities.php',
                        method: 'GET',
                        data: {
                            provinceId: provinceId
                        },
                        dataType: 'json',
                        success: function(cities) {
                            $('.input-group.city').css('display', 'block');

                            $cityDropdown.html('<option value="" selected disabled>Select City/Municipality</option>');
                            cities.forEach(city => {
                                $cityDropdown.append(`<option value="${city.name}">${city.name}</option>`);
                            });
                        },
                        error: function(xhr, status, error) {
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


            $('#tour-images').on('change', function(event) {
                const files = event.target.files;
                const $imagesPreview = $('.image-preview-container');
                const $mainImagePreview = $('#main-image-preview');
                const $thumbnailContainer = $('.thumbnail-images');

                // Clear existing image previews and thumbnails
                $imagesPreview.toggle();
                $thumbnailContainer.empty();
                $mainImagePreview.attr('src', '');

                $.each(files, function(index, file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const $img = $('<img>', {
                            src: e.target.result,
                            alt: `Image ${index + 1}`,
                        });

                        $img.on('click', function() {
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