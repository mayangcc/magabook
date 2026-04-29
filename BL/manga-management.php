<?php 
require_once '../model/database.php';
require_once '../model/mangaModel.php';
    
    class mangaManagement {
        private $mangaModel;

        public function __construct($mangaModel) {
            $database = new Database();
            $db = $database->connect();

            $this->mangaModel = new Manga($db);
        }

        public function getUserCount() {
            $response = $this->mangaModel->readUsers();
            return $response->fetch(PDO::FETCH_ASSOC);
        }

        public function getManga() {  
            $response = $this->mangaModel->readManga();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getCardManga() {
            $response = $this->mangaModel->getMangaList();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getMangaCount() {
            $response = $this->mangaModel->countManga();
            return $response->fetch(PDO::FETCH_ASSOC);
        }
        
        public function getSubscriptionStats() {
            $response = $this->mangaModel->getSubscriptionStats();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function countAuthors() {
            $response = $this->mangaModel->countAuthors();
            return $response->fetch(PDO::FETCH_ASSOC);
        }

        public function countMangaGenres() {
            $response = $this->mangaModel->countMangaGenres();
            return $response->fetch(PDO::FETCH_ASSOC);
        }

        public function totalRevenue() {
            $response = $this->mangaModel->totalRevenue();
            return $response->fetch(PDO::FETCH_ASSOC);
        }
    }
 ?>
