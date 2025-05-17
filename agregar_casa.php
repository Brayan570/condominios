<?php
$conexion = new mysqli("localhost", "root", "", "administracion");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$casa = $_POST['casa'];
$usuario = $_POST['usuario'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];

$sql = "INSERT INTO casas (casa, usuario, telefono, correo) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $casa, $usuario, $telefono, $correo);

if ($stmt->execute()) {
    header("Location: index.php?msg=agregado");
} else {
    echo "Error al insertar: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>