<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "administracion";

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica que se haya enviado el archivo
if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] == 0) {
    $filename = $_FILES['archivo_csv']['tmp_name'];

    if (($handle = fopen($filename, "r")) !== FALSE) {
        $row = 0;
        $insertadas = 0;

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if ($row == 0) {
                $row++;
                continue; // Saltar encabezados
            }

            // Validamos que tenga al menos 22 columnas
            if (count($data) >= 22) {
                $stmt = $conn->prepare("INSERT INTO excel (
                    nombre, casa, recibo, mes, admincon, innteres, cuotaseg,
                    consumoga, consumoag, consumoen, saldoant, totalfact,
                    valorag, valorga, valoren, fechapago, fechapago2, pagomes,
                    nopagomes, valortagua, valortgas, valortenergia
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "ssssssssssssssssssssss",
                    $data[0],  // nombre
                    $data[1],  // casa
                    $data[2],  // recibo
                    $data[3],  // mes
                    $data[4],  // admincon
                    $data[5],  // innteres
                    $data[6],  // cuotaseg
                    $data[7],  // consumoga
                    $data[8],  // consumoag
                    $data[9],  // consumoen
                    $data[10], // saldoant
                    $data[11], // totalfact
                    $data[12], // valorag
                    $data[13], // valorga
                    $data[14], // valoren
                    $data[15], // fechapago
                    $data[16], // fechapago2
                    $data[17], // pagomes
                    $data[18], // nopagomes
                    $data[19], // valortagua
                    $data[20], // valortgas
                    $data[21]  // valortenergia
                );

                if ($stmt->execute()) {
                    $insertadas++;
                }
            }

            $row++;
        }

        fclose($handle);

        if ($insertadas > 0) {
            echo "Archivo cargado correctamente. Filas insertadas: $insertadas.";
        } else {
            echo "El archivo fue leído, pero no se insertaron datos (verifica el formato y columnas).";
        }
    } else {
        echo "No se pudo abrir el archivo.";
    }
} else {
    echo "No se recibió ningún archivo o hubo un error al subirlo.";
}

$conn->close();
?>

