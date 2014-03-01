<?php
include_once 'dbconfig.php';


class Person{
	private $db;
	
	public static function getPersonInfoById($id){
			$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
			$sql= "SELECT * FROM `challenge` WHERE `cid` = $cid";
				if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			
			$data = $result->fetch_assoc();
			return $data;
	}
	public static function match($start, $finish) {
    $theta = $start[1] - $finish[1]; 
    $distance = (sin(deg2rad($start[0])) * sin(deg2rad($finish[0]))) + (cos(deg2rad($start[0])) * cos(deg2rad($finish[0])) * cos(deg2rad($theta))); 
    $distance = acos($distance); 
    $distance = rad2deg($distance); 
    $distance = $distance * 60 * 1.1515; 
 
    return round($distance, 2);
	}
	
	public static function getPersonByLocation($pos){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
			$sql= "SELECT * FROM `user` ";
			if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			$i=-1;
			$ppl=array();
			
			while($row = $result->fetch_assoc())
			{
				//print_r( $row);	
				//echo floatval($row['lat']);
				//echo floatval($row['long']);
				//echo floatval($pos[0]);
				//echo floatval($pos[1]);
					//echo Person::match($pos, array(floatval($row['lat']),floatval($row['long'])));
				//if(Person::match($pos, array(floatval($row['lat']),floatval($row['long'])))<2.0)
				//{
					$ppl[++$i]=$row;
				//}
			}
			return $ppl;
	}
	
	
	
	
	public function searchAll($q){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
			$q=$db->escape_string($q);
			$sql= "SELECT `cid` FROM `challenge` WHERE `title` LIKE '%$q%' ORDER BY `rating` DESC";
				if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			$data = array();
			$i=-1;
			while($row = $result->fetch_assoc()){
				$data[++$i] = $row['cid']; 
			}
			$sql= "SELECT `cid` FROM `keyword` WHERE `value` LIKE '%$q%'";
				if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			while($row = $result->fetch_assoc()){
				$data[++$i] = $row['cid']; 
			}
			$sql= "SELECT `cid` FROM `challenge` WHERE `text` LIKE '%$q%' ORDER BY `rating` DESC";
				if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			while($row = $result->fetch_assoc()){
				$data[++$i] = $row['cid']; 
			}
			return array_unique($data);
	}
	
	public function checkAnswer($cid,$answer){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
			$uid=$_SESSION['id'];
			$sql= "SELECT * FROM `attempts` WHERE `cid` = '$cid' AND `uid` = '$uid'";
			if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
				}
			if($result->num_rows==0){
				$sql= "INSERT INTO `attempts` (`uid`,`cid`,`result`,`attno`) VALUES ('$uid','$cid','wrong',0)";
				if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			}
			$sql= "SELECT * FROM `answer` WHERE `cid` = '$cid'";
				if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			$uid = $_SESSION['id'];
			$answer = trim($answer);
			while($row = $result->fetch_assoc()){
				//echo 'Checking with '.$row['value'].'<br>';
				 if(strcasecmp($row['value'], $answer)==0){
					// Update user credits
					$sql= "UPDATE `user` SET `credit` = `credit`+15 WHERE `uid`='$uid'";
					if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
					}
					//Update Challenge info
					$sql= "UPDATE `attempts` SET `result` = 'correct',`attno`=`attno`+1 WHERE `uid`='$uid' AND `cid`='$cid'";
					if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
					}
					return true;
				 }
			}
			$sql= "UPDATE `attempts` SET `attno`=`attno`+1 WHERE `uid`='$uid' AND `cid`='$cid'";
					if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
					}
			return false;
	}
	
	public static function getChallengeInfo($cid){
			$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
			$sql= "SELECT * FROM `challenge` WHERE `cid` = $cid";
				if(!$result = $db->query($sql)){
					die('There was an error running the query [' . $db->error . ']');
				}
			
			$data = $result->fetch_assoc();
			return $data;
	}
	public static function getChallengeUrl($cid){
	
		return "http://localhost/oth/challenge.php?id=".$cid;
	}
	
	public function addPerson($pic,$rating,$lat,$long){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$pic=$db->escape_string($pic);
		$rating=$db->escape_string($rating);
		
		if($pic=="") $pic = "http://i.imgur.com/D2P9tAk";
		$sql= "INSERT INTO `user` (`pic`,`rating`,`lat`,`long`) VALUES ('$pic','$rating','$lat','$long')";
		echo $sql;
		if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
				}
		$cid = $db->insert_id;
		echo $cid;
	}
	public function addAnswer($cid,$ans){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "INSERT INTO `answer` (`cid`,`value`) VALUES ('$cid','$ans')";
		if(!$result = $db->query($sql)){
					//return;
					echo $sql;
					die('There was an error running the query for adding Answers [' . $db->error . ']');
				}
	}
	public function addKeyword($cid,$key){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "INSERT INTO `keyword` (`cid`,`value`) VALUES ('$cid','$key')";
		if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query for adding Keywords [' . $db->error . ']');
				}
	}
	public static function shortenAndStripDesc($desc){
		if(strlen($desc)>150){
			 return substr(strip_tags($desc),0,150)."...." ;
		}
		else
		  return strip_tags($desc) ;
	}
}
$person = new Person();

