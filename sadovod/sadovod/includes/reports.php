<?php

function add_report($report_type, $report_text, $user_id = 0)
{
    global $wpdbx;

    $report_type = parse_int($report_type);
    $report_types = array(
        0 => __('Error parsing report.', 'oneunion'),
        1 => __('New CSP report received.', 'oneunion'),

        50 => __('New PGP report received.', 'oneunion')
    );

    if (isset($report_types[$report_type])) {
        $report_title = $report_types[$report_type];
        $report_text = stripslashes(sanitize_text_field($report_text));

        $user_id = parse_int($user_id);
        if (empty($user_id)) {
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
            }
        }

        if (!empty($report_title) && !empty($report_text)) {
            $wpdbx->insert(
                $wpdbx->prefix . 'reports',
                array(
                    'user_id' => $user_id,
                    'report_type' => $report_type,
                    'report_title' => $report_title,
                    'report_text' => $report_text
                ),
                array('%d', '%d', '%s', '%s', '%s')
            );
        }
    }
}

/**
 * @param array $report
 * 
 * @return bool
 */
function check_csp_record($report)
{
    if (!is_array($report))
        return false;

    if (isset($report['csp-report'])) {
        $pattern = array(
            'document-uri' => 'string',
            'effective-directive' => 'string',
            'disposition' => 'string',
            'status-code' => 'string',
            'blocked-uri' => 'string',
            'referrer' => 'string',
            'violated-directive' => 'string',
            'original-policy' => 'string',
            'source-file' => 'string',
            'script-sample' => 'string',
            'line-number' => 'string',
            'column-number' => 'string',
        );
        if ($differences = array_diff_key($pattern, $report['csp-report'])) {
            foreach ($differences as $difference) {
                if (!array_search($difference, $pattern))
                    return false;
            }
        }

        return true;
    } elseif (isset($report['type']) && $report['type'] == 'csp-violation') {
        $pattern = array(
            'documentURL' => 'string',
            'effectiveDirective' => 'string',
            'disposition' => 'string',
            'statusCode' => 'string',
            'blockedURL' => 'string',
            'referrer' => 'string',
            'violatedDirective' => 'string',
            'originalPolicy' => 'string',
            'sourceFile' => 'string',
            'lineNumber' => 'string',
        );

        if ($differences = array_diff_key($pattern, $report['body'])) {
            foreach ($differences as $difference) {
                if (!array_search($difference, $pattern))
                    return false;
            }
        }

        return true;
    }

    $report_text = sprintf(__('Report content: %s', 'oneunion'), json_encode($report));
    $report_text = stripslashes(sanitize_text_field($report_text));
    add_report(0, $report_text);

    return false;
}
