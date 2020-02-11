<?php
session_start();
include("config.php");
$year = $_GET["year"];
$month = $_GET["month"];
$sql1 = "SELECT first_name, last_name, COUNT(*) as sold, SUM(sales_price) as total FROM salestransaction s
        INNER JOIN user u ON s.salesperson=u.username WHERE YEAR(sales_date)='$year' AND MONTH(sales_date)='$month'
        GROUP BY salesperson ORDER BY sold desc, total desc";
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Monthly Sales Breakdown</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/js/jquery.tablesorter.min.js"></script>
    <script>
        $(document).ready(function(){
          $(function() {
            $("#results").tablesorter({});
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
      <p style="font-size:20px">Monthly Sales Breakdown</p>
    </div>
    <table id="results" style="width:60%" class="tablesorter">
      <thead>
        <tr>
        <th>Salesperson First Name</th>
        <th>Salesperson Last Name</th>
        <th>Vehicles Sold</th>
        <th>Total Sales</th>
        </tr>
      </thead>
      <tbody>
      <?php
      while($row = $result1->fetch_assoc()){
        $fname = $row["first_name"];
        $lname = $row["last_name"];
        $sold = $row["sold"];
        $sales = number_format($row["total"],2);
        echo '<tr>
        <td>', $fname, '</td>
        <td>', $lname, '</td>
        <td>', $sold, '</td>
        <td>$', $sales, '</td>
        </tr>';
      }
      echo '</tbody>
      </table>';
      ?>
  </body>
</html>
