<?php
require_once '../BL/manga-management.php';

$manga = new mangaManagement(null);

$notifications = [];
if (isset($_SESSION['user_id'])) {
    $notifications = $mangaModel->getUserNotifications($_SESSION['user_id']);
}

$hasNotifications = false;
if (isset($_SESSION['user_id'])) {
    $hasNotifications = $mangaModel->hasUnreadNotifications($_SESSION['user_id']);
}

// Fetch data
$userCount = $manga->getUserCount();
$mangaList = $manga->getManga();
$totalManga = $manga->getMangaCount();
$totalAuthors = $manga->countAuthors();
$subscriptionStats = $manga->getSubscriptionStats();

$subsCounts = [
    'Premium' => 0,
    'Annual' => 0,
    'Free' => 0
];

foreach ($subscriptionStats as $sub) {
    $subsCounts[$sub['subscriptions_name']] = $sub['total'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Magabook - Dashboard</title>
<link rel="stylesheet" href="dashboard.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script id="subscriptionData" type="application/json">
    <?php echo json_encode($subscriptionStats); ?>
</script>
<script src="../scripts/service.js"></script>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="../assets/chopper.png" alt="chopper-logo" class="chopper-logo" onclick="redirectfunc(4)">
        <h1 class="website-title" onclick="redirectfunc(4)">Ma(n)gabook</h1>
    </div>
    <a href="#"><i class="fa fa-chart-line"></i> Dashboard</a>
    <a href="#"><i class="fa fa-book"></i> Manga</a>
    <a href="#"><i class="fa fa-users"></i> Users</a>
    <a href="#"><i class="fa fa-fire"></i> Trending</a>
    <a href="#"><i class="fa fa-cog"></i> Settings</a>
</div>

<div class="main">
    <div class="header">
        <h1>Dashboard</h1>
        <div class="nav-right"> <div class="notification" onclick="toggleNotifDropdown()">
                <i class="fa-solid fa-bell"></i>
                <?php if ($hasNotifications): ?>
                    <span class="notification-dot"></span>
                <?php endif; ?>

                <div class="notif-dropdown" id="notifDropdown">
                    <?php if (!empty($notifications)): ?>
                        <?php foreach ($notifications as $notif): ?>
                            <div class="notif-item <?php echo $notif['is_read'] === 'N' ? 'unread' : ''; ?>">
                                <strong><?php echo $notif['notifications_title']; ?></strong>
                                <p><?php echo $notif['notifications_message']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-notif">No notifications</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile" onclick="toggleDropdown()">
                <i class="fa-solid fa-circle-user"></i>
                <div class="dropdown-menu" id="dropdownMenu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="#">Profile</a>
                        <a href="#">Settings</a>
                        <a href="#" onclick="logoutFunc(); return false;">Logout</a>
                    <?php else: ?>
                        <a class="login-btn">Log In</a>
                        <a class="register-btn">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <i class="fa fa-users"></i>
            <div>
                <span>Users</span>
                <strong><?php echo $userCount['user_count']; ?></strong>
            </div>
        </div>
        
        <div class="card">
            <i class="fa fa-user"></i>
            <div>
                <span>Authors</span>
                <strong><?php echo $totalAuthors['total_authors']; ?></strong>
            </div>
        </div>
        
        <div class="card">
            <i class="fa fa-book"></i>
            <div>
                <span>Manga</span>
                <strong><?php echo $totalManga['total_manga']; ?></strong>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-tags"></i>
            <div>
                <span>Genres</span>
                <strong><?php echo $manga->countMangaGenres()['total_genres']; ?></strong>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-crown"></i>
            <div>
                <span>Premium Users</span>
                <strong><?php echo $subsCounts['Premium']; ?></strong>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-calendar"></i>
            <div>
                <span>Annual Users</span>
                <strong><?php echo $subsCounts['Annual']; ?></strong>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-gift"></i>
            <div>
                <span>Free Users</span>
                <strong><?php echo $subsCounts['Free']; ?></strong>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-peso-sign"></i>
            <div>
                <span>Total Revenue</span>
                <strong><?php echo number_format($manga->totalRevenue()['total_revenue'], 2); ?></strong>
            </div>
        </div> 
        

    </div>

    <div class="dashboard-content">
        <div class="chart-container">
            <h3>Subscription Overview</h3>
            <canvas id="subscriptionChart"></canvas>
        </div>

        <div class="table-container">
            <h3>Recent Manga</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Views</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mangaList as $m): ?>
                    <tr>
                        <td><?php echo $m['manga_titles']; ?></td>
                        <td><?php echo $m['manga_authors']; ?></td>
                        <td><?php echo number_format($m['manga_views']); ?></td>
                        <td>
                            <button class="btn edit">Edit</button>
                            <button class="btn delete">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
