<?php
require_once('db.php');
$results = "";
if (isset($_POST['submit'])) {
  $min_total = $_POST["min-total"];
  
  $query = "SELECT c.first_name, c.last_name, SUM(it.quantity * im.price) AS total_dry_cleaning
                FROM jnd98092.CUSTOMER c
                INNER JOIN jnd98092.INVOICE i ON c.customer_id = i.customer_id
                INNER JOIN jnd98092.INVOICE_ITEM it ON i.invoice_id = it.invoice_id
				INNER JOIN jnd98092.ITEM im on im.item_id = it.item_id
                GROUP BY c.customer_id
				HAVING SUM(it.quantity * im.price) > ?
				ORDER BY  SUM(it.quantity * im.price) DESC";

  $stmt = $conn->prepare($query);
  $stmt->execute([$_POST["min-total"]]);
  
  foreach ($stmt as $row) {
	$results .=
	"<tr>
      <td>{$row["first_name"]}</td>
      <td>{$row["last_name"]}</td>
      <td>$" . number_format($row["total_dry_cleaning"], 2) . "</td>
    </tr>";
  }
  $results = 
	"<h4>Customer order information exceeding <b>{$_POST["min-total"]}</b>:</h4>
  <table class='table table-bordered table-striped'>
    <tr>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Total Dry Cleaning</th>
    </tr>
    {$results}
  </table>";
}

$conn = null;
?>

<!doctype html>
<html>
  <head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.css">
  </head>
  <body>
	<div class="jumbotron text-center">
      <h2>Customer Dry Cleaning Totals</h2>
    </div>
	<div class='container'>
	  <form method='post' class='mb-3'>
		<div>
		<label for="min_total">Minimum Total Dry Cleaning:</label>
			<input type="number" class="form-control" name="min-total" placeholder="Enter a number..." min=0>
		<button class='btn btn-info' name='submit'>
		  Submit
		  </button>
		</div>
	  </form>
	  <?php echo $results; ?>
	</div>
  </body>
</html>
