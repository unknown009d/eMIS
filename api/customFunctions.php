<?php

// ==== Ronit Start Here ====
$zip_n = 'archive.zip';
$root_dir = '../assets/'; // WARNING: Do not change it until you know what you are doing
$root_dir_remove_length = 3; // Change this number according to the $root_dir ../ (3)
$ast = '/documents/';

function directory($key)
{
    return str_replace(array('/', '-'), '', $key);
}
//echo directory("pp/09-23/20");
function create_docs($root, $key)
{
    $fullPath = $root . $key;
    if (!is_dir($fullPath)) {
        if (!mkdir($fullPath, 0755, true)) {
            echo "Folder not created";
            exit; // Stop execution if folder creation fails
        }
    }
}
function upld_docs($filedata, $uploadDirectory, $new_file, $type)
{

    $fileTmpPath = $filedata['tmp_name'];
    $fileName = $filedata['name'];
    $fileSize = $filedata['size'];
    $fileType = $filedata['type'];

    $newFileName = $type . "_" . $new_file . '_' . date('dmyHis');
    // Get the file extension
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileExists = true;
    $count = 0;

    // Check if the file already exists without any suffix
    if (!file_exists($uploadDirectory . $newFileName . '.' . $fileExtension)) {
        $count = 1; // Set count to 1 for the first iteration
    } else {
        // Iterate through existing files to find the highest count
        $existingFiles = glob($uploadDirectory . $newFileName . '*.' . $fileExtension);
        foreach ($existingFiles as $existingFile) {
            $existingCount = sscanf($existingFile, $uploadDirectory . $newFileName . '_%d.' . $fileExtension, $num);
            if ($existingCount && $num > $count) {
                $count = $num;
            }
        }
        // Increment the count for the next iteration
        $count++;
    }
    $destPath = $uploadDirectory . $newFileName;
    if ($count > 1) {
        $destPath .= '_' . $count;
    }
    $destPath .= '.' . $fileExtension;
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        return 1;
    }
    return 0;
}
function del_docs($zipFileName, $s_path, $filename)
{
    global $zip_n;
    $s_path .= $filename;
    $zipFileName .= $zip_n;
    if (file_exists($zipFileName)) {
        // If the ZIP file already exists, open it for appending
        $zip = new ZipArchive();
        if ($zip->open($zipFileName) === true) {
            // Append files to the existing ZIP archive

            // Example: Add a single file to the archive

            $zip->addFile($s_path, $filename);

            $zip->close();
            goto sos;
        } else {
            //Failed to open existing ZIP archive for appending.
            return -1;
        }
    } else {
        // If the ZIP file does not exist, create a new ZIP archive
        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {

            // Add files and directories to the new ZIP archive

            // Example: Add a single file to the archive
            $zip->addFile($s_path, $filename);
            $zip->close();

            goto sos;
        } else {
            return -1;
        }
    }
    sos:
    if (unlink($s_path)) {
        // File deletion successful
        //echo "   & File deleted successfully: $s_path";
        return 1;
    } else {
        // Error while deleting the file
        //echo "<br>Error deleting the file: $s_path";
        return 0;
    }
}
function countFilesInZipArchive($pid)
{
    global $zip_n, $root_dir, $ast;
    $zipFilePath = $root_dir . directory($pid) . $ast . $zip_n;
    $fileCount = 0;

    // Check if the zip archive exists
    if (file_exists($zipFilePath)) {
        // Open the zip archive
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            // Get the number of files in the zip archive
            $fileCount = $zip->numFiles;

            // Close the zip archive
            $zip->close();
        }
        return array("success" => true, 'message' => $fileCount);
    } else {
        // return array("success" => false, 'message' => $zipFilePath);
        return array("success" => false, 'message' => 0);
    }
}

function deleteFilesFromZip($pid, $filenameToDelete = null)
{
    global $zip_n, $root_dir, $ast;
    $zipFile = $root_dir . directory($pid) . $ast . $zip_n;

    // Open the original ZIP file
    $zip = new ZipArchive();
    if ($zip->open($zipFile) === true) {
        if ($filenameToDelete === null) {
            // Delete all files from the ZIP
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $zip->deleteIndex($i);
            }
            $zip->close();
            return true;
        } else {
            // Find the file index in the ZIP
            $indexToDelete = $zip->locateName($filenameToDelete);

            if ($indexToDelete !== false) {
                // Delete the specified file from the ZIP
                $zip->deleteIndex($indexToDelete);
                $zip->close();
                return true;
            } else {
                // File not found in the ZIP
                $zip->close();
                return false;
            }
        }
    } else {
        // Failed to open the ZIP file
        return false;
    }
}

