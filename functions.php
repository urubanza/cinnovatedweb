<?php
session_start();
 /* file has been composed by Pacifique Ishimwe a.k.a PIP for commercial and serious issue
    please check lisence before using this. copyright PIP allright reserved.
################################################################################################################
      PIP is an application software development libray available for PHP, Javascript, C/C++, Objective C ,Java and Python.
      And is for those who need to create great and innovative system and application softwares  designed for IoT, Artificial intergence, blockchain applications and many more advanced engeenering software both application and system softwares.
**/
/* THE CLASS OF ALL APPLICATION ROOT */
class webApp{
 	public $database;
 	public $erroMessage = "no thing wrong yet!";
 	public $err = false;
 	public $hostName = "localhost";
 	public $password_db = "";
 	public $username_db = "root";  
 	function __construct($argument) {
 		$this->database = $argument ;
 	}
 	public function _connection(){
 		// Create connection
		$conn = new mysqli($this->hostName,$this->username_db,$this->password_db);
		// Check connection
		if ($conn->connect_error) {
			$erroMessage = "!!! Connection failed: " . $conn->connect_error;
		}
		else {
		      $db_selected = mysqli_select_db($conn,$this->database);
		      if($db_selected)
		      	$this->erroMessage =  "database selected successfully";
		      else $this->erroMessage = "database <b>".$this->database."</b> was not selected <b><i>".mysqli_error($conn);
      }
      return $conn;
 	}
 	public function connection($host,$password,$username){
 		// Create connection
		$conn = new mysqli($host,$username,$password);
		// Check connection
		if ($conn->connect_error) {
			$erroMessage = "!!! Connection failed: " . $conn->connect_error;
		}
		else {
		      $db_selected = mysqli_select_db($conn,$this->database);
		      if($db_selected)
		      	$this->erroMessage =  "database selected successfully";
		      else $this->erroMessage = "database <b>".$this->database."</b> was not selected <b><i>".mysqli_error($conn);
      }
      return $conn;
 	}
 	public function getError(){
 		return $this->erroMessage;
 	}
}
/* THE CLASS OF ALL ACTIVITIES DONE BY CLIENT LIKE VISIT, VIEW, REACH, LOGIN, LOGOUT */
class PIPCLENTS extends webApp{
    
 	public $session_name = "";
 	public $session_id = "";
 	public $session_present = false;
 	public $session_type = "";
 	public $erroMessage = "please login first";
 	function __construct($argument,$type){
 		$this->session_name = $argument;
 		if(isset($_SESSION[$this->session_name])){
 			$this->session_id = $_SESSION[$this->session_name];
 			$this->session_present = true;
 		}
 		$this->session_type = $type;
 	}
 	public function set_session($id,$saveId,$conn,$page){
 		$this->session_id = $id;
 		$_SESSION[$this->session_name] = $this->session_id;
 		if(!($this->session_type==NULL)){
 			$sql = "INSERT INTO `$this->session_type` 
 			       (`session_id`,`data`,`$saveId`,`time_accessed`) 
 			       VALUES (NULL,'$page','$id',CURRENT_TIMESTAMP)";
 			$result = mysqli_query($conn,$sql);
 			if($result)
 				{$this->erroMessage = "saved successfully"; $this->session_present = true; }
 			else $this->erroMessage = "not saved ".mysqli_error($conn);
 		}
 	}
 	public function uset_session(){
 		unset($_SESSION[$this->session_name]);
 		$this->session_present = false;
 	}
 }
