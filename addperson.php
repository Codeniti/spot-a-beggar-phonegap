<?php 
include_once 'php/functions.php';

	if(isset($_POST["pic"]))
	{
		$pic_url = $_POST["pic"];
		$lat = $_POST["lat"];
		$long = $_POST['long'];
		$rating = $_POST["rating"];
		$person->addPerson($pic_url,$rating);
	}
	else{
		exit;
	}
	
  
?>
		
		