<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $report = $_POST["report"];
  switch ($report) {
    case 'seller':
      header("Location: /cs6400/seller-history.php");
      exit;
    case 'inv':
      header("Location: /cs6400/inventory-age.php");
      exit;
    case 'time':
      header("Location: /cs6400/time-in-inventory.php");
      exit;
    case 'condition':
      header("Location: /cs6400/price-per-condition.php");
      exit;
    case 'repair':
      header("Location: /cs6400/repair-statistics.php");
      exit;
    case 'sales':
      header("Location: /cs6400/monthly-sales.php");
      exit;
    default:
      header("Location: /cs6400/index.php");
      exit;
  }
}
?>