class User{
	private $db;
	
	public function addNewSocialUser($name,$oauthid,$oauth,$pic,$email){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "INSERT INTO `user` (name,oauth,oauthid,email,credit,pic,joined) VALUES ('$name','$oauth','$oauthid','$email',50,'$pic',now())";
		echo $sql;
		if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
				} 
			$uid = $db->insert_id;
			return $uid;
	} 
	public function addNewEmailUser($name,$email,$pass,$pic){
		
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$email=$db->escape_string($email);
		$pass=$db->escape_string($pass);
		$name=$db->escape_string($name);
		$hpass=sha1($pass);
		$sql= "INSERT INTO `user` (name,email,password,credit,pic,joined) VALUES ('$name','$email','$hpass',50,'$pic',now())";
		echo $sql;
		if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
				} 
			$uid = $db->insert_id;
			return $uid;
	} 
	
	public function findUserByEmail($email){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT * FROM `user` WHERE email = '$email'";
		//echo $sql.'<br>';
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		//print_r($data);
		if(!isset($data['uid']))return -1;
		else return $data['uid'];
		
	}
	public function findUserByOauthId($oauthid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT * FROM `user` WHERE oauthid = '$oauthid'";
		//echo $sql.'<br>';
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		//print_r($data);
		if(!isset($data['uid']))return -1;
		else return $data['uid'];
		
	}
	public function rate($cid,$rating){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$uid=$_SESSION['id'];
		$sql= "INSERT INTO `rated` (`uid`,`cid`,`rating`) VALUES ('$uid','$cid','$rating')";
		if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
				}
		$sql= "UPDATE `challenge` SET `rateno` = `rateno`+1,`rating`=(`rating`+'$rating')/`rateno`  WHERE `cid`='$cid'";
		if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
				}
		$uid=$_SESSION['id'];
		$sql= "UPDATE `user` SET `credit` = `credit`+1, WHERE `uid`='$uid'";
		if(!$result = $db->query($sql)){
					//return;
					die('There was an error running the query [' . $db->error . ']');
				}
	}
	public static function hasRated($cid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$uid = $_SESSION['id'];
		$sql= "SELECT * FROM `rated` WHERE `uid` = '$uid' AND `cid` = '$cid' ";
		//echo $sql.'<br>';
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		//print_r($data);
		if($result->num_rows==1){
			return true;
		}
		return false;
	}
	
	public static function hasSolved($cid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$uid = $_SESSION['id'];
		$sql= "SELECT * FROM `attempts` WHERE `uid` = '$uid' AND `cid` = '$cid' AND `result` = 'correct' ";
		//echo $sql.'<br>';
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		if($result->num_rows==1){
			return true;
		}
		return false;
	}
	
	public function getPosted($uid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT `cid` FROM `challenge` WHERE uid = $uid ORDER BY `rating` DESC";
		if(!$result = $db->query($sql)){
			return ;
			//die('There was an error running the query [' . $db->error . ']');
		}
		$challenges = array();
		$i=-1;
		while($row = $result->fetch_assoc()){
				 //print_r($row);
				//echo '<br />';
				$challenges[++$i] = $row['cid']; 
			}
		return $challenges;
	}
	
	public function getUserData($id){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT * FROM `user` WHERE `uid` = $id";
		if(!$result = $db->query($sql)){
			return ;
			//die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		return $data;
	}
	
	public static function getName($id){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT `name` FROM `user` WHERE uid = $id";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		return $data[0];
	}
	
	public static function getRank($id){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$credit = User::getCredit($id);
		$sql= "SELECT COUNT(`uid`) FROM `user` WHERE `credit` > $credit";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		$rank = $data[0]+1;
		if($rank < User::getBestRank($id)){
			User::UpdateBestRank($id,$rank);
		}
		return $rank;
	}
	
	public static function getPic($id){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT `pic` FROM `user` WHERE `uid` = $id";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		return $data[0];
	}
	public static function getBestRank($id){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT `bestrank` FROM `user` WHERE `uid` = $id";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		return $data[0];
	}
	
	public static function updateBestRank($id,$rank){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "UPDATE `user` SET `bestrank`=$rank WHERE `uid` = $id";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
	}
	
	public static function updatePic($pic){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$pic=$db->escape_string($pic);
		$uid=$_SESSION['id'];
		$sql= "UPDATE `user` SET `pic`='$pic' WHERE `uid` = $uid";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		return true;
	}
	public static function updateName($name){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$name=$db->escape_string($name);
		$uid=$_SESSION['id'];
		$sql= "UPDATE `user` SET `name`='$name' WHERE `uid` = $uid";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		return true;
	}
	
	public static function updateEmail($email){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$email=$db->escape_string($email);
		$uid=$_SESSION['id'];
		$sql= "UPDATE `user` SET `email`='$email' WHERE `uid` = $uid";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		return true;
	}
	
	
	public static function getCredit($id){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		//$id = $_SESSION['id'];
		$sql= "SELECT `credit` FROM `user` WHERE `uid` = $id";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		return $data[0];
	}
	
	public function getFollowerNo($uid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		//$id = $_SESSION['id'];
		$sql= "SELECT COUNT('follower') FROM `follow` WHERE `following` = $uid";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$data = $result->fetch_array();
		return $data[0];
	
	}
	
	public function follow($uid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$myid=$_SESSION['id'];
		$sql= "INSERT INTO `follow` (`follower`,`following`,`timest`) VALUES ('$myid','$uid',now())";
		if(!$result = $db->query($sql)){
					return false;
					//die('There was an error running the query [' . $db->error . ']');
				}	
		return true;
	}
	public function unFollow($uid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
			if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$myid=$_SESSION['id'];

		if(!(User::isFollowing($uid))){
			return false;
		}
				//echo $myid.' unfollow '.$uid;
		$sql= "DELETE FROM `follow` WHERE `follower`='$myid' AND `following`='$uid'";
		if(!$result = $db->query($sql)){
					return false;
					//die('There was an error running the query [' . $db->error . ']');
				}	
		return true;
	}
	public static function isFollowing($uid){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$myid = $_SESSION['id'];
		$sql= "SELECT * FROM `follow` WHERE `follower` = '$myid' AND `following` = '$uid' ";
		//echo $sql.'<br>';
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		//print_r($data);
		if($result->num_rows==1){
			return true;
		}
		return false;
	}
	
	public function getSolved($uid,$no=3){
		$db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if (mysqli_connect_errno()){
				   die(mysqli_connect_error());
			}
		$sql= "SELECT a.cid FROM `attempts` a,`challenge` c WHERE a.uid = $uid AND a.result='correct' AND a.cid = c.cid ORDER BY c.rating";
		if(!$result = $db->query($sql)){
			//return ;
			die('There was an error running the query [' . $db->error . ']');
		}
		$challenges = array();
		$i=-1;
		while($row = $result->fetch_assoc()){
				// print_r($row);
				//echo '<br />';
				$challenges[++$i] = $row['cid']; 
			}
		return $challenges;
	}
}
$user= new User();
?>