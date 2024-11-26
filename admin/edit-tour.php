<?php
include '../include/db_conn.php';
include '../func/user_func.php';
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', '../error_log.txt'); // Set the error log file path
session_start();
$user_id = $_SESSION['user_id'];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $tour = getTourById($conn, $id);
} else {
    $_SESSION['errorMessage'] = 'Invalid Tour ID';
    header("Location: tours.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handling image deletions
        if (!empty($_POST['deleted-images'])) {
            $deletedImagesArray = explode(',', trim($_POST['deleted-images'], ','));
            $currentImages = !empty($tour['img']) ? explode(',', trim($tour['img'], ',')) : [];
            
            // Calculate remaining images after deletion
            $remainingImages = array_diff($currentImages, $deletedImagesArray);
            error_log ("IMAGE LEFT : " . implode(',', $remainingImages));

            // If no images are left, prevent the deletion and show an error message
            if (count($remainingImages) > 0) {
                $updatedImages = implode(',', $remainingImages);
                
                // Delete the images from the server
                foreach ($deletedImagesArray as $image) {
                    $imagePath = "../upload/Tour Images/" . $image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
        
                // Update the image list in the database
                $sql = "UPDATE tours SET img = :img WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':img', $updatedImages);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            } else {
                $_SESSION['errorMessage'] = 'You cannot delete all the images. At least one image must remain, including the main image.';
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
                exit();
            }
        }
        
        
        // Handling new image uploads
        if (!empty($_FILES['tour-images']['name'][0])) {
            $tourImages = $_FILES['tour-images'];
            $currentImages = !empty($tour['img']) ? explode(',', trim($tour['img'], ',')) : [];
        
            foreach ($tourImages['name'] as $index => $imageName) {
                $newImageName = time() . "_" . basename($imageName);
                $targetPath = "../upload/Tour Images/" . $newImageName;
        
                if (move_uploaded_file($tourImages['tmp_name'][$index], $targetPath)) {
                    $currentImages[] = $newImageName;
                } else {
                    $_SESSION['errorMessage'] = 'Tour image failed to upload!';
                    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
                    exit();
                }
            }
        
            $updatedImages = implode(',', $currentImages);
        
            $sql = "UPDATE tours SET img = :img WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':img', $updatedImages);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        
        // Update other tour details (type, description, bookable, status)
        if (isset($_POST['title'], $_POST['type'], $_POST['description'], $_POST['bookable'], $_POST['status'])) {
            $title = $_POST['title'];
            $type = $_POST['type'];
            $description = $_POST['description'];
            $bookable = $_POST['bookable'];
            $status = $_POST['status'];
            
            $sql = "UPDATE tours SET title = :title, type = :type, description = :description, bookable = :bookable, status= :status WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':bookable', $bookable);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $_SESSION['successMessage'] = 'Tour updated successfully!';
            } else {
                $_SESSION['errorMessage'] = 'Failed to update tour details.';
            }
        } else {
            $_SESSION['errorMessage'] = 'Failed to update tour details.';
        }

        // Redirect after successful update
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit();
    } catch (Exception $e) {
        $_SESSION['errorMessage'] = 'An error occurred: ' . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit();
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
    <link rel="stylesheet" href="assets/css/edit.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>BaGoTours. Tours</title>
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

                        <h2>Edit <?php echo $tour['title']?></h2>
                        <p>Modify the details below to update the tour information. Ensure all changes are accurate, particularly the images and location, to provide the best experience for visitors.</p>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Tour Information</h3>
                            <hr class="section-divider">
                        </div>
                        <label for="image-preview-container">Tour Image</label>
                        <p>Ensure that the main image appears as the first image.</p>
                        <div class="image-preview-container">
                            <div class="main-image">
                                <img id="main-image-preview" src="" alt="Main Image Preview">
                            </div>
                            <div class="thumbnail-images">
                                <?php
                                $tour_images = explode(",", $tour['img']);
                                foreach ($tour_images as $index => $image):
                                ?>
                                    <div class="thumbnail-container">
                                        <img src="../upload/Tour Images/<?php echo $image; ?>" alt="Tour Image" class="thumbnail-image" data-index="<?php echo $index; ?>" />
                                        <i class="bx bxs-trash delete-icon" data-image="<?php echo $image; ?>" data-index="<?php echo $index; ?>"></i>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <input type="file" id="tour-images" name="tour-images[]" accept="image/*" multiple>
                        <p id="image-error" style="color: red; display: none;">Image must have a landscape view (16:10 aspect ratio recommended).</p>

                        <!-- Hidden input to track deleted images -->
                        <input type="hidden" id="deleted-images" name="deleted-images" value="">

                        <div class="form-group">
                            <div class="input-group" style="width:65%">
                                <label for="title">Tours Name <span class="editable">editable</span></label>
                                <input type="text" id="title" name="title" value="<?php echo $tour['title'] ?>">
                            </div>
                            <div class="input-group" style="width:35%">
                                <label for="type">Tour Type <span class="editable">editable</span></label>
                                <select name="type" id="type" required>
                                    <option value="none" selected disabled hidden>Select an Option</option>
                                    <option value="Beach Resort" <?php echo ($tour['type'] == 'Beach Resort') ? 'selected' : ''; ?>>Beach Resort</option>
                                    <option value="Campsite" <?php echo ($tour['type'] == 'Campsite') ? 'selected' : ''; ?>>Campsite</option>
                                    <option value="Falls" <?php echo ($tour['type'] == 'Falls') ? 'selected' : ''; ?>>Falls</option>
                                    <option value="Historical Landmark" <?php echo ($tour['type'] == 'Historical Landmark') ? 'selected' : ''; ?>>Historical Landmark</option>
                                    <option value="Mountain Resort" <?php echo ($tour['type'] == 'Mountain Resort') ? 'selected' : ''; ?>>Mountain Resort</option>
                                    <option value="Park" <?php echo ($tour['type'] == 'Park') ? 'selected' : ''; ?>>Park</option>
                                    <option value="Swimming Pool" <?php echo ($tour['type'] == 'Swimming Pool') ? 'selected' : ''; ?>>Swimming Pool</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label for="location">Tour Location <span>fixed</span></label>
                                <input type="text" id="location" name="location" disabled
                                    value="<?php echo $tour['address'] ?>" onclick="openMap()" readonly>
                            </div>
                            <div class="input-group">
                                <label for="bookable">Bookable <span class="editable">editable</span></label>
                                <div class="radio-group">
                                    <div class="radio">
                                        <input 
                                            type="radio" 
                                            id="bookable-yes" 
                                            name="bookable" 
                                            value="1" 
                                            <?php echo ($tour['bookable']== 1) ? 'checked' : ''; ?>
                                        >
                                        <label for="bookable-yes">Yes</label>
                                    </div>
                                    <div class="radio">
                                        <input 
                                            type="radio" 
                                            id="bookable-no" 
                                            name="bookable" 
                                            value="0" 
                                            <?php echo ($tour['bookable']==0) ? 'checked' : ''; ?>
                                        >
                                        <label for="bookable-no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label for="description">Tour Description <span class="editable">editable</span></label>
                        <textarea id="description" name="description"
                            rows="4"><?php echo $tour['description'] ?></textarea>
                            <div class="input-group">
                                <label for="status">Status <span class="editable">editable</span></label>
                                <div class="radio-group">
                                    <div class="radio">
                                        <input 
                                            type="radio" 
                                            id="status-yes" 
                                            name="status" 
                                            value="1" 
                                            <?php echo ($tour['status']== 1) ? 'checked' : ''; ?>
                                        >
                                        <label for="status-yes">Active</label>
                                    </div>
                                    <div class="radio">
                                        <input 
                                            type="radio" 
                                            id="status-no" 
                                            name="status" 
                                            value="3" 
                                            <?php echo ($tour['status']==3) ? 'checked' : ''; ?>
                                        >
                                        <label for="status-no">Inactive</label>
                                    </div>
                                </div>
                            </div>

                        <input type="hidden" id="latitude" name="latitude" value="<?php echo $tour['latitude'] ?>">
                        <input type="hidden" id="longitude" name="longitude" value="<?php echo $tour['longitude'] ?>">
                        <button type="submit" class="btn-submit">Save Edit</button>
                    </form>
                </div>
            </div>
        </main>
    </section>



    <script src="../assets/js/script.js"></script>
<script>
    $(document).ready(function () {
    // Set the first image as the main preview if available
    var firstImage = $(".thumbnail-images img").first().attr('src');
    if (firstImage) {
        $('#main-image-preview').attr('src', firstImage);
    }

    // Handle thumbnail click to change the main image
    $(".thumbnail-images img").on("click", function () {
        var imageSrc = $(this).attr("src");
        $('#main-image-preview').attr('src', imageSrc);

        $(".thumbnail-images img").removeClass('selected');
        $(this).addClass('selected');
    });

    // Initialize SweetAlert2 toast notifications
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.on('mouseenter', Swal.stopTimer);
            toast.on('mouseleave', Swal.resumeTimer);
        }
    });

    // Check if the PHP session has a success or error message and show the corresponding SweetAlert
    <?php if (isset($_SESSION['successMessage'])): ?>
        Toast.fire({
            icon: 'success',
            title: '<?php echo $_SESSION['successMessage']; ?>'
        }).then(() => {
            // Optionally, reload the page after showing success message
            window.location.reload();
        });
        <?php unset($_SESSION['successMessage']); // Clear session message ?>
    <?php elseif (isset($_SESSION['errorMessage'])): ?>
        Toast.fire({
            icon: 'error',
            title: '<?php echo $_SESSION['errorMessage']; ?>'
        }).then(() => {
            // Optionally, reload the page after showing error message
            window.location.reload();
        });
        <?php unset($_SESSION['errorMessage']); // Clear session message ?>
    <?php endif; ?>

    $(".delete-icon").on("click", function () {
        var imageSrc = $(this).data('image');
        var imageIndex = $(this).data('index');

        // Confirm deletion
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to undo this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove the image from the thumbnail list
                $(this).closest('.thumbnail-container').remove();

                // Mark this image for deletion by adding it to a hidden input field
                var deletedImages = $('#deleted-images').val();
                deletedImages = deletedImages ? deletedImages + ',' + imageSrc : imageSrc;
                $('#deleted-images').val(deletedImages);
            }
        });
    });

    // When the form is submitted, ensure deleted images are sent
    $("form").on("submit", function () {
        var deletedImages = $('#deleted-images').val();
        if (deletedImages) {
            // Append the deleted images to the form data
            $("<input>").attr({
                type: "hidden",
                name: "deleted-images",
                value: deletedImages
            }).appendTo("form");
        }
    });
