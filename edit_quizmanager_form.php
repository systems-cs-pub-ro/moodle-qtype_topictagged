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
	global $DB, $CFG;

        //Add difficulty field
        $difficultyoptions = [];
        $difficultyoptions[0] = 'Easy';
        $difficultyoptions[1] = 'Easy-Medium';
        $difficultyoptions[2] = 'Medium';
        $difficultyoptions[3] = 'Medium-Hard';
        $difficultyoptions[4] = 'Hard';
        $mform->addElement('select', 'setdifficulty', get_string('setdifficulty', 'qtype_quizmanager'),
            $difficultyoptions);

	// Get all tags used in the current context to use as selection list for the topic
        $tags = \core_tag_tag::get_tags_by_area_in_contexts('core_question', 'question', $this->contexts->all());
        $tagstrings = [];
        foreach ($tags as $tag) {
            $tagstrings[$tag->name] = $tag->name;
        }

        $showstandard = core_tag_area::get_showstandard('core_question', 'question');
        if ($showstandard != core_tag_tag::HIDE_STANDARD) {
            $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';
            $standardtags = $DB->get_records('tag',
                    array('isstandard' => 1, 'tagcollid' => core_tag_area::get_collection('core', 'question')),
                    $namefield, 'id,' . $namefield);
            foreach ($standardtags as $standardtag) {
			$tagstrings[$standardtag->$namefield] = $standardtag->$namefield;
            }
        }

	// Remove all difficulty and last_used tags from the list
	foreach ($tagstrings as $standardtag) {
		if (strpos($standardtag, "last_used") !== false)
			unset($tagstrings[$standardtag]);

		foreach ($difficultyoptions as $diffoption) {
			if (strcasecmp($standardtag, $diffoption) == 0)
				unset($tagstrings[$standardtag]);
		}
	}

	$tags_form = $mform->addElement('select', 'settags',  get_string('settags', 'qtype_quizmanager'), $tagstrings);
	$mform->addRule('settags', get_string('settagsempty', 'qtype_quizmanager'), 'required', null, 'server');
	
	$mform->addHelpButton('setdifficulty', 'setdifficulty', 'qtype_quizmanager');
	$mform->addHelpButton('settags', 'settags', 'qtype_quizmanager');

	// Add text containing the total number of available questions
	$mform->addElement('html', '
		<div class="form-control">
			<label id=id_availablequestions > ' . get_string('available_questoins', 'qtype_quizmanager') . '</label>
			<b> <label id=id_availablequestions_count></label> </b>
		</div>
	');

	$categories = array();
	foreach ($mform->_elements[1]->_optGroups as $category) {
		foreach ($category['options'] as $option) {
			$value = $option["attr"]["value"];
			$categoryid = strtok($value, ',');
			array_push($categories, $categoryid);
		}
	}
	

	// query data
	$questions_number = array();
	for ($category = 0; $category < count($categories); $category++) {
		$difficulties = array();
		foreach($difficultyoptions as $difficulty) {
			$topics = array();
			foreach($tagstrings as $topic) {
				$value = 0;
				// Actual Query
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
					WHERE question.category = ' . $categories[$category] . ' AND question.hidden = 0          
				 ';
				$questionids = $DB->get_records_sql($query);
				$value = count($questionids);			
				$topics[$topic] = $value;
			}
			$difficulties[$difficulty] = $topics;
		}
		$questions_number[$category] = $difficulties;
	}
	/*
	echo("<pre>"); 
	var_dump($query);
	echo("</pre>"); die();
	 */
	$mform->addElement('html', '
		<noscript id=id_json>' . json_encode($questions_number) . '</noscript>
                <script src=type/quizmanager/display_count.js></script>
        ');
 
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
        $difficulty = $difficultyoptions[intval($fromform['setdifficulty'])];
        $topic = $fromform["settags"];
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

        $questionids = $DB->get_records_sql($query);
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
