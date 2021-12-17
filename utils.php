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
 * Utils
 *
 * @package   qtype_topictagged
 * @copyright  2021 Andrei David; Ștefan Jumărea
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_topictagged;

defined('MOODLE_INTERNAL') || die();

class utils {
    /**
    * Insert data in table.
    * If data is already present, update
    */
    public function insert_or_update_record($table, $record, $insert_only = False) {
        global $DB;

        $old_record = $DB->get_record($table, ['questionid' => $record['questionid']]);

        if ($old_record) {
            if (!$insert_only) {
                $record['id'] = intval($old_record->id);
                $DB->update_record($table, $record);
            }
        }
        else {
            $DB->insert_record($table, $record);
        }
    }
}

class database_utils {
    /**
     * Get all questions having the selected topic and difficulty
     */
    public function get_questions($topic, $difficulty, $categoryid) {
        global $DB;

        // SQL Queries
        $sql_questionids_anytopic_anydifficulty = '
                SELECT id from {question}
                WHERE category = :categoryid AND hidden = 0 AND qtype != "topictagged" AND qtype != "random"
            ';

        $sql_questionids_anydifficulty = '
                SELECT tag_instance.itemid
                FROM {tag} tag
                    JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
                WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:topic)) = 0
                INTERSECT
                SELECT question.id
                FROM {tag_instance} tag_instance
                    JOIN {question} question ON question.id = tag_instance.itemid
                WHERE question.category = :categoryid AND question.hidden = 0
             ';

        $sql_questionids_anytopic = '
                SELECT tag_instance.itemid
                FROM {tag} tag
                    JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
                WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:difficulty)) = 0
                INTERSECT
                SELECT question.id
                FROM {tag_instance} tag_instance
                    JOIN {question} question ON question.id = tag_instance.itemid
                WHERE question.category = :categoryid AND question.hidden = 0
             ';

        $sql_questionids = '
                SELECT tag_instance.itemid
                FROM {tag} tag
                    JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
                WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:difficulty)) = 0
                INTERSECT
                SELECT tag_instance.itemid
                FROM {tag} tag
                    JOIN {tag_instance} tag_instance ON tag.id = tag_instance.tagid
                WHERE strcmp(upper(tag_instance.itemtype), \'QUESTION\') = 0 AND strcmp(upper(tag.name), upper(:topic)) = 0
                INTERSECT
                SELECT question.id
                FROM {tag_instance} tag_instance
                    JOIN {question} question ON question.id = tag_instance.itemid
                WHERE question.category = :categoryid AND question.hidden = 0
             ';
        // Actual Query
        // Treat the "Any topic" and "Any difficulty" options separately
        if ($difficulty == 'Any difficulty' && $topic == 'Any topic') {
            $query = $sql_questionids_anytopic_anydifficulty;
        } else if ($difficulty == 'Any difficulty') {
            $query = $sql_questionids_anydifficulty;
        } else if ($topic == 'Any topic') {
            $query = $sql_questionids_anytopic;
        } else {
            $query = $sql_questionids;
        }

        $questionids = $DB->get_records_sql($query,
            ['topic' => $topic, 'difficulty' => $difficulty, 'categoryid' => $categoryid]);

        return $questionids;
    }

    public function count_questions($topic, $difficulty, $categoryid) {
        return count($this->get_questions($topic, $difficulty, $categoryid));
    }
}
?>
