<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();
$tour = getTourById($conn, $_SESSION['tour_id']);
?>
<main id="main">
    <div class="head-title">
        <div class="left">
            <h1>View Tours</h1>
            <?php include 'includes/breadcrumb.php'; ?>
        </div>
    </div>
    <div class="tour-container">
        <div class="head">
            <h1><?php echo htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <i class='bx bx-edit-alt' onclick="loadDoc()"></i>
        </div>
        <div class="detail-container">
            <?php if (!empty($tour)) { ?>
                <form id="editTour" action="update_tour.php" method="POST">
                    <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <h1>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </h1>
                    <img src="../upload/Tour Images/<?php echo htmlspecialchars($tour['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Tour Image">
                    <p>
                        <strong>Address:</strong>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($tour['address'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </p>
                    <p>
                        <strong>Type:</strong>
                        <select id="tour-type" name="type" required>
                            <option value="Mountain Resort" <?php echo ($tour['type'] == 'Mountain Resort') ? 'selected' : ''; ?>>Mountain Resort</option>
                            <option value="Beach Resort" <?php echo ($tour['type'] == 'Beach Resort') ? 'selected' : ''; ?>>Beach Resort</option>
                            <option value="Historical Landmark" <?php echo ($tour['type'] == 'Historical Landmark') ? 'selected' : ''; ?>>Historical Landmark</option>
                            <option value="Park" <?php echo ($tour['type'] == 'Park') ? 'selected' : ''; ?>>Park</option>
                            <option value="Campsite" <?php echo ($tour['type'] == 'Campsite') ? 'selected' : ''; ?>>Campsite</option>
                        </select>
                    </p>                                                                                                                                                                                                            
                    <p>
                        <strong>Description:</strong>
                        <input type="text" name="description" value="<?php echo htmlspecialchars($tour['description'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </p>
                    <p>
                        <strong>Status:</strong>
                    <div class="status-container">
                        <input type="radio" id="status1" name="status" value="1" <?php echo ($tour['status'] == 1) ? 'checked' : ''; ?>>
                        <label for="status1">Active</label>
                        <input type="radio" id="status2" name="status" value="2" <?php echo ($tour['status'] == 2) ? 'checked' : ''; ?>>
                        <label for="status2">Inactive</label>
                    </div>
                    </p>
                <?php } else { ?>
                    <p>Tour not found.</p>
                <?php } ?>
        </div>
        <div id="tour-images">
            <h2>Images</h2>
            <div class="row">
                <?php if (!empty($tourImage)) {
                    foreach ($tourImage as $img) { ?>
                        <div class="col-md-3">
                            <img src="../upload/Tour Images/<?php echo htmlspecialchars($img['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Tour Image" style="width: 100%; border-radius: 10px;">
                        </div>
                    <?php }
                } else { ?>
                    <p>No images found.</p>
                <?php } ?>
            </div>
        </div>
        <a href="#" class="btn-edit" onclick="document.getElementById('editTour').submit(); return false;">Save Edit</a>
        </form>
        <a href="tour" class="btn-delete">Cancel</a>
    </div>
</main>