let selectedFiles = [];

$('#tour-images').on('change', function (event) {
    const files = event.target.files;
    const $mainImagePreview = $('#main-image-preview');
    const $thumbnailContainer = $('.thumbnail-images');

    selectedFiles = Array.from(files);


    $.each(files, function (index, file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const $img = $('<img>', {
                src: e.target.result,
                alt: `Image ${index + 1}`,
                class: 'thumbnail-image'
            });

            const $closeButton = $('<i>', {
                class: 'bx bx-x',
                'data-index': index,
                title: 'Remove image'
            });

            $img.on('click', function () {
                $mainImagePreview.attr('src', e.target.result);
                $('.thumbnail-images img').removeClass('selected');
                $img.addClass('selected');
            });

            // Add click event to the close button to remove the image from the preview
            $closeButton.on('click', function () {
                // Remove the image from the thumbnail preview
                $img.remove();
                $closeButton.remove();

                // Remove the file from the selectedFiles array
                selectedFiles = selectedFiles.filter((_, i) => i !== index);

                // Update the file input with the remaining selected files
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));

                // Reassign the updated files to the file input
                $('#tour-images')[0].files = dataTransfer.files;

                // Optionally, you can remove the image from the deleted images hidden input
                const deletedImages = $('#deleted-images').val();
                let updatedDeletedImages = deletedImages ? deletedImages + ',' + file.name : file.name;
                $('#deleted-images').val(updatedDeletedImages);
            });

            // Append the image and the close button to the thumbnail container
            const $thumbnailWrapper = $('<div>', { class: 'thumbnail-container' });
            $thumbnailWrapper.append($img, $closeButton);
            $thumbnailContainer.append($thumbnailWrapper);

            // Set the first image as the main preview if it's the first one
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