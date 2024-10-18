
function checkOrientation() {
  if (window.innerHeight > window.innerWidth) {
      // Portrait mode
      document.getElementById('rotate-message').style.display = 'flex';
  } else {
      // Landscape mode
      document.getElementById('rotate-message').style.display = 'none';
  }
}
window.addEventListener('resize', checkOrientation);
window.onload = checkOrientation;

const menuBar = document.querySelector("#content nav .bx.bx-menu");
const sidebar = document.getElementById("sidebar");

window.addEventListener("resize", function () {
  if (window.innerWidth <= 800) {
    sidebar.classList.add("hide");
  } else {
    sidebar.classList.remove("hide");
  }
});

menuBar.addEventListener("click", function () {
  sidebar.classList.toggle("hide");

  const isHidden = sidebar.classList.contains("hide");
  $.ajax({
    url: "/../../php/SideNavHidden.php",
    type: "POST",
    data: {
      sidebar_hidden: isHidden ? "hide" : "",
    },
    success: function (response) {
      console.log("Sidebar state saved successfully!");
    },
    error: function (xhr, status, error) {
      console.error("Failed to save sidebar state:", error);
    },
  });
});

if (window.innerWidth <= 800) {
  sidebar.classList.add("hide");
} else {
  sidebar.classList.remove("hide");
}

const searchIcon = document.getElementById("search-icon");
const searchContainer = document.querySelector(".search-container");
const searchInput = document.getElementById("search-input");

searchIcon.addEventListener("click", () => {
  searchContainer.classList.toggle("expanded");
  if (searchContainer.classList.contains("expanded")) {
    searchInput.focus();
  }
});

const searchButton = document.querySelector(
  "#content nav form .form-input button"
);
const searchButtonIcon = document.querySelector(
  "#content nav form .form-input button .bx"
);
const searchForm = document.querySelector("#content nav form");

searchButton.addEventListener("click", function (e) {
  if (window.innerWidth < 576) {
    e.preventDefault();
    searchForm.classList.toggle("show");
    if (searchForm.classList.contains("show")) {
      searchButtonIcon.classList.replace("bx-search", "bx-x");
    } else {
      searchButtonIcon.classList.replace("bx-x", "bx-search");
    }
  }
});

if (window.innerWidth < 768) {
  sidebar.classList.add("hide");
} else if (window.innerWidth > 576) {
  searchButtonIcon.classList.replace("bx-x", "bx-search");
  searchForm.classList.remove("show");
}

window.addEventListener("resize", function () {
  if (this.innerWidth > 576) {
    searchButtonIcon.classList.replace("bx-x", "bx-search");
    searchForm.classList.remove("show");
  }
});
function checkOrientation() {
  if (window.innerHeight > window.innerWidth) {
      // Portrait mode
      document.getElementById('rotate-message').style.display = 'flex';
  } else {
      // Landscape mode
      document.getElementById('rotate-message').style.display = 'none';
  }
}

// Add an event listener for when the orientation changes
window.addEventListener('resize', checkOrientation);

// Run the function on page load to check the initial orientation
window.onload = checkOrientation;