<?php
/**
 * Beacon API Log Handler
 *
 * This PHP script handles log messages sent through the Beacon API and stores them in specific folders based on the given page name.
 *
 */

/**
 * Retrieve data from Beacon API and process log message.
 *
 * This function retrieves data from Beacon API and processes the log message to be stored in the log file.
 *
 * @return void
 */
function processLogMessage()
{
    // Check if data is sent via the Beacon API
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.1 405 Method Not Allowed');
        exit;
    }

    $cip = $_SERVER['REMOTE_ADDR'];

    // Retrieve the raw input data sent via the Beacon API
    $data = file_get_contents('php://input');

    // If the data is not empty, process the log message
    if (!empty($data)) {
        // Decode the JSON data
        $data = json_decode($data, true);
        include 'customFunctions.php';
		$data = sanitizeValues($data);

        // Extract the page key and remove unwanted characters
        $key = $data['page'];
        $key = directory($key);

        $user = $data['user'];

        // Generate the log message with current date and time
        $message = "[$cip] [" . date('d-m-y') . " " . date('H') . ':' . date('i') . '] ['.$user.'] - ';
        $message .= $data['msg'] . PHP_EOL;

        // Set the root path for the log files
        $root = $root_dir;

        // Define the destination folder for the log file based on the page key
        $destinationfolder = $root . $key . '/';

        // Define the full path for the log file
        $destinationfile = $destinationfolder . $key . '.log';

        // Check if the destination folder exists, if not, create it
        if (!is_dir($destinationfolder)) {
            $folder = mkdir($destinationfolder, 0755);
            if (!$folder) {
                header('HTTP/1.1 500 Internal Server Error' . $folder);
                // header('HTTP/1.1 500 Internal Server Error');
                exit;
            }
        }

        // Append the message to the log file
        if (file_put_contents($destinationfile, $message, FILE_APPEND | LOCK_EX) === false) {
            header('HTTP/1.1 500 Internal Server Error');
            exit;
        }

        // Respond with success status
        header('HTTP/1.1 200 OK');
        exit;
    }

    header('HTTP/1.1 400 Bad Request');
    exit;
}

// Call the function to process the log message
processLogMessage();
