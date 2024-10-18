<style>
    .search-wrapper {
        position: relative;
        flex-grow: 1;
        max-width: 400px;
        margin-right: 20px;
    }

    .search-wrapper .fa-search {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        font-size: 16px;
        color: black;
    }

    .search-input {
        width: 100%;
        padding: 8px 8px 8px 40px;
        border-radius: 4px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    #dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
    }
</style>
<header>
    <div class="logosec">
        <img src="assets/burger.png"
            class="icn menuicn" id="menuicn" alt="menu-icon">
        <div class="logo">BagoTours</div>
    </div>

    <div class="searchbar">
        <input type="text" id="search" placeholder="Search">
        <div class="searchbtn">
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png"
                class="icn srchicn" alt="search-icon">
        </div>
    </div>

    <div class="message">
        <?php if (empty($user_id)) {
            echo "<button id='open-modal' class='login'>Login</button>";
        } else {
            echo "<div class='circle'></div>
            <img src='https://media.geeksforgeeks.org/wp-content/uploads/20221210183322/8.png' class='icn' alt=''>
            <div class='dp'>
                <img src='https://media.geeksforgeeks.org/wp-content/uploads/20221210180014/profile-removebg-preview.png'
                    class='dpicn' alt='dp'>
            </div>";
        } ?>
    </div>
</header>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#search").on("keyup", function() {
            const query = $(this).val();
            if (query.length > 1) {
                $("#dropdown").html("<div style='padding:10px;'>Loading...</div>").show();

                $.ajax({
                    url: "php/search.php",
                    method: "POST",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $("#dropdown").html(data).show();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error);
                        $("#dropdown").html("<div style='padding:10px;'>Error fetching results</div>").show();
                    }
                });

            } else {
                $("#dropdown").hide();
            }
        });

        $(document).click(function(event) {
            if (!$(event.target).closest('.search-wrapper').length) {
                $("#dropdown").hide();
            }
        });

        $(document).on("click", ".dropdown-item", function() {
            $("#search").val($(this).text());
            $("#dropdown").hide();
        });
    });
</script>