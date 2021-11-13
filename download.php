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
 * @package   qtype_quizmanager
 * @copyright 2021 Andrei David; Ștefan Jumărea
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require('utils.php');

$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid]);

$categoryid = required_param('category', PARAM_INT);
$contextid = required_param('context', PARAM_INT);

require_login($course);

// Get all questions form database with their answers and last_used tag
global $DB;
$query = '
    SELECT question.id "id", question.questiontext "question_text", GROUP_CONCAT(answers.answer) "answers", quizmanager.lastused "last_used"
    FROM {question} question
        JOIN {question_answers} answers
            ON answers.question = question.id
        JOIN {question_quizmanager} quizmanager
            ON question.id = quizmanager.questionid
    WHERE question.category = ' . $categoryid . '
    GROUP BY question.id;
';

$entries = $DB->get_records_sql($query);

/**
 * Iterate trough questions and create csv string
 * Parse every line using parse_string_for_csv function
 * Save all content of the file in a string, to be used with create_file_from_string function
 */
$utils = new \qtype_quizmanager\utils();
$str= "question_text,question hash,last_used tag\n";
foreach($entries as $entry) {
	$csv_content = array();
	$plaintext = $entry->question_text . $entry->answers;
	$csv_content[] = $entry->question_text;
	$csv_content[] = hash("sha256", $plaintext, false);
	$csv_content[] = $entry->last_used;

	$str .= $utils->parse_string_for_csv($csv_content);
}

$fs = get_file_storage();

$fileinfo = array(
'contextid' => $contextid,
'component' => 'qtype_quizmanager',
'filearea' => 'downloadarea',
'itemid' => 0,
'filepath' => '/',
'filename' => 'Questions.csv'
);

$file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
$fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
// If file already exists, delete it
if ($file) {
$file->delete();
}

// Create and send file to user
$fs->create_file_from_string($fileinfo, $str);
$file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
$fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

//var_dump($file); die();
$options = [];
$options['dontdie'] = true;
send_stored_file($file, 0, 0, false, $options);
