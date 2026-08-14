<?php
// includes/database.php

// Vérification que config.php est bien chargé
if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
    die("Erreur: Configuration de base de données manquante. Vérifiez config.php");
}

class Database {
    private static $instance = null;
    private $connection;
    
    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false
                ]
            );
            
            // Désactiver l'émulation des requêtes préparées pour plus de sécurité
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
        } catch (PDOException $e) {
            // Journalisation de l'erreur avant d'afficher un message générique
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données. Veuillez réessayer plus tard.");
        }
    }

    // Méthode pour obtenir l'instance unique
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Obtenir la connexion PDO
    public function getConnection() {
        return $this->connection;
    }

    // Méthode helper pour les requêtes préparées
    public function prepareQuery($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " - Query: " . $sql);
            throw $e;
        }
    }
}

// Initialisation sécurisée de la connexion
try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    // Vous pouvez aussi rendre $db disponible globalement si nécessaire
    global $pdo;
    $pdo = $db;
    
} catch (Exception $e) {
    // Message d'erreur convivial pour l'utilisateur
    die("Une erreur technique est survenue. Notre équipe a été notifiée.");
}