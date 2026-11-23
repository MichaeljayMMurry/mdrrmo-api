<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
http_response_code(501);
echo json_encode(['status' => 'not_implemented', 'message' => 'Incident listing requires authenticated, authorized MDRRMO personnel and an approved data policy.']);
