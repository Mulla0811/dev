<?php
echo "<pre>";
 $server2 = [
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'root',
            'database' => 'datatable',
        ];
 $server1 = [
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'root',
            'database' => 'flask_db',
        ];

        $server["server2"] = $server2;
        $server["server1"] = $server1;
        $server["table_field"] = "vName";
        // print_r($server);
        // print_r(base64_encode(json_encode($server)));

// compair two data table value if single tabel
    grunt compair_table --server=eyJzZXJ2ZXIyIjp7Imhvc3QiOiJsb2NhbGhvc3QiLCJ1c2VybmFtZSI6InJvb3QiLCJwYXNzd29yZCI6InJvb3QiLCJkYXRhYmFzZSI6ImRhdGF0YWJsZSJ9LCJzZXJ2ZXIxIjp7Imhvc3QiOiJsb2NhbGhvc3QiLCJ1c2VybmFtZSI6InJvb3QiLCJwYXNzd29yZCI6InJvb3QiLCJkYXRhYmFzZSI6ImZsYXNrX2RiIn0sInRhYmxlX2ZpZWxkIjoidk5hbWUifQ== --command='php /var/www/html/grunt_js/compair_db.php' --insert_commnd='php /var/www/html/grunt_js/insert_update_db.php'



// compair two dase table of two server
grunt  --server=eyJzZXJ2ZXIyIjp7Imhvc3QiOiJsb2NhbGhvc3QiLCJ1c2VybmFtZSI6InJvb3QiLCJwYXNzd29yZCI6InJvb3QiLCJkYXRhYmFzZSI6ImRhdGF0YWJsZSJ9LCJzZXJ2ZXIxIjp7Imhvc3QiOiJsb2NhbGhvc3QiLCJ1c2VybmFtZSI6InJvb3QiLCJwYXNzd29yZCI6InJvb3QiLCJkYXRhYmFzZSI6ImZsYXNrX2RiIn0sInRhYmxlX2ZpZWxkIjoidk5hbWUifQ== --command='php /var/www/html/grunt_js/compair_hole_db.php' --create_table_commnd='php /var/www/html/grunt_js/create_table.php'




?>