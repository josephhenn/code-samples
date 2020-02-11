<?php
session_start();
include("config.php");
$sql1 = "SELECT type, CURDATE()-MIN(purchase_date) AS MaxTime, CURDATE()-MAX(purchase_date) AS MinTime
        FROM purchasetransaction p INNER JOIN vehicle v ON p.VIN=v.VIN WHERE p.VIN NOT IN (SELECT VIN from salestransaction) GROUP BY v.type";
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Inventory Age</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/js/jquery.tablesorter.min.js"></script>
    <script>
      $(document).ready(function(){
        $(function() {
          $("#results").tablesorter({ sortList: [[0,0]] });
        });
      });
    </script>
  </head>
  <style>
  body{
    padding: 20px;
  }
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }
  th, td {
    padding: 5px;
  }
  th {
    text-align: left;
    background-color: #72a1ed;
  }
  tbody tr:hover{
    background-color: #ccc;
    cursor: pointer;
  }
  .topnav {
    background-color: #333;
    overflow: hidden;
    text-align: left;
    height: 50px;
    line-height: 50px;
    width: 60%;
  }
  .topnav span {
    padding: 10px;
    color: #f2f2f2;
    font-size: 20px;
  }
  .topnav a {
    float: right;
    color: #f2f2f2;
    text-align: center;
    padding: 0px 15px;
    text-decoration: none;
    font-size: 17px;
  }
  .topnav a:hover{
    background-color: #ddd;
    color: black;
  }
  </style>
  <body>
    <div class="topnav">
      <span> Welcome to Burdell's Ramblin' Wrecks! </span>
      <a href='index.php'>Home</a>
      <?php if(isset($_SESSION['login_user'])){
        echo "<a href='logout.php'>Logout</a>";
      }?>
    </div>
    <div>
      <p style="font-size:20px">Inventory Age Report</p>
    </div>
    <table id="results" style="width:60%" class="tablesorter">
      <thead>
        <tr>
        <th>Vehicle Type</th>
        <th>Maximum Age (Days)</th>
        <th>Average Age (Days)</th>
        <th>Minimum Age (Days)</th>
        </tr>
      </thead>
      <tbody>
      <?php
      while($row = $result1->fetch_assoc()){
        $type = $row["type"];
        $min = $row["MinTime"];
        $max = $row["MaxTime"];
        $sql2 = "SELECT AVG(CURDATE()-purchase_date) AS Age FROM purchasetransaction p INNER JOIN
                vehicle v ON p.VIN=v.VIN WHERE p.VIN NOT IN (SELECT VIN from salestransaction) AND v.type='$type'";
        $result2 = mysqli_query($conn, $sql2);
        $row2 = $result2->fetch_assoc()["Age"];
        $avg = number_format($row2,2);
        echo '<tr>
        <td>', $type, '</td>
        <td>', $max, '</td>
        <td>', $avg, '</td>
        <td>', $min, '</td>
        </tr>';
      }
      echo '</tbody>
      </table>';
      ?>
  </body>
</html>
