
<html>
<head>
  <meta name="robots" content="noindex">
</head>
<?php header('Content-type:application/json;charset=utf-8'); ?>

<?php include "search.php" ?>

<?php
  echo json_encode( search()['json'] );
  exit;
?>
</html>