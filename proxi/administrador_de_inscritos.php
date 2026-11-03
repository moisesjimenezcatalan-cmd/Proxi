<?php
require_once("./config/conexion.php");
$conexion = new base_de_datos();
$municipios = [];
/* CONSULTAR MUNICIPIOS */
$sql = "SELECT cveMun, Municipio
     FROM municipios
     ORDER BY Municipio";
$municipios = $conexion->consultar($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuentas (Perfil) | PROXI</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="css/perfil.css">
</head>

<body class="pagina-perfil">

    <main class="ventana-perfil">

        <!-- ENCABEZADO -->
        <header class="barra-perfil">
            <span>Crear cuentas (Perfil)</span>

            <a href="panel.php"
               class="cerrar-perfil"
               title="Cerrar">X</a>
        </header>

        <!-- FORMULARIO -->
        <section class="contenido-perfil">

            <form name="frm" id="frm" method="POST" autocomplete="off">

                <div class="formulario-grid">

                    <!-- NOMBRE -->
                    <div class="campo">
                        <label for="nombre">Nombre:</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            placeholder="Escribe el nombre completo"
                            required>
                    </div>

                   

                    <!-- MUNICIPIO -->
                    <div class="campo">
                        <label for="cveMun">Municipio:</label>

                        <select id="cveMun" name="cveMun" required>
                            <option value="">-Seleccione-</option>
                            <?php
                            $reg = count($municipios);
                            if($reg > 0)
                            {
                                foreach($municipios as $row)
                                {
                                    
                                    ?>
                                    <option value="<?= $row['cveMun'];?>">
                                        <?php echo $row['Municipio'];?>
                                    </option>
                                    <?php
                                }; 
                            };
                            ?>                            
                        </select>
                    </div>

                    <!-- LOCALIDAD -->
                    <div class="campo">
                        <label for="localidad">Localidad:</label>

                        <select id="localidad" name="localidad" required>
                            
                        </select>
                    </div>

                     <!-- SERVICIO -->
                    <div class="campo">
                        <label for="idservicio">Servicio:</label>

                        <select id="cveMun" name="cveMun" required>
                            <option value="">-Seleccione-</option>
                            <?php
                            $reg = count($servicios);
                            if($reg > 0)
                            {
                                foreach($servicios as $row)
                                {
                                    
                                    ?>
                                    <option value="<?= $row['idservicio'];?>">
                                        <?php echo $row['servicio'];?>
                                    </option>
                                    <?php
                                }; 
                            };
                            ?>                            
                        </select>
                    </div>

                     <!-- DIRECCIÓN -->
                    <div class="campo campo-direccion">
                        <label for="direccion">Dirección:</label>
                        <input
                            type="text"
                            id="direccion"
                            name="direccion"
                            placeholder="Calle, número y colonia"
                            required>
                    </div>

                </div>

                    <!-- Telefono -->
                    <div class="campo">
                        <label for="telefono">Telefono:</label>
                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            maxlength="20"
                            placeholder="Número de Telefono"
                            required>
                    </div>

                    <!-- CLAVE -->
                    <div class="campo">
                        <label for="clave">Clave:</label>
                        <input
                            type="password"
                            id="clave"
                            name="clave"
                            placeholder="Crea una clave"
                            autocomplete="new-password"
                            required>
                    </div>

                   

                <!-- BOTÓN GUARDAR -->
                <div class="acciones-perfil">
                    <div class="boton-guardar" id="btnGuardar">
                        Guardar
                    </div>

                    <span class="estado-perfil">
                        Status: Inactivo
                    </span>
                </div>

            </form>

        </section>

    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function (){
        $('#cveMun').on('change', function() {
            var valorSeleccionado = $(this).val();
            var textoSeleccionado = $(this).find('option:selected').text();
            
            //window.alert(valorSeleccionado);
            dato = "cveMun=" + valorSeleccionado;
            $.ajax({
                type: "POST",
                url: "./envio_mun.php",
                data: dato,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    var error;
                    console.log(XMLHttpRequest);
                    if (XMLHttpRequest.status === 404) error = "Pagina no existe" + XMLHttpRequest.status; // display some page not found error
                    if (XMLHttpRequest.status === 500) error = "Error del Servidor" + XMLHttpRequest.status; // display some server error
                    console.log(error);
                },
                success: function (msj) {
                    $("#localidad").empty();
                    $("#localidad").append('<option value="">-Seleccione-</option>');
                    let array = msj.split("|");
                    for(let i = 0; i < array.length-1; i++)
                    {
                        let localidad = array[i].split("-");
                        $("#localidad").append('<option value="' + localidad[0] + '">' + localidad[1] + '</option>');
                    };
                },
            });
        });
        $('#btnGuardar').on('click', function() {
            
            var dato = $("#frm").serialize();
            $.ajax({
                type: "POST",
                url: "./g_cuenta.php",
                data: dato,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    var error;
                    console.log(XMLHttpRequest);
                    if (XMLHttpRequest.status === 404) error = "Pagina no existe" + XMLHttpRequest.status; // display some page not found error
                    if (XMLHttpRequest.status === 500) error = "Error del Servidor" + XMLHttpRequest.status; // display some server error
                    console.log(error);
                },
                success: function (msj) {
                    if(msj == 1)
                    {
                        window.alert("Cuenta guardada con exito");
                    }
                },
            });
        });
    });        
    </script>

</body>
</html>