/* THE OF DATAS IN THE DATABASE IN FORM OF MULTIDIMENTIONAL ARRAY AND ALL OF THEIR ACTIVITIES */
class PIP_Array{
     public $AllValues;
     public $Dimension;
     public $jsons;
     public $reversed;
    
    
     function __construct($Array){
         $this->AllValues = $Array;
         $this->reversedValues = array_reverse($Array);
         $this->jsons = json_encode($Array);
     }
     // the function that is able to return values of an array excluding or including the value of which is equal to the index given on 
    // a 2 dimentional array given according to the type provided
    //INDEX is the index name or value of the array to be excluded or included in the new array
    //VALUE is the variable to be checked to match with the index provided 
    //TYPE is the string between `ONLY` and   `REMOVE` to include or exclude all 1d arrays with all datas like that respectively
     public function filterthis($INDEX,$VALUE,$TYPE){
                        $rets = array();
                        $error_mess = "";
                        $error_state = false;
                        for($ii=0; $ii<sizeof($this->AllValues); $ii++){
                              $error_state = false;
                              if($TYPE=="REMOVE"){
                                  if(!($this->AllValues[$ii][$INDEX]==$VALUE)){
                                    array_push($rets,$ARRAY[$ii]);
                                }
                              } else if($TYPE=="ONLY"){
                                 $error_state = false;
                                 if($this->AllValues[$ii][$INDEX]==$VALUE){
                                    array_push($rets,$this->AllValues[$ii]);
                                } 
                              } else {
                                  $error_state = true;
                                  $error_mess = "please specify the action `ONLY` or `REMOVE`";
                              }  
                            }
                            if($error_state){
                                return new PIP_Array(array($error_mess));
                            } 
                                
                            else return new PIP_Array($rets);
                        }
     // the function that is able to return values of an array excluding reapeted values to the index given of  
    // a 2 dimentional array given
    // INDEX is the index of the array where its repeated datas will be eliminated 
     public function filterthis_distinct($INDEX){
            $rets = array();
            for($ii=0;$ii<sizeof($this->AllValues);$ii++){
                $exist = false;
                for($iii=0;$iii<$ii;$iii++){
                    if($this->AllValues[$ii][$INDEX]==$this->AllValues[$iii][$INDEX]){
                        $exist = true;
                    }
                }
                if(!$exist) array_push($rets,$this->AllValues);
            }
            return new PIP_Array($rets);
        }
    // this is a function to return a new 2d array with matching values of the 2d arrays given
    // PIP_ARRAY2 is the object to get all distinct data to be selected from
    // INDEX2 is the index of the PIP_ARRAY object to be selected distinctly from
    // INDEX1 is the index to be selected distinctly from
     public function filterthis_multiple($PIP_ARRAY2,$INDEX2,$INDEX1){
        $ARR = array();
        $all = $PIP_ARRAY2->filterthis_distinct($INDEX2)->AllValues;
        //print_r($all);
         for($ii=0;$ii<sizeof($all);$ii++){
               array_push($ARR,$this->filterthis($INDEX1,$all[0][$ii][$INDEX2],"ONLY")->AllValues[0]);
           }
         //echo sizeof($ARR);
         return new PIP_Array($ARR);  
    }
     // this is a function to return the lowest value in the 2d  array in the specified index
    // INDEX is the index to be with others in the row
     public function lowest($INDEX){
         $lowest = $this->AllValues[0][$INDEX];
         for($ii=0;$ii<sizeof($this->AllValues);$ii++){
             if($this->AllValues[$ii][$INDEX]<$lowest){
                 $lowest = $this->AllValues[$ii][$INDEX];
             }
         }
        return $lowest;
     }
      // this is a function to return the highest value in the 2d  array in the specified index
    // INDEX is the index to be with others in the row
     public function highest($INDEX){
         $highest = $this->AllValues[0][$INDEX];
         for($ii=0;$ii<sizeof($this->AllValues);$ii++){
             if($this->AllValues[$ii][$INDEX]>$highest){
                 $highest = $this->AllValues[$ii][$INDEX];
             }
         }
        return $highest;
     } 
     // This is a function that will chech if a value given is included in the array given
    // where the INDEX is the index to check in and value is the value to be checked
     public function included($INDEX,$VALUE){
         $rets = false;
         for($ii=0;$ii<sizeof($this->AllValues);$ii++){
             if($this->AllValues[$ii][$INDEX]==$VALUE){
                 $rets = true;
             }
         }
         return $rets;
     }
    // this is a function to return json datas of the array
    
    
}
/* THE CLASS OF A SPECIFIC TABLE AND ALL ITS POSIBLE ACTIVITIES IN THE DATABASE */
class admin extends webApp{
 	public $table_name = "";
 	public $conn;
 	public $primary_key = "_id";
 	public $erroMessage = "no error found";
 	function __construct($argument,$prim,$conns){
 		$this->table_name = $argument;
 		$this->primary_key = $prim;
 		$this->conn = $conns;
 	}
 	public function login($usernames,$fields1,$passwords,$fields2,$PIPCLENTS_name,$savethis){
 		$logIn_state = false;
 		if(isset($_POST[$usernames])&&isset($_POST[$passwords])){
 			$username = $_POST[$usernames];
 			$password = $_POST[$passwords];
 			$username = stripslashes($username);
			$password = stripslashes($password); 
			$username = mysqli_real_escape_string($this->conn,$username);
			$password = mysqli_real_escape_string($this->conn, $password);
			$names =  array($fields1=>"email",$fields2=>"password");
			//$password = md5($password);
			$sql = "SELECT `$this->primary_key` FROM `$this->table_name` WHERE `$fields1` = '$username' AND `$fields2` = '$password'";
			$result = mysqli_query($this->conn,$sql);
			if($result){
		                	if(mysqli_num_rows($result) == 1)
		                        {
		                    $row=mysqli_fetch_array($result);
		                    $this->erroMessage = " $row[0] logged allready";
		                    $PIPCLENTS  = new PIPCLENTS($this->table_name,$PIPCLENTS_name);
		                    if($PIPCLENTS->session_present){
                              $this->erroMessage = "logged in successfully";
                              if($savethis){
		                        $PIPCLENTS->set_session($row[0],$this->primary_key,$this->conn,"login");
		                          }
                              $logIn_state = true;
		                    }
		                    else { 
                                if($savethis){
		                             $PIPCLENTS->set_session($row[0],$this->primary_key,$this->conn,"login");
		                          }
		                        else $PIPCLENTS->set_session($row[0],"none",$this->conn,"login"); 
		                    	$logIn_state = true; }
		                    unset($PIPCLENTS);
		                        }
		                    else {
		                      $logIn_state = false;
		                      $this->erroMessage = "Unkown $names[$fields1] or $names[$fields2]";
		                    }
		                }
		     else $this->erroMessage = "not logged in".mysqli_error($this->conn);

			}
			else $this->erroMessage = "please set all fields";
			return $logIn_state;
 	}
 	public function logOut(){
            $state = true;
            $PIPCLENTS  = new PIPCLENTS($this->table_name,NULL);
            $PIPCLENTS->uset_session();
            if($PIPCLENTS->session_present){
            	$state = false;
            }
            return $state;
 	}
 	public function edit($field,$VALUE,$id,$mode){
 	   $ids = 0;
 	   $fields = $this->primary_key;
 	   $VALUES = 0;
 	   switch ($mode) {
 	   	case 'POST':
 	   	   if(isset($_POST[$id])){
	 	   	  $ids = $_POST[$id];
	 	   }
	 	   $fields = $field;
	 	   if(isset($_POST[$VALUES])){
	 	   	  $VALUES = $_POST[$VALUE];
              $VALUES = stripslashes($VALUES); 
              $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
	 	   }
 	   		break;
 	   	case 'GET':
 	   	   if(isset($_GET[$id])){
	 	   	  $ids = $_GET[$id];
	 	   }
	 	   	  $fields = $field;
	 	   if(isset($_GET[$VALUES])){
	 	   	  $VALUES = $_GET[$VALUE];
              $VALUES = stripslashes($VALUES); 
              $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
	 	   }
 	   		break;
 	   	case 'D':
	 	   	$ids = $id;
	 	   	$fields = $field;
	 	   	$VALUES = $VALUE;
            $VALUES = stripslashes($VALUES); 
            $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
 	   		break;
 	   	default:
 	   		$ids = $id;
	 	   	$fields = $field;
	 	   	$VALUES = $VALUE;
            $VALUES = stripslashes($VALUES); 
            $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
 	   		break;
 	   }
 	   $statatus = false;
       $sql = "UPDATE `$this->table_name` SET `$fields` = '$VALUE' WHERE `$this->table_name`.`$this->primary_key` = '$ids'";
       $result = mysqli_query($this->conn,$sql);
       if($result){
			$this->erroMessage = " edit has been done well ".'  ';
			$statatus = true;
	   }
	   else {
	   	    $this->erroMessage = " failed to edit ".mysqli_error($this->conn).'  ';
			$statatus = false;
	   }
	   return $statatus;
 	}
 	public function delete($field,$id){
 	   $statatus = false;
 	   $values = 0;
 	   if(isset($_POST[$id]))
           $values = $_POST[$id];
       else $values = $id;
       $sql = "DELETE FROM `$this->table_name` WHERE `$this->table_name`.`$field` = '$values'";
       $result = mysqli_query($this->conn,$sql);
       if($result){
			$this->erroMessage = " item has been deleted ";
			$statatus = true;
	   }
	   else {
	   	    $this->erroMessage = " failed to delete ".mysqli_error($this->conn);
			$statatus = false;
	   }
	   return $statatus;
 	}
 	public function delete_($id){
 	   $statatus = false;
 	   $values = 0;
 	   if(isset($_POST[$id])) $values = $_POST[$id];
           else if(isset($_GET[$id])) $values = $_GET[$id]; 
               else $values = $id;
       $sql = "DELETE FROM `$this->table_name` WHERE `$this->table_name`.`$this->primary_key` = '$values'";
       $result = mysqli_query($this->conn,$sql);
       if($result){
			$this->erroMessage = " item has been deleted ";
			$statatus = true;
	   }
	   else {
	   	    $this->erroMessage = " failed to delete ".mysqli_error($this->conn);
			$statatus = false;
	   }
	   return $statatus;

 	}
    public function add_($fields,$values,$direct){
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
       $length_ = sizeof($direct); 
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){
           $includedInDirectValue = false;
           for($ii=0;$ii<$length_;$ii++){
               if($direct[$ii]==$key)
                 $includedInDirectValue = true;  
           }
           if($includedInDirectValue){
              $temp = $values[$key];
           } else if(isset($_POST[$values[$key]])) $temp = $_POST[$values[$key]];
                  else if(isset($_GET[$values[$key]])) $temp = $_GET[$values[$key]];
          $temp = stripslashes($temp); 
          $temp = mysqli_real_escape_string($this->conn,$temp);
          $values_s .= ",'".$temp."'";
          $includedInDirectValue = false;
 	   }
 	   $sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s) 
 	                   VALUES (NULL $values_s)";
 	   $result = mysqli_query($this->conn,$sql);
 	   if($result){
			$this->erroMessage = "data added seccessfuly ";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->erroMessage = "error occured while adding data ".mysqli_error($this->conn);
			$statatus = 0;
		}
		return $statatus;
 	}
    public function add_current_time_($fields,$values,$direct,$date_field){
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
       $length_ = sizeof($direct); 
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){
           $includedInDirectValue = false;
           for($ii=0;$ii<$length_;$ii++){
               if($direct[$ii]==$key)
                 $includedInDirectValue = true;  
           }
           if($includedInDirectValue){
              $temp = $values[$key];
           } else if(isset($_POST[$values[$key]])) $temp = $_POST[$values[$key]];
                  else if(isset($_GET[$values[$key]])) $temp = $_GET[$values[$key]];
          $temp = stripslashes($temp); 
          $temp = mysqli_real_escape_string($this->conn,$temp);
          $values_s .= ",'".$temp."'";
          $includedInDirectValue = false;
 	   }
 	   $sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s,`$date_field`) 
 	                   VALUES (NULL $values_s,CURRENT_TIMESTAMP)";
 	   $result = mysqli_query($this->conn,$sql);
 	   if($result){
			$this->erroMessage = "data added seccessfuly success";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->erroMessage = "error occured while adding data ".mysqli_error($this->conn).$sql;
			$statatus = 0;
		}
		return $statatus;
 	}
    public function add($fields,$values){
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){   
 	   	if(isset($_POST[$values[$key]]))
 	   	   $temp = $_POST[$values[$key]];
 	   	else if(isset($_GET[$values[$key]]))
 	   	   $temp = $_GET_POST[$values[$key]];
 	   	   $temp = stripslashes($temp); 
		   $temp = mysqli_real_escape_string($this->conn,$temp);
           $values_s .=",'".$temp."'";
 	   }
 	   $sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s) 
 	                   VALUES (NULL $values_s)";
 	   $result = mysqli_query($this->conn,$sql);
 	   if($result){
			$this->erroMessage = "data added seccessfuly success";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->erroMessage = "error occured while adding data ".mysqli_error($this->conn);
			$statatus = 0;
		}
		return $statatus;
 	}
 	public function add_current_time($fields,$values,$date_field){
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){
 	   	   $temp = "xxxx";
 	   	   if(isset($_POST[$values[$key]]))
 	   	   	   $temp = $_POST[$values[$key]];
 	   	   else if(isset($_GET[$values[$key]]))
 	   	   	   $temp = $_GET[$values[$key]];
 	   	   $temp = stripslashes($temp); 
		   $temp = mysqli_real_escape_string($this->conn,$temp);
           $values_s .=",'".$temp."'";
 	   }
 	   $sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s,`$date_field`) 
 	                   VALUES (NULL $values_s,CURRENT_TIMESTAMP)";
 	   $result = mysqli_query($this->conn,$sql);
 	   if($result){
			$this->erroMessage = "data added seccessfuly success";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->erroMessage = "error occured while adding data ".mysqli_error($this->conn).$sql;
			$statatus = 0;
		}
		return $statatus;
 	}
 	public function _gets_(){
        
	 	$sql = "SELECT * FROM `$this->table_name`";
	 	$admin = array();
		$result = mysqli_query($this->conn,$sql);
	    if($result){
			$this->erroMessage = "data loaded success";
	       while($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
				array_push($admin,$row);
			}
	    }
	    else {
	    		$this->erroMessage = "error occured while loading data".mysqli_error($this->conn);
                $admin[0] = false;
	    	 }
        $pipArray = new PIP_Array($admin);
		return new PIP_Array($admin);
 	}
    public function _gets_LIMIT($start,$lenght){
	 	$sql = "SELECT * FROM `$this->table_name` LIMIT $start,$lenght";
	 	$admin = array();
		$result = mysqli_query($this->conn,$sql);
	    if($result){
			$this->erroMessage = "data loaded success";
	       while($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
				array_push($admin,$row);
			}
	    }
	    else {
	    		$this->erroMessage = "error occured while loading data".mysqli_error($this->conn);
                $admin[0] = false;
	    	 }
		return new PIP_Array($admin);
 	}
    public function _gets_ORDER_LIMIT($order,$start,$lenght){
	 	$sql = "SELECT * FROM `$this->table_name` LIMIT $start,$lenght";
	 	$admin = array();
		$result = mysqli_query($this->conn,$sql);
	    if($result){
			$this->erroMessage = "data loaded success";
	       while($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
				array_push($admin,$row);
			}
	    }
	    else {
	    		$this->erroMessage = "error occured while loading data".mysqli_error($this->conn);
                $admin[0] = false;
	    	 }
		return new PIP_Array($admin);
 	}
    public function _gets__($fields,$values,$conds){
       $sql = "SELECT * FROM `$this->table_name`"; 
       $admin = array();
        for($ii=0;$ii<sizeof($fields);$ii++){
          if($ii==0){
            $sql.= " WHERE `$fields[$ii]` = '$values[$ii]'";  
          } else {
            $sql.= " ".$conds[$ii-1]."`$fields[$ii]` = `$values[$ii]`"; 
          }  
        }
       $result = mysqli_query($this->conn,$sql);
       if($result){
			$this->erroMessage = "data loaded success";
	       while($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
				array_push($admin,$row);
			}
	    }
        else {
	    		$this->erroMessage = "error occured while loading data".mysqli_error($this->conn);
                $admin[0] = false;
	    	 }
        $pipArray = new PIP_Array($admin);
		return new PIP_Array($admin);
       
    }
    public function _gets__LIMIT($fields,$values,$conds,$start,$lenght){
       $sql = "SELECT * FROM `$this->table_name`"; 
       $admin = array();
        for($ii=0;$ii<sizeof($fields);$ii++){
          if($ii==0){
            $sql.= " WHERE `$fields[$ii]` = '$values[$ii]'";  
          } else {
            $sql.= " ".$conds[$ii-1]."`$fields[$ii]` = `$values[$ii]`"; 
          }  
        }
       $result = mysqli_query($this->conn,$sql);
       if($result){
			$this->erroMessage = "data loaded success";
	       while($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
				array_push($admin,$row);
			}
	    }
        else {
	    		$this->erroMessage = "error occured while loading data".mysqli_error($this->conn);
                $admin[0] = false;
	    	 }
        $pipArray = new PIP_Array($admin);
		return new PIP_Array($admin);
       
    }
    public function _gets__ORDER_LIMIT($fields,$values,$conds,$order,$start,$lenght){
       $sql = "SELECT * FROM `$this->table_name`"; 
       $admin = array();
        for($ii=0;$ii<sizeof($fields);$ii++){
          if($ii==0){
            $sql.= " WHERE `$fields[$ii]` = '$values[$ii]'";  
          } else {
            $sql.= " ".$conds[$ii-1]."`$fields[$ii]` = `$values[$ii]`"; 
          }  
        }
       $result = mysqli_query($this->conn,$sql);
       if($result){
			$this->erroMessage = "data loaded success";
	       while($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
				array_push($admin,$row);
			}
	    }
        else {
	    		$this->erroMessage = "error occured while loading data".mysqli_error($this->conn);
                $admin[0] = false;
	    	 }
        $pipArray = new PIP_Array($admin);
		return new PIP_Array($admin);
       
    }
    public function counts(){
        $sql = "SELECT COUNT($this->primary_key) AS Numberof FROM $this->table_name";
        $result = mysqli_query($this->conn,$sql);
        return mysqli_fetch_array($result)[0];
    }
    public function counts_($FIELD,$VALUE,$conds){
        $sql = "SELECT COUNT($this->primary_key) AS totalnumber FROM `$this->table_name`";
        for($ii=0;$ii<sizeof($FIELD);$ii++){
            if($ii<1)
                $sql .= " WHERE `$FIELD[$ii]` = '$VALUE[$ii]' ";
            else $sql .= $conds[$ii-1]." `$FIELD[$ii]` = '$VALUE[$ii]'";
        }
        $result = mysqli_query($this->conn,$sql);
        return mysqli_fetch_array($result)[0];
    }
    public function register($existing,$testing,$fields,$values){
 		$POST_VALUES = array();
 		for ($i=0; $i < sizeof($existing); $i++) { 
 			$var = $testing[$i];
 			if(isset($_POST[$var]))
 				array_push($POST_VALUES,$_POST[$var]);
 			else if(isset($_GET[$var]))
 				array_push($POST_VALUES,$_GET[$var]);
 		}
 		$status = false;
        for($var=0;$var<sizeof($existing);$var++){
	         $Admin = array();
	         $Admin = $this->get_($POST_VALUES[$var],$existing[$var],$existing[$var]);
	         if(sizeof($Admin)>0){
	            $status = false;
	            $this->erroMessage = "$testing[$var] you entered is allready exist and must be unique in the system";
	         }
	         else {
	         	$status = true;
	         	$this->erroMessage = "$testing[$var] you entered is unique in the system";	
	         }
        }
        if($status){
            return $this->add($fields,$values);
        } else return $status;
 	}
 	public function register_with_current_date($existing,$testing,$fields,$values,$date_field){
 		$POST_VALUES = array();
 		for ($i=0; $i < sizeof($existing); $i++) { 
 			$var = $testing[$i];
 			if(isset($_POST[$var]))
 				array_push($POST_VALUES,$_POST[$var]);
 			else if(isset($_GET[$var]))
 				array_push($POST_VALUES,$_GET[$var]);
 		}
 		$status = false;
        for($var=0;$var<sizeof($existing);$var++){
	         $Admin = array();
	         $Admin = $this->get_($POST_VALUES[$var],$existing[$var],$existing[$var]);
	         if(sizeof($Admin)>0){
	            $status = false;
	            $this->erroMessage = "$testing[$var] you entered is allready exist and must be unique in the system";
	         }
	         else {
	         	$status = true;
	         	$this->erroMessage = "$testing[$var] you entered is unique in the system";	
	         }
        }
        if($status){
            return $this->add_current_time($fields,$values,$date_field);
        } else return $status;
 	}
}
function bracketDatas($datas){
            $checked = array();
            $numberofteams = 0;
            $availableteams = $datas; 
            $num = 0;
            $counting = 0;
            for($v=0;$v<strlen($availableteams);$v++){
                $echo = substr($availableteams,$v,1);
                if($echo=='('){
                    $numberofteams++;
                }
                else if($echo==')')
                   {
                    array_push($checked,$num);
                    $counting = 0;
                    $num = 0;
                }
                else {
                    $num = $echo + $num*pow(10,$counting);
                    $counting++;
                }
            }
            return $checked;
          }
?>