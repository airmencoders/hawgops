# Hawg Ops
https://hawg-ops.com

## CAS Planner
Allows users to create CAS scenarios in order to mission plan for training missions.

# Change Log

## 1.44.0
* Created `/req/modals/edit-cap-modal.php` to edit CAPs after creating them
* Part of Editing overhaul requested by Simple

## 1.43.2
* Began testing for the ability to change colors of drawing objects after they're created
* Essentially requires the object to be removed and then recreated and added to the map
* Requested by Simple

## 1.43.1
* Removed extraneous NTTR lines unneeded
* Requested by Diesel

## 1.43.0
* Updated NTTR Lines to compose internal boundaries
* Requested by Diesel
* Fixed bug in `/js/cas-airspace.js` (Missing `;`) which broke the `Download KML` function
* Updated `airspace-kml.php` to include old and new BMGR boundaries

## 1.42.0
* Added new BMGR Airspace to a new layer
* Moved old BMGR Airspace to a new layer
* Added both BMGR layers to the layer map
* Requested by BEAR

## 1.41.0
* Added R-6904 Airspace
* Requested by ROAD

## 1.40.1
* Fixed Bug where chits were not being properly resized when map zooms

## 1.40.0
* Created ATCAA Layer Group
* Added ATCAA Layer Group to Layer Menu
* Added Sheboygan, Oshkosh, and Black River ATCAAs
* Added additional WI and VA airspace and restricted areas
* Requested by ROAD

## 1.39.0
* Created AAR Tracks Layer Group
* Added AAR Tracks Layer Group to Layer Menu (Allows the disabling of AAR tracks from the map separate from airspace)
* Added R-4006 AAR Tracks
* Requested by ROAD

## 1.38.0
* Removed window size from CAS Instructions since chit tables were moved to a separate modal.

## 1.37.3
* API v2 Completed. 
* **TODO:** Update Codes / alert-container and testing

## 1.37.2
* Continued work on API v2

## 1.37.1
* Continued work on API v2

## 1.37.0
* created ```/maintenance``` landing page and ```site-settings``` admin page
* TODO: Handle site settings saving

## 1.36.0
* Edited ```admin-view-logs``` to have a more asthetic table layout

## 1.35.0
### NOT YET IMPLEMENTED
* Began work on Version 2 of API. Fixes include simplification and consolidation of functions.
* Began work on Version 2 of codes. Fixes include simplification and sconsolidation of functions.
* Further Feature list:
1. Change database IDs (User, Scenario, Token) from a hashed string to UUID v4
2. Maintenance Mode
* TODO:
1. Finish Functions
2. Back up database
3. Test functions
4. Implement version 2
5. Assess functionality / fix bugs

## 1.34.2
* Added more email templates
* Worked on CRON job for inactive users (>12 months is a warning, >14 months disables account, >15 months deletes account) *Inactive for now*
* Fixed email HTML tag filtering

## 1.34.1
* Added a jumbotron to ```index``` so that the landing page is no longer just blank

## 1.34.0
* Added the ability to combine scenarios into one map
* Updated links to now use ```scenario[]``` instead of ```scenario``` in ```my-scenarios```, ```admin-scenarios```, and the ```share scenario``` function
* Requested by CLEAVER

## 1.33.1
* Renamed ```VERSION.md``` (this file) to ```CHANGELOG.md```
* Created ```VERSION.md``` file
* Added version to the ```About``` section of the Navbar

## 1.33.0
* Added ```updateScenario``` function within the ```API``` and ```cas-leaflet.js```
* Updated ```save-modal``` to add a new button if viewing a scenario to either overwrite or save a new version of the scenario
* Created ```updated-scenario-do``` to handle functionality of updating the scenario
* Fixed bug in ```cas-leaflet.js``` for deleting chits with the new unique layer structure (It just brute forces all the layers by removing the chit from all the layers, regardless of its type)

## 1.32.1
* Changed ```phpMyAdmin``` configuration to explicitly deny all clients except ```localhost```
* Bitnami stated that this was the configuration when installing the AWS LAMP stack, however I found this to be ```false```
* Changing both version to ```Deny from all``` and adding ```Allow from 127.0.0.1``` to ```<IfVersion >= 2.3>``` allowed this to work as intended

