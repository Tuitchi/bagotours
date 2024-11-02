<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
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
        color: #007bff;
        /* Slightly modern blue color on hover */
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
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        /* Subtle blue glow on hover */
    }

    /* Dropdown menu styling */
    .notification-menu,
    .account-menu {
        display: none;
        position: absolute;
        background-color: white;
        border: none;
        border-radius: 8px 0 8px 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 15px;
        z-index: 1000;
        right: 0;
        top: 40px;
    }

    .notification-menu {
        width: 600px;
        height: auto;
        max-height: 80vh;
        top: 23px;
        overflow-y: scroll;
    }

    .notification-menu::-webkit-scrollbar-thumb {
        background-image:
            linear-gradient(to bottom, rgb(0, 0, 85), rgb(0, 0, 50));
    }

    .notification-menu::-webkit-scrollbar {
        width: 5px;
    }

    .notification-menu::-webkit-scrollbar-track {
        background-color: #9e9e9eb2;
    }

    /* Notification item styling */
    .notification-menu p {
        margin: 10px 0;
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-family: 'Arial', sans-serif;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }

    .notification-menu span {
        float: right;
        color: #6c757d;
        margin-top: 10px;
    }

    .notification-menu p:last-child {
        border-bottom: none;
    }

    .notification-menu p:hover {
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    /* Account menu styling */
    .account-menu {
        width: 190px;
    }

    .account-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .account-menu li {
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-family: 'Arial', sans-serif;
        font-size: 14px;
        color: #333;
    }

    @media screen and (max-width:850px) {
        header.expanded .notification,
        header.expanded .dp {
            width: 0;
            opacity: 0;
            overflow: hidden;
        }
    }

    .account-menu li:not(:last-child) {
        border-bottom: 1px solid #e9ecef;
    }

    .account-menu li:hover {
        background-color: #e2e6ea;
        color: #007bff;
        border-radius: 5px;
    }

    .notification a {
        font-size: 15px;
        text-decoration: none;
    }

    .read {
        background-color: #f8f9fa;
        color: #6c757d;
        /* Muted text */
    }

    .unread {
        background-color: #007bff;
        font-weight: bold;
        color: #333;
        /* White text */
    }
</style>

<header>
    <div class="logosec">
        <img src="assets/burger.png" class="icn menuicn" id="menuicn" alt="menu-icon">
        <div class="logo">BagoTours</div>
    </div>


    <div class="message">
        <div class="searchbar">
            <form action="search" method="GET">
                <input type="text" name="q" id="search" placeholder="Search"><i class='bx bx-search-alt'></i>
            </form>
            <div id="dropdown"></div>
        </div>
        <?php
        if (empty($user_id)) {
            echo "<button id='open-modal' class='login'>Login</button>";
        } else {
            echo "
            <div class='notification' onclick='toggleNotificationMenu()'>
                <i class='fa fa-bell'></i>
                <div class='notification-menu' id='notificationMenu'>";
            require_once("func/func.php");
            $notif = getNotifications($conn, $user_id);
            if (!empty($notif)) {
                foreach ($notif as $i) {
                    $readClass = $i['is_read'] ? 'read' : 'unread';

                    // Convert the created_at date to the desired format M. D, Y (e.g., Oct. 21, 2024)
                    $formattedDate = date('M. d, Y', strtotime($i['created_at']));

                    // Output the notification with the formatted date
                    echo "<a class='$readClass' href='" . $i['url'] . "' data-id='" . $i['id'] . "'>
                            <p>" . $i['message'] . " <span>" . $formattedDate . "</span></p>
                          </a><hr>";
                }
            } else {
                echo "<p>No Notification.</p>";
            }
            echo "
                </div>
            </div>
                
            <div class='dp' onclick='toggleAccountMenu()'>
                <img src='upload/Profile Pictures/" . $_SESSION['profile-pic'] . "' class='dpicn' alt='dp'>
                <div class='account-menu' id='accountMenu'>
                    <ul>
                        <li><a href='manage-acc'>Manage Account</a></li>
                        <li><a href='booking'>Bookings</a></li>
                        <li>Settings</li>
                        <li><a href='php/logout.php'>Logout</a></li>
                    </ul>
                </div>
            </div>";
        }
        ?>
    </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const searchIcon = document.querySelector(".bx-search-alt");
    const searchContainer = document.querySelector(".searchbar");
    const searchInput = document.getElementById("search");
    const header = document.querySelector("header");

    if (window.innerWidth <= 850) {
        searchIcon.addEventListener("click", (event) => {
            event.stopPropagation();
            searchContainer.classList.toggle("expanded");
            if (searchContainer.classList.contains("expanded")) {
                header.classList.add("expanded");
                searchInput.focus();
            } else {
                searchInput.blur();
                setTimeout(() => {
                    header.classList.remove("expanded");
                }, 10);
            }
        });
        document.addEventListener("click", (event) => {
            if (!searchContainer.contains(event.target) && searchContainer.classList.contains("expanded")) {
                searchContainer.classList.remove("expanded");
                setTimeout(() => {
                    header.classList.remove("expanded");
                }, 10);
            }
        });
    }


    $(document).ready(function () {
        let debounceTimer;
        $("#search").on("keyup", function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                const query = $("#search").val();
                if (query.length > 1) {
                    $("#dropdown").html("<div style='padding:10px;'>Loading...</div>").show();

                    $.ajax({
                        url: "php/search.php",
                        method: "POST",
                        data: { query: query },
                        success: function (data) {
                            $("#dropdown").html(data).show();
                        },
                        error: function (xhr, status, error) {
                            console.error("Error: " + error);
                            $("#dropdown").html("<div style='padding:10px;'>Error fetching results</div>").show();
                        }
                    });
                } else {
                    $("#dropdown").hide();
                }
            }, 500);
        });

        $(document).click(function (event) {
            if (!$(event.target).closest('.search-wrapper').length) {
                $("#dropdown").hide();
            }
        });

        $(document).on("click", ".dropdown-item", function () {
            $("#search").val($(this).text());
            $("#dropdown").hide();
        });
    });

    var notificationMenu = document.getElementById("notificationMenu");
    var accountMenu = document.getElementById("accountMenu");

    window.onclick = function (event) {
        if (event.target.matches('.fa-bell')) {
            notificationMenu.style.display = notificationMenu.style.display === "block" ? "none" : "block";
            accountMenu.style.display = "none";
        } else if (event.target.matches('.dpicn')) {
            accountMenu.style.display = accountMenu.style.display === "block" ? "none" : "block";
            notificationMenu.style.display = "none";
        } else {
            notificationMenu.style.display = "none";
            accountMenu.style.display = "none";
        }
    };
    $(document).on('click', '.notification-menu a', function (e) {
        e.preventDefault();

        var notificationId = $(this).data('id');
        var url = $(this).attr('href');

        // Send an AJAX request to mark the notification as read
        $.ajax({
            url: 'php/updateNotificationStatus.php',  // PHP script to handle the update
            method: 'POST',
            data: { id: notificationId },
            success: function (response) {
                console.log('Notification marked as read');
                window.location.href = url; // Redirect to the notification's URL
            },
            error: function (xhr, status, error) {
                console.error('Error: ' + error);
            }
        });
    });

</script>