<?php
$area_1 = $_POST['area_1'];
$area_2 = $_POST['area_2'];
$con=mysqli_connect("localhost","root","12345","blog");
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

mysqli_query($con,"INSERT INTO comment (id, comment, userId)
VALUES ($area_1, '$area_2',10)");


$result = mysqli_query($con,"SELECT * FROM comment");
print "All comments:";
echo "<br>";
while($row = mysqli_fetch_array($result))
  {
  echo $row['comment'];
  echo "<br>";
  }

mysqli_close($con);
print "
<div class=\"alert alert-success\">
  <p>Success! You send comment:</p>
  <ul>
    <li>ID: $area_1</li>
    <li>Text: $area_2</li>
  </ul>
</div>";
?>