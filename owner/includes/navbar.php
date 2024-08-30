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
</style>
<nav>
	<i class='bx bx-menu'></i>
	<div class="nav-right">
		<a href="#" class="notification">
			<i class='bx bxs-bell'></i>
			<span class="num">8</span>
		</a>
		<a href="" class="profile">
			<img src="../upload/Profile Pictures/<?php echo htmlspecialchars($pp); ?>" alt="Profile Picture">
		</a>
	</div>
</nav>