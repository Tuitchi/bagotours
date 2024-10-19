<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    /* Notification button styling */
.notification {
    position: relative;
    cursor: pointer;
}

.notification i {
    font-size: 24px;
    color: #333;
    transition: color 0.3s ease;
}

.notification:hover i {
    color: #007bff; /* Slightly modern blue color on hover */
}

/* Profile picture */
.dpicn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    transition: box-shadow 0.3s ease;
}

.dpicn:hover {
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.5); /* Subtle blue glow on hover */
}

/* Dropdown menu styling */
/* Notification menu styling */
.notification-menu {
    display: none;
    position: absolute;
    background-color: white;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Softer shadow for a modern look */
    width: 250px; /* Increased width for better spacing */
    padding: 15px;
    margin-top: 10px;
    z-index: 10; /* Ensure it appears above other elements */
    right: 0; /* Align the notification menu with the notification icon */
}

/* Notification item styling */
.notification-menu p {
    margin: 0;
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-family: 'Arial', sans-serif; /* Modern font */
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #e9ecef; /* Divider between notifications */
}

.notification-menu p:last-child {
    border-bottom: none; /* Remove the divider from the last item */
}

.notification-menu p:hover {
    background-color: #f8f9fa; /* Light grey background on hover */
    border-radius: 5px;
}

/* Positioning the dropdown menu directly below the icon */
.notification-menu {
    top: 35px; /* Adjust this value to position below the notification icon */
}



.account-menu {
    display: none;
    position: absolute;
    background-color: white;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Softer shadow for a modern look */
    padding: 15px;
    margin-top: 10px;
    z-index: 1000; /* Ensure it appears above other elements */
}

/* Modern styling for dropdown content */
.account-menu li {
    margin: 0;
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-family: 'Arial', sans-serif; /* Modern font */
    font-size: 14px;
    color: #333;
}


.account-menu li:hover {
    background-color: #f8f9fa; /* Light grey background on hover */
    border-radius: 5px;
}

/* Positioning the dropdown menus directly below the icons */


.account-menu {
    top: 55px; /* Adjust this value to position below the profile picture */
    right: 0;
}

/* Specific styling for the account menu */
.account-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.account-menu li {
    padding: 10px 15px;
}

.account-menu li:not(:last-child) {
    border-bottom: 1px solid #e9ecef; /* Separator between items */
}

.account-menu li:hover {
    background-color: #e2e6ea; /* Slightly darker hover effect */
    color: #007bff; /* Modern blue color on hover */
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
        <?php 
        if (empty($user_id)) {
            echo "<button id='open-modal' class='login'>Login</button>";
        } else {
            echo "
            <div class='notification' onclick='toggleNotificationMenu()'>
                <i class='fa fa-bell'></i>
                <div class='notification-menu' id='notificationMenu'>
                    <p>No new notifications</p>
                    <p>No new notifications</p>
                    <p>No new notifications</p>
                    <p>No new notifications</p>
                </div>
            </div>
                
                <div class='dp' onclick='toggleAccountMenu()'>
                    <img src='https://media.geeksforgeeks.org/wp-content/uploads/20221210180014/profile-removebg-preview.png' 
                        class='dpicn' 
                        alt='dp'>
                    <div class='account-menu' id='accountMenu'>
                        <ul>
                            <li>Manage Account</li>
                            <li>Settings</li>
                            <li>Logout</li>
                        </ul>
                    </div>
                </div>
            ";
        }
        ?>
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
<script>
    function toggleNotificationMenu() {
    const notificationMenu = document.getElementById("notificationMenu");
    notificationMenu.style.display = notificationMenu.style.display === "block" ? "none" : "block";
}

// Toggle the account menu
function toggleAccountMenu() {
    const accountMenu = document.getElementById("accountMenu");
    accountMenu.style.display = accountMenu.style.display === "block" ? "none" : "block";
}

// Close the menus if clicking outside
window.onclick = function (event) {
    if (!event.target.matches('.fa-bell') && !event.target.matches('.dpicn')) {
        document.getElementById("notificationMenu").style.display = "none";
        document.getElementById("accountMenu").style.display = "none";
    }
};

</script>