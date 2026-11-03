<?php
session_start();
require_once("./config/conexion.php");
$conexion = new base_de_datos();

$sql = "SELECT
usuarios.idUsuario,
usuarios.nombre,
usuarios.telWhats,
usuarios.cveMun,
municipios.Municipio,
usuarios.cveLoc,
localidades.localidad,
usuarios.curp,
usuarios.idStatus,
`status`.desStatus,
usuarios.idTipo,
tipousuarios.tipo
FROM
usuarios
LEFT JOIN municipios ON usuarios.cveMun = municipios.cveMun
LEFT JOIN localidades ON localidades.cveMun = municipios.cveMun AND usuarios.cveLoc = localidades.cveLoc
LEFT JOIN `status` ON usuarios.idStatus = `status`.idStatus
LEFT JOIN tipousuarios ON usuarios.idTipo = tipousuarios.idTipo
WHERE
usuarios.curp = ? AND
usuarios.clave = ?";



$res = $conexion->consultar($sql,"ss",$_POST['curp'],$_POST['clave']);
$reg = count($res);

if($reg == 1)
{
    
    foreach($res as $row)
    {
        $_SESSION['idUsuario'] = isset($row['idUsuario']) ? (int)$row['idUsuario'] : 0;
        $_SESSION['nombre'] = isset($row['nombre']) ? (string)$row['nombre'] : "";
         $_SESSION['telWhats'] = isset($row['telWhats']) ? (string)$row['telWhats'] : "";
        $_SESSION['cveMun'] = isset($row['cveMun']) ? (string)$row['cveMun'] : "";
         $_SESSION['Municipio'] = isset($row['Municipio']) ? (string)$row['Municipio'] : "";
        $_SESSION['cveLoc'] = isset($row['cveLoc']) ? (string)$row['cveLoc'] : "";
         $_SESSION['localidad'] = isset($row['localidad']) ? (string)$row['localidad'] : "";
        $_SESSION['curp'] = isset($row['curp']) ? (string)$row['curp'] : "";
         $_SESSION['idStatus'] = isset($row['idStatus']) ? (int)$row['idStatus'] : 0;
         $_SESSION['desStatus'] = isset($row['desStatus']) ? (string)$row['desStatus'] : "";
        $_SESSION['idTipo'] = isset($row['idTipo']) ? (int)$row['idTipo'] : 0;
         $_SESSION['tipo'] = isset($row['tipo']) ? (string)$row['tipo'] : "";
        echo $_SESSION['Aut'] = 1;
                
    };
}
else
{
    $_SESSION['Aut'] = 0;
    echo 2;
};
?>