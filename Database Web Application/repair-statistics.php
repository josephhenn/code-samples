<?php
session_start();
include("config.php");
$sql1 = "SELECT vendor_name, COUNT(VIN) as Repairs, SUM(total_cost) as Cost, AVG(end_date-start_date) as Time FROM repair WHERE status='completed' GROUP BY vendor_name";
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Repair Statistics</title>
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
    tr.bad{
      background-color: red;
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
      <p style="font-size:20px">Repair Statistics Report</p>
    </div>
    <table id="results" style="width:60%" class="tablesorter">
      <thead>
        <tr>
        <th>Vendor Name</th>
        <th>Number of Repairs</th>
        <th>Total Cost</th>
        <th>Average Repairs Per Vehicle</th>
        <th>Average Time of Repair (Days)</th>
        </tr>
      </thead>
      <tbody>
      <?php
      while($row = $result1->fetch_assoc()){
        $vendor = $row["vendor_name"];
        $count = $row["Repairs"];
        $cost = number_format($row["Cost"],2);
        $time = $row["Time"];
        $sql2 = "SELECT AVG(a.cnt) as Reps FROM (SELECT vendor_name, COUNT(VIN) as cnt FROM
                repair WHERE status='completed' GROUP BY vendor_name, VIN) a WHERE a.vendor_name='$vendor'";
        $result2 = mysqli_query($conn, $sql2);
        $row2 = $result2->fetch_assoc();
        $avgrepair = $row2["Reps"];
        echo '<tr>
        <td>', $vendor, '</td>
        <td>', $count, '</td>
        <td>$', $cost, '</td>
        <td>', round($avgrepair,2), '</td>
        <td>', round($time,2), '</td>
        </tr>';
      }
      echo '</tbody>
      </table>';
      ?>
  </body>
</html>
