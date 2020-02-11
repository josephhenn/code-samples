<?php
session_start();
include("config.php");
$sql1 = "SELECT CONCAT(first_name,' ',last_name) AS Name, p.customerID, COUNT(p.VIN) AS NumCars, AVG(p.purchase_price) AS AvgPrice
        FROM purchasetransaction p INNER JOIN individual i ON p.customerID=i.DLNumber GROUP BY p.customerID UNION SELECT
        b.business_name, p.customerID, COUNT(p.VIN), AVG(p.purchase_price) FROM purchasetransaction p INNER JOIN business b
        ON p.customerID=b.TaxID GROUP BY p.customerID";
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Seller History</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/js/jquery.tablesorter.min.js"></script>
    <script>
      $(document).ready(function(){
        $(function() {
          $("#results").tablesorter({ sortList: [[1,1], [2,0]] });
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
      <p style="font-size:20px">Seller History Report</p>
    </div>
    <table id="results" style="width:60%" class="tablesorter">
      <thead>
        <tr>
        <th>Seller Name</th>
        <th>Number Vehicles Sold</th>
        <th>Avg Purchase Price</th>
        <th>Avg Repairs per Vehicle</th>
        </tr>
      </thead>
      <tbody>
      <?php
      while($row = $result1->fetch_assoc()){
        $id = $row["customerID"];
        $name = $row["Name"];
        $numcars = $row["NumCars"];
        $avgprice = number_format($row["AvgPrice"],2);
        $sql2 = "SELECT AVG(Repairs) as AvgRepairs FROM
                (SELECT COUNT(*) AS Repairs, customerID FROM repair INNER JOIN purchasetransaction
                ON repair.VIN=purchasetransaction.VIN GROUP BY repair.VIN) AS a
                WHERE a.customerID='$id' GROUP BY a.customerID ";
        $result2 = mysqli_query($conn, $sql2);
        $row2 = $result2->fetch_assoc();
        $avgrepair = $row2["AvgRepairs"];
        echo '<tr>
        <td>', $name, '</td>
        <td>', $numcars, '</td>
        <td>$', $avgprice, '</td>
        <td>', round($avgrepair,2), '</td>
        </tr>';
      }
      echo '</tbody>
      </table>';
      ?>
      <script>
      $(function(){
        $("tr").each(function(){
          var col_val = $(this).find("td:eq(3)").text();
          if (col_val >= 5){
            $(this).addClass('bad');
          }
        });
      });
    </script>
  </body>
</html>
