```php
<?php
session_start();

require_once __DIR__ . "/config/conexion.php";

/*
|--------------------------------------------------------------------------
| CONSULTA DE INSCRITOS
|--------------------------------------------------------------------------
| Se relacionan usuarios, localidades y municipios.
*/

$sql = "SELECT
            u.idUsuario,
            u.nombre,
            m.municipio,
            l.localidad,
            u.direccion,
            u.telefono
        FROM usuarios AS u
        LEFT JOIN localidades AS l
            ON u.cveLoc = l.cveLoc
        LEFT JOIN municipios AS m
            ON l.cveMun = m.cveMun
        ORDER BY u.nombre ASC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Botón de alta | PROXI</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="css/inscritos.css">
</head>

<body class="pagina-inscritos">

    <main class="ventana-inscritos">

        <!-- ENCABEZADO DE LA VENTANA -->
        <header class="barra-ventana">
            <span>Botón de alta</span>

            <a href="panel.php"
               class="boton-cerrar"
               aria-label="Cerrar ventana"
               title="Cerrar">X</a>
        </header>

        <!-- CONTENIDO -->
        <section class="contenido-inscritos">

            <h1>Todos los inscritos</h1>

            <?php if ($resultado === false): ?>

                <div class="alert alert-danger">
                    No se pudieron consultar los inscritos.
                    Verifica la estructura de la tabla usuarios.
                </div>

            <?php else: ?>

                <form method="POST" action="#">

                    <div class="tabla-contenedor">

                        <table class="tabla-inscritos">

                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Municipio / Localidad</th>
                                    <th>Servicio</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Validar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if ($resultado->num_rows > 0): ?>

                                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($fila["nombre"] ?? "") ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($fila["municipio"] ?? "Sin municipio") ?>
                                                /
                                                <?= htmlspecialchars($fila["localidad"] ?? "Sin localidad") ?>
                                            </td>

                                            <td>—</td>

                                            <td>
                                                <?= htmlspecialchars($fila["direccion"] ?? "") ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($fila["telefono"] ?? "") ?>
                                            </td>

                                            <td class="celda-accion">
                                                <input
                                                    type="checkbox"
                                                    name="validar[]"
                                                    value="<?= (int)$fila["idUsuario"] ?>"
                                                    aria-label="Seleccionar para validar"
                                                >
                                            </td>

                                            <td class="celda-accion">
                                                <input
                                                    type="checkbox"
                                                    name="eliminar[]"
                                                    value="<?= (int)$fila["idUsuario"] ?>"
                                                    aria-label="Seleccionar para eliminar"
                                                >
                                            </td>
                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="7" class="sin-registros">
                                            No hay inscritos registrados.
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </form>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
```
