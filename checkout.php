<?php
session_start();

$total_price = 100; // Initialize total price variable

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
// Construct variables
$cartTotal = $total_price; // This amount needs to be sourced from your application
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
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
</head>
<body>
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
                            <td class="summary-total" style="text-align: right; font-weight: bold;">R0.00</td>
                        </tr>
                    </tbody>
                </table>
                <div class="disclaimer" style="display: flex; align-content: flex-start;">
                    <input type="checkbox" name="" id=""><span class="small">I hearby consent to providing my personal information inputted in this form to be used for delivery of the service</span>
                </div>
                <div class="placeorder" style="width: 100%;">
                <?php echo $htmlForm; ?>
                </div>
            </form>
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
            type: "residential",
            company: "",
            street_address: document.getElementById("deliveryStreetAddress").value,
            local_area: "", 
            city: document.getElementById("deliveryCity").value,
            zone: document.getElementById("deliveryProvince").value,
            country: "ZA", // Assuming South Africa
            code: document.getElementById("deliveryPostalCode").value,
            lat: 0, 
            lng: 0 
        },
        parcels: [
            {
                submitted_length_cm: 42.5, 
                submitted_width_cm: 38.5, 
                submitted_height_cm: 5.5, 
                submitted_weight_kg: 3 
            }
        ],
        declared_value: 1500, 
        collection_min_date: "2021-05-21", 
        delivery_min_date: "2021-05-21" 
    };
}



function getShippingRates() {
    const formData = getFormData();
    const apiUrl = "https://api.shiplogic.com/v2/rates";
    const bearerToken = "a601d99c75fc4c64b5a64288f97d52b4"; 

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
        
        console.log(data);

                
        const rate = data.rates[0].rate; 
        document.querySelector(".summary-shipping").innerHTML = `R${rate}`
        
        console.log("Rate:", rate);
        
        
    })
    .catch(error => {
        
        console.error("Error:", error);
    });
    
}

setInterval(getShippingRates, 5000)
</script>
</body>
</html>