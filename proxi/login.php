<?php

session_start();

require_once __DIR__ . "/config/conexion.php";


// Verificar que el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");

    exit;
}


// Recibir datos
$curp = strtoupper(trim($_POST["curp"] ?? ""));
$clave = $_POST["clave"] ?? "";


// Verificar campos
if ($curp === "" || $clave === "") {

    header("Location: index.php?error=Debes ingresar CURP y clave");

    exit;
}


// Buscar usuario
$sql = "SELECT * FROM usuarios WHERE curp = ? LIMIT 1";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("s", $curp);

$stmt->execute();

$resultado = $stmt->get_result();


// Verificar usuario
if ($resultado->num_rows === 0) {

    header("Location: index.php?error=CURP o clave incorrectos");

    exit;
}


$usuario = $resultado->fetch_assoc();


// Verificar estatus
if ($usuario["estatus"] !== "ACTIVO") {

    header("Location: index.php?error=Tu cuenta está inactiva");

    exit;
}


// Verificar contraseña
if (!password_verify($clave, $usuario["clave"])) {

    header("Location: index.php?error=CURP o clave incorrectos");

    exit;
}


// Crear sesión
$_SESSION["idUsuario"] = $usuario["idUsuario"];

$_SESSION["nombre"] = $usuario["nombre"];

$_SESSION["curp"] = $usuario["curp"];

$_SESSION["tipoUsuario"] = $usuario["tipoUsuario"];


// Enviar al panel
header("Location: panel.php");

exit;

?>