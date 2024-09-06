<div class="topnav" id="myTopnav">
  <div class="nav__logo"><a href="../user/home">BagoTours.</a></div>
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

  <img class="author-4" src="../upload/Profile Pictures/<?php echo !empty($_SESSION['profile-pic']) ? $_SESSION['profile-pic'] : 'default.jpg'; ?>" alt="profile-pic" width="40" height="40" onclick="toggleProfileDropdown()">

  <div id="profileDropdown" class="profile-dropdown">
    <a href="#">
      <i class="	fa fa-pencil-square-o"></i> Reviews
    </a>
    <a href="booking">
      <i class="fa fa-address-book-o"></i> Booking
    </a>
    <a href="manageAccount">
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
<script>
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