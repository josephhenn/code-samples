<?php
session_start();
include("config.php");

$processed = FALSE;
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $id = $_POST["customerID"];
  $street = $_POST["street"];
  $city = $_POST["city"];
  $state = $_POST["state"];
  $zip = $_POST["zip"];
  $email = $_POST["email"];
  $phone = str_replace("-","",$_POST["phone"]);
  $sql1 = "INSERT INTO customer VALUES('$id','$email','$street','$city','$state','$zip','$phone')";
  $result1 = mysqli_query($conn,$sql1);
  if($_POST["type"]=="individual"){
    $fname = $_POST["fName"];
    $lname = $_POST["lName"];
    $sql2 = "INSERT INTO individual VALUES('$id','$fname','$lname')";
    $result2 = mysqli_query($conn,$sql2);
  }else{
    $bname = $_POST["bName"];
    $name = $_POST["name"];
    $title = $_POST["title"];
    $sql2 = "INSERT INTO business VALUES('$id','$bname','$name','$title')";
    $result2 = mysqli_query($conn,$sql2);
  }
  $processed = TRUE;
}
?>

<html lang="en" dir="ltr">
  <form id="redirect" action=<?php if($_SESSION["add_sell"]=="add"){echo "add-vehicle.php";}else{echo "sales-order-form.php";}?> method="post">
    <input type="hidden" name="customerID" value="<?php echo $id;?>">
  </form>
  <script>
    <?php if($processed==TRUE){echo "document.getElementById('redirect').submit();";}?>
  </script>
</html>
