<?php
require_once __DIR__ . "/config/conexion.php";

/* FILTROS */
$servicio = (int)($_GET["servicio"] ?? 0);
$municipio = (int)($_GET["municipio"] ?? 0);
$localidad = (int)($_GET["localidad"] ?? 0);
$nombre = trim($_GET["nombre"] ?? "");

/* LISTA DE SERVICIOS */
$listaServicios = $conexion->query(
    "SELECT idServicio, servicio FROM servicios ORDER BY servicio"
);

/* LISTA DE MUNICIPIOS */
$listaMunicipios = $conexion->query(
    "SELECT cveMun, municipio FROM municipios ORDER BY municipio"
);

/* LISTA DE LOCALIDADES */
$listaLocalidades = $conexion->query(
    "SELECT cveLoc, localidad FROM localidades ORDER BY localidad"
);

/* CONSULTA DEL DIRECTORIO */
$sql = "SELECT
            u.idUsuario,
            u.nombre,
            m.municipio,
            l.localidad,
            u.telefono,
            GROUP_CONCAT(DISTINCT s.servicio SEPARATOR ', ') AS servicios
        FROM usuarios u
        LEFT JOIN localidades l ON u.cveLoc = l.cveLoc
        LEFT JOIN municipios m ON l.cveMun = m.cveMun
        LEFT JOIN detalleservicio ds ON ds.idUsuario = u.idUsuario
        LEFT JOIN servicios s ON s.idServicio = ds.idServicio
        WHERE 1=1";

$tipos = "";
$parametros = [];

if ($municipio > 0) {
    $sql .= " AND m.cveMun = ?";
    $tipos .= "i";
    $parametros[] = $municipio;
}

if ($localidad > 0) {
    $sql .= " AND l.cveLoc = ?";
    $tipos .= "i";
    $parametros[] = $localidad;
}

if ($servicio > 0) {
    $sql .= " AND EXISTS (
        SELECT 1
        FROM detalleservicio dx
        WHERE dx.idUsuario = u.idUsuario
          AND dx.idServicio = ?
    )";
    $tipos .= "i";
    $parametros[] = $servicio;
}

if ($nombre !== "") {
    $sql .= " AND u.nombre LIKE ?";
    $tipos .= "s";
    $parametros[] = "%" . $nombre . "%";
}

$sql .= " GROUP BY
            u.idUsuario,
            u.nombre,
            m.municipio,
            l.localidad,
            u.telefono
          ORDER BY u.nombre";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta. Verifica las columnas de la base de datos.");
}

if ($tipos !== "") {
    $stmt->bind_param($tipos, ...$parametros);
}

$stmt->execute();
$resultados = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Directorio - PROXI</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="css/directorio.css">
</head>

<body class="pagina-directorio">

    <main class="ventana-directorio">

        <!-- BARRA SUPERIOR -->
        <header class="barra-directorio">
            <span>Directorio (Buscador)</span>
            <a href="panel.php" class="cerrar-directorio" title="Cerrar">X</a>
        </header>

        <!-- FILTROS -->
        <section class="filtros-directorio">

            <h1>Filtros:</h1>

            <form method="GET" action="directorio.php">

                <div class="filtros-grid">

                    <div class="filtro">
                        <label for="servicio">Servicio:</label>
                        <select name="servicio" id="servicio">
                            <option value="0">Todos los servicios</option>

                            <?php if ($listaServicios): ?>
                                <?php while ($s = $listaServicios->fetch_assoc()): ?>
                                    <option
                                        value="<?= (int)$s["idServicio"] ?>"
                                        <?= $servicio === (int)$s["idServicio"] ? "selected" : "" ?>>
                                        <?= htmlspecialchars($s["servicio"]) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="filtro">
                        <label for="municipio">Municipio:</label>
                        <select name="municipio" id="municipio">
                            <option value="0">Todos los municipios</option>

                            <?php if ($listaMunicipios): ?>
                                <?php while ($m = $listaMunicipios->fetch_assoc()): ?>
                                    <option
                                        value="<?= (int)$m["cveMun"] ?>"
                                        <?= $municipio === (int)$m["cveMun"] ? "selected" : "" ?>>
                                        <?= htmlspecialchars($m["municipio"]) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="filtro">
                        <label for="localidad">Localidad:</label>
                        <select name="localidad" id="localidad">
                            <option value="0">Todas las localidades</option>

                            <?php if ($listaLocalidades): ?>
                                <?php while ($l = $listaLocalidades->fetch_assoc()): ?>
                                    <option
                                        value="<?= (int)$l["cveLoc"] ?>"
                                        <?= $localidad === (int)$l["cveLoc"] ? "selected" : "" ?>>
                                        <?= htmlspecialchars($l["localidad"]) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="filtro">
                        <label for="nombre">Nombre:</label>
                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            value="<?= htmlspecialchars($nombre) ?>"
                            placeholder="Buscar por nombre">
                    </div>

                </div>

                <div class="acciones-busqueda">
                    <button type="submit" class="boton-buscar">
                        Buscar
                    </button>

                    <a href="directorio.php" class="boton-limpiar">
                        Limpiar
                    </a>
                </div>

            </form>
        </section>

        <!-- TABLA DE RESULTADOS -->
        <section class="resultados-directorio">

            <div class="tabla-directorio-contenedor">

                <table class="tabla-directorio">

                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Municipio</th>
                            <th>Localidad</th>
                            <th>Servicio</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($resultados->num_rows > 0): ?>

                            <?php while ($fila = $resultados->fetch_assoc()): ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars($fila["nombre"] ?? "") ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($fila["municipio"] ?? "—") ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($fila["localidad"] ?? "—") ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($fila["servicios"] ?? "—") ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($fila["telefono"] ?? "—") ?>
                                    </td>
                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5" class="sin-resultados">
                                    No se encontraron registros.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>
                </table>

            </div>

        </section>

    </main>

</body>
</html>