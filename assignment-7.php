<?php
$results = "";
//this is like a library in python, brings code from abother file
require_once("db.php");
//this is a comment;
//lets establish query as a variable
$query =
	"SELECT CONCAT(first_name, ' ', last_name) as customer_name, phone, email, i.invoice_id, date_in, date_out, description, price, quantity
	FROM jnd98092.customer c 
	INNER JOIN jnd98092.invoice i USING(customer_id)
	INNER JOIN jnd98092.invoice_item ii ON i.invoice_id = ii.invoice_id
	INNER JOIN jnd98092.item it ON it.item_id = ii.item_id
	WHERE date_in > '2020-01-01'
	ORDER BY last_name ASC";
//   prepare the statement
$stmt = $conn->prepare($query);
// execute statement
$stmt->execute();
// grab the results in a single row
// $row = $stmt->fetch();
// echo $row["first_name"];

// now lets loop through and get each row
// foreach ($stmt as $row) {
//   echo $row["first_name"] . " " . $row["last_name"] . "<br>";
// }
// now lets make a table
foreach ($stmt as $row) {
  $results .=
	"<tr>
		<td>{$row["customer_name"]}</td>
		<td>{$row["phone"]}</td>
		<td>{$row["email"]}</td>
		<td>{$row["invoice_id"]}</td>
		<td>{$row["date_in"]}</td>
		<td>{$row["date_out"]}</td>
		<td>{$row["description"]}</td>
		<td>{$row["price"]}</td>
		<td>{$row["quantity"]}</td>
	<tr>";
}
$results =
  "<table class='table table-striped table-bordered'>
  	<tr>
		<th>Customer Name</th>
		<th>Phone</th>
		<th>Email</th>
		<th>Invoice_id</th>
		<th>Date in</th>
		<th>Date out</th>
		<th>Description</th>
		<th>Price</th>
		<th>Quantity</th>
	</tr>
	{$results}
</table>";

$conn = null;
?>
<!doctype html>
<html>
  <head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <h5>Specific Order Info:</h5>
      <div><?php echo $results; ?></div>
    </div>
  </body>
</html>


