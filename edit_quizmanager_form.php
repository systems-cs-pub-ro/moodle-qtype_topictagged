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
        $difficultyoptions = [];
        $difficultyoptions[0] = 'Easy';
        $difficultyoptions[1] = 'Easy-Medium';
        $difficultyoptions[2] = 'Medium';
        $difficultyoptions[3] = 'Medium-Hard';
        $difficultyoptions[4] = 'Hard';
        $mform->addElement('select', 'setdifficulty', get_string('setdifficulty', 'qtype_quizmanager'),
            $difficultyoptions);

        //Add tags field
        $autocompleteoptions = array(
            'multiple' => true,
            'tags' => true,
            'noselectionstring' => get_string('settagsempty', 'qtype_quizmanager'),
        );
        $mform->addElement('autocomplete', 'settags', get_string('settags', 'qtype_quizmanager'),
            null, $autocompleteoptions);

        //Hide default name, text, id and grade forms
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

    public function validation($fromform, $files) {
        // Check if a question exists having the selected difficulty, topic and category
        global $DB;
        $mform = $this->_form;
        $difficultyoptions = [];
        $difficultyoptions[0] = 'Easy';
        $difficultyoptions[1] = 'Easy-Medium';
        $difficultyoptions[2] = 'Medium';
        $difficultyoptions[3] = 'Medium-Hard';
        $difficultyoptions[4] = 'Hard';
        $difficulty = $difficultyoptions[intval($fromform->setdifficulty)];
        $topic = $fromform["settags"][0];
        $categoryid = strtok($fromform["category"], ',');
        $query = '
            SELECT tag_instance.itemid
            FROM {tag} tag
                JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
            WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper("' . $difficulty . '")) = 0
            INTERSECT
            SELECT tag_instance.itemid
            FROM {tag} tag
                JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
            WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper("' . $topic . '")) = 0
            INTERSECT
            SELECT question.id
            FROM {tag_instance} tag_instance
                JOIN {question} question ON question.id = tag_instance.itemid
            WHERE question.category = ' . $categoryid . '
        ';


        $questionids = $DB->get_record_sql($query);
        // If no question with specified data is found, the question will not be saved
        if (!$questionids) {
            echo '
                <script>
                    alert("No questions found having the selected difficulty and topic");
                    window.location.href = "' . $fromform["returnurl"] . '";
                </script>
            ';
            die();
            return false;
        }       
    }

    public function qtype() {
        return 'quizmanager';
    }
}
