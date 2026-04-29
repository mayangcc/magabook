<?php

session_start();
require_once '../BL/user-management.php';
require_once '../helper/sendEmail.php';

$userManagement = new UserManagement();

if (isset($_POST['username'], $_POST['password'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['fullAddress'],
    $_POST['birthdate'], $_POST['gender'])) {
    
    $result = $userManagement->registerUser($_POST); // save user

    $name = htmlspecialchars($_POST['firstName'] . ' ' . $_POST['lastName']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = "Welcome to Magabook!";

$body = '
<div style="background: #fefefee9;; font-family:Poppins, Arial, sans-serif; color:#444; padding: 40px 20px; max-width:600px; border:1px solid #eee;">
    <div style="background: #fff; border-radius: 10px;">
        <div style="background: linear-gradient(90deg, rgba(251, 194, 235, 0.7), rgba(255, 255, 255, 0.7), rgba(177, 235, 243, 0.7)   ); text-align:center; border-radius:15px 15px 0 0; padding: 15px">
            <h2 style="margin:0; color:#f06292; font-size:20px; letter-spacing:1px;">Ma(n)gabook</h2>
        </div>
            
            <h1 style="color:#f06292; font-size:24px; margin-bottom:20px; text-align:center;">
                Welcome to Magabook, <br>
                '.$name.'!
            </h1>

            <p style="line-height:1.6;  font-size:14px; margin-bottom:15px; text-align:center;">
                Thank you for registering with us. We\'re excited to have you on board! 
            </p>

            <p style="line-height:1.6;  font-size:14px; margin-bottom:15px; text-align:center;">
                Explore our vast collection of manga and enjoy your reading experience.
            </p>

            <p style="line-height:1.6;  font-size:14px; margin-bottom:25px; text-align:center;">
                If you have any questions, feel free to contact our support team at any time.
            </p>

            <p style="margin-top:30px; text-align:center; border-top: 1px solid #eee; padding-top: 20px;">
                Happy reading!<br>
                <strong style="color:#f06292;">The Magabook Team </strong>
            </p>

        <div style="background: linear-gradient(90deg, rgba(251, 194, 235, 0.3), rgba(177, 235, 243, 0.3)); padding:15px; text-align:center; margin-top:30px; margin-bottom:-10; border-radius:0 0 15px 15px;">
            <p style="margin:0; font-size:12px; color:#f06292; font-weight:bold;">
                © 2026 Magabook
            </p>
        </div>
    </div>
</div>
';
    if ($result["status"] === "error") {
        http_response_code(400);
        echo $result["message"];
    } else {
         $result = sendEmail($email, $name, $subject, $body);
    }
    exit;
    

} else if (isset($_POST['upID'], $_POST['upUsername'], $_POST['upPassword'])) {
    $userManagement->updateUserFunc($_POST['upUsername'], $_POST['upPassword'], $_POST['upID']);
    exit;
}
// User Delete
else if (isset($_POST['delID'])) {
    $userManagement->deleteDataFunc($_POST['delID']);
    exit;
}
// User Login
else if (isset($_POST['logUsername'], $_POST['logPassword'])) {
    echo $userManagement->loginUserFunc($_POST['logUsername'], $_POST['logPassword']);
    exit;
}
// User Logout
else if (isset($_POST['logout'])) {
    session_destroy();
    echo "logout";
    exit;
} 
// Search Manga
else if (isset($_POST['searchTerm'])) { 
    $results = $userManagement->searchMangaFunc($_POST['searchTerm']);

    if (empty($results)) {
        echo "<p style='padding:20px;'>No results found.</p>";
        exit;
    }

    foreach ($results as $manga) {
        echo '<div class="card-container">
            <div class="card">
                <img src="../uploads/'.$manga['manga_cover_image'].'">
                <div class="card-overlay">
                    <div class="overlay-stats">
                        <span>'.$manga['chapters_count'].' Chapters</span>
                        <span>'.$manga['views_count'].' Views</span>
                    </div>
                </div>
            </div>
            <div class="card-info">
                <h3>'.$manga['manga_titles'].'</h3>
                <p>'.$manga['manga_authors'].'</p>
            </div>
        </div>';
    }
    exit;
} 
else if (isset($_POST['markNotificationsRead'])) {
    require_once '../model/database.php';
    require_once '../model/magabookModel.php';

    $database = new Database();
    $db = $database->connect();
    $mangaModel = new Manga($db);

    if (isset($_SESSION['user_id'])) {
        $mangaModel->markAllNotificationsAsRead($_SESSION['user_id']);
        echo "success";
    }
    exit;
}
// Invalid Request
else {
    http_response_code(400);
    echo "Bad Request";
    exit;
}

?>
