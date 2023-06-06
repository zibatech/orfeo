<!DOCTYPE html>
<html>
<head>
  <title>Visor SNS</title>
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script
        src="https://code.jquery.com/jquery-3.6.0.slim.js"
        integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY="
        crossorigin="anonymous"></script>
<!-- Import WebViewer as a script tag -->
<script src='lib/webviewer.min.js'></script>

<body>
   <br>
  <div class="container"> 
    <div class="row">
      <a href="<?=$_REQUEST["src"]?>"  class="btn btn-success">Descargar documento original</a>
   </div>
  <br>
     <div class="row">
      <h5>Vista previa de documentos</h5>
      <h6>En caso de elementos no visibles <a class="btn btn-danger" href="<?=$_REQUEST["src"]?>" >descargar aquí</a>  y abrir localmente</h6>

      <div id='viewer' style='width: 100%; height: 600px; margin: 0 auto;'></div>
    </div>
  </div>

</body>
<script>
  WebViewer({
    path: 'lib', // path to the PDFTron 'lib' folder on your server
    //licenseKey: 'Insert commercial license key here after purchase',
    initialDoc: '<?=$_REQUEST["src"]?>',

    loadAsPDF: true,
    fullAPI: true,
    // initialDoc: '/path/to/my/file.pdf', // You can also use documents on your server
  }, document.getElementById('viewer'))
  .then(instance => {
    instance.setLanguage('es');
    instance.setToolbarGroup('toolbarGroup-View');
    instance.disableElements(['toolbarGroup-Shapes']);
    instance.disableElements(['toolbarGroup-Annotate']);
    instance.disableElements(['toolbarGroup-Edit']);
    instance.disableElements(['toolbarGroup-Insert']);
    instance.disableElements(['toggleNotesButton']);
    instance.disableElements(['toolbarGroup-Forms']);
    instance.disableElements(['toolbarGroup-FillAndSign']);
    instance.disableElements(['menuButton']);
    const docViewer = instance.docViewer;
    const annotManager = instance.annotManager;
    docViewer.on('documentLoaded', () => {
      // call methods relating to the loaded document

    });
  });



</script>
</html>
