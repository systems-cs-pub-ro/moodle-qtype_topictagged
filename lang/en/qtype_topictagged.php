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
 * Strings for component 'qtype_topictagged', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage topictagged
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Topic Tagged Question';
$string['pluginname_help'] = 'Select an existing question based on topic and difficulty';
$string['pluginname_link'] = 'question/type/topictagged';
$string['pluginnameadding'] = 'Adding a topic tagged question';
$string['pluginnameediting'] = 'Editing a topictagged question';
$string['pluginnamesummary'] = 'Select a question from the question bank having the selected difficulty and' .
    'topic that has not been used in a while.<br>' .
    '<p><font color=#F57542><b>Warning:</b></font><br>' .
    '<font color=#A14A28>Do not add this question into the question bank. Just add it to a quiz.</font></p><br>' .
    '<p><font color=#4287F5><b>Note: </b></font>' .
    '<font color=#1C3C70>Keep in mind that preview shows randomly questions selected by the selected difficulty and topic.</font></p>' .
    '<p><font color=#4287F5><b>Note: </b></font>' .
    '<font color=#1C3C70>If during the test there are no available questions, a random question will be selected.</font></p>' .
    '<p>For further reference, please check <a href="https://github.com/systems-cs-pub-ro/quiz-manager-moodle#readme">Documentation</a></p>';
$string['setdifficulty'] = 'Set difficulty';
$string['settags'] = 'Set topic';
$string['settagsempty'] = 'No topic selected';
$string['noquestions'] = 'No questions found having the selected difficulty and topic. No questions were added to the quiz';
$string['fileformat_warning'] = '<font color=#F57542><b>Warning: </b></font>'.
    '<font color=#A14A28><b>MXML</b> file can be re-imported; <b>CSV</b> file can <u>not</u> be re-imported.</font>';

$string['randomqnamefromtop'] = 'Faulty random question! Please delete this question.';
$string['randomqnamefromtoptags'] = 'Faulty random question! Please delete this question.';
$string['randomqnametags'] = 'Random ({$a->category}, tags: {$a->tags})';
$string['randomqplusname'] = 'Random ({$a} and subcategories)';
$string['randomqplusnamecourse'] = 'Random (Any category in this course)';
$string['randomqplusnamecoursecat'] = 'Random (Any category inside course category {$a})';
$string['randomqplusnamecoursecattags'] = 'Random (Any category inside course category {$a->category}, tags: {$a->tags})';
$string['randomqplusnamecoursetags'] = 'Random (Any category in this course, tags: {$a->tags})';
$string['randomqplusnamemodule'] = 'Random (Any category of this quiz)';
$string['randomqplusnamemoduletags'] = 'Random (Any category of this quiz, tags: {$a->tags})';
$string['randomqplusnamesystem'] = 'Random (Any system-level category)';
$string['randomqplusnamesystemtags'] = 'Random (Any system-level category, tags: {$a->tags})';
$string['randomqplusnametags'] = 'Random ({$a->category} and subcategories, tags: {$a->tags})';

$string['selectedby'] = '{$a->questionname} selected by {$a->randomname}';

$string['update_button'] = 'Update';
$string['download_button'] = 'Download';
$string['download_mode'] = 'File format';

$string['download_header'] = 'Export Questions';
$string['update_header'] = 'Update Database';

$string['question_cat'] = 'Questions Category';

$string['randomqname'] = 'Topic Tagged Question {$a}';

$string['editsettings'] = 'Topic Tagged QType Administration';

$string['setdifficulty_help'] = 'Set the difficulty of the question from the given list.';
$string['settags_help'] = 'Topics are user-defined tags that are added to the questions in question bank. Topics must preserve the form they were written when tagging the questions, otherwise there won\'t be any question selected.<br> Topics are case insensitive and you can only choose one topic for one question.';
$string['update_header_help'] = 'Update the local database to include the `last_used` tag of the questions from the selected category. The update MUST be done every time after importing a new set of questions.';
$string['download'] = 'Exporting Questions';
$string['download_help'] = 'For exporting, choose the category you want to download the questions from and then choose the file format.<ol><li><b><i><a href=https://docs.moodle.org/310/en/Moodle_XML_format> MXML </a> (Recommended)</i></b> can be easily re-imported.</li><li><i>CSV (Complex)</i> easier to manipulate data as long as you have a local copy.<br>The CSV file will only contain questions used in a quiz.<br>See <a href=https://github.com/systems-cs-pub-ro/quiz-manager-moodle#readme> Documentation </a> for more information.</li></ol>';
$string['available_questoins'] = 'Available Questions Count:';
