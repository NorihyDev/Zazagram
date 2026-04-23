<?php
// ============================================================
// includes/db.php — JSON "database" helper functions
// All read/write operations on JSON files go through here
// ============================================================

require_once __DIR__ . '/../config.php';

/**
 * Read a JSON file and return its contents as a PHP array.
 */
function db_read(string $file): array {
    $path = DATA_PATH . '/' . $file;
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    return json_decode($content, true) ?? [];
}

/**
 * Write a PHP array to a JSON file with file locking.
 */
function db_write(string $file, array $data): bool {
    $path = DATA_PATH . '/' . $file;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fp = fopen($path, 'c');
    if (!$fp) return false;
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
    return true;
}

/**
 * Get the next auto-increment ID for a collection.
 */
function db_next_id(array $records): int {
    if (empty($records)) return 1;
    return max(array_column($records, 'id')) + 1;
}

/**
 * Find a single record by field value.
 */
function db_find_one(array $records, string $field, $value): ?array {
    foreach ($records as $record) {
        if (isset($record[$field]) && $record[$field] == $value) {
            return $record;
        }
    }
    return null;
}

/**
 * Find all records matching a field value.
 */
function db_find_all(array $records, string $field, $value): array {
    return array_values(array_filter($records, fn($r) => isset($r[$field]) && $r[$field] == $value));
}

/**
 * Update a record in an array by ID and return updated array.
 */
function db_update(array $records, int $id, array $changes): array {
    return array_map(function($r) use ($id, $changes) {
        return $r['id'] === $id ? array_merge($r, $changes) : $r;
    }, $records);
}

/**
 * Delete a record from an array by ID.
 */
function db_delete(array $records, int $id): array {
    return array_values(array_filter($records, fn($r) => $r['id'] !== $id));
}

/**
 * Return a JSON HTTP response and exit.
 */
function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Current UTC timestamp string.
 */
function now(): string {
    return (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z');
}
