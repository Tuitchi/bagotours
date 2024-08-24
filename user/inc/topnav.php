<div class="topnav" id="myTopnav">
  <div class="nav__logo"><a href="#">BagoTours.</a></div>
  <a href="home">Home</a>
  <a href="map">destination</a>
  <input type="text" id="search" class="search-input" placeholder="Search...">
  <div id="dropdown" class="dropdown" style="display: none;"></div>
  <a href="javascript:void(0);" class="icon" onclick="myFunction()">
    <i class="fa fa-bars"></i>
  </a>
  <img class="author-4" src="../upload/Profile Pictures/<?php echo $_SESSION['profile-pic'] ?>" alt="">
  <a href="../php/logout.php">Logout</a>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $("#search").on("keyup", function() {
      let query = $(this).val();
      if (query.length > 2) {
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