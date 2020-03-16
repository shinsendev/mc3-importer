<?php

declare(strict_types=1);

namespace App\Component\MySQL\Import;

class ImportDatabaseFromSQLFile
{
    static function import(\PDO $connection)
    {
        //we get the file
        $filename = '../data/mc2.sql';

        // we import the file
        // Temporary variable, used to store current query
        $templine = '';

        $lines = file($filename);
        // Loop through each line
        foreach ($lines as $line)
        {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $templine .= $line;

            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';')
            {
                // Perform the query
                $connection->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
                // Reset temp variable to empty
                $templine = '';
            }
        }
        return "Tables imported successfully";
    }
}