# TODO List for BigBlueButton Extension - BN Notify Subplugin
This file lists the known subplugin issues that need to be fixed. Each task includes a reference to its corresponding Jira ticket for additional details.

**Hide Settings for RecordingsOnly**  
    _[MD-8](https://blindsidenetworks.atlassian.net/browse/MD-8)_  The subplugin category will always appear in the Activity settings form, regardless of the BBB activity Instance Type. The category should be hidden when the Instance Type is set to RecordingsOnly.
    
**Ensure compliance for guests receiving emails**  
    _[MD-17](https://blindsidenetworks.atlassian.net/browse/MD-17)_  The subplugin stores guest emails to send session reminders. We need to have a way to receive user consent, support email deletion with unsubscription, and implement necessary changes for Privacy API compliance.

**Guest users do not receive email reminders**  
    _[MD-20](https://blindsidenetworks.atlassian.net/browse/MD-20)_  When a guest user is invited to a BigBlueButton meeting and the option to send guests emails is enabled, an email will not be sent ahead of the scheduled session time.