## 1.32.0
* Created ```contact-template.php``` for contact email formatting
* Modified ```contact-do.php``` to use the new template
* Added email sanitation to filter out any HTML characters to avoid any injects that shouldn't be there

## 1.31.1
* Added ```flex-wrap``` class to ```admin-scenarios``` list

## 1.31.0
* Updated the Map Overlays
* Added unique layers for IP/friendly/hostile/survivor/bldg labels
* Added unique layers for Drawing objects
* Updated Show/Hide titles to handle new unique layers
* Updated Save function (v4) to handle new layers
* Updated Load function (v4) to handle new layers
* Gracefully "fails" by still being able to use older scenarios with new unique layers
* Requested by CLEAVER

## 1.30.1
* Changed size of Github link in Navbar

## 1.30.0
* Added Airspace KML Link to Navbar
* Created ```/airspace-kml.php``` to allow users to download a KML of the current airspace. It takes the javascript, changes it from JS variables to PHP variables and then makes a KML file and outputs it to a downloaded file.
* Removed ```/google.php``` Privacy Policy / Terms until ReCaptcha v2 is implemented.
* Added GitHub link to ```/navbar.php```
* TODO:
- [] Allow KML upload to all users (not just Admin)
- [] Rename ```talk-to-me.php``` back to ```contact.php``` and all references
- [] Add RECAPTCHA v2 to ```contact.php```
- [] Add RECPATCHA v2 to KML Upload page
- [] Add RECAPTCHA v2 to ```create-account.php```
- [] Add RECAPTCHA v2 to ```recover-account.php```
- [] Figure out why CRON job to remove old reset password tokens isn't working
- [] Once done with previous CRON job, create a new one to disable/delete old accounts


## 1.29.2
* Remove old Ops Desk Tools reference in navbar

## 1.29.1
* Loading a scenario now pans the map to the Lat/Lng of the first item of the scenario (marker/line/EA/Roz/Threat etc...)
* Changed zoom factor of fly/pan from 13 to 10
* Added Pilsung range to Korea restricted areas
* Fixed Korea LLZ-5 errors

## 1.29.0
* Created new ```CRON``` job: Runs ```/cron/flush-reset-pass-tokens.php``` every hour to clean the database of any stale tokens that are older than 30 minutes.
* Logs to ```/cron/logs```

## 1.28.0
* Update ```/do/share-scenario-do.php``` to use new email template form

## 1.27.0
* Updated ```/my-scenarios.php``` to flex-wrap each list scenario item to make it look better on smaller screens
* Updated ```/do/recover-account-do.php``` To properly encode HTML email as UTF-8
* Created Recover Account Email template (Used for disabled accounts)
* Created Share Scenario Email template
* Created Account Details Changed email template
* Updated variable MySQL Variable names
* Updated multiple functions in ```API``` allowing for the account recovery process to function properly
* Updated ```do/reset-password-do.php``` to work the password reset function
* Password Recovery Function
1. User is locked out or forgets password, goes to ```/recover-account```
2. User enters email address, that information goes to ```/do/recover-account-do.php```, email is sent to user (if account exists)
3. User gets email, clicks on link back to ```/recover-account```, token is validated 
4. User inputs new passwords, this is sent to the ```/do/reset-password.php``` script to be validated and change the password
5. Email is sent to user that account details were changed and password is reset and account is enabled.
**TODO**
* Clean up API, looks like a lot of things that could be re-written or compartmentalized better
* Potentially get rid of the scripts in the ```/do/``` directory and instead, have the POST data go to the calling file
* Work for more / better authentication, usage of flags for API functions so that things that could and should have protections for only allowing accounts/admin to have access to should.
* Write the CRON script that will clean out stale reset pass tokens every hour
* Update ```alert-container.php``` so that new codes are included

