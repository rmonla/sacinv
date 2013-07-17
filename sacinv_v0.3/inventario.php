<?php 
  /**
   * Incluyo las funciones generales
   */
    include('main/funciones_rmonla.php');
  /**
   * Conecto a la BD
   */
    include('bd/conectar.php');
  /**
   * Si vienen datos los cargo en la BD
   */
    if(validacion() == '0'){
        $fecha   = $_POST['fecha'];
        $destino = $_POST['destino'];
        switch ($destino) {
            case '1': //Entrada.
                $cant  = $_POST['cant_entrada'];
                $costo = $_POST['c_unitario'];
                agregarES($fecha, '1', $cant, $costo);
                break;
             case '2': //Salida.
                $cant = $_POST['cant_salida'];
                agregarSalida($fecha, $cant);
                break;
            
            default:
                break;
        }
    }
  function validacion(){
      $error = '0';
      if(!isset($_POST['bot_pps'])) $error = '1';
      elseif($_POST['bot_pps'] != 'Enviar') $error = '2';
      if(!isset($_POST['fecha'])) $error = '3';
      else{
          $mayor_f = sql_max('fecha', 'inventario');
          if($mayor_f == null) $mayor_f = '0';
          if($_POST['fecha'] < $mayor_f) $error = '5';
      }
      if(!isset($_POST['destino'])) $error = '6';
      else{
          switch ($_POST['destino']) {
              case '1':
                  if(!isset($_POST['cant_entrada'])) $error = '7';
                  elseif($_POST['cant_entrada'] < '1') $error = '8';
                  if(!isset($_POST['c_unitario'])) $error = '9';
                  elseif($_POST['c_unitario'] < '1') $error = '10';
                  break;
              case '2':
                  if(!isset($_POST['cant_salida'])) $error = '11';
                  elseif($_POST['cant_salida'] < '1') $error = '12';
                  break;
              default:
                  $error = '100';
                  break;
          }
      }
      if ($error != '0' and $error != '6') echo '®'.$error;
      return $error;
  }
  function agregarEntrada($fecha, $cant, $costo, $metodo = 'peps'){
      /**
       * Egrego la entrada
       */
          $datos_e = array($fecha, '1', $cant, $costo, $metodo);
          sql_insertinto('inventario', $datos_e);
      /**
       * Busco el último y penultimo id.
       */
          $acum            = false;
          $ultimoMov_id    = sql_max('id', 'inventario');
          $penultimoMov_id = sql_max('id', 'inventario', 'id < '.$ultimoMov_id);
      if($penultimoMov_id != null){ // <-- Por si no hay cargado nada.
          $saldos_UltId = $penultimoMov_id;
      /**
       * Agrego los saldos.
       */
      //Obtengo los saldos del último movimiento.
          $regs_SaldosUltMov = sql_select('saldos', 'mov = '.$saldos_UltId);
      //Cargo los saldos pero acumulo si son del mismos costo.
          while($fila_UltMov = mysql_fetch_array($regs_SaldosUltMov)){
              $lacant = $fila_UltMov['cant'];
              if($fila_UltMov['costo'] == $costo) {
                  $lacant = $fila_UltMov['cant'] + $cant;
                  $acum   = true;
              }
              $datos = array($ultimoMov_id, $lacant, $fila_UltMov['costo']);
              sql_insertinto('saldos', $datos);
          }
      }
          if(!$acum){
              $datos = array($ultimoMov_id, $cant, $costo);
              sql_insertinto('saldos', $datos);
          } 
  }  
  function agregarES($fecha, $ES, $cant, $costo, $metodo = 'peps'){
      /**
       * Egrego la entrada
       */
          $datos_e = array($fecha, $ES, $cant, $costo, $metodo);
          sql_insertinto('inventario', $datos_e);
      /**
       * Busco el último y penultimo id.
       */
          $acum            = false;
          $ultimoMov_id    = sql_max('id', 'inventario');
          $penultimoMov_id = sql_max('id', 'inventario', 'id < '.$ultimoMov_id);
      if($penultimoMov_id != null){ // <-- Por si no hay cargado nada.
          $saldos_UltId = $penultimoMov_id;
      /**
       * Agrego los saldos.
       */
      //Obtengo los saldos del último movimiento.
          $regs_SaldosUltMov = sql_select('saldos', 'mov = '.$saldos_UltId);
      //Cargo los saldos pero acumulo si son del mismos costo.
          if($ES == '2') $cant = $cant * (-1); // <-- Para que reste si es salida.
          while($fila_UltMov = mysql_fetch_array($regs_SaldosUltMov)){
              $lacant = $fila_UltMov['cant'];
              if($fila_UltMov['costo'] == $costo) {
                  $lacant = $fila_UltMov['cant'] + $cant;
                  $acum   = true;
              }
              if($lacant > '0'){
                $datos = array($ultimoMov_id, $lacant, $fila_UltMov['costo']);
                sql_insertinto('saldos', $datos);
              }
          }
      }
          if(!$acum){
              $datos = array($ultimoMov_id, $cant, $costo);
              sql_insertinto('saldos', $datos);
          } 
  }
  function agregarSalida($fecha, $cant){
    /**
     * Busco el último id de movimiento.
     */
      $ultimoMov_id    = sql_max('id', 'inventario');
      /**
       * Verifico si la tabla no está vacía.
       */
        if($ultimoMov_id != null){ // <-- Por si no hay cargado nada.
          /**
          * Verifico si la cantidad es menor o igual al total disponible.
          */
            $disponible = sql_sum_col('cant', 'saldos', 'mov = '.$ultimoMov_id);
            if($cant > $disponible){
              echo '<br>ERROR: NO SE PUEDE CARGAR UNA SALIDA MAYOR AL SALDO DISPONIBLE';
              return;
            }
          /**
           * Cargo los saldos del último movimiento.
           */
              $regs_SaldosUltMov = sql_select('saldos', 'mov = '.$ultimoMov_id);
          /**
           * Obtengo el costo para la salida.
           */
            $fila_SaldosUltMov = mysql_fetch_array($regs_SaldosUltMov);
            $costo_salida = $fila_SaldosUltMov['costo'];
            $cant_saldo  = $fila_SaldosUltMov['cant'];
            if($cant < $cant_saldo) $cant_salida = $cant;
            elseif($cant >= $cant_saldo) $cant_salida = $cant_saldo;
            /**
             * Agrego la salida usando la fx de entradas
             * para que me calcule los nuevos saldos. 
             */
              agregarES($fecha, '2', $cant_salida, $costo_salida);
            $resto = $cant - $cant_saldo;
            if($resto > '0') agregarSalida($fecha, $resto);
      }
  }
  function SaldoDisponible(){
    /**
     * Busco el último id de movimiento.
     */
      $ultimoMov_id    = sql_max('id', 'inventario');
      /**
       * Verifico si la tabla no está vacía.
       */
        if($ultimoMov_id != null){ // <-- Por si no hay cargado nada.
          /**
          * Obtengo el saldo disponible del último movimiento.
          */
            $disponible = sql_sum_col('cant', 'saldos', 'mov = '.$ultimoMov_id);
            if($disponible == null) $disponible = '0';
        }
      return $disponible;
  }

