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
 * Activity renderer class.
 *
 * @package   qtype_topictagged
 * @copyright  2021 Andrei David; Ștefan Jumărea
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_topictagged\output;

use moodle_url;
use renderable;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

/**
 * Renderer class for list of activities.
 */
class simple_form extends \moodleform {

    protected $courseid;

    /**
     * Constructor for renderer.
     *
     * @param int $courseid Course ID
     */
    public function __construct(int $courseid) {
	$return_url = '/question/type/topictagged/index.php?id=' . $courseid;
        $this->courseid = $courseid;
	parent::__construct($return_url);
    }

    public function definition() {
	$mform = $this->_form;
	$contexts = [\context_course::instance($this->courseid)];

	// Update database entries
	$mform->addElement('header', 'update_header', get_string('update_header', 'qtype_topictagged'));
        $objs = array();
	$objs[] = $mform->createElement('questioncategory', 'update_category', get_string('question_cat', 'qtype_topictagged'), array('contexts' => $contexts));
        $group = $mform->addElement('group', 'category_group', '', $objs, array('&nbsp;'), false);
        $mform->addElement('submit', 'update_button', get_string('update_button', 'qtype_topictagged'));
	$mform->closeHeaderBefore('download_header');

	// Download questions
	$mform->addElement('header', 'download_header', get_string('download_header', 'qtype_topictagged'));
        $objs = array();
	$objs[] = $mform->createElement('questioncategory', 'download_category', get_string('question_cat', 'qtype_topictagged'), array('contexts' => $contexts));
        $group = $mform->addElement('group', 'category_group', '', $objs, array('&nbsp;'), false);

        $objs = array();
	$objs[] = $mform->createElement('static', 'fileformat_label', 'file_format', get_string('download_mode', 'qtype_topictagged'));
	$objs[] = $mform->createElement('select', 'download_mode', null, ['MXML', 'CSV']); 
        $group = $mform->addElement('group', 'fileformat_group', '', $objs, array('&nbsp;'), false);
        $mform->addElement('submit', 'download_button', get_string('download_button', 'qtype_topictagged'));

	$mform->addHelpButton('download_header', 'download', 'qtype_topictagged');
	$mform->addHelpButton('update_header', 'update_header', 'qtype_topictagged');
    }
}
