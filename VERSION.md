# Hawg Ops
Private repository of website files.
website located at https://hawg-ops.com

## CAS Planner
Allows users to create CAS scenarios in order to mission plan for training missions.

# Change Log

## 1.14.0
* Added reCAPTCHA to all pages
* Finished verifyRecaptcha()
* Finished do/recaptcha
* Updated verifyRecaptcha to add the score to the loging. PROBLEM with this is that if there is rapid succession logging, then scores are not logged. 2 COAs are either to turn logging into a database, log all recaptcha scores as its own line, or create a whole new log with recaptcha scores

## 1.13.0
* Added reCAPTCHA v3 to login page
* Added do/recaptcha to handle secure token passage to API
* Added verifyRecaptcha() function to API to send token to Google
* Created Google reCAPTCHA license page and added to About menu
* Added Google license to login page
* Removed mySQL credentials from API
* Created keys/recaptcha credentials page
* Created keys/mysql credentials page
* Updated login page where if a user navigated to /login and they are already authenticated, they would go to /my-scenarios OR /cas if they were referred

## 1.12.2
* Fixed broken links in /do/contact-do where it was still sending responses to contact.php, now references talk-to-me correctly
* Updated note in login.php so people understand better that an account is NOT required

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
* Changed name of Contact page from "contact.php" to "talk-to-me.php" in an attempt to confuse spam bots looking for contact forms.

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
* Updated cas-leaflet.js to handle adding/removing of markers to the new tables
* Updated save/load functions to handle adding/removing of markers to the new tables
* Added ID number to marker options
* Updated scenario version to v3
* Fix bug where when saving a scenario to account, scenario name would not clear with the modal reset
* Requested by SPAWN/PINCH

## 1.8.4
* Add LICENSE.md for github purposes

## 1.8.3
* Update security in /opt/bitnami/apache2/conf/httpd.conf
* Create list of blacklisted IPs and deny access
* Add WX.php to start weather gonkulating

## 1.8.2
* Remove old logging functions from API/Do pages

## 1.8.1
* Airspace update
* Minor bug fixes

## 1.8.0
* Updated logging functions to use JSON notation
* Edited CAS scenario permissions to allow anyone to view any other CAS scenario as long as they are logged in
* Removed erroneous sanitizeInput functions in API
* Removed test pages from public_html
* Removed bug where navbar attempted to get user's name even if they are not logged in
* Created admin-view-log page
* Created admin-logs page

## 1.7.0
* Move to a self-hosted version of Font Awesome to avoid any TIMEOUT issues on NIPR where icons couldn't load.

## 1.6.1
* Remove "Known Issues" card from index

## 1.6.0
* Update leaflet-ruler.js to show both NM and meters
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
* Updated validation.js to handle new form validation
* Updated API to add getUserNameByEmail, getUserEmailByID
* Updated login.php to handle scenario parameter
* Updated cas.php to handle scenario and share parameter

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
* Updated cas-leaflet.js to handle the showing/hiding of titles
* Requested by GOV

## 1.3.0
* Added Building label chit
* Added listeners/handlers for building label
* Updated cas-leaflet.js to handle building label
* Requested by JTACs

## 1.2.1
* Updated 15-Line formatting
* Updated cas-leaflet.js to handle new 15-Line

## 1.2.0
* Added Leaflet.GeometryUtil.js - Used to get bearing between 2 points
* Updated styling
* Bug fixes
* Added 9-Line modals
* Added 15-Line modals
* Updated cas-leaflet.js to handle additional functions

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
