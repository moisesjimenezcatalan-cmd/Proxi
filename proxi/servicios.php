<?php
session_start();
if (!isset($_SESSION['Aut']) || (int)$_SESSION['Aut'] !== 1) {
    header('Location: index.php');
    exit;
}

require_once('./config/conexion.php');
$conexion = new base_de_datos();

// El usuario autorizado siempre se obtiene de la sesión, nunca del navegador.
$idUsuarioSesion = filter_var($_SESSION['idUsuario'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$idUsuarioSesion) {
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['csrf_servicios'])) {
    $_SESSION['csrf_servicios'] = bin2hex(random_bytes(32));
}

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Ajustar estos dos identificadores si el catálogo real utiliza otros nombres.
// Tabla: servicios | Campos: idServicio, servicio
function obtenerAsignaciones($conexion, $idUsuario) {
    return $conexion->consultar(
        'SELECT d.idDetServ, d.idServicio, s.servicio AS nombreServicio
         FROM detalleservicio d
         INNER JOIN servicios s ON s.idServicio = d.idServicio
         WHERE d.idUsuario = ? ORDER BY s.servicio',
        'i', $idUsuario
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json; charset=utf-8');
    $respuesta = ['ok' => false, 'mensaje' => 'Solicitud no válida.'];
    try {
        if (!hash_equals($_SESSION['csrf_servicios'], (string)($_POST['csrf'] ?? ''))) {
            throw new Exception('La sesión del formulario expiró. Actualiza la página.');
        }
        $accion = (string)$_POST['accion'];
        $idUsuario = $idUsuarioSesion;
        // Se rechaza cualquier petición que intente operar sobre otra cuenta.
        $idUsuarioSolicitado = filter_var($_POST['idUsuario'] ?? null, FILTER_VALIDATE_INT);
        if ($idUsuarioSolicitado !== false && $idUsuarioSolicitado !== null && $idUsuarioSolicitado !== $idUsuarioSesion) {
            throw new Exception('No tienes autorización para modificar los servicios de otro usuario.');
        }
        $usuario = $conexion->consultar('SELECT idUsuario FROM usuarios WHERE idUsuario = ?', 'i', $idUsuario);
        if (!$usuario) {
            throw new Exception('El usuario seleccionado no existe.');
        }

        if ($accion === 'listar') {
            $respuesta = ['ok' => true, 'datos' => obtenerAsignaciones($conexion, $idUsuario)];
        } elseif ($accion === 'agregar') {
            $idServicio = filter_var($_POST['idServicio'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$idServicio) {
                throw new Exception('Selecciona un servicio válido.');
            }
            $servicio = $conexion->consultar('SELECT idServicio FROM servicios WHERE idServicio = ?', 'i', $idServicio);
            if (!$servicio) {
                throw new Exception('El servicio seleccionado no existe.');
            }
            $existe = $conexion->consultar(
                'SELECT idDetServ FROM detalleservicio WHERE idUsuario = ? AND idServicio = ? LIMIT 1',
                'ii', $idUsuario, $idServicio
            );
            if ($existe) {
                throw new Exception('Esta persona ya tiene asignado ese servicio.');
            }
            $conexion->ejecutar('INSERT INTO detalleservicio (idUsuario, idServicio) VALUES (?, ?)', 'ii', $idUsuario, $idServicio);
            $respuesta = ['ok' => true, 'mensaje' => 'Servicio agregado correctamente.'];
        } elseif ($accion === 'eliminar') {
            $idDetServ = filter_var($_POST['idDetServ'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$idDetServ) {
                throw new Exception('Registro no válido.');
            }
            $resultado = $conexion->ejecutar(
                'DELETE FROM detalleservicio WHERE idDetServ = ? AND idUsuario = ?',
                'ii', $idDetServ, $idUsuario
            );
            if ($resultado['afectadas'] < 1) {
                throw new Exception('No se encontró la asociación solicitada.');
            }
            $respuesta = ['ok' => true, 'mensaje' => 'Servicio eliminado correctamente.'];
        } else {
            throw new Exception('Acción desconocida.');
        }
    } catch (Throwable $e) {
        error_log('Servicios: ' . $e->getMessage());
        $respuesta = ['ok' => false, 'mensaje' => $e instanceof Exception && in_array($e->getMessage(), [
            'La sesión del formulario expiró. Actualiza la página.', 'Selecciona un usuario válido.',
            'El usuario seleccionado no existe.', 'Selecciona un servicio válido.',
            'El servicio seleccionado no existe.', 'Esta persona ya tiene asignado ese servicio.',
            'Registro no válido.', 'No se encontró la asociación solicitada.', 'Acción desconocida.',
            'No tienes autorización para modificar los servicios de otro usuario.'
        ], true) ? $e->getMessage() : 'No fue posible completar la operación. Revisa el servidor.'];
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

$errorCatalogo = '';
$usuarios = [];
$servicios = [];
try {
    $usuarios = $conexion->consultar('SELECT idUsuario, nombre FROM usuarios WHERE idUsuario = ? LIMIT 1', 'i', $idUsuarioSesion);
    if (!$usuarios) {
        throw new Exception('El usuario de la sesión no existe en la base de datos.');
    }
    $servicios = $conexion->consultar('SELECT idServicio, servicio FROM servicios ORDER BY servicio');
} catch (Throwable $e) {
    error_log('Catálogo servicios: ' . $e->getMessage());
    $errorCatalogo = 'No se pudo cargar el catálogo. Verifica que exista la tabla servicios con los campos idServicio y servicio.';
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once('./head.php'); ?>
<body>
<style>
/* Encabezado de sesión: estilos aislados del resto del sistema. */
.sc-sesion { background: #f3f7f4; padding: 22px 16px 0; }
.sc-sesion-contenido { max-width: 1120px; margin: 0 auto; }
.sc-sesion-tarjeta { background: linear-gradient(120deg, #073e29, #126b46); color: #fff; border-radius: 18px; padding: 25px 30px; border-bottom: 4px solid #dcae34; box-shadow: 0 10px 25px rgba(7,62,41,.15); }
.sc-sesion-superior { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
.sc-sesion-icono { width: 58px; height: 58px; flex: 0 0 58px; border-radius: 16px; background: rgba(255,255,255,.13); display: grid; place-items: center; }
.sc-sesion-icono svg { width: 30px; height: 30px; }
.sc-sesion-etiqueta { color: #f1d581; font-size: .78rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; }
.sc-sesion-nombre { font-size: clamp(1.15rem, 2.5vw, 1.6rem); margin: 4px 0 0; font-weight: 700; overflow-wrap: anywhere; }
.sc-sesion-datos { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
.sc-sesion-dato { padding: 14px 17px; border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.09); border-radius: 12px; min-width: 0; }
.sc-sesion-dato span { display: block; color: #d6e8dc; font-size: .82rem; margin-bottom: 5px; }
.sc-sesion-dato strong { font-size: .98rem; font-weight: 700; overflow-wrap: anywhere; }
.sc-sesion-menu { margin-top: 16px; border-radius: 12px; overflow: visible; }
.sc-sesion-menu .navbar { border: 1px solid #dce9df; border-radius: 12px; padding: 10px 15px; box-shadow: 0 5px 15px rgba(7,62,41,.06); }
.sc-sesion-menu .navbar-brand { color: #073e29; font-weight: 600; }
.sc-sesion-menu .nav-link { color: #214b35; font-weight: 600; }
.sc-sesion-menu .nav-link:hover { color: #13764a; }
@media (max-width: 650px) {
    .sc-sesion { padding: 14px 10px 0; }
    .sc-sesion-tarjeta { padding: 20px 16px; }
    .sc-sesion-datos { grid-template-columns: 1fr; gap: 8px; }
    .sc-sesion-dato { padding: 10px 13px; }
}
</style>

<section class="sc-sesion" aria-label="Información de la sesión">
    <div class="sc-sesion-contenido">
        <div class="sc-sesion-tarjeta">
            <div class="sc-sesion-superior">
                <div class="sc-sesion-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 21v-2a8 8 0 0 1 16 0v2"></path>
                    </svg>
                </div>
                <div>
                    <div class="sc-sesion-etiqueta">Panel de usuario · Servicios Chiapas</div>
                    <h2 class="sc-sesion-nombre"><?= escapar($_SESSION['NOMBRE'] ?? $_SESSION['nombre'] ?? 'Usuario') ?></h2>
                </div>
            </div>
            <div class="sc-sesion-datos">
                <div class="sc-sesion-dato"><span>ID de usuario</span><strong>#<?= escapar($_SESSION['idUsuario'] ?? '') ?></strong></div>
                <div class="sc-sesion-dato"><span>Estatus de la cuenta</span><strong><?= escapar($_SESSION['desStatus'] ?? 'No disponible') ?></strong></div>
                <div class="sc-sesion-dato"><span>Tipo de usuario</span><strong><?= escapar($_SESSION['tipo'] ?? 'No disponible') ?></strong></div>
            </div>
        </div>
        <div class="sc-sesion-menu">
            <?php require_once('./nav.php'); ?>
        </div>
    </div>
</section>

<style>
:root { --sc-verde:#073e29; --sc-verde2:#13764a; --sc-dorado:#dcae34; --sc-fondo:#f3f7f4; }
.sc-area { background:var(--sc-fondo); min-height:75vh; padding:36px 12px 60px; font-family:inherit; }
.sc-contenedor { max-width:1120px; margin:auto; }
.sc-cabecera { background:linear-gradient(115deg,#063720,#0d6440); border-radius:20px; color:white; padding:26px 32px; display:flex; align-items:center; gap:20px; box-shadow:0 12px 28px #06372024; border-bottom:5px solid var(--sc-dorado); }
.sc-logo { width:95px; height:95px; object-fit:contain; background:#ffffff12; border-radius:14px; padding:5px; }
.sc-etiqueta { color:#f5d778; text-transform:uppercase; font-size:.78rem; letter-spacing:2px; font-weight:700; }
.sc-cabecera h1 { font-size:clamp(1.5rem,3vw,2.1rem); font-weight:750; margin:6px 0; }
.sc-cabecera p { color:#e3f2e9; margin:0; }
.sc-panel { background:white; border:1px solid #e0e9e3; border-radius:18px; overflow:hidden; box-shadow:0 9px 25px #133b2410; margin-top:25px; }
.sc-panel-titulo { padding:20px 25px; border-bottom:1px solid #e8eee9; display:flex; justify-content:space-between; align-items:center; gap:10px; }
.sc-panel-titulo h2 { font-size:1.15rem; font-weight:750; color:var(--sc-verde); margin:0; }
.sc-panel-cuerpo { padding:26px; }
.sc-label { display:block; color:#284937; font-weight:650; margin-bottom:9px; }
.sc-area .form-control { border:1px solid #cbdcd0; border-radius:10px; min-height:46px; background-color:#fff; }
.sc-area .form-control:focus { border-color:var(--sc-verde2); box-shadow:0 0 0 .2rem #13764a22; }
.sc-boton { background:var(--sc-verde2); color:white; border:0; border-radius:10px; min-height:46px; padding:0 24px; font-weight:700; cursor:pointer; }
.sc-boton:hover { background:var(--sc-verde); color:white; }
.sc-boton:disabled { opacity:.6; cursor:not-allowed; }
.sc-resumen { color:#52705c; font-size:.88rem; }
.sc-tabla { width:100%; border-collapse:collapse; }
.sc-tabla th { background:#f1f7f2; color:#31533e; font-size:.85rem; padding:15px; text-align:left; }
.sc-tabla td { padding:15px; border-top:1px solid #e8eee9; vertical-align:middle; }
.sc-numero { display:inline-block; border-radius:8px; padding:5px 10px; background:#eaf4ec; color:var(--sc-verde); font-weight:700; }
.sc-eliminar { border:1px solid #f2c8c8; color:#b33d3d; background:#fff7f7; border-radius:8px; padding:7px 12px; cursor:pointer; }
.sc-eliminar:hover { background:#fbe5e5; }
.sc-vacio { padding:35px; text-align:center; color:#607565; }
.sc-nota { font-size:.86rem; color:#688072; margin:12px 0 0; }
@media(max-width:600px) { .sc-cabecera { padding:20px; gap:12px; } .sc-logo { width:65px; height:65px; } .sc-panel-cuerpo { padding:18px; } .sc-panel-titulo { padding:18px; } .sc-tabla th,.sc-tabla td { padding:10px; } }
</style>

<main class="sc-area">
    <div class="sc-contenedor">
        <header class="sc-cabecera">
            <img class="sc-logo" src="./img/logo.png" alt="Servicios Chiapas">
            <div>
                <div class="sc-etiqueta">Servicios Chiapas · Administración</div>
                <h1>Asignación de servicios</h1>
                <p>Gestiona los servicios que ofrece cada persona registrada.</p>
            </div>
        </header>

        <?php if ($errorCatalogo): ?>
            <div class="alert alert-warning mt-4" role="alert"><?= escapar($errorCatalogo) ?></div>
        <?php endif; ?>
        <div id="sc-aviso" class="alert mt-4" role="alert" style="display:none"></div>

        <section class="sc-panel">
            <div class="sc-panel-titulo"><h2>01 · Usuario de la sesión</h2></div>
            <div class="sc-panel-cuerpo">
                <label class="sc-label" for="sc-usuario">Usuario registrado</label>
                <select id="sc-usuario" class="form-control" <?= $errorCatalogo ? 'disabled' : '' ?>>
                    
                    <?php foreach ($usuarios as $usuario): ?>
                        <option selected value="<?= (int)$usuario['idUsuario'] ?>"><?= escapar($usuario['nombre']) ?> (ID: <?= (int)$usuario['idUsuario'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <p class="sc-nota">Solo puedes administrar los servicios asociados a tu cuenta.</p>
            </div>
        </section>

        <section class="sc-panel">
            <div class="sc-panel-titulo"><h2>02 · Agregar servicio</h2></div>
            <div class="sc-panel-cuerpo">
                <form id="sc-formulario">
                    <div class="row align-items-end">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <label class="sc-label" for="sc-servicio">Servicio que presta</label>
                            <select id="sc-servicio" class="form-control" required disabled>
                                <option value="">Selecciona un servicio...</option>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?= (int)$servicio['idServicio'] ?>"><?= escapar($servicio['servicio']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4"><button id="sc-agregar" class="sc-boton w-100" type="submit" disabled>+ Agregar servicio</button></div>
                    </div>
                </form>
            </div>
        </section>

        <section class="sc-panel">
            <div class="sc-panel-titulo"><h2>03 · Servicios asignados</h2><span id="sc-total" class="sc-resumen">0 servicios</span></div>
            <div class="table-responsive">
                <table class="sc-tabla">
                    <thead><tr><th style="width:90px">ID</th><th>Servicio</th><th style="width:135px">Acciones</th></tr></thead>
                    <tbody id="sc-listado"><tr><td colspan="3" class="sc-vacio">Selecciona una persona para ver sus servicios.</td></tr></tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<?php require_once('./script.php'); ?>
<script>
$(function () {
    'use strict';
    const token = <?= json_encode($_SESSION['csrf_servicios']) ?>;
    const $usuario = $('#sc-usuario');
    const $servicio = $('#sc-servicio');
    const $agregar = $('#sc-agregar');
    let ocupado = false;

    function aviso(mensaje, error) {
        $('#sc-aviso').removeClass('alert-success alert-danger')
            .addClass(error ? 'alert-danger' : 'alert-success').text(mensaje).show();
    }
    function bloquear(valor) {
        ocupado = valor;
        $usuario.prop('disabled', valor);
        $servicio.prop('disabled', valor || !$usuario.val());
        $agregar.prop('disabled', valor || !$usuario.val());
    }
    function peticion(datos) {
        return $.ajax({ url: window.location.pathname, type: 'POST', dataType: 'json', data: Object.assign({csrf: token}, datos) });
    }
    function cargar() {
        const id = $usuario.val();
        $('#sc-aviso').hide();
        if (!id) {
            $('#sc-listado').html('<tr><td colspan="3" class="sc-vacio">Selecciona una persona para ver sus servicios.</td></tr>');
            $('#sc-total').text('0 servicios');
            bloquear(false);
            return;
        }
        bloquear(true);
        $('#sc-listado').html('<tr><td colspan="3" class="sc-vacio">Cargando servicios...</td></tr>');
        peticion({accion:'listar', idUsuario:id}).done(function (respuesta) {
            if (!respuesta.ok) { aviso(respuesta.mensaje, true); return; }
            const datos = respuesta.datos || [];
            $('#sc-total').text(datos.length + (datos.length === 1 ? ' servicio' : ' servicios'));
            const $tabla = $('#sc-listado').empty();
            if (!datos.length) {
                $tabla.append($('<tr>').append($('<td>', {colspan:3, class:'sc-vacio'}).text('Esta persona todavía no tiene servicios asignados.')));
            }
            datos.forEach(function (item) {
                const $boton = $('<button>', {type:'button', class:'sc-eliminar', text:'Eliminar'})
                    .attr('data-id', item.idDetServ);
                $('<tr>').append(
                    $('<td>').append($('<span>', {class:'sc-numero'}).text(item.idServicio)),
                    $('<td>').text(item.nombreServicio),
                    $('<td>').append($boton)
                ).appendTo($tabla);
            });
        }).fail(function () { aviso('No fue posible consultar los servicios.', true); })
          .always(function () { bloquear(false); });
    }
    $usuario.on('change', function () { $servicio.val(''); cargar(); });
    cargar();
    $('#sc-formulario').on('submit', function (e) {
        e.preventDefault();
        if (ocupado || !$usuario.val() || !$servicio.val()) { aviso('Selecciona una persona y un servicio.', true); return; }
        bloquear(true);
        peticion({accion:'agregar', idUsuario:$usuario.val(), idServicio:$servicio.val()})
            .done(function (r) { aviso(r.mensaje, !r.ok); if (r.ok) { $servicio.val(''); cargar(); } })
            .fail(function () { aviso('No fue posible agregar el servicio.', true); })
            .always(function () { if (ocupado) bloquear(false); });
    });
    $('#sc-listado').on('click', '.sc-eliminar', function () {
        if (ocupado) return;
        const idDetServ = $(this).attr('data-id');
        const confirmar = window.Swal ? Swal.fire({title:'¿Eliminar servicio?', text:'Se quitará la asociación de esta persona.', icon:'warning', showCancelButton:true, confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar', confirmButtonColor:'#13764a'}).then(r => r.isConfirmed) : Promise.resolve(confirm('¿Quitar este servicio de la persona?'));
        confirmar.then(function (aceptado) {
            if (!aceptado) return;
            bloquear(true);
            peticion({accion:'eliminar', idUsuario:$usuario.val(), idDetServ:idDetServ})
                .done(function (r) { aviso(r.mensaje, !r.ok); if (r.ok) cargar(); })
                .fail(function () { aviso('No fue posible eliminar el servicio.', true); })
                .always(function () { if (ocupado) bloquear(false); });
        });
    });
});
</script>
</body>
</html>
