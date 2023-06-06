<?php
session_start();
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
?>
<iframe scrolling="auto" src="../radicacion/NEW.php?uid=<?=$uid?>&tipoMedio=eMail&ent=2" frameborder="0" height="100%" width="100%"></iframe>
