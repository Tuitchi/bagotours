<?php
$pp = $_SESSION['profile-pic'];
?>

<style>
	nav {
		display: flex;
		justify-content: space-between;
		align-items: center;
		position: relative;
	}

	.nav-right {
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.notification,
	.profile {
		position: relative;
	}

	.notification .num {
		position: absolute;
		top: -5px;
		right: -10px;
		background-color: red;
		color: white;
		border-radius: 50%;
		padding: 2px 5px;
		font-size: 12px;
	}

	.profile img {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		object-fit: cover;
	}

	/* Notification Dropdown Styles */
	.notification-dropdown {
		position: absolute;
		top: 40px;
		right: 0;
		width: 300px;
		background-color: white;
		box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
		border-radius: 8px;
		display: none;
		flex-direction: column;
		overflow: hidden;
	}

	.notification-dropdown.active {
		display: flex;
	}

	.notification-dropdown .notification-item {
		padding: 10px;
		border-bottom: 1px solid #f0f0f0;
		cursor: pointer;
	}

	.notification-dropdown .notification-item:last-child {
		border-bottom: none;
	}

	.notification-dropdown .notification-item:hover {
		background-color: #f9f9f9;
	}

	.notification-dropdown .no-notifications {
		padding: 10px;
		text-align: center;
		color: #777;
	}
</style>

<nav>
	<i class='bx bx-menu'></i>
	<div class="nav-right">
		<a href="#" class="notification">
			<i class='bx bxs-bell'></i>
			<span id="notification-count" class="num">0</span>
		</a>
		<div class="notification-dropdown" id="notification-dropdown">
			<div class="no-notifications">No new notifications</div>
		</div>
		<a href="" class="profile">
			<img src="../upload/Profile Pictures/<?php echo htmlspecialchars($pp); ?>" alt="Profile Picture">
		</a>
	</div>
</nav>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		function fetchNotifications() {
			$.ajax({
				url: '../php/getNotifCount.php',
				method: 'POST',
				dataType: 'json',
				success: function(data) {
					console.log(data);
					$('#notification-count').text(data.count);
					let notificationDropdown = $('#notification-dropdown');
					notificationDropdown.empty();

					if (data.notifications && data.notifications.length > 0) {
						data.notifications.forEach(function(notification) {
							let notificationItem = `
                            <div class="notification-item">
                                <a class="url" data-id="${notification.id}" href="${notification.url}">${notification.message}</a>
                            </div>
                        `;
							notificationDropdown.append(notificationItem);
						});
					} else {
						notificationDropdown.html('<div class="no-notifications">No new notifications</div>');
					}
				},
				error: function() {
					console.error('Error fetching notifications');
				}
			});
		}

		fetchNotifications();
		setInterval(fetchNotifications, 30000);

		$('.notification').click(function(e) {
			e.preventDefault();
			$('#notification-dropdown').toggleClass('active');
		});

		$(document).click(function(e) {
			if (!$(e.target).closest('.notification').length && !$(e.target).closest('#notification-dropdown').length) {
				$('#notification-dropdown').removeClass('active');
			}
		});
		$('#notification-dropdown').on('click', '.url', function() {
			let notificationId = $(this).data('id');

			$.ajax({
				url: '../php/updateNotificationStatus.php',
				method: 'POST',
				data: {
					id: notificationId
				},
				success: function(response) {
					if (response.success) {
						fetchNotifications();
					} else {
						console.error('Error updating notification status');
					}
				},
				error: function() {
					console.error('Error sending update request');
				}
			});
		});
	});
</script>