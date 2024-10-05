document.addEventListener('DOMContentLoaded', () => {
  const barangaySelect = document.getElementById('barangay');
  const purokSelect = document.getElementById('purok');
  const steps = document.querySelectorAll(".step");
  const progressBars = document.querySelectorAll(".progress");
  const nextBtns = document.querySelectorAll(".next-btn");
  const prevBtns = document.querySelectorAll(".prev-btn");
  const mapboxModal = document.getElementById("mapboxModal");
  const btnSetLocation = document.getElementById("resortLoc");
  const closeMapBtn = document.querySelector(".close-map");
  const form = document.getElementById("resortOwnerForm");

  let currentStep = 0;
  let marker;

  // Event Listener for Barangay Select
  barangaySelect.addEventListener('change', function() {
      const barangay = this.value;
      purokSelect.innerHTML = '<option value="none" selected disabled hidden>Select a Purok</option>';

      if (puroksByBarangay[barangay]) {
          puroksByBarangay[barangay].forEach(purok => {
              const option = new Option(purok, purok);
              purokSelect.appendChild(option);
          });
      }
  });

  nextBtns.forEach(btn => btn.addEventListener("click", () => handleStepChange(1)));
  prevBtns.forEach(btn => btn.addEventListener("click", () => handleStepChange(-1)));

  form.addEventListener("submit", handleFormSubmit);

  function handleStepChange(direction) {
      if (direction === 1 && !validateStep(currentStep)) return;
      currentStep += direction;
      showStep(currentStep);
  }

  function showStep(stepIndex) {
      steps.forEach((step, index) => {
          step.classList.toggle("active", index === stepIndex);
          progressBars[index].classList.toggle("active", index <= stepIndex);
      });
  }

  function validateStep() {
      const inputs = steps[currentStep].querySelectorAll("input[required], select[required], textarea[required]");
      let valid = true;

      inputs.forEach(input => {
          if (!input.value || input.value === "none") {
              valid = false;
              input.style.borderColor = "red";
          } else {
              input.style.borderColor = "";
          }
      });
      return valid;
  }

  mapboxgl.accessToken =
      "pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ";
  const map = new mapboxgl.Map({
      container: "map",
      style: "mapbox://styles/mapbox/streets-v11",
      center: [122.9413, 10.4998],
      zoom: 10.2,
  });
  map.addControl(new mapboxgl.NavigationControl());
  map.addControl(
      new mapboxgl.GeolocateControl({
          positionOptions: {
              enableHighAccuracy: true,
          },
          trackUserLocation: true,
          showUserHeading: true,
      })
  );

  function reverseGeocode(lng, lat) {
      const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
      fetch(url)
          .then((response) => response.json())
          .then((data) => {
              const placeName = data.features[0]?.place_name || "Location not found";
              document.getElementById("resortLocation").value = placeName;
          })
          .catch((err) => console.error("Error in reverse geocoding: ", err));
  }

  map.on("click", function(e) {
      const lngLat = e.lngLat;
      if (marker) {
          marker.setLngLat(lngLat);
      } else {
          marker = new mapboxgl.Marker().setLngLat(lngLat).addTo(map);
      }
      reverseGeocode(lngLat.lng, lngLat.lat);
      document.getElementById("tour-longitude").value = lngLat.lng;
      document.getElementById("tour-latitude").value = lngLat.lat;
      mapboxModal.style.display = "none";
  });

  btnSetLocation.onclick = function() {
      mapboxModal.style.display = "block";
      setTimeout(() => {
          map.resize();
          map.flyTo({
              center: [122.9413, 10.4998],
              essential: true,
          });
      }, 200);
  };

  closeMapBtn.onclick = function() {
      mapboxModal.style.display = "none";
  };

  window.onclick = function(event) {
      if (event.target == mapboxModal) {
          mapboxModal.style.display = "none";
      }
  };


  function handleFormSubmit(e) {
      e.preventDefault();
      const formData = new FormData(form);
      console.log('Form Data:');
      for (let [key, value] of formData.entries()) {
          console.log(`${key}: ${value}`);
      }

      $.ajax({
          url: "../php/register_owner.php",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: handleAjaxSuccess,
          error: handleAjaxError,
      });
  }

  function handleAjaxSuccess(response) {
      console.log("Registration Response:", response);
      let data = JSON.parse(response);
      Swal.fire({
          icon: data.success === true ? 'success' : 'error',
          title:data.success === true ? 'success' : 'error',
          text :data.success === true ? data.message : data.errors,
          timer: 3000,
          showConfirmButton: false,
      }).then(() => {
          if (data.success) {
          window.location.href = "../user/form?status=success";
      }
      });
  }


  function handleAjaxError(xhr, status, error) {
      console.error("There was a problem with the AJAX operation:", error);
      Swal.fire({
          icon: "error",
          title: "Error",
          text: "An error occurred while processing your request.",
          timer: 3000,
          showConfirmButton: false,
      });
  }

  const imageUploadAreas = [{
          input: document.getElementById("fileInputTour"),
          area: document.getElementById("uploadAreaMainTour")
      },
      {
          input: document.getElementById("fileInput"),
          area: [
              document.getElementById("proofUploadArea1"),
              document.getElementById("proofUploadArea2"),
              document.getElementById("proofUploadArea3"),
          ],
      },
      {
          input: document.getElementById("fileInputTours"),
          area: [
              document.getElementById("uploadAreaTour1"),
              document.getElementById("uploadAreaTour2"),
              document.getElementById("uploadAreaTour3"),
          ],
      }
  ];

  imageUploadAreas.forEach(({
      input,
      area
  }) => {
      input.addEventListener("change", () => showMultipleImagePreview(input, area));
  });

  function showMultipleImagePreview(input, area) {
      const files = input.files;
      console.log(area);

      if (files.length > 3) {
          alert(`You can only upload up to 3 images.`);
          input.value = "";
          area.forEach(a => a.innerHTML = "No files chosen");
          return;
      }

      if (Array.isArray(area)) {
          area.forEach(a => a.innerHTML = "");
      } else {
          area.innerHTML = "";
      }

      // Loop through files and show previews
      Array.from(files).forEach((file, index) => {
          if (file) {
              const reader = new FileReader();
              reader.onload = () => {
                  if (Array.isArray(area)) {
                      if (index < area.length) {
                          area[index].innerHTML = `<img src="${reader.result}" alt="Image Preview">`;
                      }
                  } else {
                      area.innerHTML = `<img src="${reader.result}" alt="Image Preview">`;
                  }
              };
              reader.readAsDataURL(file);
          } else {
              if (Array.isArray(area)) {
                  area.forEach(a => a.innerHTML = "No file chosen");
              } else {
                  area.innerHTML = "No file chosen";
              }
          }
      });
  }

});