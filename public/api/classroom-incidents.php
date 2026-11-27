<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/db_connection.php';

const INCIDENT_TYPES = [
    'Flood', 'Typhoon / severe weather', 'Fire', 'Earthquake', 'Landslide',
    'Medical emergency', 'Other incident',
];
const DEMO_LOCATIONS = [
    'training-a' => ['Training point A', 10.25, 124.98],
    'training-b' => ['Training point B', 10.35, 125.05],
    'training-c' => ['Training point C', 10.15, 125.11],
];
const REPORT_FIELDS = 'id, incident_type, description, persons_affected, location_name,
    ST_Y(location) AS latitude, ST_X(location) AS longitude, status, created_at';

function reply(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_THROW_ON_ERROR);
    exit;
}

function validId(mixed $id): bool
{
    return is_string($id)
        && preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $id) === 1;
}

function findReport(PDO $db, string $id): array|false
{
    $statement = $db->prepare('SELECT ' . REPORT_FIELDS
        . ' FROM public.mdrrmo_classroom_incidents WHERE id = :id');
    $statement->execute(['id' => $id]);
    return $statement->fetch();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

$key = getenv('MDRRMO_DEMO_KEY');
if (!is_string($key) || strlen($key) < 32) {
    reply(503, ['error' => 'Classroom integration is awaiting configuration.']);
}
if (!hash_equals($key, $_SERVER['HTTP_X_MDRRMO_DEMO_KEY'] ?? '')) {
    reply(401, ['error' => 'Classroom API authorization required.']);
}

$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    reply(405, ['error' => 'Method not allowed.']);
}

try {
    if ($method === 'GET') {
        $id = $_GET['id'] ?? null;
        if ($id !== null && !validId($id)) {
            reply(400, ['error' => 'Invalid report ID.']);
        }
        $db = getDatabaseConnection();
        if ($id !== null) {
            $report = findReport($db, $id);
            if ($report === false) {
                reply(404, ['error' => 'Classroom report not found.']);
            }
            reply(200, ['report' => $report]);
        }
        $reports = $db->query('SELECT ' . REPORT_FIELDS
            . ' FROM public.mdrrmo_classroom_incidents ORDER BY created_at DESC, id DESC LIMIT 100')->fetchAll();
        reply(200, ['reports' => $reports]);
    }

    $contentType = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '', 2)[0]));
    if ($contentType !== 'application/json') {
        reply(415, ['error' => 'Send application/json.']);
    }
    $raw = file_get_contents('php://input', false, null, 0, 8193);
    if ($raw === false || strlen($raw) > 8192) {
        reply(413, ['error' => 'Report is too large.']);
    }
    try {
        $input = json_decode($raw, false, 8, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        reply(400, ['error' => 'Invalid JSON.']);
    }
    if (!is_object($input)) {
        reply(400, ['error' => 'Send a report object.']);
    }
    $id = $input->id ?? null;
    $type = $input->incident_type ?? null;
    $description = is_string($input->description ?? null) ? trim($input->description) : '';
    $affected = $input->persons_affected ?? null;
    $point = $input->training_point ?? null;
    $length = preg_match_all('/./us', $description);
    if (!validId($id) || !in_array($type, INCIDENT_TYPES, true)
        || $length < 5 || $length > 500 || str_contains($description, "\0")
        || !is_int($affected) || $affected < 0 || $affected > 10000
        || !is_string($point) || !isset(DEMO_LOCATIONS[$point])
        || ($input->fictional ?? null) !== true) {
        reply(422, ['error' => 'Choose an incident type and training point, enter 5–500 characters, and confirm the report is fictional.']);
    }

    [$locationName, $latitude, $longitude] = DEMO_LOCATIONS[$point];
    $db = getDatabaseConnection();
    $statement = $db->prepare('INSERT INTO public.mdrrmo_classroom_incidents
        (id, incident_type, description, persons_affected, location_name, location)
        VALUES (:id, :type, :description, :affected, :location_name,
                ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326))
        ON CONFLICT (id) DO NOTHING');
    $statement->execute([
        'id' => $id,
        'type' => $type,
        'description' => $description,
        'affected' => $affected,
        'location_name' => $locationName,
        'longitude' => $longitude,
        'latitude' => $latitude,
    ]);
    $created = $statement->rowCount() === 1;
    $report = findReport($db, $id);
    if ($report === false) {
        throw new RuntimeException('Report could not be read after insert.');
    }
    if ($report['incident_type'] !== $type || $report['description'] !== $description
        || (int) $report['persons_affected'] !== $affected || $report['location_name'] !== $locationName) {
        reply(409, ['error' => 'That report ID already belongs to a different submission.']);
    }
    reply($created ? 201 : 200, ['report' => $report]);
} catch (Throwable $error) {
    $code = $error instanceof PDOException ? $error->getCode() : 'application';
    error_log("MDRRMO classroom API failure ($code)");
    reply(503, ['error' => 'Classroom reports are temporarily unavailable.']);
}
