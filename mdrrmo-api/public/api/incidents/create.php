<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
http_response_code(501);
echo json_encode(['status' => 'not_implemented', 'message' => 'Emergency intake is disabled until an authorized MDRRMO office approves the workflow and security controls. For emergencies, contact local responders or call 911.']);
