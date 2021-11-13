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
 * @package   qtype_quizmanager
 * @copyright  2021 Andrei David; Ștefan Jumărea
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_quizmanager;

defined('MOODLE_INTERNAL') || die();

class utils {
	/**
	* Insert data in table.
	* If data is already present, update
	*/
	public function insert_or_update_record($table, $record, $insert_only = False) {
		global $DB;

		$old_record = $DB->get_record_sql('
		    SELECT id
		    FROM {' . $table . '}
		    WHERE questionid = ' . $record['questionid'] . ';
		');

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

	/**
	 * Parse string and format it for a CSV file
	 */
	public function parse_string_for_csv($raw_values, $delimiter = ',', $enclosure = '"') {
		// Create a temporary file to use fputcsv in
		$tmp_fp = fopen('php://temp.csv', 'r+');

		fputcsv($tmp_fp, $raw_values, $delimiter, $enclosure);

		// Take the string from the temporary file
		rewind($tmp_fp);
		$data = fgets($tmp_fp);

		fclose($tmp_fp);
		return $data;
	}
}


