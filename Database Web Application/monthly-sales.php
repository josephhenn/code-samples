<?php
session_start();
include("config.php");
$sql1 = "SELECT YEAR(sales_date)as year, MONTH(sales_date) as month, COUNT(*) as sold, SUM(sales_price) as income,
        SUM(sales_price-purchase_price) as net FROM salestransaction s INNER JOIN purchasetransaction p on s.VIN=p.VIN
        GROUP BY YEAR(sales_date), MONTH(sales_date) DESC";
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Monthly Sales</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/js/jquery.tablesorter.min.js"></script>
    <script>
        $(document).ready(function(){
          $(function() {
            $("#results").tablesorter({});
          });
          $('tr').click(function() {
          var href = $(this).find("a").attr("href");
          if(href) {
              window.location = href;
          }
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
      <p style="font-size:20px">Monthly Sales Summary</p>
    </div>
    <table id="results" style="width:60%" class="tablesorter">
      <thead>
        <tr>
        <th>Year</th>
        <th>Month</th>
        <th>Vehicles Sold</th>
        <th>Gross Income</th>
        <th>Net Income</th>
        </tr>
      </thead>
      <tbody>
      <?php
      while($row = $result1->fetch_assoc()){
        $year = $row["year"];
        $month = $row["month"];
        $sold = $row["sold"];
        $income = number_format($row["income"],2);
        $net = $row["net"];
        $sql2 = "SELECT SUM(total_cost) as cost from repair WHERE MONTH(start_date)='$month' and YEAR(start_date)='$year'";
        $result2 = mysqli_query($conn,$sql2);
        $cost = $result2->fetch_assoc()["cost"];
        $net = number_format($net-$cost,2);
        echo '<tr>
        <td><a href="monthly-sales-breakdown.php?month=', $month, '&year=', $year, '">', $year, '</td>
        <td>', $month, '</td>
        <td>', $sold, '</td>
        <td>$', $income, '</td>
        <td>$', $net, '</td>
        </tr>';
      }
      echo '</tbody>
      </table>';
      ?>
  </body>
</html>
