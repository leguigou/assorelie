<?php
/**
 * Connexion SQLite et migration initiale depuis les anciens fichiers JSON.
 */

function assorelie_db_path(): string
{
    $configured = getenv('ASSORELIE_DB_PATH');
    if ($configured !== false && trim($configured) !== '') {
        return $configured;
    }

    return __DIR__ . '/../data/assorelie.sqlite';
}

function assorelie_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $path = assorelie_db_path();
    $directory = dirname($path);

    if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) {
        throw new RuntimeException("Impossible de créer le dossier SQLite : {$directory}");
    }

    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA synchronous = NORMAL');

    assorelie_initialize_database($pdo);

    return $pdo;
}

function assorelie_initialize_database(PDO $pdo): void
{
    $version = (int) $pdo->query('PRAGMA user_version')->fetchColumn();
    if ($version >= 1) {
        return;
    }

    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    if ($schema === false) {
        throw new RuntimeException('Schéma SQLite introuvable.');
    }

    $pdo->beginTransaction();

    try {
        $pdo->exec($schema);
        assorelie_import_json_data($pdo);
        $pdo->exec('PRAGMA user_version = 1');
        $pdo->commit();

        @chmod(assorelie_db_path(), 0664);
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }
}

function assorelie_import_json_data(PDO $pdo): void
{
    $configuredSeedDirectory = getenv('ASSORELIE_SEED_DIR');
    $seedDirectory = $configuredSeedDirectory !== false
        && trim($configuredSeedDirectory) !== ''
        ? rtrim($configuredSeedDirectory, '/\\')
        : __DIR__ . '/../data';

    $configPath = $seedDirectory . '/config.json';
    $eventsPath = $seedDirectory . '/events.json';
    $membersPath = $seedDirectory . '/members.json';

    if (!assorelie_meta_exists($pdo, 'config_json_imported')) {
        $config = assorelie_read_json_file($configPath, []);

        $settingStatement = $pdo->prepare(
            'INSERT OR REPLACE INTO settings (section, key, value)
             VALUES (:section, :key, :value)'
        );

        foreach (['association', 'social'] as $section) {
            foreach (($config[$section] ?? []) as $key => $value) {
                $settingStatement->execute([
                    ':section' => $section,
                    ':key' => (string) $key,
                    ':value' => (string) $value,
                ]);
            }
        }

        $activityStatement = $pdo->prepare(
            'INSERT INTO activities (icon, title, description, sort_order)
             VALUES (:icon, :title, :description, :sort_order)'
        );

        foreach (($config['activities'] ?? []) as $position => $activity) {
            $activityStatement->execute([
                ':icon' => (string) ($activity['icon'] ?? ''),
                ':title' => (string) ($activity['title'] ?? ''),
                ':description' => (string) ($activity['description'] ?? ''),
                ':sort_order' => (int) $position,
            ]);
        }

        assorelie_set_meta($pdo, 'config_json_imported', date(DATE_ATOM));
    }

    if (!assorelie_meta_exists($pdo, 'events_json_imported')) {
        $eventStatement = $pdo->prepare(
            'INSERT OR IGNORE INTO events
                (id, title, date, time, location, description, link)
             VALUES
                (:id, :title, :date, :time, :location, :description, :link)'
        );

        foreach (assorelie_read_json_file($eventsPath, []) as $event) {
            $eventStatement->execute([
                ':id' => isset($event['id']) ? (int) $event['id'] : null,
                ':title' => (string) ($event['title'] ?? ''),
                ':date' => (string) ($event['date'] ?? ''),
                ':time' => (string) ($event['time'] ?? '18h00'),
                ':location' => (string) ($event['location'] ?? 'Toulon'),
                ':description' => (string) ($event['description'] ?? ''),
                ':link' => $event['link'] ?? null,
            ]);
        }

        assorelie_set_meta($pdo, 'events_json_imported', date(DATE_ATOM));
    }

    if (!assorelie_meta_exists($pdo, 'members_json_imported')) {
        $memberStatement = $pdo->prepare(
            'INSERT OR IGNORE INTO members
                (id, name, email, phone, status, created_at)
             VALUES
                (:id, :name, :email, :phone, :status, :created_at)'
        );

        foreach (assorelie_read_json_file($membersPath, []) as $member) {
            $memberStatement->execute([
                ':id' => isset($member['id']) ? (int) $member['id'] : null,
                ':name' => (string) ($member['name'] ?? ''),
                ':email' => (string) ($member['email'] ?? ''),
                ':phone' => (string) ($member['phone'] ?? ''),
                ':status' => (string) ($member['status'] ?? 'membre'),
                ':created_at' => (string) ($member['created_at'] ?? date('Y-m-d')),
            ]);
        }

        assorelie_set_meta($pdo, 'members_json_imported', date(DATE_ATOM));
    }
}

function assorelie_read_json_file(string $path, array $fallback): array
{
    if (!is_file($path)) {
        return $fallback;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException("Impossible de lire {$path}");
    }

    return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
}

function assorelie_meta_exists(PDO $pdo, string $key): bool
{
    $statement = $pdo->prepare('SELECT 1 FROM meta WHERE key = :key');
    $statement->execute([':key' => $key]);
    return $statement->fetchColumn() !== false;
}

function assorelie_set_meta(PDO $pdo, string $key, string $value): void
{
    $statement = $pdo->prepare(
        'INSERT OR REPLACE INTO meta (key, value) VALUES (:key, :value)'
    );
    $statement->execute([':key' => $key, ':value' => $value]);
}

function assorelie_site_config(): array
{
    $pdo = assorelie_db();
    $config = [
        'association' => [],
        'social' => [],
        'activities' => [],
    ];

    foreach ($pdo->query('SELECT section, key, value FROM settings') as $row) {
        if (isset($config[$row['section']])) {
            $config[$row['section']][$row['key']] = $row['value'];
        }
    }

    $statement = $pdo->query(
        'SELECT icon, title, description
         FROM activities
         WHERE enabled = 1
         ORDER BY sort_order ASC, id ASC'
    );
    $config['activities'] = $statement->fetchAll();

    return $config;
}

function assorelie_setting(string $section, string $key, string $fallback = ''): string
{
    $statement = assorelie_db()->prepare(
        'SELECT value FROM settings WHERE section = :section AND key = :key'
    );
    $statement->execute([':section' => $section, ':key' => $key]);
    $value = $statement->fetchColumn();

    return $value === false ? $fallback : (string) $value;
}
