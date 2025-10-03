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
 * BBB Utils tests class.
 *
 * @package   bbbext_bnreminders
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @coversDefaultClass  \bbbext_bnreminders\utils
 */
final class utils_test extends \advanced_testcase {
    /**
     * Test options are there.
     *
     * @return void
     * @covers ::get_timespan_options
     */
    public function test_get_timespan_options(): void {
        $result = utils::get_timespan_options();
        $this->assertEquals([
            utils::ONE_HOUR => get_string('timespan:pt1h', 'bbbext_bnreminders'),
            utils::TWO_HOURS => get_string('timespan:pt2h', 'bbbext_bnreminders'),
            utils::ONE_DAY => get_string('timespan:p1d', 'bbbext_bnreminders'),
            utils::TWO_DAYS => get_string('timespan:p2d', 'bbbext_bnreminders'),
            utils::ONE_WEEK => get_string('timespan:p1w', 'bbbext_bnreminders'),
        ], $result);
    }
}