function extractArchive($pid)
{
    global $zip_n, $root_dir, $ast;
    $zipFilePath = $root_dir . directory($pid) . $ast . $zip_n;
    $restorePath = $root_dir . directory($pid) . $ast;
    $zip = new ZipArchive();

    if ($zip->open($zipFilePath) === true) {
        // Extract all files from the archive
        $zip->extractTo($restorePath);
        $zip->close();
        deleteFilesFromZip($pid);
        return array('success' => true, 'message' => 'All the files inside the archive are restored');
    } else {
        return array('success' => false, 'message' => 'There was a problem in restoring all the files from the archive');
    }
}

function restoreFileFromArchive($pid, $fileName)
{
    global $zip_n, $root_dir, $ast;
    $archivePath = $root_dir . directory($pid) . $ast . $zip_n;
    $restorePath = $root_dir . directory($pid) . $ast;
    // Check if the archive exists
    if (!file_exists($archivePath)) {
        // "Archive not found.";
        return array('success' => false, 'message' => 'Archive not found...');
    }

    // Create a ZipArchive object
    $zip = new ZipArchive();

    // Open the archive
    if ($zip->open($archivePath) === true) {
        // Search for the specific file within the archive
        $fileIndex = $zip->locateName($fileName);

        // If the file is found, extract it to the restore path
        if ($fileIndex !== false) {
            $extracted = $zip->extractTo($restorePath, $fileName);
            $zip->close();

            if ($extracted) {
                if (deleteFilesFromZip($pid, $fileName)) {
                    return array('success' => true, 'message' => $fileName . ' restored successfully');
                }
                return array('success' => true, 'message' => $fileName . ' restored successfully but not moved from archive');
                //return "File restored successfully.";
            } else {
                return array('success' => false, 'message' => $fileName . ' was failed to restore');
                //return "Failed to restore the file.";
            }
        } else {
            $zip->close();
            return array('success' => false, 'message' => $fileName . ' not found');
            //return "File not found in the archive.";
        }
    } else {
        return array('success' => false, 'message' => 'Failed to open archive');
        //return "Failed to open the archive.";
    }
}
function listFilesInZipArchive($pid)
{
    if ($pid == null) {
        return array("success" => false, 'message' => "Proper file path isn't given...");
    }
    global $zip_n, $root_dir, $ast;
    $zipFilePath = $root_dir . directory($pid) . $ast . $zip_n;
    $fileList = [];

    // Check if the zip archive exists
    if (file_exists($zipFilePath)) {
        // Open the zip archive
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            // Get the number of files in the zip archive
            $numFiles = $zip->numFiles;

            // Iterate through each file in the zip archive
            for ($i = 0; $i < $numFiles; $i++) {
                // Get the name of the file at the current index
                $fileName = $zip->getNameIndex($i);

                // Get the file statistics for the current index
                $stat = $zip->statIndex($i);
                $fileSize = $stat['size']; // Size of the file in bytes

                // Convert the file size to KB or MB
                $fileSizeFormatted = formatSizeUnits($fileSize);

                // Add the file name and size to the file list array
                $fileList[] = array(
                    'name' => $fileName,
                    'size' => $fileSizeFormatted
                );
            }

            // Close the zip archive
            $zip->close();
        }
    }
    return array("success" => true, 'message' => $fileList);
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}



// ==== Ronit End Here ====

