<?php

declare(strict_types=1);

namespace App\Component\MySQL\Import;

use App\Component\MySQL\Connection\MySQLConnection;

class MySQLImport
{
    /**
     * @param string $fileName
     * @return string
     */
    static function import(string $fileName)
    {
        if(!$lines = file($fileName)) {
            return new \Error('No data in file '.$fileName);
        }

        $connection = MySQLConnection::connection("127.0.0.1:8889", "root", "root", "mc2");

        // Temporary variable, used to store current query
        $tempLine = '';

        // Loop through each line
        foreach ($lines as $line)
        {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $tempLine .= $line;

            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';')
            {
                // Perform the query
                try {
                    $connection->query($tempLine);
                } catch (\PDOException $e) {
                    throw new \Error($line. $e);
                }

//                // Reset temp variable to empty
                $tempLine = '';
            }
        }
        return "Tables imported successfully";
    }
}