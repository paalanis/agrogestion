<?php
session_start();
if (isset($_SESSION['usuario'])) {
header("Location: index2.php");
}
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
    <!-- Animate.css -->
    <link href="css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="css/custom.css" rel="stylesheet">
  </head> 

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="conexion/login.php" method="post">
              <h1>Ingreso</h1>
              <div>
                <input type="text" name="usuario" id="user" class="form-control" placeholder="Usuario" required="" data-toggle="tooltip" data-placement="top" title="Error al verificar sus datos">
              </div>
              <div>
                <input type="password" id="pword" name="pass" class="form-control" placeholder="Password" required="" data-toggle="tooltip" data-placement="top" title="Error al verificar sus datos">
              </div>
              <div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Aceptar</button>
                <a align="left" class="reset_pass" href="#">Olvido su password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-envira"></i> Agro Gestión</h1>
                  <p>©2016 Todos los Derechos Reservados.</p>
                </div>
              </div>
            </form>
          </section>
        </div>

       
      </div>
    </div>
  </body>
</html>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
  
 $('[data-toggle="tooltip"]').tooltip();   

           })

</script>