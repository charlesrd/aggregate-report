<?php

/**
 * Aggregates the data from the CSV to an output.json file
 * 
 * PHP version 7
 *
 * @category Index
 * @package  Index
 * @author   Charles Dyke <charlesrdyke@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://charlesrd.github.io
 */

require_once __DIR__ . '/inc/class-report.php';

use inc\Report;

/**
 * Setup variables
 */
$file = 'FL_insurance_sample.csv.zip';
$column_total = 'tiv_2012';
$column_groups = ['county', 'line'];

/**
 * Process the input file and save it to output.json
 */
$report = new Report($file);
$report->generateTotalByGroups($column_total, $column_groups);

