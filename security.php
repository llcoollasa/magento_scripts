<?php 

/*
 * USAGE : http://localhost/tests/netstarter/supee-6482/security.php?URL=http://petbarn.magdev.ns-staging.com.au&U=lasantha&P=lasantha123
 */
ini_set('display_errors', 1);
$url = $_GET["URL"];
$user = $_GET["U"];
$pwd = $_GET["P"];
$htuser = $_GET["HU"];
$htpwd = $_GET["HP"];
$show_max_products = 5;



if(!empty($htuser)){
    $client = new SoapClient(
        $url."/api/soap/?wsdl",
        array(
            'login' => $htuser,
            'password' => $htpwd
        )
    );
}
else{
    $client = new SoapClient($url."/api/soap/?wsdl");
}

echo "SOAP V1 Init </br>";

// If somestuff requires api authentification,
// then get a session token
$session = $client->login($user, $pwd);
echo "Connected to SOAP V1 using user $user </br>";

echo "Calling catalog_product list </br>";
$result = $client->call($session, 'catalog_product.list');

if(count($result) > 0){

	$rounds = 0;
	echo "</br>Showing first $show_max_products products</br></br>";
	foreach($result as $product){
		echo $product["product_id"]." - ".$product["sku"]." - ".$product["name"]."</br>";
		if($rounds < $show_max_products-1){
			$rounds ++;
		}else{
			break;
		}
		
	}


}else{
	echo "Product List is empty. Contact Developer.</br>";
}
 

//Creating a product

// get attribute set
$attributeSets = $client->call($session, 'product_attribute_set.list');
$attributeSet = current($attributeSets);

var_dump($attributeSet);

$product_sku = 'product_sku'.time();

echo "</br>product sku $product_sku about to create</br>";

try{
$result = $client->call($session, 'catalog_product.create', array('simple', $attributeSet['set_id'], $product_sku, array(
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
}catch (Exception $e){
    var_dump($e);
}

$new_pro_id = $result;

echo "New Product Created under ID: $new_pro_id</br>";

$result = $client->call($session, 'catalog_product.info',$product_sku);
var_dump($result);

$result = $client->call($session, 'catalog_product.update', array($product_sku, array(
    'categories' => array(2),
    'websites' => array(1),
    'name' => 'Product name new 2',
    'description' => 'Product description',
    'short_description' => 'Product short description',
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


echo "Product $product_sku has updated</br>";


$result = $client->call($session, 'catalog_product.info',$product_sku);
var_dump($result);


echo "Product $product_sku is deleting</br>";
$result = $client->call($session, 'catalog_product.delete', $product_sku);
var_dump($result);


echo "Please check for product sku $product_sku id $new_pro_id</br>";


// If you don't need the session anymore
$client->endSession($session);
