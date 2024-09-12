<style>
  .topnav {
  overflow: hidden;
  background-color: #333333b0;
  height: 50px;

}

.nav__logo a {
  margin-left: 20px;
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--white);
  font-family: serif;
}

.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #04AA6D;
  color: white;
}

.topnav input[type=text] {
  padding-left: 40px;
  background-color: #fff;
  border: 1px solid #ccc;
  height: 30px;
  margin-top: 7px;
  margin-left: 20px;
  border-radius: 4px;
  font-size: 17px;
  position: relative;
}

.search-wrapper {
  position: relative;
  display: inline-block;
}

.search-wrapper .fa-search {
  position: absolute;
  top: 55%;
  left: 30px;
  transform: translateY(-50%);
  font-size: 14px;
  color: black;
  z-index: 10;
}

#search {
  padding-left: 35px;
  width: 200px;
  z-index: 1;
}

.author-4 {

  float: right;
  width: 45px;
  height: 45px;
  border-radius: 100px;
  margin-top: 3px;
  margin-right: 20px;
}

.topnav .icon {
  display: none;
}

.dropdown {
  text-align: left;
  position: absolute;
  top: 39px;
  left: 355px;
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

.profile-dropdown {
  position: absolute;
  right: 10px;
  top: 50px;
  width: 150px;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  display: none;
  z-index: 1000;
}

.profile-dropdown a {
  text-align: left;
  font-size: small;
  padding: 10px;
  display: block;
  color: #333;
  text-decoration: none;
  width: 100%;
  box-sizing: border-box;
}


.profile-dropdown a:hover {
  background-color: #f1f1f1;
}

.author-4 {
  float: right;
  width: 45px;
  height: 45px;
  border-radius: 100px;
  margin-top: 3px;
  margin-right: 30px;
}

.topnav .icon {
  display: none;
}

.content {
  display: block;
  margin-top: 10px;
  width: 90%;
  margin: auto;
}

</style>
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
