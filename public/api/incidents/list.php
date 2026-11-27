<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Allow: GET, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Method not allowed.']);
    exit;
}
http_response_code(501);
echo json_encode(['success'=>false,'status'=>'not_implemented','message'=>'Incident listing is disabled until verified staff authentication and incident access policy are implemented.']);
