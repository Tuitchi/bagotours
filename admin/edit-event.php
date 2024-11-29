<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();
$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $event = getEventbyCode($conn, $id);
} else {
    header("Location: tours.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Retrieve form data
        $event_name = $_POST['event_name'] ?? null;
        $event_type = $_POST['event_type'] ?? null;
        $tags = $_POST['tags'] ?? null;
        $event_description = $_POST['event_description'] ?? null;
        $event_date_start = $_POST['event_date_start'] ?? null;
        $event_date_end = $_POST['event_date_end'] ?? null;
        $registration_deadline = !empty($_POST['registration_deadline']) ? $_POST['registration_deadline'] : null;
        $organizer_name = $_POST['organizer_name'] ?? null;
        $organizer_contact = $_POST['organizer_contact'] ?? null;
        $sponsor = $_POST['sponsor'] ?? null;

        // File upload handling
        $upload_dir = '../upload/Event/';
        $event_image = $event['event_image']; // Default to existing image

        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
            $image_name = uniqid() . '-' . basename($_FILES['event_image']['name']);
            $target_file = $upload_dir . $image_name;

            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
                $event_image = $image_name;
            } else {
                throw new Exception('Failed to upload the image.');
            }
        }


        // Update query
        $query = "
            UPDATE events 
            SET 
                event_name = :event_name,
                event_type = :event_type,
                tags = :tags,
                event_description = :event_description,
                event_date_start = :event_date_start,
                event_date_end = :event_date_end,
                registration_deadline = :registration_deadline,
                organizer_name = :organizer_name,
                organizer_contact = :organizer_contact,
                sponsor = :sponsor,
                event_image = :event_image
            WHERE event_code = :event_code
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':event_name', $event_name);
        $stmt->bindParam(':event_type', $event_type);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':event_description', $event_description);
        $stmt->bindParam(':event_date_start', $event_date_start);
        $stmt->bindParam(':event_date_end', $event_date_end);
        $stmt->bindParam(':registration_deadline', $registration_deadline);
        $stmt->bindParam(':organizer_name', $organizer_name);
        $stmt->bindParam(':organizer_contact', $organizer_contact);
        $stmt->bindParam(':sponsor', $sponsor);
        $stmt->bindParam(':event_image', $event_image);
        $stmt->bindParam(':event_code', $id); // $id is retrieved from $_GET['id']

        if ($stmt->execute()) {
            $_SESSION['successMessage'] = 'Event updated successfully.';
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            throw new Exception('Failed to update the event.');
        }
    } catch (Exception $e) {
        $_SESSION['errorMessage'] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon"
        href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/add.css">
    <link rel="stylesheet" href="assets/css/edit.css">

    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <style>
        .image-preview {
            display: block;
            width: 50vw;
            height: auto;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
    <title>BaGoTours || Edit Event</title>
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
                        <h2>Edit Event - <?php echo $event['event_name'] ?></h2>
                        <p>Please modify the details below to update the event information. Ensure all changes are
                            accurate,
                            particularly the event images, date, time, and location, to provide the best experience for
                            participants.</p>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Event Information</h3>
                            <hr class="section-divider">
                        </div>
                        <label for="event_image">Event Image</label>
                        <div class="image-preview" id="image-preview">
                            <img id="preview-image" src="../upload/Event/<?php echo $event['event_image'] ?>"
                                alt="Event Image Preview">
                        </div>
                        <input type="file" id="event_image" name="event_image" accept="image/*">
                        <p id="image-error" style="color: red; display: none;">Image must have a landscape view (16:10
                            aspect ratio recommended).
                        </p>

                        <label for="event_name">Event Name <span class="editable">editable</span></label>
                        <input type="text" id="event_name" name="event_name" value="<?php echo $event['event_name'] ?>">

                        <div class="form-group">
                            <div class="input-group">
                                <label for="event_type">Event Type <span class="editable">editable</span></label>
                                <input type="text" id="event_type" name="event_type"
                                    value="<?php echo $event['event_type'] ?>">
                            </div>

                            <div class="input-group">
                                <label for="tags">Tags <span class="editable">editable</span></label>
                                <input type="text" id="tags" name="tags" placeholder="e.g., outdoor, family-friendly"
                                    value="<?php echo $event['tags'] ?>">
                            </div>
                        </div>

                        <label for="event_description">Event Description <span class="editable">editable</span></label>
                        <textarea id="event_description" name="event_description"
                            rows="4"><?php echo $event['event_description'] ?></textarea>

                        <div class="form-group">
                            <div class="input-group">
                                <label for="event_date_start">Start Date & Time <span
                                        class="editable">editable</span></label>
                                <input type="datetime-local" id="event_date_start" name="event_date_start"
                                    value="<?php echo $event['event_date_start'] ?>">
                            </div>
                            <div class="input-group">
                                <label for="event_date_end">End Date & Time <span
                                        class="editable">editable</span></label>
                                <input type="datetime-local" id="event_date_end" name="event_date_end"
                                    value="<?php echo $event['event_date_end'] ?>">
                            </div>
                            <div class="input-group">
                                <label for="registration_deadline">Registration Deadline <span
                                        class="opt">optional</span></label>
                                <input type="datetime-local" id="registration_deadline" name="registration_deadline"
                                    value="<?php echo $event['registration_deadline'] ?>">
                            </div>
                        </div>

                        <label for="event_location">Event Location <span>fixed</span></label>
                        <input type="text" id="event_location" name="event_location" required disabled
                            value="<?php echo $event['event_location']; ?>" readonly>

                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Organizer Information <span class="opt">optional</span></h3>
                            <hr class="section-divider">
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" id="organizer_name" name="organizer_name"
                                    value="<?php echo $event['organizer_name'] ?>" placeholder="Organizer Name">
                            </div>
                            <div class="input-group">
                                <input type="text" id="organizer_contact" name="organizer_contact"
                                    value="<?php echo $event['organizer_contact'] ?>"
                                    placeholder="Organizer Contact (email, phone number or etc.)">
                            </div>
                        </div>

                        <label for="sponsor">Sponsor</label>
                        <input type="text" id="sponsor" name="sponsor" value="<?php echo $event['sponsor'] ?>">

                        <button type="submit" class="btn-submit">Save Edit</button>
                    </form>
                </div>
            </div>
            <div id="mapModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeMap()">&times;</span>
                    <h2>Select Event Location</h2>
                    <div id="map"></div>
                    <button id="confirm-location" class="btn">Confirm Location</button>
                </div>
            </div>
        </main>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
    <script>
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
        const successMessage = "<?php echo $_SESSION['successMessage'] ?? ''; ?>";
        const errorMessage = "<?php echo $_SESSION['errorMessage'] ?? ''; ?>";

        if (successMessage) {
            Toast.fire({
                icon: "success",
                title: successMessage
            });
        }

        if (errorMessage) {
            Toast.fire({
                icon: "error",
                title: errorMessage
            });
        }
        <?php unset($_SESSION['successMessage'], $_SESSION['errorMessage']); ?>
        document.addEventListener('DOMContentLoaded', () => {
            $('#event_image').on('change', function (event) {
                const $previewContainer = $('#image-preview');
                const $previewImage = $('#preview-image');
                const $imageError = $('#image-error');
                const file = event.target.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $previewImage.attr('src', e.target.result);
                        const img = new Image();
                        img.src = e.target.result;

                        img.onload = function () {
                            const aspectRatio = img.width / img.height;
                            if (aspectRatio < 1.3) {
                                $('#event_image').val('');
                                $imageError.show();
                                $previewContainer.hide();
                            } else {
                                $imageError.hide();
                                $previewContainer.show();
                            }
                        };
                    };
                    reader.readAsDataURL(file);
                }
            });

        });

        function validateForm() {
            const startDate = new Date(document.getElementById("event_date_start").value);
            const endDate = new Date(document.getElementById("event_date_end").value);

            if (startDate >= endDate) {
                alert("End date must be after the start date.");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>