?>

<?php  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>SACInv</title>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <link rel="stylesheet" type="text/css" href="css/scheme.css">
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
.Estilo1 {color: #000000}
-->
</style>        
        <script type="text/javascript">
            function ocultarmostrar(){
                x=document.getElementById("destino").value;
                switch (x){
                  case "1":
                    document.getElementById("div_cantEntrada").style.visibility="visible"; 
                    document.getElementById("c_unitario").style.visibility="visible"; 
                    document.getElementById("div_cantSalida").style.visibility="hidden"; 
                    break;
                  case "2":
                    document.getElementById("div_cantEntrada").style.visibility="hidden"; 
                    document.getElementById("c_unitario").style.visibility="hidden"; 
                    document.getElementById("div_cantSalida").style.visibility="visible"; 
                    break;
                }
            }
        </script>
    </head>
    <body>
        <!-- Start Wrapper -->
        <div class="wrapper">
            <!-- Start Header -->
              <div class="header">
                  <img src="images/logo.jpg" alt="Logo" width="310" height="100">Sistema de Administración Contable de Inventarios
              </div>
            <!-- End Header -->
            <!-- Start Navigation Bar -->
              <div class="nav-bar">
                  <ul class="nav-links">
                      <li><a href="inventario.php">Inventarios por PEPS</a></li>
                      <li><a href="inv_ueps.php">Inventarios por UEPS</a></li>
                      <li><a href="inv_ppp.php">Inventarios por PPP</a></li>
                      <li><a href="plan.php">Plan de Cuentas</a></li>
                      <li><a href="asientos.php">Asientos</a></li>
                  </ul>
              </div>
            <!-- End Navigation Bar -->
            <!-- Start Outer Content -->
            <div id="outercontent">
                <div id="centercolumn">
                    <!-- Inicio del Form -->
                    <form id="form1" method="post" action="inventario.php">
                        <table border="0" class="nomb table-style01 th">
                            <tr>
                                <th>Fecha</th>
                                <th>Destino</th>
                                <th>Q</th>
                                <th>$</th>
                            </tr>
                            <tr>
                                <td>
                                    <div align="center">
                                        <?php 
                                            $ult_fecha = strtotime(sql_max('fecha', 'inventario'));
                                            $ult_fecha = date('Y-m-d', $ult_fecha);
                                        ?>
                                        <input name="fecha" id="fecha" type="date" class="date" tabindex="1" value="<?php echo $ult_fecha; ?>">
                                    </div>
                                </td>
                                <td>
                                    <div align="center">
                                        <select name="destino" id="destino" tabindex="2" onchange="ocultarmostrar();">
                                            <option value="0">- -</option>
                                            <option value="1">Entrada</option>
                                            <option value="2">Salida</option>
                                            <!--<option value="3">Saldo</option> El Saldo solo se carga por sistema-->
                                            <option value="4">Devolución</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div id="div_cantEntrada" align="center">
                                        <input name="cant_entrada" id="cant_entrada" type="text" tabindex="3" size="10"> 
                                    </div>
                                    <div id="div_cantSalida" align="center" style="visibility: hidden">
                                        <select name="cant_salida" id="cant_salida">
                                            <?php 
                                              msj(SaldoDisponible());
                                              cargarCombo('2','', '0', '1', SaldoDisponible()); 
                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div align="center">
                                        <input name="c_unitario" id="c_unitario" type="text" style="outline:" tabindex="4" size="10">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div align="right">
                                        <p>&nbsp;</p>
                                        <p align="center">
                                            <input name="bot_pps" id="bot_pps" type="submit" value="Enviar" tabindex="5">
                                        </p>
                                        <p>&nbsp;</p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form><!-- Fin del form -->
                    <h1>&nbsp;</h1>
                    <!-- Tabla Movimientos -->
                    <table width="740" border="0" class="nomb table-style01">
                        <tr>
                            <th width="10%" rowspan="2">Fecha</th>
                            <th colspan="3">Entrada</th>
                            <th colspan="3">Salida</th>
                            <th colspan="3">Saldo</th>
                        </tr>
                        <tr>
                            <th width="10%">Q</th>
                            <th width="10%">$</th>
                            <th width="10%">Total</th>
                            <th width="10%">Q</th>
                            <th width="10%">$</th>
                            <th width="10%">Total</th>
                            <th width="10%">Q</th>
                            <th width="10%">$</th>
                            <th width="10%">Total</th>
                        </tr>
                            <?php 
                                
                                /*Cargo los registros de la tabla inventario */
                                $res_inventario = sql_select('inventario');
                                /*Armo las filas*/
                                $datos = array();
                                while($fila = mysql_fetch_array($res_inventario)){ //IniWhile_m
                                    /* Formateo la fecha */
                                        $fecha = date('d-m-y', strtotime($fila['fecha']));
                                    /* Inicializo el array datos */
                                        for ($i=0; $i<9 ; $i++) $datos[$i] = 0;
                                    /**
                                    * Armo las filas segun el destino
                                    */
                                    $destino  = $fila['destino'];
                                    $cant_m   = $fila['cant'];
                                    $costo_m  = $fila['c_unitario'];
                                    $stotal_m = $cant_m * $costo_m;
                                    $costo_m  = formatonumero($costo_m, '3');
                                    $stotal_m = formatonumero($stotal_m, '3');
                                    switch ($destino) {
                                        case '1': //Entrada
                                            $col = '0';
                                            break;
                                        case '2': //Salida
                                            $col = '3';
                                            break;
                                        default:
                                            $col = '0';
                                            break;
                                    }
                                    $datos[$col++] = $cant_m;
                                    $datos[$col++] = $costo_m;
                                    $datos[$col++] = $stotal_m;
                            ?>
                                    <tr><!-- Movimiento -->
                                        <td><?php echo $fecha; ?></td>
                                    <?php for($i=0; $i<6; $i++){ ?>
                                        <td><?php if ($datos[$i] != '0') echo $datos[$i]; ?></td>
                                    <?php } //FinFor?>
                                        <td colspan="3"> <!-- Saldos -->
                                            <table width="100%" class="saldos">
                                            <?php 
                                                $res_saldos = sql_select('saldos', 'mov ='.$fila['id']);
                                                while($fila_s = mysql_fetch_array($res_saldos)){ //IniWhile_s
                                                    $cant_s   = $fila_s['cant'];
                                                    $costo_s  = $fila_s['costo'];
                                                    $stotal_s = $cant_s * $costo_s;
                                                    $costo_s  = formatonumero($costo_s, '3');
                                                    $stotal_s = formatonumero($stotal_s, '3');
                                            ?>
                                                    <tr>
                                                        <td><?php echo $cant_s; ?></td>
                                                        <td><?php echo $costo_s; ?></td>
                                                        <td><?php echo $stotal_s; ?></td>
                                                    </tr>
                                                <?php } //FinWhile_s?>
                                            </table>
                                        </td><!-- /Saldos -->
                                    </tr><!-- /Movimiento -->
                                <?php } //FinWhile_m?>
                    </table><!-- /Tabla Movimientos -->
                </div><!-- Start Right Content --><!-- End Right Content -->
            </div><!-- End Outer Content -->
            <!-- End Outer Content -->
            <!-- Start Footer -->
            <div id="footer">
                &copy; Copyright with <strong>Miriam Canachi</strong>, <strong>Abel Salazar, Gastón Farncisco, Ricardo Monla,</strong> 2013
            </div><!-- End Footer -->
        </div><!-- End Wrapper -->
    </body>
</html>