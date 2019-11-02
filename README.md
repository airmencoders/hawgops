# Hawg Ops
Private repository of website files.
website located at https://hawg-ops.com

## CAS Planner
Allows users to create CAS scenarios in order to mission plan for training missions.

# Change Log

## 1.8.3
* Update security in /opt/bitnami/apache2/conf/httpd.conf
* Create list of blacklisted IPs and deny access

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
