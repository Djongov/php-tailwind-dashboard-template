<?php
use Database\MYSQL;
use Api\Output;
use Logs\SystemLog;

// Only trigger script if reqeuest method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $json_data = file_get_contents('php://input'); // Read the data from the POST request
    // Integrity check
    if ($json_data = json_decode($json_data)) {
        // We pretty print the JSON before adding it to the log file
        $json_data = json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        // Convert it to a PHP assoc array
        $json_array = json_decode($json_data, true);
        // Only proceed if the array key is csp-report
        if (isset($json_array['csp-report'])) {
            // include the db connection file
            // Start adding directives into variables. We check if they are set because not all csp reports contain the entire structure
            $domain = (isset($json_array['csp-report']['document-uri'])) ? parse_url($json_array['csp-report']['document-uri'], PHP_URL_HOST) : null;
            // Let's make sure that the domain is on the approved lits
            $result = MYSQL::queryPrepared("SELECT `domain` FROM `csp_approved_domains` WHERE `domain` = ?", $domain);
            if ($result->num_rows === 0) {
                //include_once $_SERVER['DOCUMENT_ROOT'] . '/functions/write-logfile.php';
                SystemLog::write('$domain = ' . $domain . ' (' . gettype($domain) . ') and attempted to send a CSP report', 'CSP Domain Not Allowed');
                Output::error('Domain not allowed', 401);
            }
            $url = (isset($json_array['csp-report']['document-uri'])) ? $json_array['csp-report']['document-uri'] : null;
            $referrer = (isset($json_array['csp-report']['referrer'])) ? $json_array['csp-report']['referrer'] : null;
            $violated_directive = (isset($json_array['csp-report']['violated-directive'])) ? $json_array['csp-report']['violated-directive'] : null;
            $effective_directive = (isset($json_array['csp-report']['effective-directive'])) ? $json_array['csp-report']['effective-directive'] : null;
            $original_policy = (isset($json_array['csp-report']['original-policy'])) ? $json_array['csp-report']['original-policy'] : null;
            $disposition = (isset($json_array['csp-report']['disposition'])) ? $json_array['csp-report']['disposition'] : null;
            $blocked_uri = (isset($json_array['csp-report']['blocked-uri'])) ? $json_array['csp-report']['blocked-uri'] : null;
            $line_number = (isset($json_array['csp-report']['line-number'])) ? $json_array['csp-report']['line-number'] : null;
            $column_number = (isset($json_array['csp-report']['column-number'])) ? $json_array['csp-report']['column-number'] : null;
            $source_file = (isset($json_array['csp-report']['source-file'])) ? $json_array['csp-report']['source-file'] : null;
            $status_code = (isset($json_array['csp-report']['status-code'])) ? $json_array['csp-report']['status-code'] : null;
            $script_sample = (isset($json_array['csp-report']['script-sample'])) ? $json_array['csp-report']['script-sample'] : null;
            // Start prepared statement
            MYSQL::queryPrepared('INSERT INTO `csp_reports` (`data`, `domain`, `url`, `referrer`, `violated_directive`, `effective_directive`, `original_policy`, `disposition`, `blocked_uri`, `line_number`, `column_number`, `source_file`, `status_code`, `script_sample`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?)', [$json_data, $domain, $url, $referrer, $violated_directive, $effective_directive, $original_policy, $disposition, $blocked_uri, $line_number, $column_number, $source_file, $status_code, $script_sample]);
            
            http_response_code(204); // Send HTTP 204 No Content response
        }
    } else {
        // If csp-report is not the top array key, throw 400 and say Incorrect data
        Output::error('Incorrect data', 400);
    }
} else {
    Output::error('Incorrect method', 405);
}
