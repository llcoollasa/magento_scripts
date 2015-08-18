<?php 

/*
 * USAGE : http://192.168.10.75/tests/netstarter/supee-6482/security.php?URL=http://petbarn.magdev.ns-staging.com.au&U=lasantha&P=lasantha123
 */
ini_set('display_errors', 1);
$url = $_GET["URL"];
$user = $_GET["U"];
$pwd = $_GET["P"];
$htuser = $_GET["HU"];
$htpwd = $_GET["HP"];
$attr_SET = $_GET["A"];
$show_max_products = 5;


/* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 */
/* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 */
/* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 *//* SOAP V2 */

echo "<hr/><hr/><hr/><hr/>";


echo "SOAP V2 Init </br>";

echo $url."/api/soap/?wsdl";

$session =null;
try {

    if(!empty($htuser)){
        $client = new SoapClient(
            $url."/api/v2_soap/?wsdl",
            array(
                'login' => $htuser,
                'password' => $htpwd
            )
        );
    }
    else{
        $client = new SoapClient($url.'/api/v2_soap/?wsdl');
    }

echo "Connected to SOAP V2 using user $user </br>";

$session = $client->login(array('username' => $user,'apiKey' => $pwd  ));
$session = $session->result;
} catch (Exception $e) {
       var_dump($e);
}
var_dump($session);
echo "Calling catalog_product list </br>";
 
$result = $client->catalogProductList(array("sessionId"=> $session,"filters"=> NULL));

if(count($result) > 0){

	$rounds = 0;
	echo "</br>Showing first $show_max_products products</br></br>";

    echo "<hr/>";
	foreach($result->result as $products){
        foreach($products as $product){
            echo $product->product_id." - ".$product->sku." - ".$product->name."</br>";
            if($rounds < $show_max_products-1){
                $rounds ++;
            }else{
                break;
            }
        }

		
	}


}else{
	echo "Product List is empty. Contact Developer.</br>";
}
 

//Creating a product

// get attribute set
//$attributeSets=NULL;
//$attributeSet=NULL;
//try{
//    //$attributeSets = $client->catalogProductAttributeSetList($session);
//    $attributeSets = $client->catalogProductAttributeSetList((object)array('sessionId' => $session));
//    var_dump($attributeSets);
//    $attributeSet = current($attributeSets);
//}catch (Exception $ex){
//    var_dump($ex);
//}



$product_sku = 'product_sku'.time();

echo "</br>product sku $product_sku about to create</br>";

$result = $client->catalogProductCreate(array('sessionId' => $session, 'type' => 'simple', 'set' => $attr_SET, 'sku' => $product_sku,'productData' => array(
    'categories' => array(2),
    'websites' => array(1),
    'name' => 'Product SUPEE 6482',
    'description' => 'Product description SUPEE 6482',
    'short_description' => 'Product short description SUPEE 6482',
    'weight' => '10',
    'status' => '1',
    'url_key' => 'product-url-key-supee-6482'.$product_sku,
    'url_path' => 'product-url-path',
    'visibility' => '4',
    'price' => '100',
    'tax_class_id' => 1,
    'meta_title' => 'Product meta title',
    'meta_keyword' => 'Product meta keyword',
    'meta_description' => 'Product meta description'
)));

$new_pro_id = $result->result;

echo "New Product Created under ID: $new_pro_id</br>";

$result = $client->catalogProductInfo(array('sessionId' => $session,'productId' => $new_pro_id));
var_dump($result);

$result = $client->catalogProductUpdate(array('sessionId' => $session, 'productId' => $new_pro_id,'productData' =>  array(
    'categories' => array(2),
    'websites' => array(1),
    'name' => 'Product name new 2',
    'description' => 'Product description',
    'short_description' => 'Product short description',
    'weight' => '10',
    'status' => '1',
    'url_key' => 'product-url-key'.$product_sku,
    'url_path' => 'product-url-path',
    'visibility' => '4',
    'price' => '100',
    'tax_class_id' => 1,
    'meta_title' => 'Product meta title',
    'meta_keyword' => 'Product meta keyword',
    'meta_description' => 'Product meta description'
)));


echo "Product $product_sku has updated</br>";


$result = $client->catalogProductInfo(array('sessionId' => $session,'productId' => $new_pro_id));
var_dump($result);


echo "Product $product_sku is deleting</br>";
$result = $client->catalogProductDelete((object)array('sessionId' => $session, 'productId' => $new_pro_id));
var_dump($result);


echo "Please check for product sku $product_sku id $new_pro_id</br>";


// If you don't need the session anymore
$client->endSession($session);




