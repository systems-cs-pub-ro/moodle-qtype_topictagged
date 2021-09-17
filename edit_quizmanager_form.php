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
    private $difficulty = "";
    private $topic = "";

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

        //Hides default name, text, id and grade forms
        $mform->addElement('html', '
                <script>
                    document.getElementById("id_name").value = \'defaultnamejs\';
                    document.getElementById("fitem_id_name").style.display = \'none\';
                    document.getElementById("id_questiontext").value= \'defaulttextjs\';
                    document.getElementById("fitem_id_questiontext").style.display = \'none\';
                    document.getElementById("id_defaultmark").value = \'1\';
                    document.getElementById("fitem_id_defaultmark").style.display = \'none\';
                    document.getElementById("fitem_id_generalfeedback").style.display = \'none\';
                    document.getElementById("fitem_id_idnumber").style.display = \'none\';
                    document.getElementById("id_tagsdheader").style.display = \'none\';
                </script>
        ');

        $actions = [];
        $actions[0] = get_string('addquestion', 'qtype_quizmanager');
        $actions[1] = get_string('syncdb', 'qtype_quizmanager');
        $actions[2] = get_string('downloadcsv', 'qtype_quizmanager');

        $mform->addElement('select', 'action', get_string('action', 'qtype_quizmanager'), $actions);
        $mform->setType('action', PARAM_INT);
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