function sanitizeValues($input)
{
    if (is_array($input)) {
        foreach ($input as &$value) {
            $value = sanitizeValues($value);
        }
        unset($value); // Unset reference variable to avoid potential issues
    } else {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    return $input;
}

function isVulnerable($input)
{
    if (is_array($input)) {
        foreach ($input as $value) {
            if (is_array($value)) {
                if (isVulnerable($value)) {
                    return true;
                }
            } else {
                $value = stripslashes($value);
                $ishtml = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                if ($ishtml !== $value) {
                    return true;
                }
            }
        }
    } else {
        $input = stripslashes($input);
        $ishtml = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        if ($ishtml !== $input) {
            return true;
        }
    }

    return false;
}

function sconvertToRupees($amount)
{
    $ones = array('Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine');
    $tens = array('', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');
    $teens = array('Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
    $suffixes = array('', 'Thousand', 'Lakh', 'Crore');

    $result = '';
    $amount = strval($amount); // Convert amount to string

    if ($amount == '0') {
        return 'Zero Rupees';
    }

    // Split the amount into groups of 3 digits
    $groups = str_split(str_pad($amount, ceil(strlen($amount) / 3) * 3, '0', STR_PAD_LEFT), 3);
    $groupCount = count($groups);

    // Process each group
    foreach ($groups as $index => $group) {
        $group = (int) $group; // Convert the group back to integer
        if ($group == 0) {
            continue; // Skip processing if the group is zero
        }

        $digits = str_split(str_pad($group, 3, '0', STR_PAD_LEFT));

        // Process the hundreds place
        if ($digits[0] != 0) {
            $result .= $ones[$digits[0]] . ' Hundred ';
        }

        // Process the tens and ones place
        $tensPlace = $digits[1];
        $onesPlace = $digits[2];

        if ($tensPlace == 0 && $onesPlace == 0) {
            // Do nothing if both tens and ones place are zero
        } elseif ($tensPlace == 0) {
            $result .= $ones[$onesPlace] . ' ';
        } elseif ($tensPlace == 1) {
            $result .= $teens[$onesPlace] . ' ';
        } else {
            $result .= $tens[$tensPlace] . ' ';
            if ($onesPlace != 0) {
                $result .= $ones[$onesPlace] . ' ';
            }
        }

        // Append the appropriate suffix
        $suffixIndex = $groupCount - $index - 1;
        if ($suffixIndex > 0 && $group != 0) {
            $result .= $suffixes[$suffixIndex] . ' ';
        }
    }

    // Append 'Rupees' to the result
    $result .= 'Rupees';

    return $result;
}


/* Function to print rupees in words... */
$ones = array(
    0 => '',
    1 => 'One',
    2 => 'Two',
    3 => 'Three',
    4 => 'Four',
    5 => 'Five',
    6 => 'Six',
    7 => 'Seven',
    8 => 'Eight',
    9 => 'Nine',
    10 => 'Ten',
    11 => 'Eleven',
    12 => 'Twelve',
    13 => 'Thirteen',
    14 => 'Fourteen',
    15 => 'Fifteen',
    16 => 'Sixteen',
    17 => 'Seventeen',
    18 => 'Eighteen',
    19 => 'Nineteen'
);

$tens = array(
    2 => 'Twenty',
    3 => 'Thirty',
    4 => 'Forty',
    5 => 'Fifty',
    6 => 'Sixty',
    7 => 'Seventy',
    8 => 'Eighty',
    9 => 'Ninety'
);
function cnvrtRupees($amount)
{
    global $ones;
    global $tens;
    $amount = number_format($amount, 2, '.', '');

    $parts = explode('.', $amount);
    $rupees = intval($parts[0]);
    $paise = isset($parts[1]) ? intval($parts[1]) : 0;

    $rupees_in_words = '';

    if ($rupees > 0) {
        if ($rupees >= 10000000) {
            $crores = intval($rupees / 10000000);
            $rupees_in_words .= cnvrtRupees($crores) . ' Crore ';
            $rupees %= 10000000;
        }

        if ($rupees >= 100000) {
            $lakhs = intval($rupees / 100000);
            $rupees_in_words .= cnvrtRupees($lakhs) . ' Lakh ';
            $rupees %= 100000;
        }

        if ($rupees >= 1000) {
            $thousands = intval($rupees / 1000);
            $rupees_in_words .= cnvrtRupees($thousands) . ' Thousand ';
            $rupees %= 1000;
        }

        if ($rupees >= 100) {
            $hundreds = intval($rupees / 100);
            $rupees_in_words .= cnvrtRupees($hundreds) . ' Hundred ';
            $rupees %= 100;
        }

        if ($rupees > 0) {
            if ($rupees >= 20) {
                $tens_digit = intval($rupees / 10);
                $rupees_in_words .= $tens[$tens_digit] . ' ';
                $rupees %= 10;
            }

            $rupees_in_words .= $ones[$rupees] . ' ';
        }
    }

    $in_words = $rupees_in_words;

    return trim($in_words);
}

function convertToRupees($amount)
{
    global $ones;
    global $tens;
    $rupees = cnvrtRupees($amount);

    $amount = number_format($amount, 2, '.', '');

    $parts = explode('.', $amount);
    $paise = isset($parts[1]) ? intval($parts[1]) : 0;

    $paise_in_words = '';

    if ($paise > 0) {
        if ($paise >= 20) {
            $tens_digit = intval($paise / 10);
            $paise_in_words .= $tens[$tens_digit] . ' ';
            $paise %= 10;
        }

        $paise_in_words .= $ones[$paise] . ' ';
        $paise_in_words .= 'Paisa';
    }

    $in_words = $rupees . " Rupees " . $paise_in_words;

    return trim($in_words);
}

/* Give ... to texts if it overflows */
function truncateText($text, $limit)
{
    if (strlen($text) > $limit) {
        $text = substr($text, 0, $limit) . '...';
    }
    return $text;
}

function getCurrentFinancialYear()
{
    $currentYear = date('Y');
    $currentMonth = date('n');
    $financialYearStartMonth = 4; // April

    if ($currentMonth < $financialYearStartMonth) {
        $currentYear--;
    }

    $financialYear = $currentYear . '-' . substr($currentYear + 1, 2, 3);
    return $financialYear;
}

function selectQ($conn, $sql, $params = [])
{
    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $types = '';
        $paramValues = [];

        foreach ($params as $param) {
            $types .= getParamType($param);
            $paramValues[] = $param;
        }

        $stmt->bind_param($types, ...$paramValues);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $rows;
}

function getParamType($param)
{
    if (is_int($param)) {
        return 'i'; // Integer
    } elseif (is_float($param)) {
        return 'd'; // Double
    } elseif (is_string($param)) {
        return 's'; // String
    } else {
        return 'b'; // Blob or unknown
    }
}

function getTestReportByPid($conn, $pid, $search = '')
{
    // Sanitize the input parameters to prevent SQL injection
    $pid = sanitizeValues($pid);
    $search = sanitizeValues($search);

    // SQL query to fetch specific columns with search filtering
    $sql = "SELECT r_no, test, cycle, status, start_date, complete_date, released_date, mec, min, maj, tot, remarks, documents, id FROM tbl_test_report WHERE p_id='$pid'";

    if (!empty($search)) {
        // Adding search filter to the query
        $search = mysqli_real_escape_string($conn, $search);
        $sql .= " AND (r_no LIKE '%$search%' OR test LIKE '%$search%')";
    }

    $sql .= " ORDER BY id DESC";

    $result = mysqli_query($conn, $sql);

    $response = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response[] = $row;
        }
    }

    return $response;
}

function rrmdir($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            if (!rrmdir($path)) {
                return "Error deleting subdirectory: $path";
            }
        } else {
            if (!unlink($path)) {
                return "Error deleting file: $path";
            }
        }
    }

    if (!rmdir($dir)) {
        return "Error deleting directory: $dir";
    }

    return true;
}

