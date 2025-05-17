<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "administracion";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$casa = $_POST['casa'] ?? '';
$mes = $_POST['mes'] ?? '';
$pagado = $_POST['pagado'] ?? 'no';

if ($casa && $mes) {
    if ($pagado === 'si') {
        $sql = "UPDATE excel SET pagomes = 'Sí', nopagomes = '' WHERE casa = ? AND mes = ?";
    } else {
        $sql = "UPDATE excel SET pagomes = '', nopagomes = 'No' WHERE casa = ? AND mes = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $casa, $mes);

    if ($stmt->execute()) {
        $mensaje = "Estado de pago actualizado correctamente.";
    } else {
        $mensaje = "Error al actualizar el pago: " . $conn->error;
    }
} else {
    $mensaje = "Datos incompletos.";
}

$conn->close();

// Redirigir de vuelta al panel con mensaje
header("Location: index.php?msg=" . urlencode($mensaje));
exit;
?>
