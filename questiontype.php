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
 * Question type class for the topictagged question type.
 *
 * @package    qtype
 * @subpackage topictagged
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/topictagged/question.php');
require_once($CFG->dirroot . '/question/type/questiontypebase.php');

require_once('utils.php');

/**
 * The topictagged question type.
 *
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_topictagged extends question_type {

    public function is_usable_by_random() {
        return false;
    }

    /**
     * This method needs to be called before the ->excludedqtypes and
     *      ->manualqtypes fields can be used.
     */
    protected function init_qtype_lists() {
        if (!is_null($this->excludedqtypes)) {
            return; // Already done.
        }
        $excludedqtypes = array();
        $manualqtypes = array();

        foreach (question_bank::get_all_qtypes() as $qtype) {
            $quotedname = "'" . $qtype->name() . "'";
            if (!$qtype->is_usable_by_random()) {
                $excludedqtypes[] = $quotedname;
            } else if ($qtype->is_manual_graded()) {
                $manualqtypes[] = $quotedname;
            }
        }
        $this->excludedqtypes = implode(',', $excludedqtypes);
        $this->manualqtypes = implode(',', $manualqtypes);
    }

    /* ties additional table fields to the database */
    public function extra_question_fields() {
        return null;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

     /**
     * @param stdClass $question
     * @param array $form
     * @return object
     */
    public function save_question($question, $form) {
        // Add Quesiton

        global $DB;
        $form->name = '';
        list($category) = explode(',', $form->category);
        $form->tags = array();

        if (empty($form->fromtags)) {
            $form->fromtags = array();
        }

        $difficultyoptions = [];
        $difficultyoptions[0] = 'Easy';
        $difficultyoptions[1] = 'Easy-Medium';
        $difficultyoptions[2] = 'Medium';
        $difficultyoptions[3] = 'Medium-Hard';
        $difficultyoptions[4] = 'Hard';
        $difficultyoptions[5] = 'Any difficulty';
        $questiondifficulty = $difficultyoptions[$form->setdifficulty];

        $form->questiontext = array(
            'text'	 => $form->settags . "###__###" . $questiondifficulty,
            'format' => 0
        );

        // Name is not a required field for random questions, but
        // parent::save_question Assumes that it is.
        return parent::save_question($question, $form);
    }

    public function save_question_options($question) {
        global $DB;

        // No options, as such, but we set the parent field to the question's
        // own id. Setting the parent field has the effect of hiding this
        // question in various places.
        $updateobject = new stdClass();
        $updateobject->id = $question->id;
        $updateobject->parent = $question->id;

        // We also force the question name to be 'Random (categoryname)'.
        $category = $DB->get_record('question_categories',
            array('id' => $question->category), '*', MUST_EXIST);
        $updateobject->topic = $question->settags;
        $updateobject->difficulty = $question->setdifficulty;

        return $DB->update_record('question', $updateobject);
    }

    /**
     * populates fields such as combined feedback 
     * also make $DB calls to get data from other tables
     */
   public function get_question_options($question) {
       parent::get_question_options($question);
       return true;
    }

    /**
     * During unit tests we need to be able to reset all caches so that each new test starts in a known state.
     * Intended for use only for testing. This is a stop gap until we start using the MUC caching api here.
     * You need to call this before every test that loads one or more random questions.
     */
    public function clear_caches_before_testing() {
        $this->availablequestionsbycategory = array();
    }

    protected function set_selected_question_name($question, $randomname) {
        $a = new stdClass();
        $a->randomname = $randomname;
        $a->questionname = $question->name;
        $question->name = get_string('selectedby', 'qtype_topictagged', $a);
    }

    /**
     * Get all the usable questions from a particular question category.
     *
     * @param int $categoryid the id of a question category.
     * @param bool whether to include questions from subcategories.
     * @param string $questionsinuse comma-separated list of question ids to
     *      exclude from consideration.
     * @return array of question records.
     */
    public function get_available_questions_from_category($categoryid, $subcategories) {
        if (isset($this->availablequestionsbycategory[$categoryid][$subcategories])) {
            return $this->availablequestionsbycategory[$categoryid][$subcategories];
        }

        $this->init_qtype_lists();
        if ($subcategories) {
            $categoryids = question_categorylist($categoryid);
        } else {
            $categoryids = array($categoryid);
        }

        $questionids = question_bank::get_finder()->get_questions_from_categories(
                $categoryids, 'qtype NOT IN (' . $this->excludedqtypes . ')');
        $this->availablequestionsbycategory[$categoryid][$subcategories] = $questionids;
        return $questionids;
    }

    /**
     * Gets all the questions from database with specific difficulty, tags and categoryid
     * @param string    question difficulty
     * @param string    question tags
     * @param int       category id from $questiondata object
     * @return array    ids of questions that fit the requirements
     */
    public function get_available_questions_with_tags($difficulty, $topic, $categoryid) {
        $db_utils = new \qtype_topictagged\database_utils();
        $questionids = $db_utils->get_questions($topic, $difficulty, $categoryid);

        /**
         * If there are no questions found with the coresponding topic and difficulty,
         * select a question only with the given difficulty.
         * If there are still no questions found, select a random question from the category.
         */
        if (count($questionids) == 0) {
            $questionids = $db_utils->get_questions('Any topic', $difficulty, $categoryid);

            if (count($questionids) == 0) {
                $questionids = $db_utils->get_questions('Any topic', 'Any diffculty', $categoryid);
            }
        }

        return $questionids;
    }

    public function make_question($questiondata) {
        return $this->choose_other_question($questiondata, array());
    }

    /**
     * Load the definition of another question picked randomly by this question.
     * @param object       $questiondata the data defining a random question.
     * @param array        $excludedquestions of question ids. We will no pick any question whose id is in this list.
     * @param bool         $allowshuffle      if false, then any shuffle option on the selected quetsion is disabled.
     * @param null|integer $forcequestionid   if not null then force the picking of question with id $forcequestionid.
     * @throws coding_exception
     * @return question_definition|null the definition of the question that was
     *      selected, or null if no suitable question could be found.
     */
    public function choose_other_question($questiondata, $excludedquestions, $allowshuffle = true, $forcequestionid = null) {
        // determine from trace if this quiz is a preview
        $contextid = $questiondata->contextid;
        $context = context::instance_by_id($contextid);
        $isPreview = has_capability('mod/quiz:preview', $context);

        $categoryid = $questiondata->categoryobject->id;
        $questionNameArray = explode("###__###", $questiondata->questiontext);
        $topic = $questionNameArray[0];
        $difficulty = $questionNameArray[1];
        $available = $this->get_available_questions_with_tags($difficulty, $topic, $categoryid);

        if ($forcequestionid !== null) {
            $forcedquestionkey = array_search($forcequestionid, $available);
            if ($forcedquestionkey !== false) {
                unset($available[$forcedquestionkey]);
                array_unshift($available, $forcequestionid);
            } else {
                throw new coding_exception('thisquestionidisnotavailable', $forcequestionid);
            }
        }

        if ($isPreview == true) {
            shuffle($available);
            $questionid = $available[0];

            $question = question_bank::load_question($questionid->questionid, $allowshuffle);
            $this->set_selected_question_name($question, $questiondata->name);

            return $question;
        }

        foreach ($available as $questionid) {
            if (in_array($questionid, $excludedquestions)) {
                continue;
            }

            $question = question_bank::load_question($questionid->questionid, $allowshuffle);
            $this->set_selected_question_name($question, $questiondata->name);

            // Update last used tag after question use
            $record = [];
            $record['questionid'] = $question->id;
            $record['lastused'] = time();

            $utils = new \qtype_topictagged\utils();

            if (!$isPreview) {
                $utils->insert_or_update_record('question_topictagged', $record);
                // get all tags
                $question_tags = core_tag_tag::get_item_tags_array('core_question', 'question', $questionid->questionid);

                $old_tag = '';

                // search for `last_used` tag
                foreach ($question_tags as $tag) {
                    if (substr($tag, 0, 10) == 'last_used:') {
                        $old_tag = $tag;
                    }
                }
                if ($old_tag != '') {
                    // delete tag
                    core_tag_tag::remove_item_tag('core_question', 'question', $questionid->questionid, $old_tag);
                }

                // add new `last_used` tag
                core_tag_tag::add_item_tag('core_question', 'question', $questionid->questionid, $context, 'last_used:' . $record['lastused']);
            }
            return $question;
        }
        return null;
    }

    /**
     * executed at runtime (e.g. in a quiz or preview)
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
        parent::initialise_combined_feedback($question, $questiondata);
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'question_topictagged') {
            return false;
        }
        $question = parent::import_from_xml($data, $question, $format, null);
        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true, false, $format->get_format($question->questiontextformat));

        return $question;
    }

    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        global $CFG;
        $pluginmanager = core_plugin_manager::instance();
        $gapfillinfo = $pluginmanager->get_plugin_info('question_topictagged');
        $output = parent::export_to_xml($question, $format);
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $output;
    }
}

