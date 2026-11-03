<?php
require_once('./config/conexion.php');
$conexion = new base_de_datos();
$municipios = $conexion->consultar('SELECT cveMun, Municipio FROM municipios ORDER BY Municipio');
if (!is_array($municipios)) { $municipios = []; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro de cuenta | Servicios Generales</title>
<link href="./css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--vino:#781e36;--vino-oscuro:#501528;--oro:#bc955b;--verde:#315c47;--fondo:#f7f6f3;--texto:#27332e;--borde:#e5e7e3;--suave:#737d76}
*{box-sizing:border-box}body.pagina-perfil{margin:0;min-height:100vh;background:radial-gradient(circle at 90% 0%,#f1e7e5 0,transparent 36%),var(--fondo);font-family:system-ui,-apple-system,'Segoe UI',sans-serif;color:var(--texto)}
.franja{height:7px;background:linear-gradient(90deg,var(--verde) 0 33%,var(--oro) 33% 66%,var(--vino) 66%)}
.contenedor{max-width:1060px;margin:0 auto;padding:32px 22px 60px}.superior{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:28px}.marca{display:flex;align-items:center;gap:15px}.marca img{width:65px;height:65px;object-fit:contain}.marca .institucion{font-weight:750;letter-spacing:.015em;font-size:17px;color:var(--vino-oscuro)}.marca small{display:block;color:var(--suave);font-size:12px;margin-top:3px}.regresar{color:var(--vino);font-size:14px;text-decoration:none;font-weight:650;padding:10px 14px;border:1px solid #e8d9dc;border-radius:10px;background:#fff}.regresar:hover{color:var(--vino-oscuro);background:#f9f1f2}
.tarjeta{background:#fff;border:1px solid #ece9e5;border-radius:20px;box-shadow:0 16px 48px rgba(54,44,38,.07);overflow:hidden}.cabecera{padding:34px 42px 28px;border-bottom:1px solid #f0efed}.etiqueta{display:inline-flex;align-items:center;gap:7px;color:var(--verde);font-size:11px;font-weight:800;letter-spacing:.11em;text-transform:uppercase}.etiqueta:before{content:'';width:7px;height:7px;background:var(--oro);border-radius:50%}.cabecera h1{font-size:clamp(25px,3vw,34px);letter-spacing:-.04em;margin:10px 0 8px;font-weight:780;color:var(--vino-oscuro)}.cabecera p{color:var(--suave);font-size:14px;margin:0;line-height:1.6}.contenido{padding:32px 42px 36px}.seccion{display:flex;align-items:center;gap:12px;margin:0 0 22px;font-size:13px;font-weight:800;color:var(--verde)}.seccion .numero{display:grid;place-items:center;width:28px;height:28px;background:#edf4ee;border-radius:9px;color:var(--verde)}.formulario-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px 24px}.campo label{display:block;margin-bottom:8px;font-size:13px;font-weight:700;color:#354139}.campo input,.campo select{display:block;width:100%;height:49px;padding:0 15px;border:1px solid #dfe3df;border-radius:10px;background:#fff;color:#26332b;font-size:14px;outline:none;transition:border-color .2s,box-shadow .2s}.campo input::placeholder{color:#9ba49d}.campo input:focus,.campo select:focus{border-color:var(--verde);box-shadow:0 0 0 3px rgba(49,92,71,.1)}.campo select:disabled{background:#f5f6f4;color:#9ba49d}.campo-direccion{grid-column:1/-1}.ayuda{display:block;color:#879089;font-size:11px;margin-top:6px}.separador{border:0;border-top:1px solid #eceeeb;margin:30px 0 25px}.acciones{display:flex;align-items:center;justify-content:space-between;gap:18px}.nota{font-size:12px;color:#7e877f;line-height:1.5;max-width:390px}.boton-guardar{border:0;background:var(--vino);color:white;border-radius:10px;padding:14px 26px;font-size:14px;font-weight:750;min-width:185px;cursor:pointer;box-shadow:0 6px 15px rgba(120,30,54,.15);transition:background .2s,transform .2s}.boton-guardar:hover{background:var(--vino-oscuro);transform:translateY(-1px)}.boton-guardar:disabled{opacity:.65;cursor:wait;transform:none}.mensaje{display:none;margin-top:18px;padding:12px 15px;border-radius:10px;font-size:13px}.mensaje.exito{display:block;background:#edf6ef;color:#285a3c;border:1px solid #cde7d4}.mensaje.error{display:block;background:#fff0f0;color:#8c2631;border:1px solid #f2d0d4}.pie{text-align:center;color:#909891;font-size:12px;margin-top:24px}
@media(max-width:680px){.contenedor{padding:20px 14px 36px}.superior{margin-bottom:18px}.marca img{width:48px;height:48px}.marca .institucion{font-size:14px}.marca small{font-size:10px}.regresar{font-size:12px;padding:9px}.cabecera{padding:26px 22px}.contenido{padding:26px 22px}.formulario-grid{grid-template-columns:1fr;gap:18px}.campo-direccion{grid-column:auto}.acciones{flex-direction:column;align-items:stretch}.boton-guardar{width:100%}.nota{order:2}}
</style>
</head>
<body class="pagina-perfil">
<div class="franja"></div>
<div class="contenedor">
<header class="superior">
  <div class="marca"><img src="./img/logo.png" alt="Logotipo institucional" onerror="this.style.display='none'">
  <div>
    <div class="institucion">Servicios Chiapas</div><small>Plataforma de registro</small></div></div>
  <a class="regresar" href="index.php">← Volver al inicio</a>
</header>
<main class="tarjeta">
  <div class="cabecera"><span class="etiqueta">Registro de usuarios</span><h1>Crear una cuenta</h1><p>Completa la información solicitada para registrar un nuevo perfil en la plataforma.</p></div>
  <section class="contenido">
  <form name="frm" id="frm" method="POST" autocomplete="off">
    <div class="seccion"><span class="numero">01</span> Datos personales y ubicación</div>
    <div class="formulario-grid">
      <div class="campo"><label for="nombre">Nombre completo</label><input type="text" id="nombre" name="nombre" placeholder="Nombre y apellidos" maxlength="150" required></div>
      <div class="campo"><label for="curp">CURP</label><input type="text" id="curp" name="curp" maxlength="18" minlength="18" placeholder="18 caracteres" autocapitalize="characters" pattern="[A-Za-z0-9]{18}" title="Introduce los 18 caracteres de tu CURP" required><small class="ayuda">Escribe los 18 caracteres de tu CURP.</small></div>
      <div class="campo"><label for="cveMun">Municipio</label><select id="cveMun" name="cveMun" required><option value="">Selecciona un municipio</option><?php foreach ($municipios as $row): ?><option value="<?= htmlspecialchars((string)$row['cveMun'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$row['Municipio'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
      <div class="campo"><label for="localidad">Localidad</label><select id="localidad" name="localidad" required disabled><option value="">Primero selecciona un municipio</option></select></div>
      <div class="campo campo-direccion"><label for="direccion">Dirección</label><input type="text" id="direccion" name="direccion" placeholder="Calle, número, colonia y referencias" required></div>
    </div>
    <hr class="separador">
    <div class="seccion"><span class="numero">02</span> Contacto y acceso</div>
    <div class="formulario-grid">
      <div class="campo"><label for="whatsapp">Número de WhatsApp</label><input type="tel" id="whatsapp" name="whatsapp" maxlength="20" placeholder="Número de contacto" required></div>
      <div class="campo"><label for="clave">Contraseña</label><input type="password" id="clave" name="clave" placeholder="Crea una contraseña" autocomplete="new-password" required></div>
    </div>
    <hr class="separador">
    <div class="acciones"><span class="nota">Verifica que tus datos sean correctos antes de guardar el registro.</span><button type="submit" class="boton-guardar" id="btnGuardar">Guardar cuenta →</button></div>
    <div id="mensaje" class="mensaje" role="status" aria-live="polite"></div>
  </form>
  </section>
</main>
<footer class="pie">Servicios Chiapas · Sistema de registro</footer>
</div>
<script src="./js/jquery-3.7.1.min.js"></script>
<script>
$(function () {
    const $localidad = $('#localidad');
    const $mensaje = $('#mensaje');
    function avisar(texto, tipo) { $mensaje.removeClass('exito error').addClass('mensaje ' + tipo).text(texto); }
    $('#curp').on('input', function () { this.value = this.value.toUpperCase(); });
    $('#cveMun').on('change', function () {
        const municipio = $(this).val();
        $localidad.empty().append($('<option>').val('').text(municipio ? 'Cargando localidades...' : 'Primero selecciona un municipio')).prop('disabled', true);
        if (!municipio) return;
        $.ajax({
            type: 'POST', url: './envio_mun.php', data: { cveMun: municipio }, dataType: 'text',
            success: function (respuesta) {
                $localidad.empty().append($('<option>').val('').text('Selecciona una localidad'));
                respuesta.split('|').forEach(function (registro) {
                    const separador = registro.indexOf('-');
                    if (separador < 1) return;
                    const id = registro.substring(0, separador);
                    const nombre = registro.substring(separador + 1);
                    $localidad.append($('<option>').val(id).text(nombre));
                });
                $localidad.prop('disabled', false);
            },
            error: function () { $localidad.empty().append($('<option>').val('').text('No se pudieron cargar las localidades')); avisar('No fue posible consultar las localidades. Intenta nuevamente.', 'error'); }
        });
    });
    $('#frm').on('submit', function (evento) {
        evento.preventDefault();
        if (!this.reportValidity()) return;
        const $boton = $('#btnGuardar');
        $boton.prop('disabled', true).text('Guardando...');
        $mensaje.removeClass('exito error').hide();
        $.ajax({
            type: 'POST', url: './g_cuenta.php', data: $(this).serialize(), dataType: 'text',
            success: function (respuesta) {
                if (String(respuesta).trim() === '1') {
                    avisar('La cuenta se guardó correctamente.', 'exito');
                } else {
                    avisar('No se pudo guardar la cuenta. Revisa los datos o consulta al administrador.', 'error');
                }
                $mensaje.show();
            },
            error: function () { avisar('Ocurrió un error de conexión al guardar. Intenta nuevamente.', 'error'); $mensaje.show(); },
            complete: function () { $boton.prop('disabled', false).text('Guardar cuenta →'); }
        });
    });
});
</script>
</body>
</html>
