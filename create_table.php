<?php


$result['create_table_data_found'] = false;
$result['message'] = "";
// $argv[0] = "ok";
// $argv[1] = 'eyJjcmVhdGVfdGFibGVfZGF0YSI6eyIwIjpbIkNSRUFURSBUQUJMRSBJRiBOT1QgRVhJU1RTIGFjY3NfaGlzdCAoYWNjc19pZCBpbnQgTk9UIE5VTEwgLCBhY2NzX2RhdGUgZGF0ZSBOT1QgTlVMTCAsIGFjY3NfcHJzbiB2YXJjaGFyKDMpIE5PVCBOVUxMICwgYWNjc19hZGRlZCBkYXRldGltZSBOT1QgTlVMTCBERUZBVUxUIENVUlJFTlRfVElNRVNUQU1QKSIsIkFMVEVSIFRBQkxFIGFjY3NfaGlzdCBBREQgUFJJTUFSWSBLRVkgKGBhY2NzX2lkYCksIE1PRElGWSBDT0xVTU4gYGFjY3NfaWRgIElOVCBBVVRPX0lOQ1JFTUVOVCJdLCIyIjpbIkNSRUFURSBUQUJMRSBJRiBOT1QgRVhJU1RTIHVzZXJfZGF0YSAoaVVzZXJJZCBpbnQgTk9UIE5VTEwgLCB2RGVwYXJ0bWVudE5hbWUgaW50IE5PVCBOVUxMICwgZURlcGFydG1lbnRUeXBlIGludCBOT1QgTlVMTCAsIHZBZGRyZXNzIGludCBOT1QgTlVMTCkiLCJBTFRFUiBUQUJMRSB1c2VyX2RhdGEgTU9ESUZZIENPTFVNTiBgaVVzZXJJZGAgSU5UIEFVVE9fSU5DUkVNRU5UIl19LCJzZXJ2ZXIiOnsic2VydmVyMiI6eyJob3N0IjoibG9jYWxob3N0IiwidXNlcm5hbWUiOiJyb290IiwicGFzc3dvcmQiOiJyb290IiwiZGF0YWJhc2UiOiJkYXRhdGFibGUifSwic2VydmVyMSI6eyJob3N0IjoibG9jYWxob3N0IiwidXNlcm5hbWUiOiJyb290IiwicGFzc3dvcmQiOiJyb290IiwiZGF0YWJhc2UiOiJmbGFza19kYiJ9LCJ0YWJsZV9maWVsZCI6InZOYW1lIn19';

if(count($argv) >= 2){
// echo "<pre>";
  $server = explode("=", $argv[1]);
  $server = json_decode(base64_decode($server[1]),true); 
  // $server = json_decode(base64_decode($server[0]),true);
 
  if(is_array($server) && count($server) > 0){
  	if(is_array($server['server']) && count($server['server']) > 0){

  		$server2 = $server['server']['server2'];
  		// Connect to the second server
        $mysqli = new mysqli(
            $server2["host"],
            $server2["username"],
            $server2["password"],
            $server2["database"]
        );

        // Check connection
        if ($mysqli->connect_error) {
            $result["message"] =
                "Connection to server 2 failed: " .
                $targetConnection->connect_error;
            returnFuntion($result);
            die(
                "Connection to server 2 failed: " .
                    $targetConnection->connect_error
            );
        }
        $create_table_arr = $server['create_table_data'];

        if (is_array($create_table_arr) && count($create_table_arr) > 0) {
        	$success_create_table_arr = [];
	    	$err_create_table_arr = [];
	    	$err_key = 0;
        	foreach ($create_table_arr as $value) {
	        	foreach ($value as $val) {
			        // Attempt to insert data into the table
			        $sql = $val;
			        try {
			            $mysqli->query($sql);
			            $success_create_table_arr[] =  $val;
			       	} catch (mysqli_sql_exception $e) {
			            $errorMessage = $e->getMessage();
			            $err_insert_arr[$err_key]["val"] =  $value;
			            $err_create_table_arr[$err_key]["message"] =  $errorMessage;
			            $err_key++;
			            // Check if the error is "Data truncated for column"
			            // if (strpos($errorMessage, "Data truncated for column") !== false) {
			            //     echo "Skipped: Data truncated for column. Continuing with the next record.<br>";
			            // } else {
			            //     throw new mysqli_sql_exception("Error: " . $errorMessage);
			            // }
			        }
			    }
			}
			$create_table_arr_data['create_table_arr']  = $create_table_arr;
		 	$create_table_arr_data['err_create_table_arr']  = $err_create_table_arr;
		 	$create_table_arr_data['success_create_table_arr']  = $success_create_table_arr;
		 	$result['message'] = "create table successfully.";
		    $result['create_table_data_found'] = true;
		    $result['result'] = $create_table_arr_data;
		    returnFuntion($result);
        }
        else{
	  		$result['message'] = "create table data not found!..";
		    $result['create_table_data_found'] = false;
		    returnFuntion($result);
	  	}	  	
	  	
  	}else{
  		$result['message'] = "server data not found!..";
	    $result['create_table_data_found'] = false;
	    returnFuntion($result);
  	}
  	
  }else{
		$result['message'] = "server data not found!..";
	    $result['create_table_data_found'] = false;
	    returnFuntion($result);
  }

}else{
	$result['message'] = "server data not found!..";
    $result['create_table_data_found'] = false;
    returnFuntion($result);
}

function returnFuntion($result){
	$result['process'] = "create_db";
	echo json_encode($result);
	exit();
}

?>