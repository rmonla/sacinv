<?php
  /* Inicilizo  las variables de conexion */
    $mysql_host       = "localhost";
    $mysql_usuario    = "root";
    $mysql_contrasena = "";
    $basedatos        = "sacinv_v_0_2";
  /* Conecto al motor de base de datos */
    if (!($conexion_mysql = mysql_connect($mysql_host, $mysql_usuario,$mysql_contrasena))){
       echo "no se pudo conectar";
       exit;
    }
  /* Guardo la conexion en una variable global para las funciones*/
    $_SESSION['conexion'] = $conexion_mysql;
  /* Selecciono la base de datos */
    if (!mysql_select_db($basedatos, $conexion_mysql)){
       echo "no se selecciono base da datos";
       exit;
    }
?>