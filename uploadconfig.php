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
 * Accept uploading files by web service token to the user draft file area.
 *
 * POST params:
 *  token => the web service user token (needed for authentication)
 *  filepath => file path (where files will be stored)
 *  [_FILES] => for example you can send the files with <input type=file>,
 *              or with curl magic: 'file_1' => '@/path/to/file', or ...
 *  itemid   => The draftid - this can be used to add a list of files
 *              to a draft area in separate requests. If it is 0, a new draftid will be generated.
 *
 * @package    quizaccess_sebserver
 * @copyright  ETH Zurich 2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * AJAX_SCRIPT - exception will be converted into JSON
 */
define('AJAX_SCRIPT', true);

/**
 * NO_MOODLE_COOKIES - we don't want any cookie
 */
define('NO_MOODLE_COOKIES', true);
require_once('../../../../config.php');
require_once($CFG->dirroot . '/webservice/lib.php');

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

$filepath = optional_param('filepath', '/', PARAM_PATH);
$itemid = optional_param('itemid', 0, PARAM_INT);

echo $OUTPUT->header();

// Authenticate the user.
$token = required_param('token', PARAM_ALPHANUM);
$quizid = required_param('quizid', PARAM_INT);
$webservicelib = new webservice();
$authenticationinfo = $webservicelib->authenticate_user($token);
$fileuploaddisabled = empty($authenticationinfo['service']->uploadfiles);
if ($fileuploaddisabled) {
    throw new webservice_access_exception('Web service file upload must be enabled
                                           in SebService webservice settings');
}
if (! $cm = get_coursemodule_from_instance('quiz', $quizid)) {
    throw new \moodle_exception('Uknown quiz with id: ' . $quizid);
}
$context = context_module::instance($cm->id);

$fs = get_file_storage();

$totalsize = 0;
$files = [];
foreach ($_FILES as $fieldname => $uploadedfile) {
    // Check upload errors.
    if (!empty($_FILES[$fieldname]['error'])) {
        switch ($_FILES[$fieldname]['error']) {
            case UPLOAD_ERR_INI_SIZE:
                throw new moodle_exception('upload_error_ini_size', 'repository_upload');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                throw new moodle_exception('upload_error_form_size', 'repository_upload');
                break;
            case UPLOAD_ERR_PARTIAL:
                throw new moodle_exception('upload_error_partial', 'repository_upload');
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new moodle_exception('upload_error_no_file', 'repository_upload');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new moodle_exception('upload_error_no_tmp_dir', 'repository_upload');
                break;
            case UPLOAD_ERR_CANT_WRITE:
                throw new moodle_exception('upload_error_cant_write', 'repository_upload');
                break;
            case UPLOAD_ERR_EXTENSION:
                throw new moodle_exception('upload_error_extension', 'repository_upload');
                break;
            default:
                throw new moodle_exception('nofile');
        }
    }

    // Scan for viruses.
    \core\antivirus\manager::scan_file($_FILES[$fieldname]['tmp_name'], $_FILES[$fieldname]['name'], true);

    $file = new stdClass();
    $file->filename = clean_param($_FILES[$fieldname]['name'], PARAM_FILE);
    // Check system maxbytes setting.
    if (($_FILES[$fieldname]['size'] > get_max_upload_file_size($CFG->maxbytes))) {
        // Oversize file will be ignored, error added to array to notify
        // web service client.
        $file->errortype = 'fileoversized';
        $file->error = get_string('maxbytes', 'error');
    } else {
        $file->filepath = $_FILES[$fieldname]['tmp_name'];
        // Calculate total size of upload.
        $totalsize += $_FILES[$fieldname]['size'];
        // Size of individual file.
        $file->size = $_FILES[$fieldname]['size'];
    }
    $files[] = $file;
}
if (count($files) > 1) { // We allow only 1 file per quiz.
    throw new moodle_exception('Only one file is allowed');
}
if (count($files) <= 0) {
    throw new \moodle_exception('File details (boundery etc) are missing.
                                Did you set the content type to multipart/form-data; boundary=');
}


$fs = get_file_storage();
$itemid  = 0;

// Get any existing file size limits.
$maxupload = get_user_max_upload_file_size($context, $CFG->maxbytes);

// Check the size of this upload.
if ($maxupload !== USER_CAN_IGNORE_FILE_SIZE_LIMITS && $totalsize > $maxupload) {
    throw new file_exception('userquotalimit');
}

$results = [];
foreach ($files as $file) {
    if (!empty($file->error)) {
        // Including error and filename.
        $results[] = $file;
        continue;
    }

    // Copy the file into temp.
    $realfilename = 'SEBServerSettings.seb';
    $destinationdir = $CFG->tempdir.'/sebserver';
    if (!is_dir($destinationdir)) {
        mkdir($destinationdir, 0777, true);
    }
    $destinationfile = $destinationdir . '/' . $realfilename;
    file_put_contents($destinationfile, $file);
    // Generate componenet file.
    $filerecord = new stdClass;
    $filerecord->component = 'quizaccess_sebserver';
    $filerecord->contextid = $context->id;
    $filerecord->filearea = 'filemanager_sebserverconfigfile';
    $filerecord->filename = $realfilename;
    $filerecord->filepath = '/';
    $filerecord->itemid = 0;
    $filerecord->filesize = $file->size;

    // Check if the file already exist.
    $existingfile = $fs->file_exists($filerecord->contextid, $filerecord->component, $filerecord->filearea,
                $filerecord->itemid, $filerecord->filepath, $filerecord->filename);
    if ($existingfile) {
          $fs->delete_area_files($filerecord->contextid, $filerecord->component, $filerecord->filearea);
    }
    if ($storedfile = $fs->create_file_from_pathname($filerecord, $destinationfile)) {
        unlink($destinationfile);
    }
    $results[] = $filerecord;

    // Log the event when a file is uploaded to the draft area.
    $logevent = \core\event\draft_file_added::create([
            'objectid' => $storedfile->get_id(),
            'context' => $context,
            'other' => [
                    'itemid' => $filerecord->itemid,
                    'filename' => $filerecord->filename,
                    'filesize' => $filerecord->filesize,
                    'filepath' => $filerecord->filepath,
                    'contenthash' => $storedfile->get_contenthash(),
            ],
     ]);
     $logevent->trigger();

}
$DB->set_field('quizaccess_sebserver', 'sebservercalled', 1, ['sebserverquizid' => $quizid]);
echo json_encode($results);
