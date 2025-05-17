<?php
require('fpdf.php');

class PDF extends FPDF {
    function Header() {
        $this->Image('template.png', 0, 0, 210, 297); // fondo A4
    }
}

$conn = new mysqli("localhost", "root", "", "administracion");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM excel";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        // ==== DATOS DE CABECERA ====
        $pdf->SetXY(35, 42); // Factura #
        $pdf->Cell(0, 10, $fila['recibo'], 0, 1);

        $pdf->SetXY(160, 42); // Fecha
        $pdf->Cell(0, 10, $fila['mes'], 0, 1);

        $pdf->SetXY(35, 50); // Cliente
        $pdf->Cell(0, 10, $fila['nombre'], 0, 1);

        $pdf->SetXY(160, 50); // Teléfono
        $pdf->Cell(0, 10, '---', 0, 1); // Si tienes campo de teléfono en DB, cámbialo

        $pdf->SetXY(35, 58); // Dirección
        $pdf->Cell(0, 10, $fila['casa'], 0, 1);

        $pdf->SetXY(160, 58); // Email
        $pdf->Cell(0, 10, '---', 0, 1); // Igual, si lo tienes

        // ==== PRODUCTOS ====
        $pdf->SetXY(30, 87); // Administración
        $pdf->Cell(0, 10, $fila['admincon'], 0, 1);

        $pdf->SetXY(30, 95); // Consumo Agua
        $pdf->Cell(0, 10, $fila['consumoag'], 0, 1);

        $pdf->SetXY(30, 103); // Consumo Gas
        $pdf->Cell(0, 10, $fila['consumoga'], 0, 1);

        $pdf->SetXY(30, 111); // Consumo Energía
        $pdf->Cell(0, 10, $fila['consumoen'], 0, 1);

        $pdf->SetXY(30, 119); // Intereses
        $pdf->Cell(0, 10, $fila['innteres'], 0, 1);

        $pdf->SetXY(30, 127); // Saldo Anterior
        $pdf->Cell(0, 10, $fila['saldoant'], 0, 1);

        // ==== TOTALES ====
        $pdf->SetXY(160, 152); // Subtotal
        $pdf->Cell(0, 10, $fila['totalfact'], 0, 1);

        $pdf->SetXY(160, 160); // Impuestos (si aplica)
        $pdf->Cell(0, 10, '0', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(160, 168); // TOTAL
        $pdf->Cell(0, 10, $fila['totalfact'], 0, 1);

        // ==== GUARDAR FACTURA ====
        $nombreArchivo = 'factura_' . $fila['id'] . '.pdf';
        $pdf->Output('F', $nombreArchivo);
        echo "Factura generada: $nombreArchivo<br>";
    }
} else {
    echo "No hay registros.";
}

$conn->close();
?>
