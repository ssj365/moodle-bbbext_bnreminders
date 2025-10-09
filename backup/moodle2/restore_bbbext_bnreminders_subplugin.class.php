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
 * Provides the information for restore.
 *
 * @package   bbbext_bnreminders
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class restore_bbbext_bnreminders_subplugin extends restore_subplugin {
    /**
     * Returns the paths to be handled by the subplugin.
     *
     * @return array
     */
    protected function define_bigbluebuttonbn_subplugin_structure() {
        $paths = [];

        $elename = $this->get_namefor('');
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/bbbext_bnreminders');
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = $this->get_namefor('rem');
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/bbbext_bnreminders_rem');
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = $this->get_namefor('guests');
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/bbbext_bnreminders_guests');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    /**
     * Process a bigbluebuttonbn_guests restore (additional table).
     *
     * @param array $data The data in object form
     * @return void
     */
    public function process_bbbext_bnreminders_guests(array $data) {
        global $DB;
        $data = (object) $data;
        // Apply modifications.
        $data->bigbluebuttonbnid = $this->get_new_parentid('bigbluebuttonbn');
        $data->usermodified = $this->get_mappingid('user', $data->usermodified);
        $data->userfrom = $this->get_mappingid('user', $data->userfrom);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        // Insert the bigbluebuttonbn_logs record.
        $newitemid = $DB->insert_record('bbbext_bnreminders_guests', $data);
        // Immediately after inserting associated record, call this.
        $this->set_mapping('bbbext_bnreminders_guests', $data->id, $newitemid);
    }

    /**
     * Processes one subplugin instance additional parameter (enabled or not).
     *
     * @param mixed $data
     */
    public function process_bbbext_bnreminders($data) {
        global $DB;

        $data = (object) $data;
        $data->bigbluebuttonbnid = $this->get_new_parentid('bigbluebuttonbn');
        $DB->insert_record('bbbext_bnreminders', $data);
    }

    /**
     * Processes one subplugin instance additional parameter (reminders).
     *
     * @param mixed $data
     */
    public function process_bbbext_bnreminders_rem($data) {
        global $DB;

        $data = (object) $data;
        $data->bigbluebuttonbnid = $this->get_new_parentid('bigbluebuttonbn');
        $DB->insert_record('bbbext_bnreminders_rem', $data);
    }
}
