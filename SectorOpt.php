<?php
function selectDistinctName($connection, $tableName, $attributeName, $pulldownName, $defaultValue)
{
    // Query to find distinct values of $attributeName in $tableName
    $distinctQuery = "SELECT DISTINCT {$attributeName} FROM {$tableName} ORDER BY {$attributeName}";

    // Run the distinctQuery on the databaseName
    if (!($result_name = $connection->query($distinctQuery))) {
        die("Error executing query: " . $connection->error);
    }

    // Start the select widget
    print "\n<select name=\"{$pulldownName}\">";

    // Retrieve each row from the query
    while ($row = $result_name->fetch_assoc()) {
        // Get the value for the attribute to be displayed
        $result = $row[$attributeName];

        // Check if a defaultValue is set and, if so, is it the current database value?
        if (isset($defaultValue) && $result == $defaultValue) {
            // Yes, show as selected
            print "\n\t<option value=\"{$result}\" SELECTED>{$result}</option>";
        } else {
            // No, just show as an option
            print "\n\t<option value=\"{$result}\">{$result}</option>";
        }
    }

    print "\n</select>";
}
?>