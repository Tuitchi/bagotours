<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $email = $user['email'];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if (empty($gender) || empty($firstname) || empty($lastname) || empty($role)) {
        $_SESSION['errorMessage'] = "All fields are required.";
    } else {
        try {
            $sql = "UPDATE users 
                    SET firstname = :firstname, 
                        lastname = :lastname, 
                        gender = :gender, 
                        role = :role, 
                        status = :status
                    WHERE id = :id";  // Using id as the identifier
            
            // Prepare the statement
            $stmt = $conn->prepare($sql);
            
            // Bind the parameters
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT); // Binding the id parameter properly

            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['successMessage'] = "User successfully updated!";
                echo "<script>
                        window.location.href = window.location.href;
                      </script>";
                exit();
            } else {
                $_SESSION['errorMessage'] = "Database error: Unable to update record.";
            }
        } catch (PDOException $e) {
            $_SESSION['errorMessage'] = "Database error: " . $e->getMessage();
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
    <title>BaGoTours || Edit User</title>
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
                        <h2>Edit User - <?php echo $user['firstname'] . " " . $user['lastname'] ?></h2>
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
                                    <input type="text" id="firstname" name="firstname" placeholder="First Name"
                                        value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : $user['firstname']; ?>"
                                        required>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="lastname" name="lastname" placeholder="Last Name"
                                        value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : $user['lastname']; ?>"
                                        required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group" style="width: 60%;">
                                    <label for="gender">Gender<span>required</span></label>
                                    <select name="gender" id="gender" required>
                                        <option value="" selected disabled>Select Gender</option>
                                        <option value="male" <?php echo (isset($user['gender']) && $user['gender'] == 'male') || (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo (isset($user['gender']) && $user['gender'] == 'female') || (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="male" <?php echo (isset($user['gender']) && $user['gender'] == 'other') || (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>

                                <div class="input-group">
                                    <label for="country">Address<span>Fixed</span></label>
                                    <input type="text" id="address"
                                        value="<?php echo $user['home_address']; ?>" disabled>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="input-group" style="width: 100%;">
                                    <label for="username">Username<span>Fixed</span></label>
                                    <input type="text" id="username" name="username"
                                        value="<?php echo $user['username']; ?>" disabled>
                                </div>
                                <div class="input-group" style="width: 30%;">
                                    <label for="role">Role<span>required</span></label>
                                    <select name="role" id="role" required>
                                        <option value="" selected disabled>Select a Role</option>
                                        <option value="user" <?php echo ((isset($user['role']) && $user['role'] == 'user') || (isset($_POST['role']) && $_POST['role'] == 'user')) ? 'selected' : ''; ?>>
                                            User</option>
                                        <option value="owner" <?php echo ((isset($user['role']) && $user['role'] == 'owner') || (isset($_POST['role']) && $_POST['role'] == 'owner')) ? 'selected' : ''; ?>>Tourist Spot Owner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="email">Email<span>Fixed</span></label>
                                    <input type="text" id="email" name="email"
                                        value="<?php echo $user['email']; ?>"
                                        disabled>
                                </div>
                            </div>
                            <div class="input-group">
                            <label for="status">Status <span class="editable">editable</span></label>
                            <div class="radio-group">
                                <div class="radio">
                                    <input
                                        type="radio"
                                        id="status-yes"
                                        name="status"
                                        value="1"
                                        <?php echo ($user['status'] == 1) ? 'checked' : ''; ?>>
                                    <label for="status-yes">Active</label>
                                </div>
                                <div class="radio">
                                    <input
                                        type="radio"
                                        id="status-no"
                                        name="status"
                                        value="0"
                                        <?php echo ($user['status'] == 0) ? 'checked' : ''; ?>>
                                    <label for="status-no">Inactive</label>
                                </div>
                            </div>

                        </div>
                            <button type="submit" class="btn-submit">Save Edit</button>
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

        // Display success or error messages only if they are not empty
        <?php if (!empty($_SESSION['successMessage'])): ?>
            Toast.fire({
                icon: "success",
                title: "<?php echo htmlspecialchars($_SESSION['successMessage'], ENT_QUOTES, 'UTF-8'); ?>"
            });
            // Clear the session message after displaying
            <?php unset($_SESSION['successMessage']); ?>
        <?php elseif (!empty($_SESSION['errorMessage'])): ?>
            Toast.fire({
                icon: "error",
                title: "<?php echo htmlspecialchars($_SESSION['errorMessage'], ENT_QUOTES, 'UTF-8'); ?>"
            });
            // Clear the session message after displaying
            <?php unset($_SESSION['errorMessage']); ?>
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