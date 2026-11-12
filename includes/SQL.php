<?php

require_once('DB.php');

class SQL
{

    // Verificar si existe usuario
    public static function existeUsuario($usuario)
    {

        $conn = DB::conn();

        $sql = "SELECT idUser FROM users_login
                WHERE usuario = :usuario
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':usuario' => $usuario
        ]);

        return $stmt->fetch();
    }

    // Insertar datos personales
    public static function insertarUsuarioData($nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo)
    {

        $conn = DB::conn();

        $sql = "INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo)
                VALUES(:nombre, :apellidos, :email, :telefono, :fecha, :direccion, :sexo)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':telefono' => $telefono,
            ':fecha' => $fecha,
            ':direccion' => $direccion,
            ':sexo' => $sexo
        ]);

        return $conn->lastInsertId();
    }

    // Insertar login
    public static function insertarLogin($idUser, $usuario, $password)
    {

        $conn = DB::conn();

        $sql = "INSERT INTO users_login (idUser, usuario, password, rol)
                VALUES (:idUser, :usuario, :password, 'user')";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idUser' => $idUser,
            ':usuario' => $usuario,
            ':password' => $password
        ]);
    }
    public static function actualizarDatosUsuario($idUser, $nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo)
    {

        $conn = DB::conn();

        $sql = "UPDATE users_data SET nombre = :nombre, apellidos = :apellidos, email = :email, telefono = :telefono, fecha_nacimiento = :fecha, direccion = :direccion, sexo = :sexo
                WHERE idUser = :idUser";
        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':telefono' => $telefono,
            ':fecha' => $fecha,
            ':direccion' => $direccion,
            ':sexo' => $sexo,
            ':idUser' => $idUser
        ]);
    }
    public static function actualizarPassword($idUser, $password)
    {

        $conn = DB::conn();

        $sql = "UPDATE users_login SET password = :password
                WHERE idUser = :idUser";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':password' => $password,
            ':idUser' => $idUser
        ]);
    }
    public static function obtenerUsuario($idUser)
    {

        $conn = DB::conn();

        $sql = "SELECT * FROM users_data
                WHERE idUser = :idUser
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':idUser' => $idUser
        ]);

        return $stmt->fetch();
    }
    public static function obtenerNoticias()
    {

        $conn = DB::conn();

        $sql = "SELECT noticias.*, users_data.nombre, users_data.apellidos FROM noticias
                JOIN users_data ON noticias.idUser = users_data.idUser
                ORDER BY fecha DESC";

        $stmt = $conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public static function obtenerLoginUsuario($usuario)
    {

        $conn = DB::conn();

        $sql = "SELECT * FROM users_login
                WHERE usuario = :usuario
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':usuario' => $usuario
        ]);

        return $stmt->fetch();
    }
    public static function crearCita($idUser, $fecha, $motivo)
    {

        $conn = DB::conn();

        $sql = "INSERT INTO citas (idUser, fecha_cita, motivo_cita)
            VALUES (:idUser, :fecha, :motivo)";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idUser' => $idUser,
            ':fecha' => $fecha,
            ':motivo' => $motivo
        ]);
    }
    public static function eliminarCita($idCita, $idUser)
    {

        $conn = DB::conn();

        $sql = "DELETE FROM citas WHERE idCita = :idCita
                AND idUser = :idUser AND fecha_cita >= CURDATE()";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idCita' => $idCita,
            ':idUser' => $idUser
        ]);
    }
    public static function actualizarCita($idCita, $idUser, $fecha, $motivo)
    {

        $conn = DB::conn();

        $sql = "UPDATE citas SET fecha_cita = :fecha, motivo_cita = :motivo
                WHERE idCita = :idCita
                AND idUser = :idUser";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':fecha' => $fecha,
            ':motivo' => $motivo,
            ':idCita' => $idCita,
            ':idUser' => $idUser
        ]);
    }
    public static function obtenerCitas($idUser)
    {

        $conn = DB::conn();

        $sql = "SELECT * FROM citas
                WHERE idUser = :idUser
                ORDER BY fecha_cita ASC";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':idUser' => $idUser
        ]);

        return $stmt->fetchAll();
    }
    public static function eliminarCitaAdmin($idCita)
    {

        $conn = DB::conn();

        $sql = "DELETE FROM citas
            WHERE idCita = :idCita";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idCita' => $idCita
        ]);
    }
    public static function actualizarCitaAdmin($idCita, $fecha, $motivo)
    {

        $conn = DB::conn();

        $sql = "UPDATE citas SET fecha_cita = :fecha, motivo_cita = :motivo
            WHERE idCita = :idCita";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':fecha' => $fecha,
            ':motivo' => $motivo,
            ':idCita' => $idCita
        ]);
    }
    public static function obtenerUsuarios()
    {

        $conn = DB::conn();

        $sql = "SELECT idUser, nombre, apellidos FROM users_data
            ORDER BY nombre ASC";

        $stmt = $conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public static function obtenerTodasLasCitas()
    {

        $conn = DB::conn();

        $sql = "SELECT citas.*, users_data.nombre, users_data.apellidos FROM citas
            JOIN users_data
                ON citas.idUser = users_data.idUser
            ORDER BY fecha_cita ASC";

        $stmt = $conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public static function obtenerCitasPorUsuario($idUser)
    {
        $conn = DB::conn();

        $sql = "SELECT c.*, u.nombre, u.apellidos FROM citas c INNER JOIN users_data u ON c.idUser = u.idUser WHERE c.idUser = :idUser ORDER BY c.fecha_cita ASC";

        $stmt = $conn->prepare($sql);

        $stmt->execute([':idUser' => $idUser]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function crearNoticia($titulo, $imagen, $texto, $fecha, $idUser)
    {

        $conn = DB::conn();

        $sql = "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser)
            VALUES (:titulo, :imagen, :texto, :fecha, :idUser)";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':titulo' => $titulo,
            ':imagen' => $imagen,
            ':texto' => $texto,
            ':fecha' => $fecha,
            ':idUser' => $idUser
        ]);
    }
    public static function obtenerImagenNoticia($idNoticia)
    {

        $conn = DB::conn();

        $sql = "SELECT imagen FROM noticias
            WHERE idNoticia = :idNoticia";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':idNoticia' => $idNoticia
        ]);

        return $stmt->fetch();
    }
    public static function eliminarNoticia($idNoticia)
    {

        $conn = DB::conn();

        $sql = "DELETE FROM noticias
            WHERE idNoticia = :idNoticia";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idNoticia' => $idNoticia
        ]);
    }
    public static function actualizarNoticia($idNoticia, $titulo, $texto)
    {

        $conn = DB::conn();

        $sql = "UPDATE noticias SET titulo = :titulo, texto = :texto
            WHERE idNoticia = :idNoticia";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':titulo' => $titulo,
            ':texto' => $texto,
            ':idNoticia' => $idNoticia
        ]);
    }
    public static function insertarLoginAdmin($idUser, $usuario, $password, $rol)
    {

        $conn = DB::conn();

        $sql = "INSERT INTO users_login (idUser, usuario, password, rol)
            VALUES (:idUser, :usuario, :password, :rol)";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idUser' => $idUser,
            ':usuario' => $usuario,
            ':password' => $password,
            ':rol' => $rol
        ]);
    }
    public static function eliminarLogin($idUser)
    {

        $conn = DB::conn();

        $sql = "DELETE FROM users_login
            WHERE idUser = :idUser";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idUser' => $idUser
        ]);
    }
    public static function eliminarUsuarioData($idUser)
    {

        $conn = DB::conn();

        $sql = "DELETE FROM users_data
            WHERE idUser = :idUser";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':idUser' => $idUser
        ]);
    }
    public static function actualizarUsuarioAdmin($idUser, $nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo)
    {

        $conn = DB::conn();

        $sql = "UPDATE users_data
            SET nombre = :nombre,
                apellidos = :apellidos,
                email = :email,
                telefono = :telefono,
                fecha_nacimiento = :fecha,
                direccion = :direccion,
                sexo = :sexo
            WHERE idUser = :idUser";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':telefono' => $telefono,
            ':fecha' => $fecha,
            ':direccion' => $direccion,
            ':sexo' => $sexo,
            ':idUser' => $idUser
        ]);
    }
    public static function actualizarRolUsuario($idUser, $rol)
    {

        $conn = DB::conn();

        $sql = "UPDATE users_login SET rol = :rol
            WHERE idUser = :idUser";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':rol' => $rol,
            ':idUser' => $idUser
        ]);
    }
    public static function obtenerUsuariosAdmin()
    {

        $conn = DB::conn();

        $sql = "SELECT users_data.*, users_login.usuario, users_login.rol FROM users_data
            JOIN users_login
                ON users_data.idUser = users_login.idUser
            ORDER BY users_data.nombre ASC";

        $stmt = $conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
