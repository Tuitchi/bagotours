// TOGGLE SIDEBAR
const menuBar = document.querySelector("#content nav .bx.bx-menu");
const sidebar = document.getElementById("sidebar");

menuBar.addEventListener("click", function () {
  sidebar.classList.toggle("hide");

  const isHidden = sidebar.classList.contains("hide");

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../../php/SideNavHidden.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.send("sidebar_hidden=" + (isHidden ? "1" : "0"));

  xhr.onload = function () {
    if (xhr.status === 200) {
      console.log("Sidebar state saved successfully!");
    } else {
      console.error("Failed to save sidebar state:", xhr.statusText);
    }
  };
  xhr.onerror = function () {
    console.error("Network error: Unable to save sidebar state.");
  };
});

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

const switchMode = document.getElementById("switch-mode");

switchMode.addEventListener("change", function () {
  if (this.checked) {
    document.body.classList.add("dark");
  } else {
    document.body.classList.remove("dark");
  }
});
