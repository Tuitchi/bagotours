let menuicn = document.querySelector(".menuicn");
let nav = document.querySelector(".navcontainer");

menuicn.addEventListener("click", (event) => {
  event.stopPropagation();
  nav.classList.toggle("navclose");
});

const slide = document.querySelector(".carousel-slide");
const indicators = document.querySelectorAll(".carousel-indicators div");
let currentIndex = 0;
const totalSlides = document.querySelectorAll(".carousel-item").length;
let slideWidth = slide.offsetWidth;

// Function to move to a specific slide
function goToSlide(index) {
  currentIndex = index;
  slide.style.transform = `translateX(-${index * slideWidth}px)`;
  updateIndicators();
}

// Function to update active indicator
function updateIndicators() {
  indicators.forEach((indicator, index) => {
    indicator.classList.remove("active");
    if (index === currentIndex) {
      indicator.classList.add("active");
    }
  });
}

// Next and Previous slide functions
function nextSlide() {
  currentIndex = (currentIndex + 1) % totalSlides;
  goToSlide(currentIndex);
}

function prevSlide() {
  currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
  goToSlide(currentIndex);
}

// Autoplay functionality
function autoPlay() {
  nextSlide();
}

let autoPlayInterval = setInterval(autoPlay, 10000);

// Recalculate slide width on window resize
window.addEventListener("resize", () => {
  slideWidth = slide.offsetWidth;
  goToSlide(currentIndex);
});

// JavaScript Logic for numbered pagination
const cards = document.querySelectorAll(".cards");
const cardsPerPage = 8; // Number of cards to display per page
let currentPage = 1;

function displayCards(page) {
  const startIndex = (page - 1) * cardsPerPage;
  const endIndex = startIndex + cardsPerPage;

  cards.forEach((card, index) => {
    if (index >= startIndex && index < endIndex) {
      card.style.display = "block";
      card.classList.add("show"); // Add class to trigger fade-in
    } else {
      card.style.display = "none";
      card.classList.remove("show"); // Remove class to prevent fade-in
    }
  });

  updatePagination();
}

function updatePagination() {
  const paginationContainer = document.getElementById("pagination");
  paginationContainer.innerHTML = ""; // Clear previous pagination buttons
  const totalPages = Math.ceil(cards.length / cardsPerPage);

  for (let i = 1; i <= totalPages; i++) {
    const button = document.createElement("button");
    button.textContent = i;
    if (i === currentPage) {
      button.classList.add("active");
    }
    button.addEventListener("click", () => {
      currentPage = i;
      displayCards(currentPage);
    });
    paginationContainer.appendChild(button);
  }
}

// Initial setup
displayCards(currentPage);