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

namespace bbbext_bnreminders;

/**
 * Utility class
 *
 * @package   bbbext_bnreminders
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class utils {
    // ISO 8601 duration format.
    /**
     * One hour.
     */
    const ONE_HOUR = 'PT1H';
    /**
     * Two hours.
     */
    const TWO_HOURS = 'PT2H';
    /**
     * One day.
     */
    const ONE_DAY = 'P1D';
    /**
     * Two days.
     */
    const TWO_DAYS = 'P2D';
    /**
     * One week.
     */
    const ONE_WEEK = 'P1W';

    /**
     * All timespan options.
     */
    const TIMESPAN_OPTIONS = [
        self::ONE_HOUR,
        self::TWO_HOURS,
        self::ONE_DAY,
        self::TWO_DAYS,
        self::ONE_WEEK,
    ];
    /**
     * File area for email reminders.
     */
    const EMAIL_REMINDER_FILEAREA = "emailmessage";

    /**
     * Get timespan options
     *
     * @return array|\lang_string[]|string[]
     * @throws \coding_exception
     */
    public static function get_timespan_options(): array {
        return array_combine(
            self::TIMESPAN_OPTIONS,
            array_map(
                fn($optionname) => get_string('timespan:' . strtolower($optionname), 'bbbext_bnreminders'),
                self::TIMESPAN_OPTIONS
            )
        );
    }

    /**
     * Replace the variables in the text.
     *
     * @param array $vars
     * @param string $text
     * @return string
     */
    public static function replace_vars_in_text(array $vars, string $text): string {
        foreach ($vars as $key => $value) {
            $search[] = '{$' . $key . '}';
            $replace[] = (string) $value;
            if ($search) {
                $text = str_replace($search, $replace, $text);
            }
        }
        return $text;
    }
}