## 1.26.0
* Okay, so this is what was decided...
* Removed Leaflet KML plugin and references
* Added toGeoJSON plugin
* User will upload a KML file that will then be parsed into a GeoJSON feature set and added to the map. Then, when the user saves the scenario, the function will save the GeoJSON layer to the text to be saved.
* User will have the option to view/manage their KML files within the system to add to other scenarios or delete.
* TODO: Modify the save/load functionality as follows:
**SAVE FUNCTION**
1. Save the filename/location of the KML file
**LOAD FUNCTION**
1. Attempt to load the KML file, fail gracefully if it isn't found (alert the user that they may have deleted it and will need to re-save to stop the error or re-upload as required and re-save)
2. If successfully loaded, load KML as normal to the ```labels_kml``` layer so that the user can upload multiple KML files to the scenario
* TODO: Export all airspace into a KML file for download
* TODO: Create a ```/my-kmls``` page where the user can manage their KML files (view, delete, download)
* TODO: When a user uploads a ```.kml``` it will go to the ```/public_html/kml/<user_id>/``` folder or create one if it doesn't exist. Then the filename will be whatever they choose. Implement something if the file already exists, then it will ask if they want to overwrite or not with a warning that if they have another scenario that uses this same KML, it may have adverse affects and WILL change the overlays in that scenario as well. OPTIONS: add an upload-time so that this doesn't happen, PRO: easier for users, CON: I can see this getting to where storage is an issue.
* TODO: implement RECAPTCHAv2 for file uploads!!! (and contact/share)
* Updated ```licenses.php```

## 1.25.0
* Added Leaflet KML plugin to load KML files. The difference with this one is that it requires the KML to be uploaded to the server to pull the information whereas the FileLayer plugin would parse it into an actual leaflet layer to then be used via normal Leaflet Polygons. There are other plugins that do that functionality, but I think that this will work fine.
* Updated license attribution
* Future update would be to change the save/load process to save the user ID and the filename of the KML within the stored text which would then load the KML when loading the saved scenario. Ideally, it would translate the KML into a polygon so that the save/load process would not need to be changed. Further research required for that. 
* At this time, this is published but only available to administrators

## 1.24.0
* Removed all of ```VERSION 1.23.0```, Could not get Leaflet FileLayer to function due to some compatibility issues, will re-attack with a new plugin.
* Changed hosting of Leaflet.js from self-hosted to a CDN.
* Updated Leaflet.js to 1.6.0
* Updated license attribution

## 1.23.2
* Updated ```VERSION.md``` formatting

## 1.23.1
* Remove reference to m_ruler from cas-leaflet.js. This was an old reference that was put into the main NM ruler.

## 1.23.0
* Add Leaflet FileLayer plugin by Makin a Corpus (MIT)
* Add toGeoJSON by tmcw (BSD-2) (Dependency of Leaflet FileLayer)
* Update license attribution
* Update /req/head/cas-head.php to include the Leaflet.FileLayer plugin
* Update /req/head/cas-head.php to include togeojson
* Edited ```index.js``` / ```gpx.js``` / ```kml.js``` / ```shared.js``` in ```togeojson``` to include the ```.js``` in filenames

## 1.22.5
* Add China Lake MOA / Restricted areas
* Requested by PUMA

## 1.22.4
* Update legal links to open in new tab

## 1.22.3
* ```/do/contact-do.php``` now requires user to be logged in
* Thanks to @tylerthetiger for the bug report

## 1.22.2
* Moved the ```faa-users``` file to non-respository directory
* Thanks to @tylerthetiger for the issue

## 1.22.1
* Removed Online/Offline div from Navbar

