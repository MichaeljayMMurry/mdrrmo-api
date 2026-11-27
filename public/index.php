<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(['project'=>'mdrrmo-api','status'=>'starter','notice'=>'Educational API scaffold only. Emergency incident submission is not live.']);
