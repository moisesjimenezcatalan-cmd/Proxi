<?php
require_once("./config/conexion.php");
$conexion = new base_de_datos();

$nombre = isset($_POST['nombre']) ? (string)$_POST['nombre'] : "";
$telWhats = isset($_POST['telWhats']) ? (string)$_POST['telWhats'] : "";
$cveMun = isset($_POST['cveMun']) ? (string)$_POST['cveMun'] : "";
$cveLoc = isset($_POST['cveLoc']) ? (string)$_POST['cveLoc'] : "";
$curp = isset($_POST['curp']) ? (string)$_POST['curp'] : "";
$clave = isset($_POST['clave']) ? (string)$_POST['clave'] : "";

$sql = "INSERT INTO `usuarios` (`nombre`, `telWhats`, `cveMun`, `cveLoc`, `curp`, `clave`, `idStatus`, `idTipo`) VALUES 
(?, ?, ?, ?, ?, ?, '1', '2')";
$res = $conexion->ejecutar($sql,"ssssss",$nombre, $telWhats, $cveMun, $cveLoc, $curp, $clave);
echo 1;
?>