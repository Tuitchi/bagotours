<style>
  /* .topnav {
  overflow: hidden;
  background-color: #333333b0;
  height: 50px;
  display: flex;
  padding: 0 10px;
  } */
  .topnav {
    align-items: center;
    overflow: hidden;
    background-color: #333333b0;
    height: 50px;
    display: flex;
  }

  .nav__logo {
    flex: 1;
  }

  .nav__logo a {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--white);
    font-family: serif;
    text-decoration: none;
  }

  .topnav a {
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

  .icon {
    margin-right: 10px;
    color: white;
    cursor: pointer;
    transition: color 0.3s;
    font-weight: 600;
    display: none;
  }

  .search-wrapper {
    margin-left: auto;
    /* Pushes the search to the right */
    display: flex;
    align-items: center;
  }

  .search-input {
    padding: 4px;
    border: none;
    margin-left: 20px;
    font-size: 17px;
    border-radius: 10px;
  }

  .author-4 {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin-left: 20px;
    cursor: pointer;
  }

  .profile-dropdown {
    display: none;
    position: absolute;
    right: 10px;
    top: 60px;
    /* Adjust as needed */
    background-color: #333;
    min-width: 150px;
    z-index: 1;
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    /* Align items vertically */
  }

  .profile-dropdown a {
    padding: 12px 16px;
    text-decoration: none;
    color: #f2f2f2;
  }

  .profile-dropdown a:hover {
    background-color: #ddd;
    color: black;
  }


  /* Responsive Styling for Mobile */
  @media screen and (max-width: 768px) {
    .topnav {
      flex-direction: column;
      align-items: flex-start;
    }

    .topnav a {
      display: block;
      width: 100%;
      text-align: left;
      padding: 10px 16px;
    }

    .search-wrapper {
      width: 100%;
      margin: 10px 0;
    }

    .search-input {
      width: calc(100% - 20px);
    }

    .topnav .icon {
      display: block;
      cursor: pointer;
    }

    .topnav.responsive .search-wrapper,
    .topnav.responsive a {
      display: block;
    }
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
  <a href="javascript:void(0);" class="icon" onclick="myFunction()">
    <i class="fa fa-bars"></i>
  </a>
  <img class="author-4" src="../upload/Profile Pictures/<?php echo !empty($_SESSION['profile-pic']) ? $_SESSION['profile-pic'] : 'default.jpg'; ?>" alt="profile-pic" onclick="toggleProfileDropdown()">
  <div id="profileDropdown" class="profile-dropdown">
    <a href="#">
      <i class="fa fa-pencil-square-o"></i> Reviews
    </a>
    <a href="booking">
      <i class="fa fa-address-book-o"></i> Booking
    </a>
    <a href="acc">
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

  function myFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
  }

  function loadBook() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("main").innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", "booking", true);
    xhttp.send();
  }

  function loadManageAcc() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("main").innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", "manageAccount", true);
    xhttp.send();
  }

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
    if (dropdown.style.display === "block") {
      dropdown.style.display = "none";
    } else {
      dropdown.style.display = "block";
    }
  }

  window.onclick = function(event) {
    var dropdown = document.getElementById("profileDropdown");
    var profilePic = document.querySelector('.author-4');

    if (!profilePic.contains(event.target) && !dropdown.contains(event.target)) {
      dropdown.style.display = "none";
    }
  };

  function confirmLogout() {
    return confirm('Do you want to log out?');
  }

  function myFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
  }


  function showTab(tabNumber) {
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
      tab.classList.remove('active');
    });
    document.getElementById('tab' + tabNumber).classList.add('active');
  }
</script>