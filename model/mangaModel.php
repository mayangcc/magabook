<?php 
class Manga {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function updateRegistration($username, $password, $usersID) { // hindi ko pa po 'to ginagamit
            $query = "UPDATE tbl_users SET users_username = :users_username, users_password = :users_password, users_updatedAt = :users_updatedAt WHERE users_id = :users_id";
            $response = $this->conn->prepare($query);
            
            date_default_timezone_set('Asia/Manila');
            $currentDateTime = date("Y-m-d H:i:s");

            $response->bindParam(":users_username", $username);
            $response->bindParam(":users_password", $password);
            $response->bindParam(":users_id", $usersID);
            $response->bindParam(":users_updatedAt", $currentDateTime);

            $response->execute();
            return $response;
    }

    public function deleteRegistration($usersID) { // hindi ko pa rin po 'to ginagamit
            $query = "DELETE FROM tbl_users WHERE users_id = :users_id";
            $response = $this->conn->prepare($query);
            $response->bindParam(":users_id", $usersID);
            $response->execute();
            return $response;
    }

    public function readManga() {
        $query = "SELECT manga_id, manga_titles, manga_authors, manga_cover_image, manga_views FROM tbl_manga";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function readUsers() {
        $query = "SELECT COUNT(*) AS user_count FROM tbl_users";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function countAuthors() {
        $query = "SELECT COUNT(DISTINCT manga_authors) AS total_authors FROM tbl_manga";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function getMangaList() {
        $query = "SELECT 
                    m.manga_id,
                    m.manga_titles,
                    m.manga_authors,
                    m.manga_cover_image,
                    m.manga_views,
                    (SELECT COUNT(*) FROM tbl_chapters c WHERE c.manga_id = m.manga_id) AS chapters_count
                FROM tbl_manga m
                ORDER BY m.created_at DESC";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function countManga() {
        $query = "SELECT COUNT(*) AS total_manga FROM tbl_manga";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function countMangaGenres() {
        $query = "SELECT COUNT(DISTINCT manga_genres_name) AS total_genres FROM tbl_manga_genres";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function totalRevenue() {
        $query = "SELECT SUM(subscriptions_price) AS total_revenue FROM tbl_user_subscriptions u JOIN tbl_subscriptions s ON u.subscriptions_id = s.subscriptions_id";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function getSubscriptionStats() {
        $query = "SELECT 
                    s.subscriptions_name,
                    COUNT(u.users_id) AS total
                FROM tbl_subscriptions s
                LEFT JOIN tbl_user_subscriptions u 
                ON u.subscriptions_id = s.subscriptions_id
                GROUP BY s.subscriptions_name
                ORDER BY s.subscriptions_id ASC";

        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }


}
?>