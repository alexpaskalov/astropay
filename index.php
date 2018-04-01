<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 29.03.2018
 * Time: 23:32
 */
require_once 'AstroPayStreamline.class.php';

echo "New Invoice";


$invoide = 'Invoice1234';// Unique transaction identification at the merchant site.
$amount = 99; // Payment amount
$bankCode = 'TE'; // Test bank code due to API
$country = 'BR'; // user's country
$iduser = '123'; // Unique user id at the merchant side
$cpf = '24736865997'; // User’s personal identification number
$name = 'ASTROPAY TESTING'; // User’s full name.
$email = 'testing@astropaycard.com'; // User’s email address.
$currency = 'BRL'; // Transaction currency
$description = 'Test transaction';
$bdate = '04/03/1984';
$return_url='';
$confirmation_url='';

$transactionResults = [
    6   =>	'Transaction not found in the system',
    7   =>	'Pending transaction awaiting approval',
    8   =>	'Operation rejected by the bank',
    9   =>	'Amount Paid. Transaction successfully concluded'
];
?>
    <br/>
<?php
echo "Bank code: ".$bankCode;
?>
    <br/>
<?php

$aps = new AstroPayStreamline();

$banks = $aps->get_banks_by_country($country, 'json');
$banksArray = json_decode($banks, true);

// check if bank exists (is available for merchant account)
foreach ($banksArray as $bank){
    if($bank['code'] == $bankCode){
        break;
    }else{
        $bank = FALSE;
    }
}

if($bank){
    $newinvoice = $aps->newinvoice($invoide, $amount, $bankCode, $country, $iduser, $name, $email, $currency, $description, $bdate, $address='', $zip='', $city='', $state='', $return_url, $confirmation_url);
    $newinvoice = json_decode($newinvoice, TRUE);
    if($newinvoice['status']){
        echo 'Error: '. $newinvoice['desc'] . ' (code '.$newinvoice['error_code'].')';
    }else{
        $url = $newinvoice['link']; //
        header("Location: $url");
        die();
    }
}else{
    echo $bankCode.' payment method is not aviable for merchant account.';
}


// Return redirection page
if(!empty($_POST['result']) && !empty($_POST['x_invoice'])){  // condition to define needed POST

    echo $transactionResults[$_POST['result']]; // alert about transaction status

    // and to check status manually (for example executing ajax requests from frontend) again but here below doing it automatically
    $status = json_decode($aps->get_status($_POST['x_invoice']));
    echo $transactionResults[$status['result']];
}


// Confirmation page (page for receiving every status update from Astropay)
if(!empty($_POST['result']) && !empty($_POST['x_invoice'])){  // condition to define needed POST
    $status = $transactionResults[$_POST['result']];
    // here we can use the updated operation status, for example white to the DB and show in the user profile
}