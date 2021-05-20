<?php

/**
 * Report Class File
 * 
 * PHP version 7
 *
 * @category Report
 * @package  Report
 * @author   Charles Dyke <charlesrdyke@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://charlesrd.github.io
 */

namespace inc;

use ZipArchive;

/**
 * Aggregates data from a Report
 * 
 * Report Class
 *
 * @category Report
 * @package  Report
 * @author   Charles Dyke <charlesrdyke@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://charlesrd.github.io
 */

class Report
{
    /**
     * Report file
     */
    private $_file;

    /**
     * Output file
     */
    protected $output_file = "output.json";

    /**
     * __construct
     *
     * @param string $file The input file
     * 
     * @return void
     */
    public function __construct(string $file)
    {
        $this->file = $this->_getFile($file);

        /**
         * If the file is not found, return the error message 
         **/
        if (!$this->file) {
            return "Error! The file was not found: $file";
        }
    }
    
    
    /**
     * _getFile
     *
     * @param string $file The input file
     * 
     * @return string
     */
    private function _getFile(string $file) : string
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        /**
         * If the file is a zip file, then unzip it to the root directory
         */
        if ($extension == 'zip') {
            
            $zip = new ZipArchive;

            if ($zip->open($file) === true) {
                $zip->extractTo(dirname(__FILE__, 2));
                $zip->close();

                /**
                 * The unzipped file should have the same name as the zip file 
                 * without the zip extension
                 */
                $unzipped_file = pathinfo($file, PATHINFO_FILENAME);
                return $unzipped_file;
            } else {
                return "Error! The file was not able to be unzipped";
            }

            return $file;
        }
    }

    
    /**
     * Generate a JSON string of a column_total grouped by other columns
     * and save the output to a file
     *
     * @param string $column_total  The name of the column that will be summed
     * @param array  $column_groups An array of column names to group by
     * 
     * @return void
     */
    public function generateTotalByGroups(string $column_total, array $column_groups) : void
    {
        /**
         * Setup variables
         */
        $return_array = [];
        $contents = file_get_contents($this->file);
        $lines = explode("\r", $contents);
        $header_line_array = explode(',', $lines[0]);

        /**
         * Get the column indexes from the file
         */
        $column_total_index = array_search($column_total, $header_line_array);
        $column_group_index = [];

        foreach ($column_groups as $name) {
            $column_group_index[$name] = array_search($name, $header_line_array);
        }
        
        /**
         * Loop through the file line by line
         */
        $count = 0;
        foreach ($lines as $line) {
            
            /**
             * Skip the header line
             */
            if ($count == 0) {
                $count++;
                continue;
            }

            /**
             * Break the line into an array
             */
            $line_array = explode(',', $line);

            /**
             * Get the values from the line for each column group
             * and sum them together by the column_total
             */
            foreach ($column_group_index as $group_name => $line_index) {
                $line_index_value = $line_array[$line_index];
                $column_total_value = $line_array[$column_total_index];
                $current_value = $return_array[$group_name][$line_index_value][$column_total] ?? 0;
                $return_array[$group_name][$line_index_value][$column_total] = number_format($column_total_value + $current_value, 2, ".", "");
            }

             /**
              * Increment the counter
              */
             $count++;
        }

        /**
         * Save the JSON to the output file
         */
        $json = json_encode($return_array);
        $this->saveOutputFile($json);
    }

    
    /**
     * Save the data to the output file
     *
     * @param string $data The input string data
     * 
     * @return void
     */
    protected function saveOutputFile(string $data) : void
    {
        file_put_contents(dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . $this->output_file, $data);        
    }
}
