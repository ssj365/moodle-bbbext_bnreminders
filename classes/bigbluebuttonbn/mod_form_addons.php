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

namespace bbbext_bnemail\bigbluebuttonbn;

use bbbext_bnemail\utils;
use context;
use html_writer;
use mod_bigbluebuttonbn\instance;
use MoodleQuickForm;
use pix_icon;
use stdClass;

/**
 * A class for the main mod form extension
 *
 * @package   bbbext_bnemail
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
            $this->bigbluebuttonbndata->bnemail_paramcount = count($data["bnemail_timespan"] ?? []);
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
        $bnemailrecords = $DB->get_records(mod_instance_helper::SUBPLUGIN_REMINDERS_TABLE, ['bigbluebuttonbnid' => $id]);
        if ($bnemailrecords) {
            $bnemailrecords = array_values($bnemailrecords);
            foreach ($bnemailrecords as $bnemailrecord) {
                $data["bnemail_timespan"][] = $bnemailrecord->timespan ?? '';
            }
        }
        $bnemailrecord = $DB->get_record(mod_instance_helper::SUBPLUGIN_TABLE, ['bigbluebuttonbnid' => $id]);
        $data["bnemail_reminderenabled"] = $bnemailrecord->reminderenabled ?? false;
        $data["bnemail_remindertoguestsenabled"] = $bnemailrecord->remindertoguestsenabled ?? false;
        $data["bnemail_id"] = $bnemailrecord->id ?? 0;
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
        // This is where we can add the data from the bnemail table to the data provided.
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
        $isdeleting = optional_param_array('bnemail_paramdelete', [], PARAM_RAW);
        // Get the index of the delete button that was pressed.
        if (!empty($isdeleting)) {
            $firstindex = array_key_first($isdeleting);
            // Then reassign values from the deleted group to the previous group.
            $paramcount = optional_param('bnemail_paramcount', 0, PARAM_INT);
            for ($index = $firstindex; $index < $paramcount; $index++) {
                $nextindex = $index + 1;
                if ($this->mform->elementExists("bnemail_timespan[{$nextindex}]")) {
                    $this->mform->getElement("bnemail_timespan[$index]")
                        ->setValue($this->mform->getElementValue("bnemail_timespan[$nextindex]"));
                }
            }
            $newparamcount = $paramcount - 1;
            $this->mform->removeElement("bnemail_paramgroup[{$newparamcount}]");
            $this->mform->getElement('bnemail_paramcount')->setValue($newparamcount);
        }
    }

    /**
     * Add new form field definition
     */
    public function add_fields(): void {
        global $OUTPUT;
        $this->mform->addElement('header', 'bnemail', get_string('bnemail:header', 'bbbext_bnemail'));
        $this->mform->addHelpButton('bnemail', 'bnemail', 'bbbext_bnemail');
        $this->mform->addElement('advcheckbox', 'bnemail_reminderenabled', get_string('bnemail:enabled', 'bbbext_bnemail'));
        $this->mform->disabledIf('bnemail_reminderenabled', 'openingtime[enabled]', 'notchecked', 0);
        $this->mform->setType('bnemail_reminderenabled', PARAM_BOOL);

        $this->mform->addElement('advcheckbox', 'bnemail_remindertoguestsenabled',
            get_string('bnemail:guestenabled', 'bbbext_bnemail'));
        $this->mform->disabledIf('bnemail_remindertoguestsenabled', 'guestallowed', 'notchecked', 0);
        $this->mform->disabledIf('bnemail_remindertoguestsenabled', 'openingtime[enabled]', 'notchecked', 0);
        $this->mform->hideIf('bnemail_remindertoguestsenabled', 'bnemail_reminderenabled', 'notchecked', 0);
        $this->mform->setType('bnemail_remindertoguestsenabled', PARAM_BOOL);

        $paramcount = optional_param('bnemail_paramcount', $this->bigbluebuttonbndata->bnemail_paramcount ?? 0, PARAM_RAW);
        $paramcount += optional_param('bnemail_addparamgroup', 0, PARAM_RAW) ? 1 : 0;
        $isdeleting = optional_param_array('bnemail_paramdelete', [], PARAM_RAW);
        foreach ($isdeleting as $index => $value) {
            // This prevents the last delete button from submitting the form.
            $this->mform->registerNoSubmitButton("bnemail_paramdelete[$index]");
        }
        $bellicon = new pix_icon('i/bell', get_string('timespan:bell', 'bbbext_bnemail'), 'bbbext_bnemail');
        for ($index = 0; $index < $paramcount; $index++) {
            $paramicon = $this->mform->createElement('html', $OUTPUT->render($bellicon));
            $paramname =
                $this->mform->createElement(
                    'select', "bnemail_timespan[$index]",
                    get_string('timespan', 'bbbext_bnemail'),
                    utils::get_timespan_options()
                );
            $paramtext =
                $this->mform->createElement('html', html_writer::span(get_string('reminder:message', 'bbbext_bnemail'), 'mx-3'));
            $paramdelete = $this->mform->createElement('submit', "bnemail_paramdelete[$index]", get_string('delete'), [], false,
                ['customclassoverride' => 'btn btn-secondary float-left']);

            $this->mform->addGroup([$paramicon, $paramname, $paramtext, $paramdelete], "bnemail_paramgroup[$index]", '', [' '],
                false);
            $this->mform->hideIf("bnemail_paramgroup[$index]", 'bnemail_reminderenabled', 'notchecked', 0);
            $this->mform->disabledIf("bnemail_paramgroup[$index]", 'openingtime[enabled]', 'notchecked', 0);
            $this->mform->setType("bnemail_timespan[$index]", PARAM_ALPHANUM);
            $this->mform->setType("bnemail_paramdelete[$index]", PARAM_RAW);
            $this->mform->disabledIf("bnemail_timespan[$index]", 'openingtime[enabled]');
            $this->mform->registerNoSubmitButton("bnemail_paramdelete[$index]");
        }
        // Add a button to add new param groups.
        $this->mform->addElement('submit', 'bnemail_addparamgroup', get_string('addreminder', 'bbbext_bnemail'));
        $this->mform->hideIf('bnemail_addparamgroup', 'bnemail_reminderenabled');
        $this->mform->disabledIf('bnemail_addparamgroup', 'openingtime[enabled]', 'notchecked', 0);
        $this->mform->setType('bnemail_addparamgroup', PARAM_TEXT);
        $this->mform->registerNoSubmitButton('bnemail_addparamgroup');
        $this->mform->addElement('hidden', 'bnemail_paramcount');
        $this->mform->setType('bnemail_paramcount', PARAM_INT);
        $this->mform->setConstants(['bnemail_paramcount' => $paramcount]);

        // Add the original openingtime to validate if it changes between updates.
        $this->mform->addElement('hidden', 'bnemail_openingtime');
        $this->mform->setType('bnemail_openingtime', PARAM_INT);
        $this->mform->setConstants(['bnemail_openingtime' => $this->bigbluebuttonbndata->openingtime]);
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
        if (!empty($data['bnemail_timespan'])) {
            // Check if we have duplicate values in array.
            $unique = array_unique($data['bnemail_timespan']);
            if (count($unique) !== count($data['bnemail_timespan'])) {
                // Find the second occurence of the duplicate value.
                $duplicates = array_diff_assoc($data['bnemail_timespan'], $unique);
                if (!empty($duplicates)) {
                    $firstduplicatekey = array_key_first($duplicates);
                    $errors["bnemail_addparamgroup"] = get_string('error:duplicate', 'bbbext_bnemail');
                }
            }
        }
        return $errors;
    }
}
