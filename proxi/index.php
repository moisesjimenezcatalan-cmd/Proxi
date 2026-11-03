<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#073c29">
    <title>Iniciar sesión | Servicios Chiapas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --verde:#073c29; --verde-claro:#116943; --dorado:#e8b33d; --fondo:#f4f7f3; }
        * { box-sizing:border-box; }
        body { min-height:100vh; margin:0; background:var(--fondo); color:#19372b; font-family:system-ui,-apple-system,"Segoe UI",sans-serif; }
        .pagina { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:32px 16px; }
        .panel { width:100%; max-width:1060px; display:grid; grid-template-columns:1fr 1fr; border-radius:26px; overflow:hidden; background:white; box-shadow:0 24px 75px rgba(9,53,35,.13); }
        .presentacion { position:relative; isolation:isolate; padding:54px 48px; display:flex; flex-direction:column; justify-content:space-between; background:linear-gradient(145deg,#053322,#0a5737 72%,#147c48); color:white; min-height:620px; overflow:hidden; }
        .presentacion::before,.presentacion::after { content:""; position:absolute; border:1px solid rgba(232,179,61,.24); border-radius:50%; z-index:-1; }
        .presentacion::before { width:520px;height:520px;right:-260px;top:-160px; }
        .presentacion::after { width:410px;height:410px;left:-230px;bottom:-200px; }
        .marca { display:inline-flex;align-items:center;gap:10px;font-size:.8rem;font-weight:700;letter-spacing:.17em;text-transform:uppercase;color:#ffe5a1; }
        .marca i { font-size:1.3rem; }
        .logo-grande { display:block; width:min(100%,340px); max-height:330px; object-fit:contain; margin:10px auto 20px; filter:drop-shadow(0 15px 22px rgba(0,0,0,.18)); }
        .presentacion h1 { font-size:clamp(1.8rem,3vw,2.5rem);font-weight:800;letter-spacing:-.04em;line-height:1.15;margin-bottom:14px; }
        .presentacion p { color:#d5e9dd;line-height:1.7;margin-bottom:0;max-width:350px; }
        .linea-dorada { height:4px;width:52px;background:var(--dorado);border-radius:6px;margin-bottom:20px; }
        .pie-marca { font-size:.8rem;color:#b9d6c4; }
        .acceso { display:flex;align-items:center;justify-content:center;padding:65px 58px; }
        .formulario { width:100%;max-width:390px; }
        .etiqueta-superior { color:var(--verde-claro);font-size:.76rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase; }
        .formulario h2 { font-size:2rem;font-weight:800;letter-spacing:-.045em;margin:12px 0 9px; }
        .descripcion { color:#718078;font-size:.94rem;line-height:1.6;margin-bottom:34px; }
        .form-label { font-weight:650;font-size:.9rem;margin-bottom:9px; }
        .input-group { border:1px solid #dce6df;border-radius:12px;overflow:hidden;transition:border-color .2s,box-shadow .2s; }
        .input-group:focus-within { border-color:#248a59;box-shadow:0 0 0 4px rgba(17,105,67,.10); }
        .input-group-text { border:0;background:#f8faf8;color:#54806a;padding:0 16px; }
        .form-control { min-height:53px;border:0;background:#f8faf8;font-size:.94rem; }
        .form-control:focus { background:#f8faf8;box-shadow:none; }
        .form-control::placeholder { color:#9ba9a0; }
        .ver-clave { border:0;background:#f8faf8;color:#54806a;padding:0 16px; }
        .btn-entrar { min-height:54px;border:0;border-radius:12px;background:linear-gradient(110deg,#075035,#127348);font-weight:700;box-shadow:0 10px 24px rgba(9,86,54,.19);transition:transform .2s,filter .2s; }
        .btn-entrar:hover,.btn-entrar:focus { background:linear-gradient(110deg,#06452e,#0c633d);transform:translateY(-1px);filter:brightness(1.05); }
        .btn-entrar:disabled { opacity:.7;transform:none; }
        .registro { margin-top:29px;padding-top:24px;border-top:1px solid #edf1ee;text-align:center;font-size:.91rem;color:#718078; }
        .registro a { color:var(--verde-claro);font-weight:750;text-decoration:none; }
        .registro a:hover { text-decoration:underline; }
        .sello { margin-top:35px;color:#9aa79e;font-size:.78rem;text-align:center; }
        @media(max-width:800px) { .panel { max-width:520px;grid-template-columns:1fr; } .presentacion { min-height:auto;padding:28px 30px 24px;text-align:center; } .marca { justify-content:center; } .logo-grande { width:160px;max-height:160px;margin:12px auto 8px; } .presentacion h1 { font-size:1.45rem;margin-bottom:6px; } .presentacion p,.linea-dorada,.pie-marca { display:none; } .acceso { padding:38px 30px 40px; } }
        @media(max-width:420px) { .pagina { padding:12px; } .panel { border-radius:18px; } .acceso { padding:32px 22px; } .formulario h2 { font-size:1.75rem; } }
        @media(prefers-reduced-motion:reduce) { *,*::before,*::after { transition:none!important; } }
    </style>
</head>
<body>
<main class="pagina">
    <div class="panel">
        <section class="presentacion" aria-label="Presentación de Servicios Chiapas">
            <div class="marca"><i class="bi bi-stars" aria-hidden="true"></i> Plataforma de servicios</div>
            <div>
                <img src="./img/logo.png" alt="Logotipo de Servicios Chiapas" class="logo-grande">
                <div class="linea-dorada"></div>
                <h1>Bienvenido a<br>Servicios Chiapas</h1>
                <p>Tu espacio para acceder a los servicios de manera sencilla, segura y desde cualquier lugar.</p>
            </div>
            <div class="pie-marca"><i class="bi bi-shield-check me-2" aria-hidden="true"></i>Acceso a la plataforma PROXI</div>
        </section>
        <section class="acceso" aria-labelledby="titulo-acceso">
            <div class="formulario">
                <span class="etiqueta-superior">Acceso a usuarios</span>
                <h2 id="titulo-acceso">Iniciar sesión</h2>
                <p class="descripcion">Ingresa tu CURP y contraseña para continuar a tu cuenta.</p>
                <?php if (isset($_GET['error']) && is_string($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <div id="mensaje" class="alert alert-danger d-none" role="alert" aria-live="polite"></div>
                <form action="./validate.php" method="POST" autocomplete="off" id="frm" name="frm">
                    <div class="mb-4">
                        <label for="curp" class="form-label">CURP</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-vcard" aria-hidden="true"></i></span>
                            <input type="text" id="curp" name="curp" class="form-control" placeholder="Ingresa tu CURP" minlength="18" maxlength="18" pattern="[A-Za-z0-9]{18}" title="Ingresa una CURP de 18 caracteres" autocomplete="username" autocapitalize="characters" spellcheck="false" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="clave" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock" aria-hidden="true"></i></span>
                            <input type="password" id="clave" name="clave" class="form-control" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
                            <button type="button" id="mostrarClave" class="ver-clave" aria-label="Mostrar contraseña" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-entrar w-100" id="btnEntrar"><i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>Entrar a mi cuenta</button>
                </form>
                <div class="registro">¿Aún no tienes una cuenta? <a href="perfil.php">Crear cuenta <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
                <div class="sello"><i class="bi bi-shield-lock me-1" aria-hidden="true"></i> Protege tus datos de acceso</div>
            </div>
        </section>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function () {
    const $form = $('#frm');
    const $boton = $('#btnEntrar');
    const $mensaje = $('#mensaje');
    const textoBoton = $boton.html();

    $('#curp').on('input', function () { this.value = this.value.toUpperCase().replace(/\s/g, ''); });
    $('#mostrarClave').on('click', function () {
        const visible = $('#clave').attr('type') === 'text';
        $('#clave').attr('type', visible ? 'password' : 'text');
        $(this).attr('aria-pressed', String(!visible)).attr('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        $(this).find('i').toggleClass('bi-eye', visible).toggleClass('bi-eye-slash', !visible);
    });
    $form.on('submit', function (evento) {
        evento.preventDefault();
        if (!this.checkValidity()) { this.reportValidity(); return; }
        $mensaje.addClass('d-none').text('');
        $boton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Verificando...');
        $.ajax({
            type: 'POST',
            url: './validate.php',
            data: $form.serialize(),
            dataType: 'text',
            timeout: 20000
        }).done(function (respuesta) {
            const resultado = respuesta.trim();
            if (resultado === '1') {
                window.location.href = 'bienvenido.php';
            } else if (resultado === '2') {
                $('#clave').val('').trigger('focus');
                $mensaje.text('La CURP o la contraseña son incorrectas. Inténtalo nuevamente.').removeClass('d-none');
            } else {
                $mensaje.text('No fue posible validar el acceso. Inténtalo nuevamente.').removeClass('d-none');
            }
        }).fail(function () {
            $mensaje.text('No se pudo conectar con el servidor. Revisa tu conexión e inténtalo de nuevo.').removeClass('d-none');
        }).always(function () {
            $boton.prop('disabled', false).html(textoBoton);
        });
    });
});
</script>
</body>
</html>
