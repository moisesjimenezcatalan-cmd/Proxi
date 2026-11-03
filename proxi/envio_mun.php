<?php
$cveMun = $_POST['cveMun'];
require_once("./config/conexion.php");
$conexion = new base_de_datos();

$sql = "SELECT
localidades.cveLoc,
localidades.localidad,
localidades.cveMun
FROM
localidades
WHERE
localidades.cveMun = ?";
$res = $conexion->consultar($sql,"s",$cveMun);
$reg = count($res);
$valores = "";
if($reg > 0)
{
    foreach($res as $row)
    {
        $valores = $valores . $row['cveLoc'] . '-' . $row['localidad'] . "|";
    };
    echo $valores;
};

?>