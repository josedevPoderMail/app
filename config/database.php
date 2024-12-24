<?php
class Database {
    private $host = '207.244.125.209';
    private $db_name = 'podermail_rolPMW';
    private $username = 'podermail_jose';
    private $password = 'podermail_jose';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error en la conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
