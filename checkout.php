<?php
session_start();

// Function to remove an item from the cart by SKU
function removeItemFromCart($sku) {
    if(isset($_SESSION['cart'][$sku])) {
        unset($_SESSION['cart'][$sku]);
    }
}


// Check if 'remove-from-cart' parameter is present in the URL
if(isset($_GET['remove-from-cart'])) {
    // Get the SKU of the product to remove from cart
    $product_sku = $_GET['remove-from-cart'];
    // Call the function to remove the item from the cart
    removeItemFromCart($product_sku);
}

$total_quantity = 0;
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $sku => $item) {
        $total_quantity += $item['quantity'];
    }
}

$total_price = 0; // Initialize total price variable
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $total_price += ($item['quantity'] * $item['price']);
    }
}
if(isset($_GET['total'])) {
    // Retrieve the total value passed as query parameter
    $cartTotal = $_GET['total'];
}
/**
 * @param array $data
 * @param null $passPhrase
 * @return string
 */
function generateSignature($data, $passPhrase = null) {
    // Create parameter string
    $pfOutput = '';
    foreach( $data as $key => $val ) {
        if($val !== '') {
            $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
        }
    }
    // Remove last ampersand
    $getString = substr( $pfOutput, 0, -1 );
    if( $passPhrase !== null ) {
        $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
    }
    return md5( $getString );
} 


$cartTotal = $total_price;
$passphrase = 'jt7NOE43FZPn';
$data = array(
    // Merchant details
    'merchant_id' => '10000100',
    'merchant_key' => '46f0cd694581a',
    'return_url' => 'http://www.yourdomain.co.za/return.php',
    'cancel_url' => 'http://www.yourdomain.co.za/cancel.php',
    'notify_url' => 'http://www.yourdomain.co.za/notify.php',
    // Buyer details
    'name_first' => 'First Name',
    'name_last'  => 'Last Name',
    'email_address'=> 'test@test.com',
    // Transaction details
    'm_payment_id' => '1234', //Unique payment ID to pass through to notify_url
    'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
    'item_name' => 'Order#123'
);

$signature = generateSignature($data, $passphrase);
$data['signature'] = $signature;

// If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
$testingMode = true;
$pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
$htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post">';
foreach($data as $name=> $value)
{
    $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
}
$htmlForm .= '<button type="submit" class="placeorder">Place Order </button></form>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<?php include("../header/header.php"); ?>
    <div class="container">
        <div class="checkout-body" style="display: flex;">
            <div class="checkout-fields" style="display: flex; flex-direction: column;">
                <strong>Details</strong>
                <div class="first-and-last" style="width: 100%; display: flex;">
                    <input id="first" style="width: 50%; margin-right: 10px;" type="text" placeholder="First Name">
                    <input id="last" style="width: 50%;"type="text" placeholder="Last Name" required>
                </div>
                <div class="street-address" style="width: 100%;">
                    <strong>Street Address</strong>
                    <input id="deliveryStreetAddress" style="width: 100%;" type="text" placeholder="Street Address and house number">
                    <input id="deliveryApartment" style="width: 100%;" type="text" placeholder="Apartment, suite, etc (optional)">
                    <strong>City</strong>
                    <input id="deliveryCity" style="width: 100%;" type="text" placeholder="Town / City">
                    <select id="deliveryProvince" style="width: 100%;" name="">
                        <option>Select an option</option>
                        <option>Eastern Cape</option>
                        <option>Free State</option>
                        <option>Gauteng</option>
                        <option>KwaZulu-Natal</option>
                        <option>Limpopo</option>
                        <option>Mpumalanga</option>
                        <option>Northern Cape</option>
                        <option>North West</option>
                        <option>Western Cape</option>
                    </select>
                    <input id="deliveryPostalCode" style="width: 100%;" type="text" placeholder="Postal / Zip Code">
                </div>
                <div class="contact-details" style="width: 100%;">
                    <strong>Contact Details</strong>
                    <input type="text" style="width: 100%;" placeholder="Phone Number">
                    <input type="text" style="width: 100%;" placeholder="Email address">
                </div>
                <div class="order-notes" style="width: 100%; display: flex; flex-direction: column;">
                    <div class="label"><strong>Order Notes (optional)</strong></div>
                    <p>
                        <textarea name="" id="" cols="30" rows="10"></textarea>
                    </p>
                </div>
            </div>
            <div class="checkout-summary" >
            
                <strong>Order Summary</strong>
                <table>
                    <tbody class="summary">
                        <tr>
                            <th class="summary-title" style="text-align: left;">Subtotal</th>
                            <td class="summary-subtotal" style="text-align: right;">R<?php echo $total_price; ?></td>
                        </tr>
                        <tr>
                            <th class="summary-title" style="text-align: left;">Shipping</th>
                            <td class="summary-shipping" style="text-align: right;">R0.00</td>
                        </tr>
                        <tr>
                            <th class="summary-title" style="text-align: left;">Total</th>
                            <td class="summary-total" style="text-align: right; font-weight: bold;">R<?php echo $total_price; ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="disclaimer" style="display: flex; align-content: flex-start;">
                    <input type="checkbox" name="" id=""><span class="small">I hearby consent to providing my personal information inputted in this form to be used for delivery of the service</span>
                </div>
                <div class="placeorder" style="width: 100%;">
                <?php echo $htmlForm; ?>
                </form>
            </div>
            </div>
        </div>
    </div>
