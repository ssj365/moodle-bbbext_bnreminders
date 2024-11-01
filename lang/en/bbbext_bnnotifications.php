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
 * Language File.
 *
 * @package   bbbext_bnnotifications
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */

defined('MOODLE_INTERNAL') || die();
$string['addreminder'] = 'Add reminder';
$string['bnnotifications'] = 'Send email reminders before the session starts';
$string['bnnotifications:enabled'] = 'Send email reminders before session';
$string['bnnotifications:guestenabled'] = 'Add guests to the list of users to send the reminder to';
$string['bnnotifications:header'] = 'Reminders';
$string['bnnotifications:preferences'] = 'BigBlueButton reminders preferences';
$string['bnnotifications_help'] = 'If enabled and a start date is set, send email reminders for users registered to the activity';
$string['check_emails_reminder'] = 'Check emails reminder';
$string['email_reminder_subject'] = 'Reminder for the meeting {$a->name}';
$string['email_unsubscribe_message'] = '<span>
You can unsubscribe to this reminder by clicking on the following <a href="{$a->unsubscribeurl}">Unsubscribe link</a>.
</span>';
$string['emailtemplate'] = 'Email template';
$string['emailtemplate:default'] = '<p>
Hi,<br><br>
This is a reminder for the meeting <a href="{$url}">{$name}</a> that will start on {$date}.
</p>
';
$string['emailtemplate:desc'] = 'Email template when sending reminders. The following variables can be used:
{$course_fullname}: the course fullname,
{$course_shortname}: the course shortname,
{$date}: the meeting date and time,
{$name}: the meeting name';
$string['error:duplicate'] = 'You have already one reminder for this meeting for the same time span';
$string['messageprovider:reminder'] = 'BigBlueButton email reminder';
$string['pluginname'] = 'BigBlueButton BN Notifications';
$string['reminder'] = 'Reminder';
$string['reminder:message'] = 'before meeting starts';
$string['reminder:openingtime:disabled'] = 'Opening time is disabled';
$string['subscribed'] = 'Subscribed';
$string['subscribed:cancel'] = 'No changes have been made to your subscription';
$string['subscribed:success'] = 'Subscribed to {$a->name} reminders successfully!';
$string['subscriptions'] = 'Subscriptions';
$string['timespan'] = 'Time span';
$string['timespan:bell'] = 'Timespan';
$string['timespan:p1d'] = 'One day';
$string['timespan:p1w'] = 'One week';
$string['timespan:p2d'] = 'Two days';
$string['timespan:pt1h'] = 'One hour';
$string['timespan:pt2h'] = 'Two hours';
$string['unsubscribe'] = 'Unsubscribe';
$string['unsubscribe:label'] = 'Are you sure you want to unsubscribe ?';
$string['unsubscribe:managepreferences'] = 'Manage reminder preferences';
$string['unsubscribe:title'] = 'Manage BigBlueButton Reminder Subscriptions';
$string['unsubscribe:title:meeting'] = 'Unsubscribe to the reminder for BigBlueButton activity {$a}';
$string['unsubscribed'] = 'Unsubscribed';
$string['unsubscribed:success'] = 'Unsubscribed from {$a->name} reminders successfully!';
