<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();
    $tour = getTourById($conn, $_SESSION['tour_id']);
?>
<main id="main">
    <div class="head-title">
        <div class="left">
            <h1>Edit Tours</h1>
            <?php include 'includes/breadcrumb.php'; ?>
        </div>
    </div>
    <div id="map" style="height: 400px; width: 80%; margin-top: 20px;"></div>
    <div class="tour-container">
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
                <a href="#" class="btn-edit" onclick="document.getElementById('editTour').submit(); return false;">Save Edit</a>
            </form>
            <a href="tour" class="btn-delete">Cancel</a>
        <?php } else { ?>
            <p>Tour not found.</p>
        <?php } ?>
    </div>
</main>