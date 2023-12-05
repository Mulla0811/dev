<?php

$result["compair_data_found"] = false;
$result["message"] = "";
if (count($argv) >= 2) {
    $server = explode("=", $argv[1]);
    $server = json_decode(base64_decode($server[1]), true);
    if (is_array($server) && count($server) > 0) {
        // echo "<pre>";
        // $server2 = [
        //     "host" => "localhost",
        //     "username" => "root",
        //     "password" => "root",
        //     "database" => "datatable",
        // ];

        // $server1 = [
        //     "host" => "localhost",
        //     "username" => "root",
        //     "password" => "root",
        //     "database" => "flask_db",
        // ];
        $server1 = $server["server1"];
        $server2 = $server["server2"];
        $sourceConnection = new mysqli(
            $server1["host"],
            $server1["username"],
            $server1["password"],
            $server1["database"]
        );

        // Check connection
        if ($sourceConnection->connect_error) {
            $result["message"] =
                "Connection to server 1 failed: " .
                $sourceConnection->connect_error;
            returnFuntion($result);
            die(
                "Connection to server 1 failed: " .
                    $sourceConnection->connect_error
            );
        }

        // Connect to the second server
        $targetConnection = new mysqli(
            $server2["host"],
            $server2["username"],
            $server2["password"],
            $server2["database"]
        );

        // Check connection
        if ($targetConnection->connect_error) {
            $result["message"] =
                "Connection to server 2 failed: " .
                $targetConnection->connect_error;
            returnFuntion($result);
            die(
                "Connection to server 2 failed: " .
                    $targetConnection->connect_error
            );
        }

        $sourceTables = [];
        $targetTables = [];

        $sourceResult = $sourceConnection->query("SHOW TABLES");
        $targetResult = $targetConnection->query("SHOW TABLES");
        while ($row = $sourceResult->fetch_row()) {
            $sourceTables[] = $row[0];
        }

        while ($row = $targetResult->fetch_row()) {
            $targetTables[] = $row[0];
        }

        $diiffrence_arr = array_diff($sourceTables, $targetTables);

        $create_table_query = [];
        $alterQuery = [];
        if (count($diiffrence_arr) > 0) {
            $sourceTableStructure = [];
            foreach ($diiffrence_arr as $key => $value) {
                $sourceTableStructure = fetchTableStructure(
                    $sourceConnection,
                    $value
                );
                // $targetTableStructure = fetchTableStructure($targetConnection, $value);
                $create_table_query = generateCreateTableQuery(
                    $value,
                    $sourceTableStructure
                );
                $alterQuery = generateAlterTableQuery(
                    $value,
                    $sourceTableStructure,
                    $targetTableStructure
                );
                $query_data[$key] = [];
                array_push($query_data[$key], $create_table_query);
                if ($alterQuery != "") {
                    array_push($query_data[$key], $alterQuery);
                }
            }
        }

        $result_arr["compair_data_found"] = false;
        $result_arr["message"] = "no any changes two database.";
        $result_arr["result_data"] = [];
        if (is_array($query_data) && count($query_data) > 0) {
        	$return_data['create_table_data'] = $query_data;
        	$return_data['server'] = $server;
            $result_arr["result_data"]= base64_encode(json_encode($return_data));
            $result_arr["compair_data_found"] = true;
            $result_arr["message"] = "changes found in two database.";
        }
        returnFuntion($result_arr);
    } else {
        $result["message"] = "server data not found!..";
        $result["compair_data_found"] = false;
        returnFuntion($result);
    }
} else {
    $result["message"] = "server data not found!..";
    $result["compair_data_found"] = false;
    returnFuntion($result);
}

function returnFuntion($result)
{
    $result["process"] = "compair_hole_db";
    echo json_encode($result);
    exit();
}

// Function to fetch table structure from a database
function fetchTableStructure($connection, $tableName)
{
    $structure = [];

    $result = $connection->query("DESCRIBE $tableName");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $structure[] = $row;
        }

        $result->close();
    }

    return $structure;
}

// Function to generate CREATE TABLE query
function generateCreateTableQuery($tableName, $columns)
{
    $createQuery = "CREATE TABLE IF NOT EXISTS $tableName (";

    foreach ($columns as $column) {
        $columnName = $column["Field"];
        $columnType = $column["Type"];
        $nullable = $column["Null"] === "YES" ? "NULL" : "NOT NULL";
        $default =
            $column["Default"] !== null ? "DEFAULT {$column["Default"]}" : "";

        $createQuery .= "$columnName $columnType $nullable $default, ";
    }

    // Remove the trailing comma and close the query
    $createQuery = rtrim($createQuery, ", ") . ")";

    return $createQuery;
}

// Function to fetch column structure from a table
function fetchColumnStructure($connection, $tableName)
{
    $columns = [];

    $result = $connection->query("SHOW COLUMNS FROM $tableName");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row;
        }

        $result->close();
    }

    return $columns;
}

// Function to generate ALTER TABLE query
function generateAlterTableQuery($tableName, $sourceColumns, $targetColumns)
{
    $alterQuery = "ALTER TABLE $tableName";
    // Check if the target table has a primary key
    $data = [];
    $hasPrimaryKey = false;
    $hasPrimaryKeyColumn = "";
    foreach ($sourceColumns as $column) {
        if ($column["Key"] === "PRI") {
            $hasPrimaryKeyColumn = $column["Field"];
            $hasPrimaryKey = true;
            break;
        }
    }

    // If the target table doesn't have a primary key, add it
    if ($hasPrimaryKey) {
        $alterQuery .= " ADD PRIMARY KEY (`$hasPrimaryKeyColumn`),";
    }

    // Check if the target table has an auto-increment column
    $hasAutoIncrement = false;
    $hasAutoInColumn = "";
    foreach ($sourceColumns as $column) {
        if (strpos(strtolower($column["Extra"]), "auto_increment") >= 0) {
            $hasAutoIncrement = true;
            $hasAutoInColumn = $column["Field"];
            $hasAutoInColumnVal = $column["Default"];
            break;
        }
    }
    // If the target table doesn't have an auto-increment column, add it
    if ($hasAutoIncrement) {
        $alterQuery .= " MODIFY COLUMN `$hasAutoInColumn` INT AUTO_INCREMENT,";
    }

    // Remove the trailing comma
    $alterQuery = rtrim($alterQuery, ",");

    return $alterQuery;
}
?>
