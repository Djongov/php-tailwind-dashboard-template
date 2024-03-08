<?php

namespace Models;

use App\Database\MYSQL;
use App\Logs\SystemLog;
use App\Exceptions\CSP as CSPException;

class CSP
{
    public function domainExist(string $domain) : bool
    {
        $result = MYSQL::queryPrepared("SELECT `domain` FROM `csp_approved_domains` WHERE `domain` = ?", $domain);
        return $result->num_rows === 1;
    }
    public function save(array $jsonArray, string $domain) : bool
    {
        $url = $jsonArray['csp-report']['document-uri'] ?? null;
        $referrer = $jsonArray['csp-report']['referrer'] ?? null;
        $violatedDirective = $jsonArray['csp-report']['violated-directive'] ?? null;
        $effectiveDirective = $jsonArray['csp-report']['effective-directive'] ?? null;
        $originalPolicy = $jsonArray['csp-report']['original-policy'] ?? null;
        $disposition = $jsonArray['csp-report']['disposition'] ?? null;
        $blockedUri = $jsonArray['csp-report']['blocked-uri'] ?? null;
        $lineNumber = $jsonArray['csp-report']['line-number'] ?? null;
        $columnNumber = $jsonArray['csp-report']['column-number'] ?? null;
        $sourceFile = $jsonArray['csp-report']['source-file'] ?? null;
        $statusCode = $jsonArray['csp-report']['status-code'] ?? null;
        $scriptSample = $jsonArray['csp-report']['script-sample'] ?? null;

        $save = MYSQL::queryPrepared('INSERT INTO `csp_reports` (`data`, `domain`, `url`, `referrer`, `violated_directive`, `effective_directive`, `original_policy`, `disposition`, `blocked_uri`, `line_number`, `column_number`, `source_file`, `status_code`, `script_sample`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?)', [json_encode($jsonArray), $domain, $url, $referrer, $violatedDirective, $effectiveDirective, $originalPolicy, $disposition, $blockedUri, $lineNumber, $columnNumber, $sourceFile, $statusCode, $scriptSample]);

        if ($save->affected_rows === 1) {
            return true;
        } else {
            SystemLog::write('CSP report not saved', 'CSP Report Not Saved');
            throw (new CSPException())->reportNotSaved();
        }
    }
}
