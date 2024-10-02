BigBlueButton Extension - BN Email Subplugin
=======================
* Copyright: Blindside Networks Inc
* License:  GNU GENERAL PUBLIC LICENSE Version 3


Overview
===========
The BN Email subplugin enhances the BigBlueButtonBN module by sending automated reminder emails to users before a session starts. These reminders include a link to join the meeting and are sent to the email address registered with the user's account.


Features
===========
* **Automated Email Reminders:** Send reminder emails to users at predefined intervals before a session starts.
* **Customizable Email Content:** Modify the text of the emails sent to users.
* **Subscription Management:** Allow users to manage their preferences for receiving reminder emails.


Installation
============
Prerequisites
------------
* Moodle environment with BigBlueButtonBN module installed.
* Cron must be operational to ensure timely delivery of reminders.

Git installation
------------
1. Clone the repository:

`git clone https://github.com/blindsidenetworks-ps/moodle-bbbext_bnemail.git`

2. Rename the downloaded directory:

`mv moodle-bbbext_bnemail bnemail`

3. Move the folder to the Moodle BigBlueButtonBN extensions directory:

`mv bnemail /var/www/html/moodle/mod/bigbluebuttonbn/extension/`

4. Run the Moodle upgrade script:

`sudo /usr/bin/php /var/www/html/moodle/admin/cli/upgrade.php`

Manual installation
------------
1. Download the sub plugin zip file and extract it.
2. Place the extracted folder into `mod/bigbluebuttonbn/extension/`
3. Rename the folder `bnemail`
4. Access Moodle's Admin UI at `Site administration > Plugins > Install plugins` to complete the installation.


Configuration
============
Access the subplugin configuration under
`Site Administration > Plugins > BigBlueButton > Manage BigBlueButton extension plugins`

Here, admins can enable/disable the subplugin, manage settings, or uninstall it.


Usage
============
Setting Up Reminder Emails
------------
Configure reminder emails via the BigBlueButton activity settings. Set the start date for the session, then use the "Add reminder" button to specify when reminders should be sent (e.g., 1 hour, 1 day before the session).

Setting Up Reminder Emails
------------
Modify the email templates from the subplugin settings. Available variables include:
* `{$url}`: Activity URL.
* `{$course_fullname}`: Full name of the course.
* `{$course_shortname}`: Short name of the course.
* `{$date}`: Date and time of the meeting.
* `{$name}`: Name of the meeting.

Managing Email Subscriptions
------------
Users can manage their email reminder preferences through:
* User Preferences: Navigate to the Preferences Page under BigBlueButton reminders preferences.
* Unsubscription Link: Each email contains an unsubscription link allowing users to opt-out.


Troubleshooting
============
* Emails Not Sent/Received: Ensure cron is running regularly as email delivery depends on scheduled cron tasks.


Requirements
============
Requires BigBlueButtonBN module version > 2022112802

For more detailed updates and support, visit the [BN Email Subplugin GitHub Repository](https://github.com/blindsidenetworks-ps/moodle-bbbext_bnemail)
