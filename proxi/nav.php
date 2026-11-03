<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
        Servicios <b>Chiapas</b>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Modulos
          </a>
          <ul class="dropdown-menu">
            <?php if($_SESSION['idTipo'] == 1){?>
            <li>
                <a onclick="window.open('./altas.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Altas
                </a>
            </li>
            <?php
            };
            
            ?>
            <li>
                <a onclick="window.open('./buscador.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Directorio
                </a>
            </li>
            <li>
                <a onclick="window.open('./servicios.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Servicios
                </a>
            </li>
            
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Catálogos
          </a>
          <ul class="dropdown-menu">
            <li>
                <a onclick="window.open('./cat_servicios.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Servicios
                </a>
            </li>
            <li>
                <a onclick="window.open('./cat_municipios.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Municipios
                </a>
            </li>
            <li>
                <a onclick="window.open('./buscador.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Localidades
                </a>
            </li>
            <li>
                <a onclick="window.open('./cat_tipo_usuarios.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Tipo de usuarios
                </a>
            </li>
            <li>
                <a onclick="window.open('./buscador.php','_self');" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Estatus
                </a>
            </li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Cuenta
          </a>
          <ul class="dropdown-menu">
            <li>
                <a onclick="salir();" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" href="#">
                    Salir
                </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>