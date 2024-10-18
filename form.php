<?php
session_start();
include '../include/db_conn.php';
$pageRole = "user";
require_once '../php/accValidation.php';

$toast = '';
$user_id = $_SESSION['user_id'];

require_once('../func/user_func.php');
registerExpiry($conn, $user_id);
$status = registerStatus($user_id);

if (isset($_GET['process'])) {
    $toast = $_GET['process'];

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Tourist Attraction</title>
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <style>
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            max-height: auto;
            margin: 0;
        }

        #resortOwnerForm {
            margin: 40px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        main input[type="text"],
        main select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #resortLocation {
            float: left;
            width: 70%;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        #resortLoc {
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 15%;
            float: left;
            height: 40px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }


        .upload-area,
        .tourImages {
            width: 100%;
            height: auto;
            border: 2px dashed #ccc;
            text-align: center;
            cursor: pointer;
        }

        .tourImages {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .MainTour {
            border: 1px solid #ccc;
            width: 100%;
            height: 200px;
        }

        .upload-areaTour {
            display: flex;
            border: 1px solid #ccc;
            width: 32.83%;
            height: 100px;
        }


        .upload-area:hover {
            background-color: #f9f9f9;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .prev-btn,
        .next-btn,
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .prev-btn:hover,
        .next-btn:hover,
        input[type="submit"]:hover {
            background-color: #218838;
        }

        .progress-container {
            display: flex;
            margin-bottom: 20px;
        }

        .progress {
            flex: 1;
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-right: 5px;
        }

        .progress.active {
            background-color: #28a745;
            transition: background-color 0.5s ease;
        }

        .progress:last-child {
            margin-right: 0;
        }

        .upload-area img,
        .tourImages img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            position: relative;
            margin: 10% auto;
            padding: 20px;
            background-color: white;
            width: 80%;
            height: 80%;
        }

        .close-map {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
        }

        #map {
            width: 100%;
            height: 94%;
        }

        textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
    </style>
</head>

<body>
    <?php include('inc/topnav.php'); ?>
    <main>
        <?php
        switch ($status) {
            case null:
        ?>
                <form id="resortOwnerForm" enctype="multipart/form-data">
                    <div class="progress-container">
                        <div class="progress active"></div>
                        <div class="progress"></div>
                        <div class="progress"></div>
                        <div class="progress"></div>
                    </div>

                    <div class="step active" id="step1">
                        <h2>Tourist Attraction Details</h2>
                        <label for="resortName">Tourist Attraction Name:</label>
                        <input type="text" id="resortName" name="title" required>

                        <label for="type">Type of Attraction:</label>
                        <select name="type" id="type" required>
                            <option value="none" selected disabled hidden>Select an Option</option>
                            <option value="Beach Resort">Beach Resort</option>
                            <option value="Campsite">Campsite</option>
                            <option value="Falls">Falls</option>
                            <option value="Historical Landmark">Historical Landmark</option>
                            <option value="Mountain Resort">Mountain Resort</option>
                            <option value="Park">Park</option>
                            <option value="Swimming Pool">Swimming Pool</option>
                        </select>

                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4" required></textarea>

                        <div class="step-buttons" style="float:right">
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <div class="step" id="step2">
                        <h2>Tourist Attraction Image</h2>
                        <label for="img">Image:</label>
                        <p style="font-size:smaller;">Insert your tour image below.</p>
                        <div class="tourImages">
                            <input type="file" id="fileInputTour" name="img" accept="image/*" required>
                            <div class="MainTour" id="uploadAreaMainTour"></div>
                            <input type="file" id="fileInputTours" name="tourImage[]" accept="image/*" multiple required style="width: 100%;">
                            <div class="upload-areaTour" id="uploadAreaTour1"></div>
                            <div class="upload-areaTour" id="uploadAreaTour2"></div>
                            <div class="upload-areaTour" id="uploadAreaTour3"></div>
                        </div>

                        <div class="step-buttons">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <div class="step" id="step3">
                        <h2>Location Details</h2>
                        <label for="resortLocation">Location:</label>
                        <p style="font-size:smaller;">Available only in the Bago City area.</p>
                        <div style="clear:both;">
                            <input type="text" id="resortLocation" name="address" required readonly>
                            <button id="resortLoc" type="button"><i class="fa fa-map-marker"></i></button>
                            <select id="barangay" name="barangay" style="width: 44%; float: left;margin-right:10px" required>
                                <option value="none" selected disabled hidden>Select a Barangay</option>
                                <option value="Abuanan">Abuanan</option>
                                <option value="Alianza">Alianza</option>
                                <option value="Atipuluan">Atipuluan</option>
                                <option value="Bacong">Bacong</option>
                                <option value="Bagroy">Bagroy</option>
                                <option value="Balingasag">Balingasag</option>
                                <option value="Binubuhan">Binubuhan</option>
                                <option value="Busay">Busay</option>
                                <option value="Calumangan">Calumangan</option>
                                <option value="Caridad">Caridad</option>
                                <option value="Dulao">Dulao</option>
                                <option value="Ilijan">Ilijan</option>
                                <option value="Lag-asan">Lag-asan</option>
                            </select>
                            <select id="purok" name="purok" style="width: 44%; float: left;" required>
                                <option value="none" selected disabled hidden>Select a Purok</option>
                            </select>
                            <input type="hidden" id="tour-latitude" name="latitude">
                            <input type="hidden" id="tour-longitude" name="longitude">
                        </div><br style="clear:both;" />

                        <div class="step-buttons">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <div id="mapboxModal" class="modal">
                        <div class="modal-content">
                            <span class="close-map">&times;</span>
                            <div id="map"></div>
                        </div>
                    </div>

                    <div class="step" id="step4">
                        <h2>Proof of Permits</h2>

                        <label for="proof">Proof:</label>
                        <select name="proof" id="proof" required>
                            <option value="none" selected disabled hidden>Select an Option</option>
                            <option value="Business permit">Business Permit</option>
                            <option value="Occupancy permit">Occupancy Permit</option>
                            <option value="Building Permit">Building Permit</option>
                            <option value="Mayor's Permit">Mayor's Permit</option>
                            <option value="Barangay Permit">Barangay Permit</option>
                        </select>

                        <p style="font-size:smaller;">Insert your proof image below.</p>
                        <input type="file" id="fileInput" name="proofImage[]" multiple accept="image/*" required>
                        <div class="upload-area" id="proofUploadArea1"></div>
                        <div class="upload-area" id="proofUploadArea2"></div>
                        <div class="upload-area" id="proofUploadArea3"></div>

                        <div class="step-buttons">
                            <button type="button" class="prev-btn">Previous</button>
                            <input type="submit" value="Submit">
                        </div>
                    </div>

                </form>
            <?php
                break;
            case 0:
            ?>
                <p>Your registration is pending approval. You will be notified once approved.</p>
            <?php
                break;
            case 1:
            ?>
                <p>Your registration has been approved. Are you sure you want to become a <strong>tourist attraction owner?</strong>
                    <a href="../php/updateUserStatus.php">Click here!</a>
                </p>
            <?php
                break;
            case 2:
            ?>
                <p>Your registration has been denied. Please try again next week.</p>
            <?php
                break;
            default:
            ?>
                <p>An unknown error occurred. Please try again later.</p>
        <?php
        }
        ?>
    </main>
    <?php include 'inc/footer.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/purok.js"></script>
    <script src="assets/js/form.js"></script>
</body>

</html>