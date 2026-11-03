<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("America/Mexico_City");
ini_set('date.timezone','America/Mexico_City');

set_time_limit(0);
ini_set('max_execution_time', 0);

class base_de_datos
{
    private object $conexion;
    public string $db_proxi;

    public function __construct()
    {
        $this->db_proxi = "proxi";
        $this->conexion = new mysqli("127.0.0.1","root","",$this->db_proxi,"3307");
        if($this->conexion->connect_error)
        {
            die("Error de conexion: " . $this->conexion->connect_error);
        };
        $this->conexion->set_charset("utf8mb4");
    }
    
    public function ejecutar($sql, $tipos = "", ...$params)
    {
        $stmt = null;

        try
        {
            if(!$this->conexion)
            {
                throw new Exception("No hay conexión con la base de datos.");
            };

            /* Detectamos el tipo de sentencia */
            $sqlLimpio = ltrim($sql);
            $tipoSql = strtoupper(strtok($sqlLimpio, " "));

            $this->conexion->begin_transaction();

            $stmt = $this->conexion->prepare($sql);
            if(!$stmt)
            {
                throw new Exception($this->traducirError($this->conexion->errno, $this->conexion->error));
            };

            if($tipos !== "" && !empty($params))
            {
                if(!$stmt->bind_param($tipos, ...$params))
                {
                    throw new Exception($this->traducirError($stmt->errno, $stmt->error));
                };
            };

            if(!$stmt->execute())
            {
                throw new Exception($this->traducirError($stmt->errno, $stmt->error));
            };

            $afectadas = $stmt->affected_rows;
            $insert_id = $this->conexion->insert_id;

            $this->conexion->commit();
            $stmt->close();

            switch($tipoSql)
            {
                case 'INSERT':
                    return [
                        'ok' => true,
                        'tipo' => 'INSERT',
                        'insert_id' => $insert_id,
                        'afectadas' => $afectadas
                    ];

                case 'UPDATE':
                    return [
                        'ok' => true,
                        'tipo' => 'UPDATE',
                        'afectadas' => $afectadas
                    ];

                case 'DELETE':
                    return [
                        'ok' => true,
                        'tipo' => 'DELETE',
                        'afectadas' => $afectadas
                    ];

                case 'REPLACE':
                    return [
                        'ok' => true,
                        'tipo' => 'REPLACE',
                        'insert_id' => $insert_id,
                        'afectadas' => $afectadas
                    ];

                default:
                    return [
                        'ok' => true,
                        'tipo' => $tipoSql,
                        'afectadas' => $afectadas,
                        'insert_id' => $insert_id
                    ];
            };
        }
        catch (Exception $e)
        {
            if($this->conexion)
            {
                $this->conexion->rollback();
            };

            if($stmt)
            {
                $stmt->close();
            };

            throw new Exception(
                "Error en la función ejecutar(): " .
                $this->traducirError($this->conexion ? $this->conexion->errno : 0, $e->getMessage())
            );
        };
    }

    
    public function consultar($sql, $tipos = "", ...$params)
    {
        try
        {
            if(!$this->conexion)
            {
                throw new Exception("No hay conexión con la base de datos.");
            };
            $stmt = $this->conexion->prepare($sql);
            if(!$stmt)
            {
                throw new Exception($this->traducirError($this->conexion->errno, $this->conexion->error));
            };
            if($tipos !== "" && !empty($params))
            {
                /* echo "<pre>";
                echo "===== DEBUG consultar() =====" . PHP_EOL;

                echo "Cantidad de signos ? en SQL: " . substr_count($sql, "?") . PHP_EOL;
                echo "Cantidad de letras en tipos: " . strlen($tipos) . PHP_EOL;
                echo "Cantidad de params recibidos: " . count($params) . PHP_EOL;

                echo PHP_EOL . "Tipos:" . PHP_EOL;
                var_dump($tipos);

                echo PHP_EOL . "Params:" . PHP_EOL;
                var_dump($params);

                echo PHP_EOL . "SQL:" . PHP_EOL;
                echo $sql;

                echo "</pre>"; */

                //exit;

                /* if(!$stmt->bind_param($tipos, ...$params))
                {
                    throw new Exception($this->traducirError($stmt->errno, $stmt->error));
                }; */
                $referencias = [];

                foreach($params as $key => $value)
                {
                    $referencias[$key] = &$params[$key];
                };

                if(!$stmt->bind_param($tipos, ...$referencias))
                {
                    throw new Exception($this->traducirError($stmt->errno, $stmt->error));
                };
            };
            if(!$stmt->execute())
            {
                throw new Exception($this->traducirError($stmt->errno, $stmt->error));
            };
            $res = $stmt->get_result();
            $datos = [];
            if($res)
            {
                $datos = $res->fetch_all(MYSQLI_ASSOC);
            };
            $stmt->close();
            return $datos;
        }
        catch(Exception $e)
        {
            $codigo = $this->conexion->errno ?: 0;
            $detalle = $this->conexion->error ?: $e->getMessage();
            throw new Exception("Error en consultar(): " . $this->traducirError($codigo, $detalle));
        };
    }

