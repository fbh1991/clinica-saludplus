<?php

include('.env.php');

class DB {

    public static function conn() {

        global $host, $db, $user, $pass;

        try {

            $conn = new PDO(
                "mysql:host=$host;dbname=$db;charset=utf8",
                $user,
                $pass
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $conn;

        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());
        }
    }
}

?>