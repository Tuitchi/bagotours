<div class="topnav" id="myTopnav">
  <div class="nav__logo">
    <a href="../user/home">BagoTours.</a>
  </div>
  <a href="home">Home</a>
  <a href="map">Destination</a>
  <div class="search-wrapper">
    <i class="fa fa-search"></i>
    <input type="text" id="search" class="search-input" placeholder="Search...">
  </div>
  <div id="dropdown" class="dropdown" style="display: none;"></div>
  <a href="javascript:void(0);" class="icon" onclick="toggleResponsiveNav()">
    <i class="fa fa-bars"></i>
  </a>
  <img class="author-4" src="../upload/Profile Pictures/<?php echo !empty($_SESSION['profile-pic']) ? $_SESSION['profile-pic'] : 'default.jpg'; ?>" alt="profile-pic" onclick="toggleProfileDropdown()">
  <div id="profileDropdown" class="profile-dropdown" style="display: none;">
    <a href="review">
      <i class="fa fa-pencil-square-o"></i> Reviews
    </a>
    <a href="booking">
      <i class="fa fa-address-book-o"></i> Booking
    </a>
    <a href="acc">
      <i class="fa fa-user-circle"></i> Manage Account
    </a>
    <a href="setting">
      <i class="fa fa-cog"></i> Settings
    </a>
    <a href="../php/logout.php" onclick="return confirmLogout()">
      <i class="fa fa-sign-out"></i> Logout
    </a>
  </div>
</div>

<script>
  function confirmLogout() {
    return confirm('Do you want to log out?');
  }

  function toggleResponsiveNav() {
    const nav = document.getElementById("myTopnav");
    if (nav.className === "topnav") {
      nav.className += " responsive";
    } else {
      nav.className = "topnav";
    }
  }

  $(document).ready(function() {
    $("#search").on("keyup", function() {
      const query = $(this).val();
      if (query.length > 1) {
        $.ajax({
          url: "../php/search.php",
          method: "POST",
          data: { query: query },
          success: function(data) {
            $("#dropdown").html(data).show();
          }
        });
      } else {
        $("#dropdown").hide();
      }
    });

    $(document).on("click", ".dropdown-item", function() {
      $("#search").val($(this).text());
      $("#dropdown").hide();
    });
  });

  function toggleProfileDropdown() {
    const dropdown = document.getElementById("profileDropdown");
    dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
  }

  window.onclick = function(event) {
    const dropdown = document.getElementById("profileDropdown");
    const profilePic = document.querySelector('.author-4');

    if (!profilePic.contains(event.target) && !dropdown.contains(event.target)) {
      dropdown.style.display = "none";
    }
  };
</script>