<script>
    // Function to get form data
    function getFormData() {
    return {
        collection_address: {
            type: "business",
            company: "uAfrica.com",
            street_address: "1188 Lois Avenue",
            local_area: "Menlyn",
            city: "Pretoria",
            zone: "Gauteng",
            country: "ZA",
            code: "0181",
            lat: -25.7863272,
            lng: 28.277583
        },
        delivery_address: {
            type: "business",
            company: "uAfrica.com",
            street_address: document.getElementById("deliveryStreetAddress").value,
            local_area: "Menlyn",
            city: document.getElementById("deliveryCity").value,
            zone: document.getElementById("deliveryProvince").value,
            country: "ZA",
            code: document.getElementById("deliveryPostalCode").value,
        },
        parcels: [
            {
                submitted_length_cm: 42.5, // You might need to capture this data from the form if available
                submitted_width_cm: 38.5, // You might need to capture this data from the form if available
                submitted_height_cm: 5.5, // You might need to capture this data from the form if available
                submitted_weight_kg: 3 // You might need to capture this data from the form if available
            }
        ],

    };
}

// Function to make the API request
function getShippingRates() {
    const formData = getFormData();
    const apiUrl = "https://api.shiplogic.com/v2/rates";
    const bearerToken = "a601d99c75fc4c64b5a64288f97d52b4"; // Change this to your actual bearer token

    fetch(apiUrl, {
        method: "POST",
        headers: {
            "Authorization": `Bearer ${bearerToken}`,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        // Handle the response data here
        console.log(data);

        // Extract the rate from the response
        const rate = data.rates[0].rate; // Assuming there's only one rate in the array
        document.querySelector(".summary-shipping").innerHTML = `R${rate}`
        
        // Now you can use the 'rate' variable as needed
        console.log("Rate:", rate);
        
        // Send the rate value via AJAX to process.php
        $.ajax({
            type: 'POST',
            url: 'process.php',
            data: {variable: rate},
            success: function(response){
                $('.summary-total').html(`R${response}`);
            }
        });
        
    })
    .catch(error => {
        // Handle errors here
        console.error("Error:", error);
    });
    
}

// Call getShippingRates initially
getShippingRates();

// Set interval to call getShippingRates every 5 seconds
setInterval(getShippingRates, 5000);

</script>



<script>

</script>
</body>
</html>
