<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * 
 *
 * @package   qtype_topictagged
 * @copyright 2021 Andrei David; Ștefan Jumărea
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;

require('../../../config.php');
require($CFG->libdir . '/csvlib.class.php');
require_once('utils.php');

$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid]);

$categoryid = required_param('category', PARAM_INT);
$contextid = required_param('context', PARAM_INT);

require_login($course);

// Get all questions form database with their answers and last_used tag
global $DB;
global $sql_question_answer_lastused;
require('consts.php');
$query = $sql_question_answer_lastused;

$entries = $DB->get_records_sql($query, ['categoryid' => strval($categoryid)]);

// Initiate a new csv_export_writer object
$csv = new csv_export_writer();
$csv->set_filename('questions');

// Add file header
$fields = ['question text', 'question hash', 'last_used tag'];
$csv->add_data($fields);

/**
 * Iterate trough questions and create csv string
 * Parse every line using parse_string_for_csv function
 * Use moodle csvlib.class.php lib to create the csv file
 */
foreach($entries as $entry) {
	$csv_content = array();
	$plaintext = $entry->question_text . $entry->answers;
	$csv_content[] = $entry->question_text;
	$csv_content[] = hash("sha256", $plaintext, false);
	$csv_content[] = $entry->last_used;

	$csv->add_data($csv_content);
}

$csv->download_file();
