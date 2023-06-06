<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>    
</head>
<div class="alert alert-success" role="alert">
  <h3 class="alert-heading">Información!!!!</h3>
    <p><?php  echo 'Borrador: ' . $_GET["borrador"];   ?></p>
    <hr>
    <p><?php  echo 'Radicado: ' . $_GET["radicado"];   ?></p>
</div>


<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">ORFEO</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <p class="lead">
          Se realiza cambio de Borrador a número Radicado. 
          <?php 
            $ultDigito = substr($_GET["radicado"], -1);
            if($ultDigito == 4 || $ultDigito == 5 || $ultDigito == 6 || $ultDigito == 7) {
              $esNotificacion = true;
            } else {
              $esNotificacion = false;
            }
            if($esNotificacion == true) { ?>
                El radicado es enviado a quien tenga el rol Pre Gestor Notificación de la dependencia que continúe el trámite. Si no existe el rol se envia a quien lo creó.
          <?php } ?>
           </p>
        <p><?php  echo 'Borrador: ' . $_GET["borrador"];   ?></p>
        <hr>
        <p><?php  echo 'Radicado: ' . $_GET["radicado"];   ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
    $(document).ready( function () {
        $('#staticBackdrop').modal('show');
    });
</script>   
