<?php


$result['compair_data_found'] = false;
$result['message'] = "";

if(count($argv) >= 2){

	$server = explode("=", $argv[1]);
	$insert_arr = [];
	$update_arr = [];
	$insert_arr_opt = false;
	$update_arr_opt = false;
	$server = base64_decode($server[1]);	
	$server = json_decode($server, TRUE);
	$return_arr = [];
	if(count($server['insert_data']) > 0){
		$insert_arr = $server['insert_data'];
		$insert_column = implode(", ", array_keys($server['insert_data'][0]));
		$insert_arr_opt = true;
	}
	 
	if(count($server['update_data']) > 0){
		$update_arr = $server['update_data'];
		$update_arr_opt = true;
	}
																				
	$table_field = $server['server']['table_field'];
	$server = $server['server']['server2'];

	if(!(count($server) > 0)){
		$result['message'] = "server data not found!..";
    	$result['insert_update_data'] = false;
        returnFuntion($result);
	}
	
    $tableName = 'mod_setting';
    // Connect to the first server
    $mysqli = new mysqli($server['host'], $server['username'], $server['password'], $server['database']);

	
        // Check connection
    if ($mysqli->connect_error) {
        $result['message'] = "Connection to server 2 failed: " . $mysqli1->connect_error;
        returnFuntion($result);
    }


    $inser_arr_data = [];
    $inser_arr_data['insert_arr_opt'] = $insert_arr_opt ? "1":"0";
    if($insert_arr_opt){
    	$success_insert_arr = [];
    	$err_insert_arr = [];
    	$err_key = 0;
    	 foreach ($insert_arr as $value) {
			$insert_val = [];
			$insert_val = "('" . implode("', '", $value) . "')";
		        // Attempt to insert data into the table
		        $sql = "INSERT INTO $tableName  ($insert_column) VALUES $insert_val";
		        try {
		            $mysqli->query($sql);
		            $success_insert_arr[] =  $value;
		       	} catch (mysqli_sql_exception $e) {
		            $errorMessage = $e->getMessage();
		            $err_insert_arr[$err_key]["val"] =  $value;
		            $err_insert_arr[$err_key]["message"] =  $errorMessage;
		            $err_key++;
		            // Check if the error is "Data truncated for column"
		            // if (strpos($errorMessage, "Data truncated for column") !== false) {
		            //     echo "Skipped: Data truncated for column. Continuing with the next record.<br>";
		            // } else {
		            //     throw new mysqli_sql_exception("Error: " . $errorMessage);
		            // }
		        }
		    } 
		 $inser_arr_data['insert_arr_opt']  = $insert_arr_opt;
		 $inser_arr_data['err_insert_arr']  = $err_insert_arr;
		 $inser_arr_data['success_insert_arr']  = $success_insert_arr;
    }
    $return_arr['insert_arr'] = $inser_arr_data;

    
    $update_arr_data = [];
    $update_arr_data['update_arr_opt'] = $update_arr_opt ? "1":"0";
    if($update_arr_opt){
    	$success_update_arr = [];
    	$err_update_arr = [];
    	$err_key = 0;
    	
    	foreach ($update_arr as $value) {
			$update_val = [];
			
			$update_val = [];
			$where_con = '';
			foreach ($value as $key => $val) {
				if($key == $table_field){
					$where_con = "$key = '$val'";
				}

				$update_val[] = "$key = '$val'";
				
			}
			$update_val = implode(", ", $update_val) ;
			
		        // Attempt to insert data into the table
		        $sql = "UPDATE $tableName SET $update_val WHERE $where_con";
		        try {
		            $mysqli->query($sql);
		            $success_update_arr[] =  $value;
		       	} catch (mysqli_sql_exception $e) {
		            $errorMessage = $e->getMessage();
		            $err_update_arr[$err_key]["val"] =  $value;
		            $err_update_arr[$err_key]["message"] =  $errorMessage;
		            $err_key++;
		            // Check if the error is "Data truncated for column"
		            // if (strpos($errorMessage, "Data truncated for column") !== false) {
		            //     echo "Skipped: Data truncated for column. Continuing with the next record.<br>";
		            // } else {
		            //     throw new mysqli_sql_exception("Error: " . $errorMessage);
		            // }
		        }
		}
		$update_arr_data['update_arr_opt']  = $insert_arr_opt;
		$update_arr_data['err_update_arr']  = $err_update_arr;
		$update_arr_data['success_insert_arr']  = $success_update_arr; 
    }
	$return_arr['update_arr'] = $update_arr_data;
	$result['message'] = "inser data successfully.";
    $result['insert_update_data'] = true;
    $result['result_data'] = $return_arr;
    returnFuntion($result);
    

}else{
	$result['message'] = "server data not found!..";
    $result['insert_update_data'] = false;
    returnFuntion($result);
}


function returnFuntion($result){
  $result['process'] = "insert_update";
  echo json_encode($result);
  exit();
}

exit();




?>