<?php
session_start();
include("config.php");
$sql1 = "SELECT type, AVG(sales_date-purchase_date) AS Time FROM purchasetransaction p INNER JOIN salestransaction s ON p.VIN=s.VIN
        INNER JOIN vehicle v ON p.VIN=v.VIN GROUP BY v.type";
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Average Time in Inventory</title>
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
      <p style="font-size:20px">Average Time in Inventory Report</p>
    </div>
    <table id="results" style="width:60%" class="tablesorter">
      <thead>
        <tr>
        <th>Vehicle Type</th>
        <th>Avg Time in Inventory (Days)</th>
        </tr>
      </thead>
      <tbody>
      <?php
      while($row = $result1->fetch_assoc()){
        $type = $row["type"];
        $time = number_format($row["Time"],2);
        if($time == 0){
          $time = "N/A";
        }
        echo '<tr>
        <td>', $type, '</td>
        <td>', $time, '</td>
        </tr>';
      }
      echo '</tbody>
      </table>';
      ?>
  </body>
</html>