## 1.22.0
* Removed the online/offline functionality (wasn't doing anything at this time - might revisit later)
* Added recover-account functionality for accounts that are disabled to recover
* Added ```/recover-account.php```
* Added ```/do/recover-account-do.php```
* Added recoverAccount() function to ```api-v1.php```
* Added ```emails/``` folder to hold email templates
* Created ```emails/recover-account-mail.php``` email template

## 1.21.2
* Added close button to Chit List Modal

## 1.21.1
* Created Chit List Modal
* Moved Chit Lists from the bottom of the screen to new modal
* Created "View Chit List" button on right side of screen
* Updated instructions to note that some components may not display correctly if user does not have the window full-screen

## 1.21.0
* Added White Sands / Fort Bliss Airspace
* Requested by Gator

## 1.20.0
* Added Thailand Airspace for CG Exercise
* Requested by South/Basic/White from 25FS

## 1.19.0
* Added DD MM.MMMM Lat/Long to map popup when clicking map or using the Fly to coordinates button
* Requested by Gov (WSEP)

## 1.18.0
* Add CO MOAs and Ranges
* Requested by Fort Carson ASOS

## 1.17.3
* Re-add the update of userLastLogin in login() in API

## 1.17.2
* Removed redundant logging in login function

## 1.17.1
* Added disabling function to login

## 1.17.0
* Add enable/disable accounts to API
* Add grant/revoke admin to API
* Update admin-users to use new functions
* Update createLog
* Bring API back to speed after erroneous removal
* Updated Codes
* Updated Navigation logs
* Moved User ID to a tooltip in admin-users
* Added settings button in admin-users
* Cleaned up codes / alert-bar 

## 1.16.1
* Update Github links in navbar

## 1.16.0
* Move MySQL tables/columns to private file not uploaded to git repo now that it is public
* Update License

## 1.15.2
* Fix typo in API

## 1.15.1
* Re-added "create account" button that was erroneously removed when removing reCAPTCHA notice

## 1.15.0
* Removed reCAPTCHA v3 from all pages due to multiple false positives (One is too many)

## 1.14.1
* Fixed typo in ```verification.js``` where it wouldn't catch name/email

## 1.14.0
* Added reCAPTCHA to all pages
* Finished verifyRecaptcha()
* Finished ```do/recaptcha```
* Updated verifyRecaptcha to add the score to the loging. PROBLEM with this is that if there is rapid succession logging, then scores are not logged. 2 COAs are either to turn logging into a database, log all recaptcha scores as its own line, or create a whole new log with recaptcha scores

## 1.13.0
* Added reCAPTCHA v3 to login page
* Added ```do/recaptcha``` to handle secure token passage to API
* Added verifyRecaptcha() function to API to send token to Google
* Created Google reCAPTCHA license page and added to About menu
* Added Google license to login page
* Removed mySQL credentials from API
* Created ```keys/recaptcha``` credentials page
* Created ```keys/mysql``` credentials page
* Updated login page where if a user navigated to ```/login``` and they are already authenticated, they would go to ```/my-scenarios``` OR ```/cas``` if they were referred

## 1.12.2
* Fixed broken links in ```/do/contact-do.php``` where it was still sending responses to ```contact.php```, now references ```talk-to-me.php``` correctly
* Updated note in ```login.php``` so people understand better that an account is NOT required

## 1.12.1
* Updated admin-iplog to have link to view location of user's authenticated IP addresses 

## 1.12.0
* Added getNumberOfScenariosByUser($user) to API
* Updated admin-users to include number of scenarios in the button text

## 1.11.0
* Fixed typos
* Added iplog table to database
* Added logIP to API
* Added calls to logIP in createAccount and login in API
* Added getIPLogByUser($user) to API
* Created admin-iplog to view user's authenticated IP addresses, access by clicking on user's ID in admin-users

## 1.10.0
* Fixed typo in alert-container API_SAVE_SCENARIO_SCENARIO_ID_NOT_RECIEVED
* Fixed accidental constant vs. function in API
* Changed Admin-Users from a list to a table similar to admin-logs
* Added lastLogin column to database
* Updated login function in API to update lastLogin in database
* Updated getAllUsers function in API to pass lastLogin from database
* Added Last Login column to admin-users

## 1.9.9
* Removed PayPal link. It hasn't ever been used to donate and presents a vulnerability by releasing personal email address.

## 1.9.8.1
* Changed name of Contact page from ```contact.php``` to ```talk-to-me.php``` in an attempt to confuse spam bots looking for contact forms.

## 1.9.8
* Added an account counter in admin-users
* Other misc. fixes

## 1.9.7
* Fixed typo in API>Login logging

## 1.9.6
* Fixed BMGR conventional range / STAC No Impact area boundaries

## 1.9.5
* Add Github link to Admin menu

## 1.9.4
* Changed how createKey in the API functions to be more random
* Updated airspace for BMGR in Arizona

## 1.9.3
* Update error in previous commit message

## 1.9.2
* Fixed bug comparing JSON object to null instead of undefined
* Fixed bug when creating marker ID's: if none existed then assigns an ID, but if an ID exists that is < the current marker tracker, then re-assigns an ID to ensure unique IDs

## 1.9.1
* Added link to IP address lookup in logs for easy viewing source for blacklisting purposes

## 1.9.0
* Added Friendly/Hostile/Threat tables at bottom of map
* Updated ```cas-leaflet.js``` to handle adding/removing of markers to the new tables
* Updated save/load functions to handle adding/removing of markers to the new tables
* Added ID number to marker options
* Updated scenario version to v3
* Fix bug where when saving a scenario to account, scenario name would not clear with the modal reset
* Requested by SPAWN/PINCH

## 1.8.4
* Add ```LICENSE.md``` for github purposes

## 1.8.3
* Update security in ```/opt/bitnami/apache2/conf/httpd.conf```
* Create list of blacklisted IPs and deny access
* Add ```WX.php``` to start weather gonkulating

## 1.8.2
* Remove old logging functions from API/Do pages

## 1.8.1
* Airspace update
* Minor bug fixes

## 1.8.0
* Updated logging functions to use JSON notation
* Edited CAS scenario permissions to allow anyone to view any other CAS scenario as long as they are logged in
* Removed erroneous sanitizeInput functions in API
* Removed test pages from ```public_html```
* Removed bug where navbar attempted to get user's name even if they are not logged in
* Created ```admin-view-log``` page
* Created ```admin-logs``` page

## 1.7.0
* Move to a self-hosted version of Font Awesome to avoid any TIMEOUT issues on NIPR where icons couldn't load.

## 1.6.1
* Remove "Known Issues" card from index

## 1.6.0
* Update ```leaflet-ruler.js``` to show both NM and meters
* Add "Known Issues" card to index

## 1.5.4
* Add a Online/Offline notification for users so that way if NIPR goes down they can tell that they won't be able to use the site.
* Add a fail() function to the $.get function for elevation API that will allow the chit popups (As of 24 Oct, 19 the nationalmap.gov/epqs service is down)

## 1.5.3
* Fix bug with elevation API to still allow popups for chits

## 1.5.2
* Added Admin Scenarios page to view user scenarios
* Added Admin Users page to view users
* Updated API to handle admin functions

## 1.5.1
* Updated My Scenarios page to add a share button
* Created Share modal to handle sharing of scenarios
* Updated ```validation.js``` to handle new form validation
* Updated API to add getUserNameByEmail, getUserEmailByID
* Updated ```login.php``` to handle scenario parameter
* Updated ```cas.php``` to handle scenario and share parameter

## 1.5.0
* Added user accounts
* Added login/logout pages
* Added My Scenarios page
* Created private API to handle database/backend functions
* Added DO pages to handle login/logout/save/load functions
* Updated Scripts to use validation.js
* Created Terms of Use
* Created Privacy Policy
* Created Cookie Policy


## 1.4.0
* Updated airspace
* Added version control to the scenario save files
* Updated save/load functions for version control
* Added new layer group to hold threat markers 
* Added GOB (Chits) and MOB (Threat Rings) to the layer options menu
* Update save/load functions to handle new layer group
* Gracefully handles loading without the new layer group
* Added message warning users about loading an old version of a scenario
* Requested by 74FS/DOI

## 1.3.1
* Added show/hide title buttons
* Updated ```cas-leaflet.js``` to handle the showing/hiding of titles
* Requested by GOV

## 1.3.0
* Added Building label chit
* Added listeners/handlers for building label
* Updated ```cas-leaflet.js``` to handle building label
* Requested by JTACs

## 1.2.1
* Updated 15-Line formatting
* Updated ```cas-leaflet.js``` to handle new 15-Line

## 1.2.0
* Added ```Leaflet.GeometryUtil.js``` - Used to get bearing between 2 points
* Updated styling
* Bug fixes
* Added 9-Line modals
* Added 15-Line modals
* Updated ```cas-leaflet.js``` to handle additional functions

## 1.1.1
* Added SRV Chit
* Added "data" field to markers (Used for 9-Lines / 15-Lines)
* Updated Save/Load function to accept new "data" field

## 1.1.0
* Updated various stylings
* Updated save & load functions to be more human readable
* Updated "click" functions to add new options for save & load functions
* Updated threat ring functions to allow different units
* Updated threat ring functions, custom threats now put user input in center

## 1.0.0
* Initial commit
