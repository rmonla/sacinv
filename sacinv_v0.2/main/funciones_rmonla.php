<?php 
  function sql_insertinto($tabla, $datos){
    /* Crago los campos */
      $campos = array();
      $campos['inventario'][0] = 'fecha';
      $campos['inventario'][1] = 'destino';
      $campos['inventario'][2] = 'cant';
      $campos['inventario'][3] = 'c_unitario';
    /*Construyo la consulta SQL*/
      /*Armo la string de campos*/
        $sql_campos = '';
        for ($i=0; $i < (count($campos[$tabla]))-1; $i++)  
          $sql_campos.= $campos[$tabla][$i].', '; 
        $sql_campos.= $campos[$tabla][$i];
      /*Armo la string de datos*/
        $sql_datos = '';
        for ($i=0; $i < (count($datos))-1; $i++)  
          $sql_datos.= '\''.$datos[$i].'\', '; 
        $sql_datos.= '\''.$datos[$i].'\'';
      /*Armo la consulta*/
        $sql = 'INSERT INTO '.$tabla.' 
                  ( '.$sql_campos.' ) 
                  VALUES 
                  ( '.$sql_datos.')';
      //var_dump($sql);
      //exit;
    /*Ejecuto la consulta SQL*/
       if (!mysql_query($sql, $_SESSION['conexion']))
        echo 'ERROR: NO SE PUDO AGREGAR EL REGISTRO EN LA TABLA '.$tabla;
  }
  function sql_select($tabla, $where = '*'){
    /* Armo la condición where */
      if($where == '*') $where = '';
      else $where = ' WHERE '.$where;
    /*Armo la consulta*/
      $sql = 'SELECT * FROM '.$tabla.$where;
    /*Ejecuto la consulta SQL*/
       if (!$resultado = mysql_query($sql, $_SESSION['conexion']))
        echo 'ERROR: NO SE PUDO SELECCIONAR '.$where.' EN LA TABLA '.$tabla;
    return $resultado;
  }
  function sql_sum_col($columna, $tabla) {
    /*Armo la consulta*/
      $sql = 'SELECT SUM('.$columna.') FROM '.$tabla;
    /*Ejecuto la consulta SQL*/
       if (!$resultado = mysql_query($sql, $_SESSION['conexion']))
        echo 'ERROR: NO SE PUDO SUMAR LA COLUMNA '.$columna.' DE LA TABLA '.$tabla;
      $fila = mysql_fetch_array($resultado);
    return $fila[0];
  }
  function formatonumero ($numero, $estilo = '1') {
    $negativo = 0;
    if($numero < 0) $negativo = 1;
    switch ($estilo) {
      case '1': //Números con seprador de miles y comas.
        $numero = number_format($numero, 0, ",", ".");
        break;
      case '2': //Números IDEM 1 + 2 decimales.
        $numero = number_format($numero, 2, ",", ".");
        break;
      case '3': //Números IDEM 2 + símbolo $.
        $numero = '$ '.(number_format($numero, 2, ",", "."));
        break;
      
      default:
        # code...
        break;
    }
    if($negativo) $numero = '('.$numero.')';
    return $numero;
  }
  function cargarcombo(
                        $tipo, 
                        $pred_texto = 'Su Opción', 
                        $pred_val = '0', 
                        $val_1 = '1',
                        $val_2 = '1',
                        $val_3 = '0'
                      ) {
            /*
            (requerido)$tipo       = [1] => id's de tabla, [2] => val's desde hasta.
            (opcional )$pred_texto = String de la opción predeterminada del combo.
            (opcional )$pred_val   = Valor de la opción predeterminada del combo.
            (opcional )$val_1      = tipo[1] => Nombre de la tabla.
            (opcional )$val_2      = tipo[1] => Columna valores a mostrar en el combo.
            (opcional )$val_3      = tipo[1] => Columna id's del combo.
            (opcional )$val_1      = tipo[2] => Valor inicial a cargar.
            (opcional )$val_2      = tipo[2] => Valor final a cargar.
            (opcional )$val_3      = tipo[2] => [0] Asc, [1]Desc; Sentido de carga.
            */
    /*Option predeterminado*/
      $options = '<option value="'.$pred_val.'">- '.$pred_texto.' -</option>\n';
    /*Opciones de tipo de carga*/
      switch ($tipo) {
        case '2': //Valores desde y hasta.
          /*Cargo variables*/
            $val_ini = $val_1;
            $val_fin = $val_2;
            $sentido = $val_3;
          /*Armo la strig de los options*/
            switch ($sentido) {
              case '1': //Carga descendente.
                for ($i=$val_ini; $i >= $val_fin; $i--)  
                  $options.= '<option value="'.$i.'">'.$i.'</option>\n';
                break;
              default: //Carga ascendente.
                for ($i=$val_ini; $i <= $val_fin; $i++)  
                  $options.= '<option value="'.$i.'">'.$i.'</option>\n';
                break;
            }
          break;
        default: //id's desde una tabla. 
          /*Cargo variables*/
            $tabla = $val_1;
            $col_val = $val_2;
            $col_id = $val_3;
          /*Obtengo los datos de la BD*/
            $regs_tabla = sql_select($tabla);
          /*Armo la strig de los options*/
            if(!isset($col_id)) $col_id = '0';
              $var = $col_id;
              var_dump($var);
              exit;
            while ($fila = mysql_fetch_array($regs_tabla)) {
              $options.= '<option value="'.$fila[$col_id].'">'.$fila[$col_val].'</option>\n';
            }
          break;
      }
    /*Imprimo los options*/
      echo $options;
  }
?>