<?php
// $argv = Array
// ( '/var/www/html/grunt_js/compair_db.php', 'server=eyJzZXJ2ZXIyIjp7Imhvc3QiOiJsb2NhbGhvc3QiLCJ1c2VybmFtZSI6InJvb3QiLCJwYXNzd29yZCI6InJvb3QiLCJkYXRhYmFzZSI6ImRhdGF0YWJsZSJ9LCJzZXJ2ZXIxIjp7Imhvc3QiOiJsb2NhbGhvc3QiLCJ1c2VybmFtZSI6InJvb3QiLCJwYXNzd29yZCI6InJvb3QiLCJkYXRhYmFzZSI6ImZsYXNrX2RiIn0sInRhYmxlX2ZpZWxkIjoidk5hbWUifQ=='
// )

// print_r(count($argv));
// exit();
$result['compair_data_found'] = false;
$result['message'] = "";

if(count($argv) >= 2){
  
  $server = explode("=", $argv[1]);

    if($server[0] == 'server'){
      $server_data = $server[1];
          // print_r(base64_decode($server_data));
          $server = json_decode(base64_decode($server_data),true);
          
         
         if(count($server['server1']) == 0 || count($server['server2']) == 0 ){

            $result['message'] = "server data not found!..";
            returnFuntion($result);
         }
          // print_r($server);

        // Database connection parameters for the first server
           $server1 = $server['server1'];
        // $server2 = [
        //     'host' => 'localhost',
        //     'username' => 'root',
        //     'password' => 'root',
        //     'database' => 'datatable',
        // ];

        // Database connection parameters for the second server
            $server2 = $server['server2'];
        // $server1 = [
        //     'host' => 'localhost',
        //     'username' => 'root',
        //     'password' => 'root',
        //     'database' => 'flask_db',
        // ];

        // $server1["server2"] = $server2;
        // $server1["server1"] = $server1;
        // $server1["table_field"] = "vName";
        // print_r($server1);
        // print_r(base64_encode(json_encode($server1)));

        $table_field = $server['table_field'];
        $tableName = 'mod_setting';

        // Connect to the first server
        $mysqli1 = new mysqli($server1['host'], $server1['username'], $server1['password'], $server1['database']);

        // Check connection
        if ($mysqli1->connect_error) {
          $result['message'] = "Connection to server 1 failed: " . $mysqli1->connect_error;
          returnFuntion($result);
            die("Connection to server 1 failed: " . $mysqli1->connect_error);
        }

        // Connect to the second server
        $mysqli2 = new mysqli($server2['host'], $server2['username'], $server2['password'], $server2['database']);

        // Check connection
        if ($mysqli2->connect_error) {
          $result['message'] = "Connection to server 2 failed: " . $mysqli2->connect_error;
          returnFuntion($result);
          die("Connection to server 2 failed: " . $mysqli2->connect_error);
        }

        // Query to fetch all rows from the first table
        $query1 = "SELECT * FROM {$server1['database']}.$tableName";
        $result1 = $mysqli1->query($query1);
        $results_array1 = [];
        while ($row = $result1->fetch_assoc()) {
          $results_array1[] = $row;
        }


        // Check for errors
        if (!$result1) {
          $result['message'] = "Error in query for server 1: " . $mysqli1->error;
          returnFuntion($result);
            die("Error in query for server 1: " . $mysqli1->error);
        }

        // Query to fetch all rows from the second table
        $query2 = "SELECT * FROM {$server2['database']}.$tableName";
        $result2 = $mysqli1->query($query2);

        // $result2 = $mysqli2->query($query2);
        $results_array2 = [];
        while ($row = $result2->fetch_assoc()) {
          $results_array2[] = $row;
        }

        // Check for errors
        if (!$result2) {
          $result['message'] = "Error in query for server 2: " . $mysqli2->error;
          returnFuntion($result);
            die("Error in query for server 2: " . $mysqli2->error);
        }

        // Fetch all rows from the first table
        $rows1 = $results_array1;

        // Fetch all rows from the second table
        $rows2 = $results_array2;
        $mysqli1->close();
        $mysqli2->close();

        // Compare each row from the first table with the second table
        $missingRows = [];

        // $result = check_diff_multi($a1, $a2);
        $result=your_array_diff($results_array1, $results_array2,$server);
        returnFuntion($result);
    }else{
      $result['message'] = "server data not found!..";
      $result['compair_data_found'] = false;
       
    }

}else{
    $result['message'] = "server data not found!..";
    $result['compair_data_found'] = false;
    returnFuntion($result);
}



function returnFuntion($result){
  $result['process'] = "compair";
  echo json_encode($result);
  exit();
}
function your_array_diff($arraya, $arrayb,$server) {
	// echo "<pre>";
	$insert_arr = [];
	$update_arr = [];	
    foreach ($arraya as $keya => $valuea) {
    	// print_r($valuea['vName']);
    	
    	if(!in_array($valuea['vName'], array_column($arrayb, 'vName'))){
    		$insert_arr[] = $valuea;
    	}else{
    		// print_r(in_array($valuea['vName'], $insert_arr));

    		 if (!in_array($valuea, $arrayb)) {
    		 	$update_arr[] = $valuea;
	            unset($arraya[$keya]);
	         }
    	}

    }
    $result["insert_data"] =  $insert_arr;
   	$result["update_data"] =  $update_arr;
    $result["server"] =  $server;
    $result_arr['result_data'] = base64_encode(json_encode($result));
   	$result_arr["compair_data_found"] =  true;
    $result_arr["message"] =  "database data compair successfully!.";
   	if(count($insert_arr) == 0 && count($update_arr) == 0){
   		$result_arr["compair_data_found"] =  false;
      $result_arr["message"] =  "no any changes two database.";
   	}
    return $result_arr;
}


?>