    public function basura(&...$vars)
    {
        try
        {
            foreach($vars as &$var)
            {
                // Si es un resultado MySQLi válido, lo libera
                if(is_object($var) && $var instanceof mysqli_result)
                {
                    @mysqli_free_result($var);
                };
                // Elimina la variable del espacio actual
                unset($var);
            };
            // Fuerza la recolección de basura
            gc_collect_cycles();
            return true;
        }
        catch(Exception $e)
        {
            error_log("Error en basura(): " . $e->getMessage());
            return false;
        };
    }

    private function traducirError($codigo, $detalle = "")
    {
        switch ($codigo)
        {
            /* 🔐 Conexión y permisos */
            case 1044: return "El usuario no tiene permisos para acceder a la base de datos."; break;
            case 1045: return "Acceso denegado: usuario o contraseña incorrectos."; break;
            case 1049: return "La base de datos seleccionada no existe."; break;
            case 2002: return "No se pudo conectar al servidor de la base de datos (posible error de red o nombre de host incorrecto)."; break;
            case 2003: return "No se pudo establecer conexión con el servidor MySQL (verifica el puerto o firewall)."; break;
            case 2005: return "El nombre del host es incorrecto o no se puede resolver."; break;
            case 2013: return "Conexión perdida durante la consulta o tiempo de espera agotado."; break;

            /* 🧱 Estructura o sintaxis SQL */
            case 1054: return "Campo desconocido en la consulta. Verifica los nombres de las columnas."; break;
            case 1064: return "Error de sintaxis SQL. Revisa los parámetros o comillas en la consulta."; break;
            case 1146: return "La tabla consultada no existe en la base de datos."; break;
            case 1103: return "Nombre de tabla o campo no válido."; break;
            case 1110: return "Se ha utilizado un alias duplicado en la consulta."; break;
            case 1149: return "Comando SQL no reconocido o no permitido."; break;
            case 1060: return "Columna duplicada en SELECT o INSERT."; break;
            case 1114: return "La tabla está llena o no hay espacio disponible para insertar más datos."; break;

            /* 🔁 Integridad de datos */
            case 1062: return "Violación de clave única: el registro ya existe."; break;
            case 1265: return "Valor no permitido en campo ENUM o valor truncado."; break;
            case 1366: return "Error de tipo de dato: uno de los valores no corresponde al tipo definido."; break;
            case 1048: return "Se intentó insertar NULL en un campo que no lo permite (NOT NULL)."; break;
            case 1136: return "El número de columnas no coincide con el número de valores en el INSERT."; break;
            case 1171: return "Definición incorrecta de clave primaria (columna sin índice o valor no permitido)."; break;
            case 1172: return "La subconsulta devuelve más de una fila donde solo se esperaba una."; break;
            case 1364: return "Campo sin valor por defecto y no fue especificado en la consulta."; break;
            case 1690: return "Valor fuera de rango en una operación aritmética."; break;

            /* 🔗 Claves foráneas */
            case 1216: return "No se puede agregar o actualizar: clave foránea inexistente en la tabla relacionada."; break;
            case 1217: return "No se puede eliminar o actualizar el registro porque está en uso por otra tabla."; break;
            case 1451: return "No se puede eliminar el registro: restricción de integridad referencial."; break;
            case 1452: return "No se puede agregar o actualizar por clave foránea inexistente."; break;

            /* ⚡ Transacciones y servidor */
            case 1205: return "Tiempo de espera excedido por bloqueo de registro (lock wait timeout)."; break;
            case 1213: return "Deadlock detectado durante la transacción. Intenta nuevamente."; break;
            case 2006: return "El servidor MySQL ha cerrado la conexión (MySQL server has gone away)."; break;
            case 2014: return "Comando fuera de secuencia. Verifica el flujo de consultas o el uso de statements."; break;

            /* 🧮 Procedimientos y funciones */
            case 1305: return "La función o procedimiento almacenado no existe."; break;
            case 1414: return "Número incorrecto de argumentos en el procedimiento almacenado."; break;
            case 1337: return "Definición inválida de función o procedimiento."; break;
            case 1436: return "Valor devuelto por el procedimiento no válido o fuera de rango."; break;

            /* 🧩 Privilegios y seguridad */
            case 1142: return "Acceso denegado: no tienes permisos para consultar esta tabla."; break;
            case 1143: return "Acceso denegado: no tienes permisos para acceder a una o más columnas."; break;
            case 1227: return "Operación no permitida: privilegios insuficientes."; break;
            case 1229: return "Se requieren privilegios SUPER o FILE para ejecutar esta operación."; break;
            case 1449: return "El usuario o host definido en el trigger o evento no existe."; break;

            /* 🗄️ Archivos y almacenamiento */
            case 1016: return "La tabla está dañada o corrupta. Considera ejecutar REPAIR TABLE."; break;
            case 1021: return "No hay suficiente espacio en disco para completar la operación."; break;
            case 1030: return "Error general del motor de almacenamiento. Revisa permisos o configuración."; break;
            case 1169: return "Violación de clave única o duplicado de índice detectado."; break;

            /* 🧰 Otros */
            case 1193: return "Variable del sistema desconocida o no soportada por esta versión de MySQL."; break;
            case 1525: return "Error en definición de particiones o columnas. Verifica la estructura de la tabla."; break;

            /* ⚙️ Desconocido */
            default:
                return "Error MySQL ($codigo): $detalle"; break;
        };
    }
    
    public function escribir(&$var)
    {
        if(isset($var))
        {
            if(is_string($var) || is_numeric($var))
            {
                $var = trim($var);
                $var = html_entity_decode($var, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $var = htmlspecialchars($var);
                $var = str_replace(
                    "#",
                    '',
                    $var
                );
            };
        }
        else
        {
            $var = null;
        };
        return $var; 
    }

    public function busqueda($texto, $buscar)
    {
        $texto = $this->escribir($texto);
        $buscar = $this->escribir($buscar);
        $reemplazar = "<span>" . $buscar . "</span>";

        if(strpos($texto, $buscar) !== false)
        {
            $texto = str_replace($buscar, $reemplazar, $texto);
        };
        return $texto;
    }

    public function v_uniqid()
    {
        $valor_uniquid = uniqid();
        $valor_uniquid = (string)$this->escribir($valor_uniquid);
        return $valor_uniquid;
    }

    public function v_data($data)
    {
        $json = base64_decode($data);
        $datos = json_decode($json, true);
        return $datos;
    }
    
    public function c_data(array $parametros)
    {
        $json = json_encode($parametros);
        $base = base64_encode($json);
        $data = urlencode($base);

        return $data;
    }

};
?>