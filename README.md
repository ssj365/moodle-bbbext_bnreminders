# BigBlueButton BN Reminders
*(formerly BN Notify)*

**Never let students miss a session again.**
BN Reminders is a BigBlueButton extension for Moodle that improves attendance and engagement by sending timely, customizable reminder emails before sessions.

Developed and supported by **Blindside Networks** — the company that started the BigBlueButton project.

---

## ✨ Features
- **Automated Email Reminders** – Notify students at predefined intervals (e.g., 1 day or 1 hour before).
- **Customizable Email Templates** – Personalize reminder messages with placeholders like `{$url}`, `{$course_fullname}`, `{$date}`, `{$name}`.
- **Subscription Management** – Allow users to manage preferences or unsubscribe directly from emails.
- **Course Integration** – Add reminders per BigBlueButton activity.
- **Admin Control** – Configure defaults globally via *Site administration > Plugins > BigBlueButton*.

---

## ⚡ Why Choose BN Reminders?
- **Boost Attendance** – Students won’t forget scheduled sessions.
- **Improve Engagement** – Timely communication builds better learning habits.
- **Seamless Integration** – Works natively with BigBlueButtonBN.
- **Trusted Development** – Backed by Blindside Networks, creators of BigBlueButton.

---

## 📦 Installation

### Requirements
- Moodle with BigBlueButtonBN module (≥ 2022112802).
- Cron enabled for scheduled tasks (email delivery).

### From GitHub
```bash
git clone https://github.com/blindsidenetworks-ps/moodle-bbbext_bnreminders.git
mv moodle-bbbext_bnreminders /var/www/html/moodle/mod/bigbluebuttonbn/extension/bnreminders
php admin/cli/upgrade.php
```

### Manual

1. Download and extract the ZIP.
2. Place the folder under: mod/bigbluebuttonbn/extension/
3. Rename it to bnreminders.
4. Complete installation via Moodle’s Admin UI.

---

## ⚙️ Configuration

- **Admin settings:** Site administration > Plugins > BigBlueButton > Manage extensions
- **Activity settings:** Add reminders directly within each BigBlueButton activity.
- **Template editing:** Use variables like:
***{$url}*** – Activity URL
***{$course_fullname}*** – Course name
***{$date}*** – Session date/time
***{$name}*** – Meeting name

User preferences: Students can opt in/out or unsubscribe directly from reminder emails.

---

## ❗ Troubleshooting

- **Emails not sent** – Check that cron is running regularly.
- **No reminders available** – Verify the BigBlueButton activity has a scheduled start date.

---

## 🧩 Version Compatibility

| Moodle Version | Plugin Branch | Notes                                |
|----------------|---------------|--------------------------------------|
| Moodle 4.5+    | main          | Requires BigBlueButtonBN ≥ 2024100700 |

---

## 📣 Support & Feedback

File issues and feature requests via the GitHub tracker


For commercial support (hosting, integration, customization), visit [Blindside Networks](https://blindsidenetworks.com/)

---

## 👥 Credits

Maintained by Blindside Networks, creators of BigBlueButton.
Released under the GNU GPL v3 License.