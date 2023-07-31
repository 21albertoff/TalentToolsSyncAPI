<?php
require_once('controllers/functions.php');

echo enviarDatos("https://talentoo-back-b7wdvo6clq-ew.a.run.app/api/positions/push",obtenerDatosPosiciones(), $token);

?>


