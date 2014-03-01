<?php 

include_once 'php/functions.php';

$out['result']='fail';
if(isset($_POST['lat'])&&isset($_POST['long'])){
	//print_r($_POST);
	$ppl = $person->getPersonByLocation(array(floatval($_POST['lat']),floatval($_POST['long'])));
	echo json_encode($ppl);
}
?>
