document.addEventListener("DOMContentLoaded", function () {
  let currentStep = 0;
  const steps = document.querySelectorAll(".step");
  const progressBars = document.querySelectorAll(".progress");
  const nextBtns = document.querySelectorAll(".next-btn");
  const prevBtns = document.querySelectorAll(".prev-btn");
  const uploadArea = document.getElementById("uploadArea");

  function showStep(stepIndex) {
    steps.forEach((step, index) => {
      step.classList.toggle("active", index === stepIndex);
      progressBars[index].classList.toggle("active", index <= stepIndex);
    });
  }

  nextBtns.forEach((btn) =>
    btn.addEventListener("click", () => {
      if (validateStep(currentStep)) {
        currentStep++;
        showStep(currentStep);
      }
    })
  );

  prevBtns.forEach((btn) =>
    btn.addEventListener("click", () => {
      currentStep--;
      showStep(currentStep);
    })
  );

  function validateStep() {
    let valid = true;
    const inputs = steps[currentStep].querySelectorAll(
      "input[required], select[required], textarea[required]"
    );

    inputs.forEach((input) => {
      if (!input.value || input.value === "none") {
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

  let marker;

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

  map.on("click", function (e) {
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

  btnSetLocation.onclick = function () {
    mapboxModal.style.display = "block";
    setTimeout(() => {
      map.resize();
      map.flyTo({
        center: [122.9413, 10.4998],
        essential: true,
      });
    }, 200);
  };

  closeMapBtn.onclick = function () {
    mapboxModal.style.display = "none";
  };

  window.onclick = function (event) {
    if (event.target == mapboxModal) {
      mapboxModal.style.display = "none";
    }
  };
  fileInput.addEventListener("change", (event) => {
    event.preventDefault();
    const file = fileInput.files[0];
    let fileType = file.type;
    let validExtensions = ["image/jpeg", "image/jpg", "image/png"];
    if (validExtensions.includes(fileType)) {
      let fileReader = new FileReader();
      fileReader.onload = () => {
        uploadArea.style.backgroundColor = "#fff";
        const img = document.createElement("img");
        img.src = fileReader.result;
        img.alt = "";
        uploadArea.innerHTML = "";
        uploadArea.appendChild(img);
      };
      fileReader.readAsDataURL(file);
    } else {
      Swal.fire({
        icon: "error",
        title: "Invalid File",
        text: "Only JPEG, JPG, and PNG images are allowed.",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  });

  const form = document.getElementById("resortOwnerForm");
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("../php/register_owner.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok.");
        }
        return response.json();
      })
      .then((data) => {
        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Success",
            text: data.message,
            timer: 3000,
            showConfirmButton: false,
          }).then(() => {
            window.location.href = "../user/form?status=success";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.message,
            timer: 3000,
            showConfirmButton: false,
          });
        }
      })
      .catch((error) => {
        console.error("There was a problem with the fetch operation:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "An error occurred while processing your request.",
          timer: 3000,
          showConfirmButton: false,
        });
      });
  });
});
