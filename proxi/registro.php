<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PROXI | Crear cuenta</title>

    <!-- Bootstrap -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" 
        rel="stylesheet"
    >

    <!-- CSS de PROXI -->
    <link rel="stylesheet" href="css/estilos.css">

</head>

<body>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="contenedor-registro">

        <!-- ENCABEZADO -->
        <div class="encabezado-registro">

            <div>

                <h1>Perfil</h1>

                <p>Crear cuenta</p>

            </div>

            <!-- BOTONES DE VENTANA -->
            <div class="botones-ventana">

                <span>×</span>

                <span>□</span>

            </div>

        </div>


        <!-- FORMULARIO -->
        <form action="#" method="POST">

            <!-- NOMBRE -->
            <div class="mb-3">

                <label for="nombre" class="form-label">
                    Nombre
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    class="form-control"
                    required
                >

            </div>


            <!-- WHATSAPP Y MUNICIPIO -->
            <div class="row">

                <!-- WHATSAPP -->
                <div class="col-md-7 mb-3">

                    <label for="whatsapp" class="form-label">
                        WhatsApp
                    </label>

                    <input
                        type="tel"
                        id="whatsapp"
                        name="whatsapp"
                        class="form-control"
                        maxlength="10"
                        placeholder="Número de WhatsApp"
                    >

                </div>


                <!-- MUNICIPIO -->
                <div class="col-md-5 mb-3">

                    <label for="municipio" class="form-label">
                        Municipio
                    </label>

                    <select
                        id="municipio"
                        name="municipio"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Seleccionar
                        </option>

                        <option value="1">
                            Venustiano Carranza
                        </option>

                        <option value="2">
                            Tuxtla Gutiérrez
                        </option>

                        <option value="3">
                            San Cristóbal de las Casas
                        </option>

                    </select>

                </div>

            </div>


            <!-- LOCALIDAD -->
            <div class="mb-3">

                <label for="localidad" class="form-label">
                    Localidad
                </label>

                <input
                    type="text"
                    id="localidad"
                    name="localidad"
                    class="form-control"
                    required
                >

            </div>


            <!-- DIRECCIÓN -->
            <div class="mb-3">

                <label for="direccion" class="form-label">
                    Dirección
                </label>

                <input
                    type="text"
                    id="direccion"
                    name="direccion"
                    class="form-control"
                    required
                >

            </div>


            <!-- CURP Y CLAVE -->
            <div class="row">

                <!-- CURP -->
                <div class="col-md-6 mb-3">

                    <label for="curp" class="form-label">
                        CURP
                    </label>

                    <input
                        type="text"
                        id="curp"
                        name="curp"
                        class="form-control"
                        maxlength="18"
                        required
                    >

                </div>


                <!-- CLAVE -->
                <div class="col-md-6 mb-3">

                    <label for="clave" class="form-label">
                        Clave
                    </label>

                    <input
                        type="password"
                        id="clave"
                        name="clave"
                        class="form-control"
                        required
                    >

                </div>

            </div>


            <!-- PARTE INFERIOR -->
            <div class="parte-inferior">

                <!-- BOTÓN GUARDAR -->
                <button
                    type="submit"
                    class="btn btn-primary btn-guardar"
                >

                    <i class="bi bi-save"></i>

                    Guardar

                </button>


                <!-- STATUS -->
                <div class="status">

                    <label for="estatus" class="form-label">
                        Status
                    </label>

                    <select
                        id="estatus"
                        name="estatus"
                        class="form-select"
                    >

                        <option value="ACTIVO">
                            Activo
                        </option>

                        <option value="INACTIVO">
                            Inactivo
                        </option>

                    </select>

                </div>

            </div>

        </form>

    </div>


    <!-- Bootstrap JS -->
    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>