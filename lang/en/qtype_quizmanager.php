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
 * Strings for component 'qtype_quizmanager', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage quizmanager
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'quizmanager';
$string['pluginname_help'] = 'Create a quizmanager question type with insert question description here.';
$string['pluginname_link'] = 'question/type/quizmanager';
$string['pluginnameadding'] = 'Adding a quizmanager question';
$string['pluginnameediting'] = 'Editing a quizmanager question';
$string['pluginnamesummary'] = 'A quizmanager question type that allows something to be done that can test a student on some understanding.';
$string['setdifficulty'] = 'Set difficulty';
$string['settags'] = 'Set topic';
$string['settagsempty'] = 'No topic selected';

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

$string['action'] = 'Desired action';
$string['addquestion'] = 'Add Question';
$string['update_button'] = 'Update';
$string['download_button'] = 'Download';
$string['download_mode'] = 'File format';

$string['download_header'] = 'Download questions';
$string['update_header'] = 'Update database';

$string['question_cat'] = 'Questions Category';

$string['randomqname'] = 'Quiz Manager question {$a}';

$string['editsettings'] = 'Quiz Manager Administration';

$string['setdifficulty_help'] = 'Set the difficulty of the question from the given list.';
$string['settags_help'] = '<i>Note:</i> Topics are case insensitive and you can only choose one topic for one question.';
$string['update_button_help'] = 'Update the local database to include the `last_used` tag of the questions from the selected category. The update MUST be done every time after importing a new set of questions.';
$string['download_mode_help'] = 'Choose the format for the questions to be exported.<ol><li><b><i>MXML (Recommanded)</i></b> can be easily re-imported.</li><li><i>CSV (Complex)</i> easier to manipulate data as long as you have a local copy.<br>See <a href=https://github.com/systems-cs-pub-ro/quiz-manager-moodle#custom-quiz-moodle-plugin> README.md </a> for more information</li></ol>';
