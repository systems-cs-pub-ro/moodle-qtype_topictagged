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
 * Question type class for the quizmanager question type.
 *
 * @package    qtype
 * @subpackage quizmanager
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /*https://docs.moodle.org/dev/Question_types#Question_type_and_question_definition_classes*/


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/quizmanager/question.php');


/**
 * The quizmanager question type.
 *
 * @copyright  2021 Andrei David; Ștefan Jumărea

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_quizmanager extends question_type {

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
     * Insert data in table.
     * If data is already present, update
     */
    private function insert_or_update_record($table, $record) {
        global $DB;

        $old_record = $DB->get_record_sql('
            SELECT id
            FROM {' . $table . '}
            WHERE questionid = ' . $record['questionid'] . ';
        ');

        if ($old_record) {
            $record['id'] = intval($old_record->id);
            $DB->update_record($table, $record);
        }
        else {
            $DB->insert_record($table, $record);
        }
    }

     /**
     * @param stdClass $question
     * @param array $form
     * @return object
     */
    public function save_question($question, $form) {
        if ($form->action == '0') {
            // Add Quesiton
            global $DB;

            $form->name = '';
            list($category) = explode(',', $form->category);

            if (!$form->includesubcategories) {
                if ($DB->record_exists('question_categories', ['id' => $category, 'parent' => 0])) {
                    // The chosen category is a top category.
                    $form->includesubcategories = true;
                }
            }

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
            $questiondifficulty = $difficultyoptions[$form->setdifficulty];

            $form->questiontext = array(
                'text'	 => $form->settags[0] . "-" . $questiondifficulty,
                'format' => 0
            );

            // Name is not a required field for random questions, but
            // parent::save_question Assumes that it is.
            return parent::save_question($question, $form);
        }
        else if ($form->action == '1') {
            // Sync DB

            $categoryid = strtok($form->category, ',');
            // Get all question from category having the tag `last_used` set
            global $DB;
            $query = '
                SELECT all_entries.itemid, all_entries.name
                FROM (
                    SELECT tag_instance.itemid, tag.name, tag_instance.contextid
                    FROM {tag} tag
                    JOIN {tag_instance} tag_instance
                    ON tag.id = tag_instance.tagid
                WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0
                    AND tag.name like "last_used%"
                ) AS all_entries
                JOIN {question} question
                ON question.id = all_entries.itemid
                WHERE question.category = ' . $categoryid . ';
            ';

            // iterate through question
            $records = $DB->get_records_sql($query);
            foreach ($records as $raw_record) {
                $record = [];
                $record['questionid'] = $raw_record->itemid;
                $record['lastused'] = intval(substr($raw_record->name, 10));
                $this->insert_or_update_record('question_quizmanager', $record);
            }

            // Display confirmation message and redirect to previous page
            echo '
                <script>
                    alert("Sync successful\n");
                    window.location.href = "' . $form->returnurl . '";
                </script>
            ';
            die();
        }
        else if ($form->action == '2') {
            // Download CSV

            $categoryid = strtok($form->category, ',');
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
            $string = "question_text,answers,last_used\n";

            // Iterate trough questions and create csv string
            foreach ($entries as $entry) {
                $plaintext = $entry->question_text . $entry->answers;
                $string .= '"' . $entry->question_text . '"';
                $string .= ',';
                $string .= hash("sha256", $plaintext, false);
                $string .= ',';
                $string .= $entry->last_used;
                $string .= "\n";
            }

            // Set file options
            require_login($course, true);
            $fs = get_file_storage();

            $fileinfo = array(
                'contextid' => $question->contextid,
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
            $fs->create_file_from_string($fileinfo, $string);
            $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

            $options = [];
            $options['dontdie'] = true;
            send_stored_file($file, 0, 0, false, $options);

            echo '
                <script>
                    window.location.href = "' . $form->returnrul . '";
                </script>
            ';
            die();
        }
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
        $updateobject->name = $this->question_name($category, $question->includesubcategories, $question->fromtags);
	$updateobject->topic = $question->settags[0];
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

    /**
     * Random questions always get a question name that is Random (cateogryname).
     * This function is a centralised place to calculate that, given the category.
     * @param stdClass $category the category this question picks from. (Only ->name is used.)
     * @param bool $includesubcategories whether this question also picks from subcategories.
     * @param string[] $tagnames Name of tags this question picks from.
     * @return string the name this question should have.
     */
    public function question_name($category, $includesubcategories, $tagnames = []) {
        $categoryname = '';
        if ($category->parent && $includesubcategories) {
            $stringid = 'randomqplusname';
            $categoryname = shorten_text($category->name, 100);
        } else if ($category->parent) {
            $stringid = 'randomqname';
            $categoryname = shorten_text($category->name, 100);
        } else if ($includesubcategories) {
            $context = context::instance_by_id($category->contextid);

            switch ($context->contextlevel) {
                case CONTEXT_MODULE:
                    $stringid = 'randomqplusnamemodule';
                    break;
                case CONTEXT_COURSE:
                    $stringid = 'randomqplusnamecourse';
                    break;
                case CONTEXT_COURSECAT:
                    $stringid = 'randomqplusnamecoursecat';
                    $categoryname = shorten_text($context->get_context_name(false), 100);
                    break;
                case CONTEXT_SYSTEM:
                    $stringid = 'randomqplusnamesystem';
                    break;
                default: // Impossible.
            }
        } else {
            // No question will ever be selected. So, let's warn the teacher.
            $stringid = 'randomqnamefromtop';
        }

        if ($tagnames) {
            $stringid .= 'tags';
            $a = new stdClass();
            if ($categoryname) {
                $a->category = $categoryname;
            }
            $a->tags = implode(', ', array_map(function($tagname) {
                return explode(',', $tagname)[1];
            }, $tagnames));
        } else {
            $a = $categoryname ? : null;
        }

        $name = get_string($stringid, 'qtype_quizmanager', $a);

        return shorten_text($name, 255);
    }

    protected function set_selected_question_name($question, $randomname) {
        $a = new stdClass();
        $a->randomname = $randomname;
        $a->questionname = $question->name;
	$question->name = get_string('selectedby', 'qtype_quizmanager', $a);
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
        global $DB;
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
                WHERE question.category = ' . $categoryid . ';
        ';

        // TODO -> use get_records_sql in order to sort the elements or modify the query to do it
        $questionids = $DB->get_record_sql($query);
        // If no question with specified data is found, die
        // TODO -> remove the question last introduced, since the same error is thrown
        //      every time the user tries to acces/edit the quiz
        if (!$questionids) {
            echo ' <script> alert("No questions found"); </script> ';
            die();
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
        $categoryid = $questiondata->categoryobject->id;
	$topic = strtok($questiondata->questiontext, "-");
	$difficulty = strtok("\n");
        $available = $this->get_available_questions_with_tags($difficulty, $topic, $categoryid);
        shuffle($available);

        if ($forcequestionid !== null) {
            $forcedquestionkey = array_search($forcequestionid, $available);
            if ($forcedquestionkey !== false) {
                unset($available[$forcedquestionkey]);
                array_unshift($available, $forcequestionid);
            } else {
                throw new coding_exception('thisquestionidisnotavailable', $forcequestionid);
            }
        }

        foreach ($available as $questionid) {
            if (in_array($questionid, $excludedquestions)) {
                continue;
            }

            $question = question_bank::load_question($questionid, $allowshuffle);
            $this->set_selected_question_name($question, $questiondata->name);

            // Update last used tag after question use
            $record = [];
            $record['questionid'] = $question->id;
            $record['lastused'] = time();
            $this->insert_or_update_record('question_quizmanager', $record);

            return $question;
        }
        return null;
    }



    /**
     * executed at runtime (e.g. in a quiz or preview 
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
        parent::initialise_combined_feedback($question, $questiondata);
    }

   public function initialise_question_answers(question_definition $question, $questiondata,$forceplaintextanswers = true){ 
     //TODO
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'question_quizmanager') {
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
        $gapfillinfo = $pluginmanager->get_plugin_info('question_quizmanager');
        $output = parent::export_to_xml($question, $format);
        //TODO
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $output;
    }


    public function get_random_guess_score($questiondata) {
        // TODO.
        return 0;
    }

    public function get_possible_responses($questiondata) {
        // TODO.
        return array();
    }
}
