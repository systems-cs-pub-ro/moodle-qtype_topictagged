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
 * Defines the editing form for the topictagged question type.
 *
 * @package    qtype
 * @subpackage topictagged
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once('utils.php');

/**
 * topictagged question editing form definition.
 *
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_topictagged_edit_form extends question_edit_form {
    private $difficulty = "";
    private $topic = "";
    private $difficultyoptions = ['Easy', 'Easy-Medium', 'Medium', 'Medium-Hard', 'Hard', 'Any difficulty'];

    protected function definition_inner($mform) {
        //Add fields specific to this question type
        //remove any that come with the parent class you don't want
	global $DB, $CFG;

    $db_utils = new \qtype_topictagged\database_utils();

    //Add difficulty field
    $mform->addElement('select', 'setdifficulty', get_string('setdifficulty', 'qtype_topictagged'),
        $this->difficultyoptions);

	// Get all tags used in the current context to use as selection list for the topic
        $tags = \core_tag_tag::get_tags_by_area_in_contexts('core_question', 'question', $this->contexts->all());
        $tagstrings = [];
	$tagstrings['Any topic'] = 'Any topic';
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

		foreach ($this->difficultyoptions as $diffoption) {
			if (strcasecmp($standardtag, $diffoption) == 0)
				unset($tagstrings[$standardtag]);
		}
	}

	$tags_form = $mform->addElement('select', 'settags',  get_string('settags', 'qtype_topictagged'), $tagstrings);
	
	$mform->addHelpButton('setdifficulty', 'setdifficulty', 'qtype_topictagged');
	$mform->addHelpButton('settags', 'settags', 'qtype_topictagged');

	// Add text containing the total number of available questions
	$mform->addElement('html', '
		<div class="form-control">
			<label id=id_availablequestions > ' . get_string('available_questoins', 'qtype_topictagged') . '</label>
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
	// for each category
	for ($category = 0; $category < count($categories); $category++) {
		$difficulties = array();
		// for each difficulty
		foreach($this->difficultyoptions as $difficulty) {
			$topics = array();

			// for each topic
			foreach($tagstrings as $topic) {
				// count available questions
                $value = $db_utils->count_questions($topic, $difficulty, $categories[$category]);
				$topics[$topic] = $value;
			}
			$difficulties[$difficulty] = $topics;
		}
		$questions_number[$category] = $difficulties;
    }

	// export as JSON to a hidden text HTML tag
	// add JS script
	$mform->addElement('html', '
		<noscript id=id_json>' . json_encode($questions_number) . '</noscript>
                <script src=type/topictagged/display_count.js></script>
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
                </script>
        ');

    }

    public function add_action_buttons($cancel = true, $submitlabel = null) {
	parent::add_action_buttons($cancel, $submitlabel);

	$mform = $this->_form;
	$mform->addElement('html', '
		<script>
			document.getElementById("id_updatebutton").style.display = \'none\';
			document.getElementById("id_tagsheader").style.display = \'none\';
		</script>');
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
        $difficulty = $this->difficultyoptions[intval($fromform['setdifficulty'])];
        $topic = $fromform["settags"];
        $categoryid = strtok($fromform["category"], ',');

    $db_utils = new \qtype_topictagged\database_utils();
	// Create the query
	// Treat the "Any topic" and "Any difficulty" options separately
    $questionids = $db_utils->get_questions($topic, $difficulty, $categoryid);
        // If no question with specified data is found, the question will not be saved
        if (!$questionids) {
            echo '
                <script>
                    alert("' . get_string('noquestions', 'qtype_topictagged') . '");
                    window.location.href = "' . $fromform["returnurl"] . '";
                </script>
            ';
            die();
            return false;
        }
    }

    public function qtype() {
        return 'topictagged';
    }
}
