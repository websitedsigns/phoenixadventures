<?php
// Collect form fields
$name      = $_POST['name'] ?? '';
$email     = $_POST['email'] ?? '';
$phone     = $_POST['phone'] ?? '';
$date      = $_POST['date'] ?? '';
$adults    = $_POST['adults'] ?? 0;
$children  = $_POST['children'] ?? 0;
$addons    = $_POST['addons'] ?? '0';
$requests  = $_POST['requests'] ?? '';
$total     = $_POST['total'] ?? '0';

// --------------------
// Customer Confirmation Email
// --------------------
$subjectCustomer = "Booking Confirmation - Phoenix Adventures";
$messageCustomer = file_get_contents("confirmation-email.html");
$messageCustomer = str_replace("{{name}}", htmlspecialchars($name), $messageCustomer);
$messageCustomer = str_replace("{{total}}", htmlspecialchars($total), $messageCustomer);

$headersCustomer  = "MIME-Version: 1.0\r\n";
$headersCustomer .= "Content-type:text/html;charset=UTF-8\r\n";
$headersCustomer .= "From: Phoenix Adventures <info@phoenixadventures.co.uk>\r\n";

mail($email, $subjectCustomer, $messageCustomer, $headersCustomer);

// --------------------
// Admin Notification Email
// --------------------
$subjectAdmin = "New Booking - Disneyland Paris";
$messageAdmin = "
  <h2>New Booking Received</h2>
  <p><strong>Name:</strong> {$name}</p>
  <p><strong>Email:</strong> {$email}</p>
  <p><strong>Phone:</strong> {$phone}</p>
  <p><strong>Travel Date:</strong> {$date}</p>
  <p><strong>Adults:</strong> {$adults}</p>
  <p><strong>Children:</strong> {$children}</p>
  <p><strong>Add-ons:</strong> {$addons}</p>
  <p><strong>Special Requests:</strong> {$requests}</p>
  <p><strong>Total Price:</strong> £{$total}</p>
";

$headersAdmin  = "MIME-Version: 1.0\r\n";
$headersAdmin .= "Content-type:text/html;charset=UTF-8\r\n";
$headersAdmin .= "From: Phoenix Adventures <info@phoenixadventures.co.uk>\r\n";

// Change this to your email address
$adminEmail = "you@phoenixadventures.co.uk";

mail($adminEmail, $subjectAdmin, $messageAdmin, $headersAdmin);

// Redirect back to thank-you page
header("Location: /thank-you-stripe.html?name=" . urlencode($name) . "&total=" . urlencode($total));
exit;
?>
