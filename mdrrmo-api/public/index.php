<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
echo json_encode(['project' => 'mdrrmo-api', 'status' => 'starter', 'notice' => 'API scaffold only; emergency incident submission is not live.']);
