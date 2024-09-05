<style>
  .dropdown {
    position: absolute;
    top: 39px;
    left: 338px;
    max-width: 400px;
    min-width: 300px;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: flex;
    flex-direction: column;
  }

  .dropdown-item {
    width: 300px;
    cursor: pointer;
    color: black;
  }

  .dropdown-item:hover {
    background-color: #f1f1f1;
  }

  .icon {
    display: none;
  }

  /* Dropdown for profile picture */
  .profile-dropdown {
    position: absolute;
    right: 10px; /* Adjust as needed */
    top: 50px;
    width: 150px;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: none; /* Initially hidden */
    z-index: 1000;
  }

  .profile-dropdown a {
    text-align: left;
    font-size: small;
    padding: 10px;
    display: block;
    color: #333;
    text-decoration: none;
    width: 100%; /* Ensures links take full width */
    box-sizing: border-box; /* Include padding in width */
  }


  .profile-dropdown a:hover {
    background-color: #f1f1f1;
  }
</style>

<div class="topnav" id="myTopnav">
  <div class="nav__logo"><a href="../user/home">BagoTours.</a></div>
  <a href="home">Home</a>
  <a href="map">Destination</a>
  <input type="text" id="search" class="search-input" placeholder="Search...">
  <div id="dropdown" class="dropdown" style="display: none;"></div>
  <a href="javascript:void(0);" class="icon" onclick="myFunction()">
    <i class="fa fa-bars"></i>
  </a>

  <img class="author-4" src="../upload/Profile Pictures/<?php echo !empty($_SESSION['profile-pic']) ? $_SESSION['profile-pic'] : 'default.jpg'; ?>" alt="profile-pic" width="40" height="40" onclick="toggleProfileDropdown()">

  <div id="profileDropdown" class="profile-dropdown">
  <a href="#">
    <i class="	fa fa-pencil-square-o"></i> Reviews
  </a>
  <a href="#">
    <i class="fa fa-address-book-o"></i> Bookings
  </a>
  <a href="#">
    <i class="fa fa-user-circle"></i> Manage Account
  </a>
  <a href="#">
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
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $("#search").on("keyup", function() {
      let query = $(this).val();
      if (query.length > 1) {
        $.ajax({
          url: "../php/search.php",
          method: "POST",
          data: {
            query: query
          },
          success: function(data) {
            $("#dropdown").html(data);
            $("#dropdown").css("display", "block");
          }
        });
      } else {
        $("#dropdown").css("display", "none");
      }
    });

    $(document).on("click", ".dropdown-item", function() {
      $("#search").val($(this).text());
      $("#dropdown").css("display", "none");
    });
  });
  function toggleProfileDropdown() {
    var dropdown = document.getElementById("profileDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
  }
  window.onclick = function(event) {
    if (!event.target.matches('.author-4')) {
      var dropdown = document.getElementById("profileDropdown");
      if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }
  };
</script>
