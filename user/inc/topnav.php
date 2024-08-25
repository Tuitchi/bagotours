<style>
  .dropdown {
    position: absolute;
    top: 39px;
    left: 420px;
    width: calc(100% - 1124px);
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: flex;
    flex-direction: column;
  }

  .dropdown-item {
    padding: 10px;
    cursor: pointer;
    color: #333;
  }

  .dropdown-item:hover {
    background-color: #f1f1f1;
  }

  .icon {
    display: none;
  }
</style>
<div class="topnav" id="myTopnav">
  <div class="nav__logo"><a href="../user/home">BagoTours.</a></div>
  <a href="home">Home</a>
  <a href="map">destination</a>
  <input type="text" id="search" class="search-input" placeholder="Search...">
  <div id="dropdown" class="dropdown" style="display: none;"></div>
  <a href="javascript:void(0);" class="icon" onclick="myFunction()">
    <i class="fa fa-bars"></i>
  </a>
  <img class="author-4" src="../upload/Profile Pictures/<?php echo !empty($_SESSION['profile-pic']) ? $_SESSION['profile-pic'] : 'default.jpg'; ?>" alt="profile-pic">

  <a href="../php/logout.php">Logout</a>
</div>
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
</script>