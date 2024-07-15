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
 * Strings for the quizaccess_sebserver plugin.
 *
 * @package   quizaccess_sebserver
 * @author    Kristina Isacson (kristina.isacson@id.ethz.ch)
 * @copyright 2024 ETH Zurich
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'SEB Server';
$string['quizismanagedbysebserver'] = 'The Exam is managed by SEB Server. You must launch Safe Exam Browser before attempting the exam.';
$string['managedbysebserver'] = 'The Exam is managed by SEB Server. Changing values might affect the exam setup, please proceed carefully. You can deactivate the connection to the SEB Server via the SEB Server section below';
$string['enablesebserver'] = 'Enable SEB Server';
$string['sebserver:managesebserver'] = 'Manage SEB Server';
$string['privacy:metadata'] = 'The SEB Server access rule plugin does not store any personal data.';
$string['connectionnotsetupyet'] = 'The connection is not setup yet';
$string['notemplate'] = 'No template';
$string['sebserverexamtemplate'] = 'Exam template';
$string['sebserverexamtemplate_help'] = 'Select an Exam template that suits your exam scenario';
$string['showquitbtn'] = 'Show quit button';
$string['sebserverquitsecret'] = 'Quit/unlock password';
$string['sebserverquitsecret_help'] = 'This password is prompted when users try to quit SEB with the "Quit" button or Ctrl-Q (CMD -Q) in the main browser window. If no quit password is set, then SEB just prompts "Are you sure you want to quit SEB?".';
$string['modificationinstruction'] = 'If you want to change settings, you must disable SEB Server and save the quiz settings, then enable SEB Server again. You can now select new values for the settings fields and save the test settings.';
$string['adminsonly'] = 'Admins only';
$string['resetseb'] = 'Release SEB Server connection';
$string['resetseb_help'] = 'Website administrators can terminate the connection to the SEB server.';
$string['examnotrestrictedyet'] = 'The browser exam key check is not activated! In order for the test to be completed, the browser exam key check must be activated in the SEB server.';
$string['launchsebserverconfig'] = 'Start Safe Exam Browser';
$string['downloadsebserverconfig'] = 'Download SEB Server configuration file';
$string['sebseverconfignotfound'] = ' SEB Server configuration file is not found!';
$string['autologintosebserver'] = 'SEB Server monitoring';
$string['templatemustbeselected'] = 'You must select an Exam template';
$string['connectionid'] = 'Connection ID';
$string['connectionname'] = 'Connection name';
$string['connectionurl'] = 'Connection endpoint';
$string['autologinurl'] = 'SEB Server autologin';
$string['accesstoken'] = 'Token';
$string['templateid'] = 'ID';
$string['templatename'] = 'Name';
$string['templatedescription'] = 'Description';
$string['templates'] = 'Templates';
$string['setting:sebserverconnectiondetails'] = 'Connection details';
$string['setting:sebserversettings'] = 'Settings';
$string['quizaccess/sebserver:canusesebserver'] = 'Can use SEB Server';
$string['quizaccess/sebserver:candeletesebserver'] = 'Can deactivate SEB Server';
$string['quizaccess/sebserver:managesebserverconnection'] = 'Can manage Seb Server connection';
$string['quizaccess/seb:sebserverautologinlink'] = 'Can autologin into SEB Server';
$string['selectemplate'] = 'Select Exam template';
$string['sebservertemplateid'] = 'SEB Server template';
$string['sebservertemplateid_help'] = 'Select an Exam template that suits your exam scenario';
$string['manageddevicetemplate'] = 'Exam Configuration ID =';