<?php
require_once("db.php");
$customers = $message = $items = $invoiceID = "";
// query ids and names
$stmt = $conn->query(
  "SELECT customer_id, CONCAT(first_name, ' ', last_name) as full_name
  FROM jnd98092.customer
  ORDER BY last_name ASC;");
// run through each row and add to dropdown options
foreach ($stmt as $row){
 $customers .= "<option value='{$row["customer_id"]}'>{$row["full_name"]}</option>"; 
}
// next thing is to populate item desc
// query ids and description
$stmt = $conn->query(
  "SELECT item_id, description
  FROM jnd98092.item
  ORDER BY description ASC;");
// run through each row and add to dropdown options
foreach ($stmt as $row){
  $items .= "<option value='{$row["item_id"]}'>{$row["description"]}</option>"; 
}
if (isset($_POST['submit'])) {
  $today = date("Y-m-d");
  $query =
	"SELECT invoice_id
	FROM jnd98092.invoice
	WHERE customer_id = ? AND date_in = '$today'";
  $stmt = $conn->prepare($query);
  //   execute query
  $stmt->execute([$_POST["customer-id"]]);

  // check if invoice exists
  if ($stmt->rowCount() > 0) {
	$row = $stmt->fetch();
	$invoiceID = $row["invoice_id"];
  } else {
	$stmt = $conn->prepare("INSERT INTO jnd98092.invoice (date_in, customer_id)
		Values (Now(), ?)");
	$stmt->execute([$_POST["customer-id"]]);
	// get id of new record
	$invoiceID = $conn->lastinsertid();
  }
  $query =
	"SELECT *
	FROM jnd98092.invoice_item
	WHERE invoice_id = ? AND item_id = ?";
  $stmt = $conn->prepare($query);
  //   execute query
  $stmt->execute([$invoiceID, $_POST["item-id"]]);
  // check if any rows are returned
  if ($stmt->rowCount() > 0) {
	$query =
	  "UPDATE jnd98092.invoice_item
        SET quantity = quantity + ?
		WHERE invoice_id = ? AND item_id = ?";
	$stmt = $conn->prepare($query);
	//   execute query
	$stmt->execute([$_POST["quantity"], $invoiceID, $_POST["item-id"]]);
	
	//message
	$message .= "<div class='alert alert-success'>Successfully inserted the item <b>#{$_POST["item-id"]}</b> on invoice <b>#{$invoiceID}</b> with the quantity increased by <b>{$_POST["quantity"]}</b>.</div>";
  } else {
        $query =
        "INSERT INTO jnd98092.invoice_item (invoice_id, item_id, quantity)
        VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$invoiceID, $_POST["item-id"], $_POST["quantity"] ]);
	// message
	$message .= "<div class='alert alert-success'>Successfully inserted the item <b>#{$_POST["item-id"]}</b> on invoice <b>#{$invoiceID}</b> with the quantity <b>{$_POST["quantity"]}</b>.</div>";
    }
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
	  <h1>Invoice Creation</h1>
	</div>
	<div class='container'>
	  <form method="post" class="mb-3">
		<div class="form-group">
		  <label>Customer:</label>
		  <select class='form-control' name='customer-id'>
			<option value="" disabled selected>Select a customer...</option>
			<?php echo $customers; ?>
		  </select>
		</div>
		<div class="form-group">
		  <label>Item Description:</label>
		  <select class='form-control' name='item-id'>
			<option value="" disabled selected>Select an item description...</option>
			<?php echo $items; ?>
		  </select>
		</div>
		<div class="form-group">
		  <label>Quantity:</label>
		  <input class="form-control" type="text" name="quantity" placeholder="Enter a quantity..."> 
		</div>
		<div>
		  <button class="btn btn-primary" name="submit">
			Submit!
		  </button>
		</div>
	  </form>
	  <?php echo $message; ?>
	</div>
  </body>
</html>