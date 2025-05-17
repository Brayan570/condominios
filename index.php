<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "administracion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Dashboard: Consultas para datos
$sql_ingresos = "SELECT IFNULL(SUM(monto),0) AS total_ingresos FROM pagos WHERE estado = 'pagado'";
$res_ingresos = $conn->query($sql_ingresos);
$total_ingresos = $res_ingresos ? $res_ingresos->fetch_assoc()['total_ingresos'] : 0;

$sql_consumos = "SELECT IFNULL(SUM(monto),0) AS total_consumos FROM pagos";
$res_consumos = $conn->query($sql_consumos);
$total_consumos = $res_consumos ? $res_consumos->fetch_assoc()['total_consumos'] : 0;

$sql_casas = "SELECT COUNT(*) AS cantidad_casas FROM casas";
$res_casas = $conn->query($sql_casas);
$cantidad_casas = $res_casas ? $res_casas->fetch_assoc()['cantidad_casas'] : 0;

$sql_pendientes = "SELECT COUNT(*) AS pagos_pendientes FROM pagos WHERE estado = 'no pagado'";
$res_pendientes = $conn->query($sql_pendientes);
$pagos_pendientes = $res_pendientes ? $res_pendientes->fetch_assoc()['pagos_pendientes'] : 0;

$sql_completos = "SELECT COUNT(*) AS pagos_completos FROM pagos WHERE estado = 'pagado'";
$res_completos = $conn->query($sql_completos);
$pagos_completos = $res_completos ? $res_completos->fetch_assoc()['pagos_completos'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insertar_casa'])) {
    $casa = $conn->real_escape_string($_POST['casa']);
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $correo = $conn->real_escape_string($_POST['correo']);

    $sql_insert = "INSERT INTO casas (casa, usuario, telefono, correo) VALUES ('$casa', '$usuario', '$telefono', '$correo')";
    if (!$conn->query($sql_insert)) {
        echo "<script>alert('Error al insertar datos: " . $conn->error . "');</script>";
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$sql = "SELECT casa, usuario, telefono, correo FROM casas ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel Principal</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar fijo */
        .sidebar {
            height: 100vh;
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding-top: 20px;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar a,
        .sidebar button {
            display: block;
            color: white;
            background: none;
            border: none;
            padding: 15px 20px;
            text-align: left;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover,
        .sidebar button:hover {
            background-color: #34495e;
        }

        /* Contenido desplazado y con espacio para sidebar */
        .content {
            margin-left: 240px; /* espacio para sidebar */
            padding: 30px 40px;
            min-height: 100vh;
            background: #f8f9fa;
            transition: margin-left 0.3s ease;
            overflow-x: auto;
            position: relative;
            z-index: 1;
        }

        /* Secciones ocultas por defecto excepto active */
        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        /* Formulario estilos */
        label, select, input {
            display: block;
            margin-bottom: 12px;
            font-size: 16px;
            width: 100%;
            max-width: 400px;
        }

        input[type="submit"] {
            padding: 12px 25px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #34495e;
        }

        /* Tabla contenedor con scroll horizontal */
        .table-container {
            overflow-x: auto;
            margin-top: 30px;
            max-width: 100%;
            position: relative;
            z-index: 1;
        }

        /* Tabla moderna */
        .tabla-moderna {
            width: 100%;
            border-collapse: collapse;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            background: white;
            table-layout: auto;
        }

        .tabla-moderna thead {
            background-color: #2c3e50;
            color: white;
        }

        .tabla-moderna th,
        .tabla-moderna td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .tabla-moderna tr:hover {
            background-color: #f1f1f1;
        }

        /* Dashboard con grid responsivo */
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .card h2 {
            margin: 0;
            font-size: 2.2rem;
            color: #2c3e50;
            font-weight: 700;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 1.1rem;
            color: #555;
            font-weight: 600;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                z-index: 10;
            }

            .content {
                margin-left: 0;
                padding: 20px;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Menú</h2>
        <a onclick="mostrarSeccion('dashboard')" href="javascript:void(0)">DASHBOARD</a>
        <a onclick="mostrarSeccion('casas')" href="javascript:void(0)">CASAS</a>
        <a onclick="mostrarSeccion('montos')" href="javascript:void(0)">MONTOS</a>
        <a onclick="mostrarSeccion('facturacion')" href="javascript:void(0)">GENERAR FACTURAS</a>
        <button onclick="mostrarSeccion('cargar')">CARGAR BASE DE DATOS</button>
        <button onclick="mostrarSeccion('marcarPago')">MARCAR PAGO</button>
    </div>

    <div class="content">
        <div id="dashboard" class="section active">
            <h1>Dashboard</h1>
            <div class="dashboard">
                <div class="card">
                    <h2>$<?php echo number_format($total_ingresos, 2, ',', '.'); ?></h2>
                    <p>Ingresos Totales</p>
                </div>
                <div class="card">
                    <h2>$<?php echo number_format($total_consumos, 2, ',', '.'); ?></h2>
                    <p>Consumos Totales</p>
                </div>
                <div class="card">
                    <h2><?php echo $cantidad_casas; ?></h2>
                    <p>Cantidad de Casas</p>
                </div>
                <div class="card">
                    <h2><?php echo $pagos_pendientes; ?></h2>
                    <p>Pagos Pendientes</p>
                </div>
                <div class="card">
                    <h2><?php echo $pagos_completos; ?></h2>
                    <p>Pagos Completos</p>
                </div>
            </div>
        </div>

        <div id="cargar" class="section">
            <h1>Cargar Base de Datos</h1>
            <form action="procesar_archivo.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="archivo_csv" accept=".csv" required>
                <input type="submit" name="submit" value="Subir CSV">
            </form>
        </div>

        <div id="facturacion" class="section">
            <h1>Generar Facturas</h1>
            <form action="generar_facturas.php" method="post">
                <input type="submit" value="Generar Facturas">
            </form>
        </div>

        <div id="marcarPago" class="section">
            <h1>Marcar Pago</h1>
            <form action="actualizar_pago.php" method="POST">
                <label for="casa">Casa (identificador):</label>
                <input type="text" id="casa" name="casa" required>

                <label for="mes">Mes:</label>
                <input type="text" id="mes" name="mes" required>

                <label for="pagado">Estado de Pago:</label>
                <select id="pagado" name="pagado" required>
                    <option value="si">Pagado</option>
                    <option value="no">No Pagado</option>
                </select>

                <input type="submit" value="Actualizar Estado de Pago">
            </form>
        </div>

        <div id="casas" class="section">
            <h1>Casas y Usuarios</h1>

            <form method="POST" action="">
                <input type="hidden" name="insertar_casa" value="1" />
                <label for="casa">Casa:</label>
                <input type="text" id="casa" name="casa" required />

                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required />

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required />

                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required />

                <input type="submit" value="Insertar Casa" />
            </form>

            <div class="table-container">
                <table class="tabla-moderna">
                    <thead>
                        <tr>
                            <th>Casa</th>
                            <th>Usuario</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['casa']); ?></td>
                                    <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">No hay datos para mostrar</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="montos" class="section">
            <h1>Montos</h1>
            <p>Aquí puedes agregar contenido de la sección Montos</p>
        </div>

    </div>

    <script>
        function mostrarSeccion(id) {
            // Ocultar todas las secciones
            const secciones = document.querySelectorAll('.section');
            secciones.forEach(sec => sec.classList.remove('active'));
            // Mostrar la sección deseada
            document.getElementById(id).classList.add('active');
        }
    </script>

</body>
</html>
