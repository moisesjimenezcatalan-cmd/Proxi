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
            <div>
                <div class="sc-etiqueta">Servicios Chiapas · Inicio</div>
                <h1>Bienvenido al sistema</h1>
                <p>Consulta las opciones disponibles desde el menú de navegación.</p>
            </div>
        </header>
        <section class="sc-panel">
            <div class="sc-panel-titulo"><h2>Panel principal</h2></div>
            <div class="sc-panel-cuerpo">
                Has iniciado sesión correctamente. Selecciona un módulo del menú superior para continuar.
            </div>
        </section>
    </div>
</main>

<?php require_once('./script.php'); ?>
</body>
</html>
