<?php
session_start();
if (!isset($_SESSION['Aut']) || (int)$_SESSION['Aut'] !== 1) {
    header('Location: index.php');
    exit;
}
require_once('./config/conexion.php');
$conexion = new base_de_datos();
function escaparBienvenida($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
if (empty($_SESSION['csrf_servicios'])) {
    $_SESSION['csrf_servicios'] = bin2hex(random_bytes(32));
}
$mensaje = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string)($_POST['accion'] ?? '');
    $id = filter_var($_POST['idServicio'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $nombre = trim((string)($_POST['servicio'] ?? ''));
    try {
        if (!hash_equals($_SESSION['csrf_servicios'], (string)($_POST['csrf'] ?? ''))) {
            throw new Exception('Solicitud no válida. Actualiza la página.');
        }
        if (!in_array($accion, ['agregar', 'editar', 'eliminar'], true)) {
            throw new Exception('Acción no permitida.');
        }
        if ($accion !== 'agregar' && !$id) {
            throw new Exception('Identificador de servicio inválido.');
        }
        if ($accion !== 'eliminar' && ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 250)) {
            throw new Exception('El nombre debe tener entre 1 y 250 caracteres.');
        }
        if ($accion === 'agregar') {
            $conexion->ejecutar('INSERT INTO servicios (servicio) VALUES (?)', 's', $nombre);
        } elseif ($accion === 'editar') {
            $existe = $conexion->consultar('SELECT idServicio FROM servicios WHERE idServicio = ?', 'i', $id);
            if (!$existe) throw new Exception('El servicio ya no existe.');
            $conexion->ejecutar('UPDATE servicios SET servicio = ? WHERE idServicio = ?', 'si', $nombre, $id);
        } else {
            $existe = $conexion->consultar('SELECT idServicio FROM servicios WHERE idServicio = ?', 'i', $id);
            if (!$existe) throw new Exception('El servicio ya no existe.');
            $conexion->ejecutar('DELETE FROM servicios WHERE idServicio = ?', 'i', $id);
        }
        $_SESSION['mensaje_servicios'] = ['agregar' => 'Servicio agregado correctamente.', 'editar' => 'Servicio actualizado correctamente.', 'eliminar' => 'Servicio eliminado correctamente.'][$accion];
        header('Location: cat_servicios.php');
        exit;
    } catch (Throwable $e) {
        error_log('Catálogo de servicios: ' . $e->getMessage());
        $error = $e instanceof Exception && in_array($e->getMessage(), ['Solicitud no válida. Actualiza la página.', 'Acción no permitida.', 'Identificador de servicio inválido.', 'El nombre debe tener entre 1 y 250 caracteres.', 'El servicio ya no existe.'], true) ? $e->getMessage() : 'No fue posible completar la operación. Verifica que el servicio no esté relacionado con otros registros.';
    }
}
if (isset($_SESSION['mensaje_servicios'])) {
    $mensaje = $_SESSION['mensaje_servicios'];
    unset($_SESSION['mensaje_servicios']);
}
$servicios = [];
try {
    $servicios = $conexion->consultar('SELECT idServicio, servicio FROM servicios ORDER BY idServicio DESC');
} catch (Throwable $e) {
    error_log('Consulta servicios: ' . $e->getMessage());
    $error = 'No se pudo consultar el catálogo de servicios.';
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once('./head.php'); ?>
<body>
<style>
/* Mismo encabezado de sesión y menú que servicios.php. */
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

/* Área de inicio: misma paleta, cabecera y paneles de servicios.php. */
.sc-area { background: #f3f7f4; min-height: 75vh; padding: 36px 12px 60px; font-family: inherit; }
.sc-contenedor { max-width: 1120px; margin: auto; }
.sc-cabecera { background: linear-gradient(115deg,#063720,#0d6440); border-radius: 20px; color: white; padding: 26px 32px; display: flex; align-items: center; gap: 20px; box-shadow: 0 12px 28px #06372024; border-bottom: 5px solid #dcae34; }
.sc-logo { width: 95px; height: 95px; object-fit: contain; background: #ffffff12; border-radius: 14px; padding: 5px; }
.sc-etiqueta { color: #f5d778; text-transform: uppercase; font-size: .78rem; letter-spacing: 2px; font-weight: 700; }
.sc-cabecera h1 { font-size: clamp(1.5rem,3vw,2.1rem); font-weight: 750; margin: 6px 0; }
.sc-cabecera p { color: #e3f2e9; margin: 0; }
.sc-panel { background: white; border: 1px solid #e0e9e3; border-radius: 18px; overflow: hidden; box-shadow: 0 9px 25px #133b2410; margin-top: 25px; }
.sc-panel-titulo { padding: 20px 25px; border-bottom: 1px solid #e8eee9; }
.sc-panel-titulo h2 { font-size: 1.15rem; font-weight: 750; color: #073e29; margin: 0; }
.sc-panel-cuerpo { padding: 26px; color: #52705c; }
@media (max-width: 650px) {
    .sc-sesion { padding: 14px 10px 0; }
    .sc-sesion-tarjeta { padding: 20px 16px; }
    .sc-sesion-datos { grid-template-columns: 1fr; gap: 8px; }
    .sc-sesion-dato { padding: 10px 13px; }
    .sc-cabecera { padding: 20px; gap: 12px; }
    .sc-logo { width: 65px; height: 65px; }
    .sc-panel-cuerpo, .sc-panel-titulo { padding: 18px; }
}

.sc-toolbar { display:flex; gap:12px; justify-content:space-between; align-items:center; flex-wrap:wrap; }
.sc-btn { border:0; border-radius:10px; padding:11px 17px; font-weight:700; cursor:pointer; }
.sc-btn-primary { background:#0d6440; color:white; }
.sc-btn-primary:hover { background:#073e29; color:white; }
.sc-btn-secondary { background:#e9f0eb; color:#214b35; }
.sc-input { width:100%; border:1px solid #cdded2; border-radius:10px; padding:11px 13px; background:#fff; color:#173c29; }
.sc-input:focus { outline:2px solid #9ec9ac; border-color:#0d6440; }
.sc-tabla { width:100%; border-collapse:collapse; }
.sc-tabla th { background:#f1f7f3; color:#214b35; text-align:left; padding:14px; }
.sc-tabla td { border-top:1px solid #e8eee9; padding:13px 14px; color:#253e30; }
.sc-tabla tbody tr:hover { background:#f8fbf9; }
.sc-acciones { display:flex; gap:8px; }
.sc-icon-btn { border:0; border-radius:9px; padding:8px 11px; cursor:pointer; font-weight:700; }
.sc-editar { color:#124f9b; background:#e9f1ff; }
.sc-eliminar { color:#a32626; background:#ffebeb; }
.sc-alert { border-radius:10px; padding:12px 16px; margin:18px 0; }
.sc-ok { color:#155a35; background:#e5f6ea; }
.sc-error { color:#8c2020; background:#fff0ef; }
.sc-modal-fondo { position:fixed; inset:0; z-index:1100; background:rgba(0,0,0,.55); display:none; justify-content:center; align-items:center; padding:18px; }
.sc-modal-fondo.abierto { display:flex; }
.sc-modal { background:#fff; border-radius:18px; width:100%; max-width:490px; padding:26px; box-shadow:0 20px 60px #0003; }
.sc-modal h2 { color:#073e29; font-size:1.3rem; margin:0 0 18px; }
.sc-modal label { display:block; margin-bottom:7px; font-weight:650; color:#214b35; }
.sc-modal-pie { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
@media(max-width:650px) { .sc-tabla th,.sc-tabla td { padding:10px 8px; } .sc-toolbar > * { width:100%; } }
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
                    <h2 class="sc-sesion-nombre"><?= escaparBienvenida($_SESSION['NOMBRE'] ?? $_SESSION['nombre'] ?? 'Usuario') ?></h2>
                </div>
            </div>
            <div class="sc-sesion-datos">
                <div class="sc-sesion-dato"><span>ID de usuario</span><strong>#<?= escaparBienvenida($_SESSION['idUsuario'] ?? '') ?></strong></div>
                <div class="sc-sesion-dato"><span>Estatus de la cuenta</span><strong><?= escaparBienvenida($_SESSION['desStatus'] ?? 'No disponible') ?></strong></div>
                <div class="sc-sesion-dato"><span>Tipo de usuario</span><strong><?= escaparBienvenida($_SESSION['tipo'] ?? 'No disponible') ?></strong></div>
            </div>
        </div>
        <div class="sc-sesion-menu">
            <?php require_once('./nav.php'); ?>
        </div>
    </div>
</section>

<main class="sc-area">
 <div class="sc-contenedor">
  <header class="sc-cabecera">
   <img class="sc-logo" src="./img/logo.png" alt="Servicios Chiapas">
   <div><div class="sc-etiqueta">Servicios Chiapas · Administración</div><h1>Catálogo de servicios</h1><p>Registra, consulta, modifica y elimina los servicios de la plataforma.</p></div>
  </header>
  <?php if ($mensaje): ?><div class="sc-alert sc-ok" role="status"><?= escaparBienvenida($mensaje) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="sc-alert sc-error" role="alert"><?= escaparBienvenida($error) ?></div><?php endif; ?>
  <section class="sc-panel">
   <div class="sc-panel-titulo sc-toolbar"><h2>Servicios registrados (<?= count($servicios) ?>)</h2><button class="sc-btn sc-btn-primary" type="button" id="nuevoServicio">+ Agregar servicio</button></div>
   <div class="sc-panel-cuerpo">
    <label for="buscarServicio" style="display:block;margin-bottom:8px;color:#214b35;font-weight:650">Buscar servicio</label>
    <input class="sc-input" id="buscarServicio" type="search" placeholder="Buscar por ID o nombre..." autocomplete="off" style="max-width:440px;margin-bottom:20px">
    <div style="overflow-x:auto"><table class="sc-tabla"><thead><tr><th scope="col">ID</th><th scope="col">Servicio</th><th scope="col">Acciones</th></tr></thead><tbody id="tablaServicios">
    <?php foreach ($servicios as $fila): ?>
     <tr data-busqueda="<?= escaparBienvenida($fila['idServicio'] . ' ' . $fila['servicio']) ?>">
      <td><?= (int)$fila['idServicio'] ?></td><td><?= escaparBienvenida($fila['servicio']) ?></td>
      <td><div class="sc-acciones"><button type="button" class="sc-icon-btn sc-editar" data-accion="editar" data-id="<?= (int)$fila['idServicio'] ?>" data-nombre="<?= escaparBienvenida($fila['servicio']) ?>" aria-label="Editar servicio">✎ Editar</button><button type="button" class="sc-icon-btn sc-eliminar" data-accion="eliminar" data-id="<?= (int)$fila['idServicio'] ?>" data-nombre="<?= escaparBienvenida($fila['servicio']) ?>" aria-label="Eliminar servicio">✕ Eliminar</button></div></td>
     </tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <p id="sinResultados" style="display:none;margin-top:18px">No se encontraron servicios.</p>
    <?php if (!$servicios): ?><p style="margin-top:18px">No hay servicios registrados.</p><?php endif; ?>
   </div>
  </section>
 </div>
</main>
<div class="sc-modal-fondo" id="modalServicio" role="presentation">
 <div class="sc-modal" role="dialog" aria-modal="true" aria-labelledby="tituloModal">
  <h2 id="tituloModal">Agregar servicio</h2>
  <form method="post" action="cat_servicios.php" id="formServicio">
   <input type="hidden" name="csrf" value="<?= escaparBienvenida($_SESSION['csrf_servicios']) ?>">
   <input type="hidden" name="accion" id="accionServicio" value="agregar">
   <input type="hidden" name="idServicio" id="idServicio">
   <label for="nombreServicio">Nombre del servicio</label>
   <input class="sc-input" type="text" name="servicio" id="nombreServicio" maxlength="250" required placeholder="Ej. Plomería">
   <div class="sc-modal-pie"><button class="sc-btn sc-btn-secondary" type="button" id="cancelarServicio">Cancelar</button><button class="sc-btn sc-btn-primary" type="submit" id="guardarServicio">Guardar servicio</button></div>
  </form>
 </div>
</div>
<?php require_once('./script.php'); ?>
<script>
(function () {
 const modal = document.getElementById('modalServicio');
 const form = document.getElementById('formServicio');
 const accion = document.getElementById('accionServicio');
 const id = document.getElementById('idServicio');
 const nombre = document.getElementById('nombreServicio');
 const titulo = document.getElementById('tituloModal');
 const guardar = document.getElementById('guardarServicio');
 function cerrar() { modal.classList.remove('abierto'); }
 function abrir(tipo, identificador, texto) {
   form.reset(); accion.value = tipo; id.value = identificador || ''; nombre.value = texto || '';
   titulo.textContent = tipo === 'agregar' ? 'Agregar servicio' : tipo === 'editar' ? 'Modificar servicio' : 'Eliminar servicio';
   guardar.textContent = tipo === 'agregar' ? 'Guardar servicio' : tipo === 'editar' ? 'Guardar cambios' : 'Sí, eliminar';
   nombre.parentElement.querySelector('label').style.display = tipo === 'eliminar' ? 'none' : 'block';
   nombre.style.display = tipo === 'eliminar' ? 'none' : 'block';
   nombre.required = tipo !== 'eliminar';
   guardar.style.background = tipo === 'eliminar' ? '#a32626' : '';
   let aviso = document.getElementById('avisoEliminar');
   if (!aviso) { aviso = document.createElement('p'); aviso.id = 'avisoEliminar'; nombre.after(aviso); }
   aviso.textContent = tipo === 'eliminar' ? '¿Deseas eliminar el servicio «' + texto + '»? Esta acción no se puede deshacer.' : '';
   modal.classList.add('abierto');
   if (tipo !== 'eliminar') nombre.focus(); else guardar.focus();
 }
 document.getElementById('nuevoServicio').addEventListener('click', () => abrir('agregar', '', ''));
 document.getElementById('cancelarServicio').addEventListener('click', cerrar);
 modal.addEventListener('click', e => { if (e.target === modal) cerrar(); });
 document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrar(); });
 document.getElementById('tablaServicios').addEventListener('click', e => {
   const boton = e.target.closest('button[data-accion]');
   if (boton) abrir(boton.dataset.accion, boton.dataset.id, boton.dataset.nombre);
 });
 document.getElementById('buscarServicio').addEventListener('input', function () {
   const q = this.value.trim().toLocaleLowerCase('es'); let visibles = 0;
   document.querySelectorAll('#tablaServicios tr').forEach(fila => {
     const coincide = fila.dataset.busqueda.toLocaleLowerCase('es').includes(q);
     fila.hidden = !coincide; if (coincide) visibles++;
   });
   document.getElementById('sinResultados').style.display = visibles ? 'none' : 'block';
 });
})();
</script>
</body>
</html>
