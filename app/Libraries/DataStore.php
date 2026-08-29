<?php

namespace App\Libraries;

/**
 * DataStore
 *
 * A tiny flat-file "database" that keeps every table as a PHP array
 * (mirrors the structure of the original localStorage prototype) and
 * persists everything to writable/data/db.json between requests.
 *
 * No MySQL/SQLite setup required — this is intentional.
 */
class DataStore
{
    protected static ?array $db = null;
    protected static string $file = '';

    protected static function file(): string
    {
        if (self::$file === '') {
            self::$file = WRITEPATH . 'data' . DIRECTORY_SEPARATOR . 'db.json';
        }
        return self::$file;
    }

    protected static function seed(): array
    {
        return [
            'residents' => [
                ['id' => 'R-001', 'first' => 'Juan', 'middle' => 'Santos', 'last' => 'Dela Cruz', 'birth' => '1990-04-12', 'sex' => 'Male', 'civil' => 'Married', 'contact' => '09171234567', 'purok' => 'Purok 1', 'occupation' => 'Farmer', 'voter' => 'Registered', 'senior' => 'No', 'pwd' => 'No'],
                ['id' => 'R-002', 'first' => 'Maria', 'middle' => 'Lopez', 'last' => 'Santos', 'birth' => '1995-08-21', 'sex' => 'Female', 'civil' => 'Single', 'contact' => '09181234567', 'purok' => 'Purok 2', 'occupation' => 'Teacher', 'voter' => 'Registered', 'senior' => 'No', 'pwd' => 'No'],
                ['id' => 'R-003', 'first' => 'Pedro', 'middle' => '', 'last' => 'Garcia', 'birth' => '1958-02-03', 'sex' => 'Male', 'civil' => 'Widowed', 'contact' => '09191234567', 'purok' => 'Purok 3', 'occupation' => 'Retired', 'voter' => 'Registered', 'senior' => 'Yes', 'pwd' => 'No'],
            ],
            'households' => [
                ['id' => 'HH-001', 'head' => 'R-001', 'purok' => 'Purok 1', 'members' => '1'],
                ['id' => 'HH-002', 'head' => 'R-002', 'purok' => 'Purok 2', 'members' => '1'],
            ],
            'certificates' => [
                ['id' => 'CERT-001', 'resident' => 'R-001', 'type' => 'Barangay Clearance', 'purpose' => 'Employment', 'date' => date('Y-m-d'), 'amount' => 50],
            ],
            'requests' => [
                ['id' => 'REQ-001', 'resident' => 'R-002', 'type' => 'Certificate of Residency', 'purpose' => 'School', 'date' => date('Y-m-d'), 'status' => 'Pending'],
            ],
            'cases' => [
                ['id' => 'B-2026-001', 'complainant' => 'R-001', 'respondent' => 'R-002', 'nature' => 'Noise Complaint', 'date' => date('Y-m-d'), 'status' => 'Pending', 'details' => 'Excessive noise at night.', 'action' => 'Scheduled mediation.'],
            ],
            'meetings' => [
                ['id' => 'M-001', 'title' => 'Barangay Assembly', 'date' => date('Y-m-d', strtotime('+3 day')), 'time' => '09:00', 'venue' => 'Barangay Hall'],
            ],
            'documents' => [
                ['id' => 'RES-2026-001', 'number' => 'RES-2026-001', 'title' => 'Waste Management Resolution', 'type' => 'Resolution', 'date' => date('Y-m-d')],
            ],
            'payments' => [
                ['id' => 'OR-001', 'resident' => 'R-001', 'type' => 'Barangay Clearance', 'amount' => 50, 'date' => date('Y-m-d')],
            ],
            'officials' => [
                ['id' => 'O-001', 'name' => 'Juan Dela Cruz', 'position' => 'Punong Barangay', 'term' => '2023-2026', 'contact' => '09171234567'],
                ['id' => 'O-002', 'name' => 'Maria Santos', 'position' => 'Barangay Secretary', 'term' => '2023-2026', 'contact' => '09181234567'],
            ],
            'users' => [
                ['id' => 'U-001', 'username' => 'admin', 'name' => 'Administrator', 'role' => 'Admin', 'status' => 'Active', 'password' => 'admin123'],
                ['id' => 'U-002', 'username' => 'secretary', 'name' => 'Maria Santos', 'role' => 'Secretary', 'status' => 'Active', 'password' => 'secretary123'],
            ],
            'logs' => [
                ['time' => date('Y-m-d H:i A'), 'user' => 'system', 'action' => 'Database initialized'],
            ],
        ];
    }

    protected static function load(): void
    {
        if (self::$db !== null) {
            return;
        }

        $dir = dirname(self::file());
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        if (is_file(self::file())) {
            $json = file_get_contents(self::file());
            $data = json_decode($json, true);
            if (is_array($data)) {
                self::$db = $data;
                return;
            }
        }

        self::$db = self::seed();
        self::persist();
    }

    protected static function persist(): void
    {
        file_put_contents(self::file(), json_encode(self::$db, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public static function all(string $table): array
    {
        self::load();
        return self::$db[$table] ?? [];
    }

    public static function find(string $table, string $id): ?array
    {
        foreach (self::all($table) as $row) {
            if (($row['id'] ?? null) === $id) {
                return $row;
            }
        }
        return null;
    }

    public static function nextId(string $table, string $prefix, int $pad = 3): string
    {
        $rows = self::all($table);
        $max  = 0;
        foreach ($rows as $row) {
            $id = (string) ($row['id'] ?? '');
            if (str_starts_with($id, $prefix)) {
                $num = (int) substr($id, strlen($prefix));
                $max = max($max, $num);
            }
        }
        return $prefix . str_pad((string) ($max + 1), $pad, '0', STR_PAD_LEFT);
    }

    public static function insert(string $table, array $row): array
    {
        self::load();
        self::$db[$table][] = $row;
        self::persist();
        return $row;
    }

    public static function update(string $table, string $id, array $changes): bool
    {
        self::load();
        foreach (self::$db[$table] ?? [] as $i => $row) {
            if (($row['id'] ?? null) === $id) {
                self::$db[$table][$i] = array_merge($row, $changes);
                self::persist();
                return true;
            }
        }
        return false;
    }

    public static function delete(string $table, string $id): bool
    {
        self::load();
        $before = count(self::$db[$table] ?? []);
        self::$db[$table] = array_values(array_filter(self::$db[$table] ?? [], fn ($r) => ($r['id'] ?? null) !== $id));
        self::persist();
        return count(self::$db[$table]) < $before;
    }

    public static function count(string $table): int
    {
        return count(self::all($table));
    }

    public static function log(string $user, string $action): void
    {
        self::load();
        array_unshift(self::$db['logs'], [
            'time'   => date('Y-m-d h:i A'),
            'user'   => $user,
            'action' => $action,
        ]);
        // keep the log from growing forever
        self::$db['logs'] = array_slice(self::$db['logs'], 0, 300);
        self::persist();
    }

    public static function export(): string
    {
        self::load();
        return json_encode(self::$db, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public static function import(string $json): bool
    {
        $data = json_decode($json, true);
        if (! is_array($data)) {
            return false;
        }
        self::$db = $data;
        self::persist();
        return true;
    }

    public static function reset(): void
    {
        self::$db = self::seed();
        self::persist();
    }
}
