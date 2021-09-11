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
 * Defines the editing form for the quizmanager question type.
 *
 * @package    qtype
 * @subpackage quizmanager
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * quizmanager question editing form definition.
 *
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_quizmanager_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        //Add fields specific to this question type
        //remove any that come with the parent class you don't want

        //Add difficulty field
        $difficultyoptions = array(
            'multiple' => false,
            'tags' => true,
            'noselectionstring' => get_string('setdifficultyempty', 'qtype_quizmanager'),
        );
        $mform->addElement('autocomplete', 'setdifficulty', get_string('setdifficulty', 'qtype_quizmanager'),
            null, $difficultyoptions);

        //Add tags field
        $autocompleteoptions = array(
            'multiple' => true,
            'tags' => true,
            'noselectionstring' => get_string('settagsempty', 'qtype_quizmanager'),
        );
        $mform->addElement('autocomplete', 'settags', get_string('settags', 'qtype_quizmanager'),
            null, $autocompleteoptions);

        // To add combined feedback (correct, partial and incorrect).
        $this->add_combined_feedback_fields(true);
        // Adds hinting features.
        $this->add_interactive_settings(true, true);
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function qtype() {
        return 'quizmanager';
    }
}