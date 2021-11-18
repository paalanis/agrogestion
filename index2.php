<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: index.php");
}
include 'conexion/conexion.php';
$deposito=$_SESSION['deposito'];
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
date_default_timezone_set("America/Argentina/Mendoza");
setlocale(LC_ALL,"es_ES");
$hoy = date("l");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AgroGestion</title>
    
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="css/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="css/green.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="css/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="css/daterangepicker.css" rel="stylesheet">
    <!-- tablas -->
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="css/scroller.bootstrap.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="css/custom.css" rel="stylesheet">   
    <link href="css/cargando.css" rel="stylesheet">
    <link href="css/formato.css" rel="stylesheet">

  </head>

  <?php 
   include 'abm/reporte/db.php';
  ?>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index2.php" class="site_title"><i class="fa fa-envira"></i> <span>AgroGestión</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="images/user.png" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Bienvenido,</span>
                <h2><?php echo utf8_encode($_SESSION['nom_ape'])?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a href="index2.php"><i class="fa fa-home"></i> Inicio</a></li>
                  <li><a><i class="fa fa-edit"></i> Parte de Trabajo <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a class="menu" href="#" title="nuevo_partediario">Nuevo</a></li>
                      <li><a class="menu" href="#" title="reporte_menu-partescargados">Partes Cargados</a></li>
                      <li><a class="menu" href="#" title="reporte_menu-partespropios">Reporte Propios</a></li>
                      <li><a class="menu" href="#" title="reporte_menu-partesterceros">Reporte Tercerizado</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-industry"></i> Cosecha <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a class="menu" href="#">Nuevo</a></li>
                      <li><a class="menu" href="#">Remitos Pendientes</a></li>
                      <li><a class="menu" href="#">Remitos Terminados</a></li>
                      <li><a class="menu" href="#">Reporte Integral</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-tint"></i>Parte de Riego <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a class="menu" href="#" title="nuevo_asignar-valvulas">Asignar válvulas</a></li>
                      <li><a class="menu" href="#" title="nuevo_parteriego">Nuevo</a></li>
                      <li><a class="menu" href="#">Reporte</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-flask"></i> Insumos <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a class="menu" href="#">Nuevo Remito</a></li>
                      <li><a class="menu" href="#">Traspaso - Salida</a></li>
                      <li><a class="menu" href="#">Traspaso - Entrada</a></li>
                      <li><a class="menu" href="#">Existencias</a></li>
                      <li><a class="menu" href="#">Consumos</a></li>
                    </ul>
                  </li>
                  <li><a href="javascript:void(0)"><i class="fa fa-cloud"></i> Meteorología <span class="label label-success pull-right">Próximamente</span></a></li>
                </ul>
              </div>
              <div class="menu_section">
                <h3>Configuración</h3>
                 <ul class="nav side-menu">
                   <li><a><i class="fa fa-cog"></i> Altas <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a class="menu" href="#" title="nuevo_bodega">Bodega</a></li>
                      <li><a class="menu" href="#" title="nuevo_caudalimetro">Caudalímetro</a></li>
                      <li><a class="menu" href="#" title="nuevo_cosechadora">Cosechadora</a></li>
                      <li><a class="menu" href="#" title="nuevo_cuartel">Cuartel</a></li>
                      <li><a class="menu" href="#" title="nuevo_finca">Finca</a></li>
                      <li><a class="menu" href="#" title="nuevo_implemento">Implemento</a></li>
                      <li><a class="menu" href="#" title="nuevo_insumos">Insumos</a></li>
                      <li><a class="menu" href="#" title="nuevo_labor">Labor</a></li>
                      <li><a class="menu" href="#" title="nuevo_maquinaria">Maquinaria</a></li>
                      <li><a class="menu" href="#" title="nuevo_operaciones">Operación</a></li>
                      <li><a class="menu" href="#" title="nuevo_personal">Personal</a></li>
                      <li><a class="menu" href="#" title="nuevo_transporte">Transporte</a></li>
                      <li><a class="menu" href="#" title="nuevo_valvula">Válvula</a></li>
                      <li><a class="menu" href="#" title="nuevo_variedad">Variedad</a></li>
                      <li><a class="menu" href="#" title="nuevo_varios">Varios</a></li>
                    </ul>
                  </li>
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Sistema">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Bloquear">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Salir" href="conexion/logout.php">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
             <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="images/user.png" alt=""><?php echo utf8_encode($_SESSION['usuario'])?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="javascript:;"><i class="fa fa-cog pull-right"></i> Sistema</a></li>
                    <li><a href="javascript:;"><i class="fa fa-question-circle pull-right"></i> Ayuda</a></li>
                    <li><a href="conexion/logout.php"><i class="fa fa-sign-out pull-right"></i> Salir</a></li>
                  </ul>
                </li>
                <li role="presentation" class="dropdown">
                  <a href="index_finca.php"><span class="badge bg-green"><?php echo utf8_encode($_SESSION['finca_usuario']);?></span></a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div id="panel_inicio">
                <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row tile_count">
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Jornales Utilizados</span>
              <div class="count"><?php echo $jor_mes;?></div>
              <span class="count_bottom"><i class="green"> En el mes</i></span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Jornales Acumulados</span>
              <div class="count red"><?php echo $jor_total;?></div>
              <span class="count_bottom"><i class="red">90%</i> del Presupuesto</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-clock-o"></i> Horas Maquinaria</span>
              <div class="count"><?php echo $tractor_mes;?></div>
              <span class="count_bottom"><i class="green">En el mes</i></span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-clock-o"></i> Horas Maquinaria</span>
              <div class="count"><?php echo $tractor_total;?></div>
              <span class="count_bottom"><i class="green">Acumuladas</i></span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Mm. Regados del Mes</span>
              <div class="count">200</div>
              <span class="count_bottom"><i class="green">12%</i> del Proyectado</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Milímetros Regados</span>
              <div class="count">2,315</div>
              <span class="count_bottom"><i class="green"> Acumulados</i></span>
            </div>
            
          </div>
          <!-- /top tiles -->

                    <div class="row">


             <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Jornales por Ha <small>consumidos</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="#"></a>
                        </li>
                        <li><a href="#"></a>
                        </li>
                      </ul>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                  </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <canvas id="jor_has"></canvas>
                  </div>
                </div>
              </div>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Detalle Jornales <small>Top 5 consumidos</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#"></a>
                          </li>
                          <li><a href="#"></a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas id="top_cinco" width="484" height="242" style="width: 484px; height: 242px;"></canvas>
                  </div>
                </div>
                </div>
            </div>

             <div class="row">
              
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Riego <small>milímetros anuales</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="#"></a>
                        </li>
                        <li><a href="#"></a>
                        </li>
                      </ul>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                  </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <canvas id="riego"></canvas>
                  </div>
                </div>
              </div>
            </div>

          <br />

          <div class="row">
           


            <div class="col-md-8 col-sm-8 col-xs-12">

              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                </div>
              </div>

              <div class="row">

                <!-- Start to do list -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>Pendientes <small></small></h2>
                      <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                          <ul class="dropdown-menu" role="menu">
                            <li><a href="#"></a>
                            </li>
                            <li><a href="#"></a>
                            </li>
                          </ul>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                      </ul>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                      <div class="">
                        <ul class="to_do">
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 1 </p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 2</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 3</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 4</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 5</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 6</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 7</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Ejemplo 8</p>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End to do list -->
                
                <!-- start of weather widget -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>El Clima <small><?php echo utf8_encode($_SESSION['finca_usuario']);?></small></h2>
                      <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                          <ul class="dropdown-menu" role="menu">
                            <li><a href="#"></a>
                            </li>
                            <li><a href="#"></a>
                            </li>
                          </ul>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                      </ul>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="temperature"><b><?php echo strftime("%A");?></b>, <?php echo strftime("%H:%M");?>
                            
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4">
                          <div class="weather-icon">
                            <canvas height="84" width="84" id="partly-cloudy-day"></canvas>
                          </div>
                        </div>
                        <div class="col-sm-8">
                          <div class="weather-text">
                            <h2><br><i>Parcialmente nublado</i></h2>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="weather-text pull-right">
                          <h3 class="degrees">23</h3>
                        </div>
                      </div>

                      <div class="clearfix"></div>

                      <div class="row weather-days">
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Lun</h2>
                            <h3 class="degrees">25</h3>
                            <canvas id="clear-day" width="32" height="32"></canvas>
                            <h5>15 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Mar</h2>
                            <h3 class="degrees">25</h3>
                            <canvas height="32" width="32" id="rain"></canvas>
                            <h5>12 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Mie</h2>
                            <h3 class="degrees">27</h3>
                            <canvas height="32" width="32" id="snow"></canvas>
                            <h5>14 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Jue</h2>
                            <h3 class="degrees">28</h3>
                            <canvas height="32" width="32" id="sleet"></canvas>
                            <h5>15 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Vie</h2>
                            <h3 class="degrees">28</h3>
                            <canvas height="32" width="32" id="wind"></canvas>
                            <h5>11 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Sab</h2>
                            <h3 class="degrees">26</h3>
                            <canvas height="32" width="32" id="cloudy"></canvas>
                            <h5>10 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- end of weather widget -->
              </div>
            </div>

             <div class="col-md-4 col-sm-4 col-xs-12">
              
            </div>

          </div>
        </div>  
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer class="footer_fixed footer">
          <div class="pull-right">
            Desarrolado por Pablo Alanis y Mariano Savina
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
        <!-- jQuery -->
    <!-- Bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="js/fastclick.js"></script>
    <!-- NProgress -->
    <script src="js/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="js/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="js/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="js/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="js/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="js/skycons.js"></script>
    <!-- DateJS -->
    <script src="js/date.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="js/moment.min.js"></script>
    <script src="js/daterangepicker.js"></script>
    <!-- tablas -->
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>
    <script src="js/responsive.bootstrap.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="js/custom.js"></script>
    <script src="js/fx_nuevo.js"></script>
    
    
    

  </body>
</html>

