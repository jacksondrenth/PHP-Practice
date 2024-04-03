<?php
// import php connection
require_once("db.php");

// set blank phpo var
$results = $customers = $message ="";

if (isset($_POST["submit"])) {
	$customerId = $_POST["customer"];
  
//   set up query
  $query =
	"DELETE FROM jnd98092.customer
	WHERE customer_id = ?";
  
  // prepare against SQL injections
    $stmt = $conn->prepare($query);

    // execute the delete query
    if ($stmt->execute([$customerId])) {
        $message = "<div class='alert alert-success'>Successfully deleted the customer with ID: <b>{$customerId}</b>.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Failed to delete the customer with ID: <b>{$customerId}</b>.</div>";
    }
  
}

// query customer names
$query =
  "SELECT *
  FROM jnd98092.customer
  ORDER BY last_name";
// prepare against SQL injections
$stmt = $conn->prepare($query);
// execute query
$stmt->execute();
// run through results
foreach ($stmt as $row) {
  $customers .= "<option value='{$row["customer_id"]}'>{$row["first_name"]} {$row["last_name"]}</option>";
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
      <h1>Delete a Customer</h1>
    </div>
	<div class='container'>
	  <form method="post" class="mb-3">
		<div class="form-group">
		  <select class="form-control" name="customer">
			<option value="" disabled selected> Select a customer... </option>
			<?php echo $customers; ?>
		  </select>
		</div>
		<button class="btn btn-info" name="submit">
		  Submit
		</button>
	  </form>
	  <?php echo $message; ?>
	</div>
  </body>
</html>