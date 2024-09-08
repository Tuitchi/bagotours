<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Tourist Attraction</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #resortOwnerForm {
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

        input[type="text"],
        select,
        input[type="file"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #resortLocation {
            float: left;
            width: 80%;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        #resortLoc {
            width: 15%;
            float: left;
            height: 40px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .upload-area {
            border: 2px dashed #ccc;
            text-align: center;
            cursor: pointer;
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
        }

        .progress:last-child {
            margin-right: 0;
        }

        .upload-area img {
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
        <form id="resortOwnerForm" action="../php/register_owner.php" method="post" enctype="multipart/form-data">
            <div class="progress-container">
                <div class="progress active"></div>
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

                <div class="step-buttons">
                    <button type="button" class="next-btn">Next</button>
                </div>
            </div>

            <div class="step" id="step2">
                <h2>Location Details</h2>
                <label for="resortLocation">Location:</label>
                <div style="clear:both;">
                    <input type="text" id="resortLocation" name="address" readonly>
                    <button id="resortLoc" type="button"><i class="fa fa-map-marker"></i></button>
                    <select id="barangay" name="barangay" style="width: 44%; float: left;" required>
                        <option value="none" selected disabled hidden>Select a Barangay</option>
                        <option value="Abuanan">Abuanan</option>
                        <option value="Alianza">Alianza</option>
                    </select>
                    <select id="purok" name="purok" style="width: 44%; float: left;" required>
                        <option value="none" selected disabled hidden>Select a Purok</option>
                        <option value="Abuanan">Abuanan</option>
                        <option value="Alianza">Alianza</option>
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

            <div class="step" id="step3">
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
                <div class="upload-area" id="uploadArea">
                    <p>Drag & Drop or Click to Upload File</p>
                    <input type="file" id="fileInput" name="proofImage" hidden required>
                </div>

                <div class="step-buttons">
                    <button type="button" class="prev-btn">Previous</button>
                    <input type="submit" value="Submit">
                </div>
            </div>
        </form>
    </main>
    <?php include 'inc/footer.php' ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 0;
            const steps = document.querySelectorAll(".step");
            const progressBars = document.querySelectorAll(".progress");
            const nextBtns = document.querySelectorAll(".next-btn");
            const prevBtns = document.querySelectorAll(".prev-btn");
            const uploadArea = document.getElementById("uploadArea");
            const fileInput = document.getElementById("fileInput");


            function showStep(stepIndex) {
                steps.forEach((step, index) => {
                    step.classList.toggle("active", index === stepIndex);
                    progressBars[index].classList.toggle("active", index <= stepIndex);
                });
            }

            nextBtns.forEach(btn => btn.addEventListener("click", () => {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                }
            }));

            prevBtns.forEach(btn => btn.addEventListener("click", () => {
                currentStep--;
                showStep(currentStep);
            }));

            function validateStep() {
                let valid = true;
                const inputs = steps[currentStep].querySelectorAll("input[required], select[required], textarea[required]");

                inputs.forEach(input => {
                    if (!input.value || input.value === 'none') {
                        valid = false;
                        input.style.borderColor = "red";
                    } else {
                        input.style.borderColor = "";
                    }
                });
                return valid;
            }

            const mapboxModal = document.getElementById("mapboxModal");
            const btnSetLocation = document.getElementById("resortLoc");
            const closeMapBtn = document.querySelector(".close-map");

            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';
            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [122.9413, 10.4998],
                zoom: 10.2
            });
            map.addControl(new mapboxgl.NavigationControl());
            map.addControl(new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true
                },
                trackUserLocation: true,
                showUserHeading: true
            }));

            let marker;

            function reverseGeocode(lng, lat) {
                const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const placeName = data.features[0]?.place_name || "Location not found";
                        document.getElementById('resortLocation').value = placeName;
                    })
                    .catch(err => console.error("Error in reverse geocoding: ", err));
            }

            map.on('click', function(e) {
                const lngLat = e.lngLat;
                if (marker) {
                    marker.setLngLat(lngLat);
                } else {
                    marker = new mapboxgl.Marker().setLngLat(lngLat).addTo(map);
                }
                reverseGeocode(lngLat.lng, lngLat.lat);
                document.getElementById('tour-longitude').value = lngLat.lng;
                document.getElementById('tour-latitude').value = lngLat.lat;
                mapboxModal.style.display = "none";
            });

            btnSetLocation.onclick = function() {
                mapboxModal.style.display = "block";
                map.resize();
            };

            closeMapBtn.onclick = function() {
                mapboxModal.style.display = "none";
            };

            window.onclick = function(event) {
                if (event.target == mapboxModal) {
                    mapboxModal.style.display = "none";
                }
            };

            uploadArea.addEventListener('click', () => fileInput.click());

            uploadArea.addEventListener('dragover', (event) => {
                event.preventDefault();
                uploadArea.style.backgroundColor = '#f4f4f4';
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.style.backgroundColor = '#fff';
            });

            uploadArea.addEventListener('drop', (event) => {
                event.preventDefault();
                const file = event.dataTransfer.files[0];
                DisplayFile(file);

            });
            fileInput.addEventListener('change', (event) => {
                event.preventDefault();
                const file = fileInput.files[0];
                DisplayFile(file);
            });

            function DisplayFile(file) {
                let fileType = file.type;
                let validExtensions = ['image/jpeg', 'image/jpg', 'image/png'];
                if (validExtensions.includes(fileType)) {
                    let fileReader = new FileReader();
                    fileReader.onload = () => {
                        uploadArea.style.backgroundColor = '#fff';
                        const img = document.createElement('img');
                        img.src = fileReader.result;
                        img.alt = '';
                        uploadArea.innerHTML = '';
                        uploadArea.appendChild(img);
                    };
                    fileReader.readAsDataURL(file);
                } else {
                    alert('Invalid file type. Only JPEG, JPG, PNG images are allowed.');
                }
            }

        });
    </script>
</body>

</html>