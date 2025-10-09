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

namespace bbbext_bnreminders\local\persistent;

use core\persistent;
use mod_bigbluebuttonbn\instance;

/**
 * Guest email record for a given Meeting.
 *
 * @package   bbbext_bnreminders
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class guest_email extends persistent {
    /**
     * Table name
     */
    const TABLE = 'bbbext_bnreminders_guests';

    /**
     * Helper to create a guest email record for an instance and a given email (string).
     *
     * @param string $email
     * @param int $bigbluebuttonid
     * @param int|null $fromuserid
     * @return void
     */
    public static function create_guest_mail_record(string $email, int $bigbluebuttonid, ?int $fromuserid = null) {
        global $USER;
        $instance = instance::get_from_instanceid($bigbluebuttonid);
        if (!$instance) {
            throw new \moodle_exception('invalidinstanceid', 'bbbext_bnreminders');
        }
        $fromuserid = $fromuserid ?? $USER->id;
        $guest = self::get_record(['email' => $email, 'bigbluebuttonbnid' => $bigbluebuttonid, 'userfrom' => $fromuserid]);
        if (!$guest) {
            $guest = new guest_email(0, (object) [
                'bigbluebuttonbnid' => $bigbluebuttonid,
                'email' => $email,
                'userfrom' => $fromuserid,
                'issent' => false,
            ]);
            $guest->create();
        }
        $guest->set('issent', false);
        $guest->set('isenabled', true);
        $guest->save();
    }

    /**
     * Persistent data defintion
     */
    protected static function define_properties() {
        return [
            'bigbluebuttonbnid' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'email' => [
                'type' => PARAM_EMAIL,
                'null' => NULL_NOT_ALLOWED,
            ],
            'userfrom' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'issent' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'isenabled' => [
                'type' => PARAM_BOOL,
                'default' => true,
            ],
        ];
    }
}
