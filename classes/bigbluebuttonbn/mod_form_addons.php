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

namespace bbbext_bnnotify\bigbluebuttonbn;

use bbbext_bnnotify\utils;
use context;
use html_writer;
use mod_bigbluebuttonbn\instance;
use MoodleQuickForm;
use pix_icon;
use stdClass;

/**
 * A class for the main mod form extension
 *
 * @package   bbbext_bnnotify
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class mod_form_addons extends \mod_bigbluebuttonbn\local\extension\mod_form_addons {

    /**
     * Max File size for the editor.
     */
    const MAX_FILE_SIZE = 1024 * 1024;

    /**
     * Constructor
     *
     * @param MoodleQuickForm $mform
     * @param stdClass|null $bigbluebuttonbndata
     * @param string|null $suffix
     */
    public function __construct(MoodleQuickForm &$mform, ?stdClass $bigbluebuttonbndata = null, ?string $suffix = null) {
        parent::__construct($mform, $bigbluebuttonbndata, $suffix);
        // Supplement BBB data with additional information.
        if (!empty($bigbluebuttonbndata->id)) {
            $data = $this->retrieve_additional_data($bigbluebuttonbndata->id);
            $this->bigbluebuttonbndata = (object) array_merge((array) $this->bigbluebuttonbndata, $data);
            $this->bigbluebuttonbndata->bnnotify_paramcount = count($data["bnnotify_timespan"] ?? []);
        }
    }

    /**
     * Retrieve data from the database if any.
     *
     * @param int $id
     * @return array
     */
    private function retrieve_additional_data(int $id): array {
        global $DB;
        $data = [];
        $bnnotifyrecords = $DB->get_records(mod_instance_helper::SUBPLUGIN_REMINDERS_TABLE, ['bigbluebuttonbnid' => $id]);
        if ($bnnotifyrecords) {
            $bnnotifyrecords = array_values($bnnotifyrecords);
            foreach ($bnnotifyrecords as $bnnotifyrecord) {
                $data["bnnotify_timespan"][] = $bnnotifyrecord->timespan ?? '';
            }
        }
        $bnnotifyrecord = $DB->get_record(mod_instance_helper::SUBPLUGIN_TABLE, ['bigbluebuttonbnid' => $id]);
        $data["bnnotify_reminderenabled"] = $bnnotifyrecord->reminderenabled ?? false;
        $data["bnnotify_remindertoguestsenabled"] = $bnnotifyrecord->remindertoguestsenabled ?? false;
        $data["bnnotify_id"] = $bnnotifyrecord->id ?? 0;
        return $data;
    }

    /**
     * Allows modules to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data passed by reference
     */
    public function data_postprocessing(stdClass &$data): void {
    }

    /**
     * Get the context.
     *
     * @return context|null
     */
    private function get_context(): ?context {
        if (!empty($this->bigbluebuttonbndata->id)) {
            return instance::get_from_instanceid($this->bigbluebuttonbndata->id)->get_context();
        }
        return null;
    }

    /**
     * Get the editor options.
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_editor_options() {
        $context = $this->get_context();
        if (!empty($context)) {
            $maxbytes = get_course($context->get_course_context()->instanceid)->maxbytes;
            $maxbytes = $maxbytes > 0 ? $maxbytes : self::MAX_FILE_SIZE;
        } else {
            $maxbytes = self::MAX_FILE_SIZE;
        }
        $options = [
            'trusttext' => $context ? has_capability('moodle/site:trustcontent', $context) : false,
            'subdirs' => false,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $maxbytes,
        ];

        if (!empty($context)) {
            $options['context'] = $context;
        }
        return $options;
    }

    /**
     * Allow module to modify the data at the pre-processing stage.
     *
     * This method is also called in the bulk activity completion form.
     *
     * @param array|null $defaultvalues
     */
    public function data_preprocessing(?array &$defaultvalues): void {
        // This is where we can add the data from the bnnotify table to the data provided.
        if (!empty($defaultvalues['id'])) {
            $data = $this->retrieve_additional_data(intval($defaultvalues['id']));
            $defaultvalues = array_merge($defaultvalues, $data);
        }
    }

    /**
     * Can be overridden to add custom completion rules if the module wishes
     * them. If overriding this, you should also override completion_rule_enabled.
     * <p>
     * Just add elements to the form as needed and return the list of IDs. The
     * system will call disabledIf and handle other behaviour for each returned
     * ID.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules(): array {
        return [];
    }

    /**
     * Called during validation. Override to indicate, based on the data, whether
     * a custom completion rule is enabled (selected).
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are;
     *   default returns false
     */
    public function completion_rule_enabled(array $data): bool {
        return false;
    }

    /**
     * Form adjustments after setting data
     *
     * @return void
     */
    public function definition_after_data() {
        // After data.
        $isdeleting = optional_param_array('bnnotify_paramdelete', [], PARAM_RAW);
        // Get the index of the delete button that was pressed.
        if (!empty($isdeleting)) {
            $firstindex = array_key_first($isdeleting);
            // Then reassign values from the deleted group to the previous group.
            $paramcount = optional_param('bnnotify_paramcount', 0, PARAM_INT);
            for ($index = $firstindex; $index < $paramcount; $index++) {
                $nextindex = $index + 1;
                if ($this->mform->elementExists("bnnotify_timespan[{$nextindex}]")) {
                    $this->mform->getElement("bnnotify_timespan[$index]")
                        ->setValue($this->mform->getElementValue("bnnotify_timespan[$nextindex]"));
                }
            }
            $newparamcount = $paramcount - 1;
            $this->mform->removeElement("bnnotify_paramgroup[{$newparamcount}]");
            $this->mform->getElement('bnnotify_paramcount')->setValue($newparamcount);
        }
    }

    /**
     * Add new form field definition
     */
    public function add_fields(): void {
        global $OUTPUT;
        $this->mform->addElement('header', 'bnnotify', get_string('bnnotify:header', 'bbbext_bnnotify'));
        $this->mform->addHelpButton('bnnotify', 'bnnotify', 'bbbext_bnnotify');
        $this->mform->addElement('advcheckbox', 'bnnotify_reminderenabled',
            get_string('bnnotify:enabled', 'bbbext_bnnotify'));
        $this->mform->disabledIf('bnnotify_reminderenabled', 'openingtime[enabled]', 'notchecked', 0);
        $this->mform->setType('bnnotify_reminderenabled', PARAM_BOOL);

        $this->mform->addElement('advcheckbox', 'bnnotify_remindertoguestsenabled',
            get_string('bnnotify:guestenabled', 'bbbext_bnnotify'));
        $this->mform->disabledIf('bnnotify_remindertoguestsenabled', 'guestallowed', 'notchecked', 0);
        $this->mform->disabledIf('bnnotify_remindertoguestsenabled', 'openingtime[enabled]', 'notchecked', 0);
        $this->mform->hideIf('bnnotify_remindertoguestsenabled', 'bnnotify_reminderenabled', 'notchecked', 0);
        $this->mform->setType('bnnotify_remindertoguestsenabled', PARAM_BOOL);

        $paramcount = optional_param('bnnotify_paramcount',
            $this->bigbluebuttonbndata->bnnotify_paramcount ?? 0, PARAM_RAW);
        $paramcount += optional_param('bnnotify_addparamgroup', 0, PARAM_RAW) ? 1 : 0;
        $isdeleting = optional_param_array('bnnotify_paramdelete', [], PARAM_RAW);
        foreach ($isdeleting as $index => $value) {
            // This prevents the last delete button from submitting the form.
            $this->mform->registerNoSubmitButton("bnnotify_paramdelete[$index]");
        }
        $bellicon = new pix_icon('i/bell', get_string('timespan:bell', 'bbbext_bnnotify'), 'bbbext_bnnotify');
        for ($index = 0; $index < $paramcount; $index++) {
            $paramicon = $this->mform->createElement('html', $OUTPUT->render($bellicon));
            $paramname =
                $this->mform->createElement(
                    'select', "bnnotify_timespan[$index]",
                    get_string('timespan', 'bbbext_bnnotify'),
                    utils::get_timespan_options()
                );
            $paramtext = $this->mform->createElement(
                'html', html_writer::span(get_string('reminder:message', 'bbbext_bnnotify'), 'mx-3'));
            $paramdelete = $this->mform->createElement('submit', "bnnotify_paramdelete[$index]",
                get_string('delete'), [], false, ['customclassoverride' => 'btn btn-secondary float-left']);

            $this->mform->addGroup([$paramicon, $paramname, $paramtext, $paramdelete],
                "bnnotify_paramgroup[$index]", '', [' '], false);
            $this->mform->hideIf("bnnotify_paramgroup[$index]", 'bnnotify_reminderenabled', 'notchecked', 0);
            $this->mform->disabledIf("bnnotify_paramgroup[$index]", 'openingtime[enabled]', 'notchecked', 0);
            $this->mform->setType("bnnotify_timespan[$index]", PARAM_ALPHANUM);
            $this->mform->setType("bnnotify_paramdelete[$index]", PARAM_RAW);
            $this->mform->disabledIf("bnnotify_timespan[$index]", 'openingtime[enabled]');
            $this->mform->registerNoSubmitButton("bnnotify_paramdelete[$index]");
        }
        // Add a button to add new param groups.
        $this->mform->addElement('submit', 'bnnotify_addparamgroup', get_string('addreminder', 'bbbext_bnnotify'));
        $this->mform->hideIf('bnnotify_addparamgroup', 'bnnotify_reminderenabled');
        $this->mform->disabledIf('bnnotify_addparamgroup', 'openingtime[enabled]', 'notchecked', 0);
        $this->mform->setType('bnnotify_addparamgroup', PARAM_TEXT);
        $this->mform->registerNoSubmitButton('bnnotify_addparamgroup');
        $this->mform->addElement('hidden', 'bnnotify_paramcount');
        $this->mform->setType('bnnotify_paramcount', PARAM_INT);
        $this->mform->setConstants(['bnnotify_paramcount' => $paramcount]);

        // Add the original or default openingtime to validate if it changes between updates.
        $this->mform->addElement('hidden', 'bnnotify_openingtime');
        $this->mform->setType('bnnotify_openingtime', PARAM_INT);
        $this->mform->setConstants(['bnnotify_openingtime' => $this->bigbluebuttonbndata->openingtime ?? 0]);
    }

    /**
     * Validate form and returns an array of errors indexed by field name
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation(array $data, array $files): array {
        $errors = [];
        if (!empty($data['bnnotify_timespan'])) {
            // Check if we have duplicate values in array.
            $unique = array_unique($data['bnnotify_timespan']);
            if (count($unique) !== count($data['bnnotify_timespan'])) {
                // Find the second occurence of the duplicate value.
                $duplicates = array_diff_assoc($data['bnnotify_timespan'], $unique);
                if (!empty($duplicates)) {
                    $firstduplicatekey = array_key_first($duplicates);
                    $errors["bnnotify_addparamgroup"] = get_string('error:duplicate', 'bbbext_bnnotify');
                }
            }
        }
        return $errors;
    }
}
