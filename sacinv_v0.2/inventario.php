<?php 
  /* Conecto a la BD */
    include('bd/conectar.php');
  /* Incluyo las funciones */
    include('main/funciones_rmonla.php');

    /*
    //$var = sql_sum_col('cant', 'inventario');
    $var = mysql_fetch_array(sql_select('inventario'));
    var_dump($var);
    //print_r($var);
    exit;
   /*
   var day=new Date().getDay();
switch (day)
{
case 0:
  x="Today it's Sunday";
  break;
case 1:
  x="Today it's Monday";
  break;
case 2:
  x="Today it's Tuesday";
  break;
case 3:
  x="Today it's Wednesday";
  break;
case 4:
  x="Today it's Thursday";
  break;
case 5:
  x="Today it's Friday";
  break;
case 6:
  x="Today it's Saturday";
  break;
}
    */ 

  /* Si vienen datos los cargo en la BD */
    if(isset(
        $_POST['bot_pps'], 
        $_POST['fecha'], 
        $_POST['destino'], 
        $_POST['cant'],
        $_POST['c_unitario']
      )){
      /*Cargo los datos en la tabla inventario*/
        $fecha = date('Y-m-d', strtotime($_POST['fecha']));
        $datos = array(
                  $fecha, 
                  $_POST['destino'], 
                  $_POST['cant'], 
                  $_POST['c_unitario']
                  );
        sql_insertinto('inventario', $datos);
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SACInv</title>
<link rel="stylesheet" type="text/css" href="css/main.css" />
<link rel="stylesheet" type="text/css" href="css/scheme.css" />
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<style type="text/css">
<!--
.Estilo1 {color: #000000}
-->
</style>
<script type="text/javascript">
  function controles(destino){
    switch (destino){
      case 0:
        x="Today it's Sunday";
        break;
    }
  }
  //alert("Un mensaje de prueba");
</script>
</head>
<body>
<!-- Start Wrapper -->
<div class="wrapper">
  <!-- Start Header -->
  <div class="header"> <img src="images/logo.jpg" alt="Logo" width="310" height="100" />Sistema de Administración Contable de Inventarios </div>
  <!-- End Header -->
  <!-- Start Navigation Bar -->
  <div class="nav-bar">
    <ul class="nav-links">
      <li><a href="inventario.php">Inventarios por PEPS</a></li>
      <li><a href="inv_ueps.php">Inventarios por UEPS</a></li>
      <li><a href="inv_ppp.php">Inventarios por PPP</a></li>
      <li><a href="http://ricardo.monla.com.ar/proy/13/oce_sacinv/">Plan de Cuentas</a></li>
      <li><a href="http://ricardo.monla.com.ar/proy/13/oce_sacinv/">Asientos</a></li>
      
    </ul>
  </div>
  <!-- End Navigation Bar -->
  <!-- Start Outer Content -->
  <div id="outercontent">
    <div id="centercolumn">
      <form id="form1" method="post" action="inventario.php">
        <table border="0" class="nomb table-style01 th">
              <tr>
                <th scope="col">Fecha</th>
                <th scope="col">Destino</th>
                <th scope="col">Q</th>
                <th scope="col">$</th>
              </tr>
              <tr>
                <td><div align="center">
                  <input name="fecha" type="date" class="date" tabindex="1" value="<?php if(isset($fecha)) echo $fecha; ?>" />
                </div></td>
                <td><div align="center">
                  <select name="destino" id="destino" tabindex="2">
                    <option value="1">Entrada</option>
                    <option value="2">Salida</option>
                    <option value="3">Devoluci&oacute;n</option>
                  </select>
                </div></td>
                <td><div align="center">
                  <input name="cant" type="text" id="cant" tabindex="3" size="10" />
                  <select name="cant_salida" id="cant_salida">
                    <?php cargarcombo('2','', '0', '1', sql_sum_col('cant', 'inventario')); ?>
                  </select>
                </div></td>
                <td><div align="center">
                  <input name="c_unitario" type="text" id="costo" style="outline:" tabindex="4" size="10" />
                </div></td>
              </tr>
              <tr>
                <td colspan="4"><div align="right">
                  <p>&nbsp;                  </p>
                  <p align="center">
                    <input type="submit" name="bot_pps" value="Enviar" tabindex="5" />
                  </p>
                  <p>&nbsp;                    </p>
                </div></td>
              </tr>
        </table>
      </form>
      <h1>&nbsp;</h1>
      <table width="740" border="0" class="nomb table-style01">
        <tr>
          <th width="10%" rowspan="2" scope="col">Fecha</th>
          <th colspan="3" scope="col">Entrada</th>
          <th colspan="3" scope="col">Salida</th>
          <th colspan="3" scope="col">Saldo</th>
        </tr>
        <tr>
          <th width="10%"><div align="center">Q</div></th>
          <th width="10%"><div align="center">$</div></th>
          <th width="10%"><div align="center">Total</div></th>
          <th width="10%"><div align="center">Q</div></th>
          <th width="10%"><div align="center">$</div></th>
          <th width="10%"><div align="center">Total</div></th>
          <th width="10%"><div align="center">Q</div></th>
          <th width="10%"><div align="center">$</div></th>
          <th width="10%"><div align="center">Total</div></th>
        </tr>
        <?php 
          /*Cargo los registros de la tabla inventario */
            $res_inventario = sql_select('inventario');
          /*Armo las filas*/
            $datos = array();
            while($fila = mysql_fetch_array($res_inventario)){
              /* Formateo la fecha */
                $fecha = date('d-m-y', strtotime($fila['fecha']));
              /* Inicializo el array datos */
                for ($i=0; $i<4 ; $i++) $datos[$i] = 0;
              /* Defino la columna donde cargar los datos */
                $i = 0;
                if ($fila['destino'] == 2) $i = 2;
              /* Cargo los datos al array */
                $datos[$i++] = $fila['cant'];
                $datos[$i++] = $fila['c_unitario'];
              /* Calculo los s/totales */
                $st_entrada = $datos[0] * $datos[1];
                $st_salida = $datos[2] * $datos[3];
              /* Calculo los saldos */
                $saldo_cant = $datos[0] + $datos[2];
                $saldo_costo = $datos[1] + $datos[3];
                $st_saldo = $saldo_cant * $saldo_costo;
        ?>
            <tr class="bg">
              <td>
                <div align="center" class="Estilo1">
                  <?php echo $fecha; ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php if ($datos[0] != '0') echo formatonumero($datos[0]); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php if ($datos[1] != '0') echo formatonumero($datos[1] , 3); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php if ($st_entrada != '0') echo formatonumero($st_entrada , 3); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php if ($datos[2] != '0') echo formatonumero($datos[2]); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php if ($datos[3] != '0') echo formatonumero($datos[3], 3); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php if ($st_salida != '0') echo formatonumero($st_salida, 3); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php echo formatonumero($saldo_cant); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php echo formatonumero($saldo_costo, 3); ?>
                </div>
              </td>
              <td>
                <div align="center" class="Estilo1">
                  <?php echo formatonumero($st_saldo, 3) ; ?>
                </div>
              </td>
            </tr>
        <?php } ?>
      </table>
      <p>&nbsp;</p>
    </div>
    <!-- Start Right Content --><!-- End Right Content -->
  </div>
  <!-- End Outer Content -->
  <!-- End Outer Content -->
  <!-- Start Footer -->
  <div id="footer"> &copy; Copyright with <strong>Miriam Canachi</strong>, <strong>Abel Salazar, Gastón Farncisco, Ricardo Monla, </strong>2013 </div>
  <!-- End Footer -->
</div>
<!-- End Wrapper -->
</body>
</html>
