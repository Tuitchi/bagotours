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
        width: 70vw;
        height: auto;
        max-height: 80vw;
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

    .account-menu a {
        text-decoration: none;
        font-weight: 700;
    }

    .account-menu li {
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-family: 'Arial', sans-serif;
        font-size: 14px;
        color: #71a3c1;
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

    .notification .num {
        position: absolute;
        top: -5px;
        right: 3px;
        font-weight: 500;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 3px 5px;
        width: 18px;
        height: 18px;
        font-size: 12px;
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
        <?php if (empty($user_id)): ?>
            <button id="open-modal" class="login">Login</button>
        <?php else: ?>
            <?php
            require_once("func/func.php");
            $notif = getNotifications($conn, $user_id);
            $unreadNotifications = array_filter($notif, function ($notifItem) {
                return $notifItem['is_read'] == 0;
            });

            // Count unread notifications
            $notifCount = count($unreadNotifications);
            ?>
            <div class="notification" onclick="toggleNotificationMenu()">
                <i class="fa fa-bell">
                    <span id="notification-count" class="num"><?= $notifCount ?></span>
                </i>
                <div class="notification-menu" id="notificationMenu">
                    <?php if (!empty($notif)): ?>
                        <?php foreach ($notif as $i): ?>
                            <?php
                            $readClass = $i['is_read'] ? 'read' : 'unread';
                            $formattedDate = date('M. d, Y', strtotime($i['created_at']));
                            ?>
                            <a class="<?= $readClass ?>" href="<?= $i['url'] ?>" data-id="<?= $i['id'] ?>">
                                <p><?= $i['message'] ?> <span><?= $formattedDate ?></span></p>
                            </a>
                            <hr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No Notification.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dp" onclick="toggleAccountMenu()">

                <div class="profile-container">
                    <?php require_once 'func/user_func.php';
                    if ($profile = fetchProfilePicture($conn, $user_id)) {
                        echo '<img src="' . $profile . '" class="dpicn" alt="dp">';
                    }?>
                </div>
                <div class="account-menu" id="accountMenu">
                    <ul>
                        <a href="manage-acc">
                            <li>Manage Account</li>
                        </a>
                        <a href="booking">
                            <li>Bookings</li>
                        </a>
                        <hr>
                        <a onclick="logout()">
                            <li style="color:red;">Logout</li>
                        </a>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/jquery-3.7.1.min.js"></script>

<script>
    const searchIcon = document.querySelector(".bx-search-alt");
    const searchContainer = document.querySelector(".searchbar");
    const searchInput = document.getElementById("search");
    const header = document.querySelector("header");
    const searchIconChanger = document.querySelector('.bx bx-search-alt')

    function logout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of your account.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log me out',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'php/logout.php';
            }
        });
    }
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

        $.ajax({
            url: 'php/updateNotificationStatus.php',
            method: 'POST',
            data: { id: notificationId },
            success: function (response) {
                console.log('Notification marked as read');
                window.location.href = url;
            },
            error: function (xhr, status, error) {
                console.error('Error: ' + error);
            }
        });
    });

</script>