function removeDirectory($rawpath) {
    global $root_dir;
    $delpath = $root_dir . directory($rawpath);
    
    if (is_dir($delpath)) {
        $result = rrmdir($delpath);
        if ($result === true) {
            return [1, "Directory Removed"];
        } else {
            return [0, $result];
        }
    } else {
        return [1,"Couldn't find any directory"];
    }
}

function fdateeasy($inputDate) {
    $dateObj = new DateTime($inputDate);

    // Get day, month, and year
    $day = $dateObj->format('j');
    $month = $dateObj->format('F');
    $year = $dateObj->format('Y');

    // Format day with suffix
    $dayWithSuffix = $day . 'th';
    if ($day == 1 || $day == 21 || $day == 31) {
        $dayWithSuffix = $day . 'st';
    } else if ($day == 2 || $day == 22) {
        $dayWithSuffix = $day . 'nd';
    } else if ($day == 3 || $day == 23) {
        $dayWithSuffix = $day . 'rd';
    }

    // Get hours and minutes
    $hours = $dateObj->format('H');
    $minutes = $dateObj->format('i');

    // Convert hours to 12-hour format and determine AM/PM
    $ampm = $hours >= 12 ? 'pm' : 'am';
    $formattedHours = ($hours % 12) ?: 12;

    // Combine formatted parts into the desired output string
    $formattedDate = "{$dayWithSuffix} {$month} {$year} at {$formattedHours}:{$minutes}{$ampm}";

    return $formattedDate;
}

function addZeros($data, $num = 4) {
    return str_pad($data, $num, '0', STR_PAD_LEFT);
}

function fmny($date) {
    $dateTime = new DateTime($date);
    return $dateTime->format("My"); // Month Year format, e.g., "Aug23"
}