<?php

namespace App\Libraries;

/**
 * Who sees what.
 *
 * A resident account whose resident is listed in the Officials page
 * (officials.resident_id) automatically gets the officials interface instead
 * of the resident portal:
 *   - Barangay Secretary  -> can add / edit / delete (same as a Secretary account)
 *   - any other position  -> view only
 * Taking the name off the Officials page (or giving the seat to someone else)
 * ends that access on the very next page they open.
 */
class Access
{
    public const EDIT_POSITION = 'Barangay Secretary';

    /** Adds officials.resident_id if missing and links existing names to residents. */
    public static function ensureOfficialLinks(): void
    {
        try {
            $db = db_connect();
            if (! $db->fieldExists('resident_id', 'officials')) {
                $db->query('ALTER TABLE `officials` ADD COLUMN `resident_id` VARCHAR(20) NULL DEFAULT NULL AFTER `name`');
            }

            $unlinked = array_filter(
                DataStore::all('officials'),
                fn ($o) => trim((string) ($o['name'] ?? '')) !== '' && empty($o['resident_id'])
            );
            if (! $unlinked) {
                return;
            }

            $residents = DataStore::all('residents');
            foreach ($unlinked as $o) {
                $needle  = mb_strtolower(trim((string) $o['name']));
                $matches = array_values(array_filter(
                    $residents,
                    fn ($r) => mb_strtolower(trim($r['first'] . ' ' . $r['last'])) === $needle
                ));
                // only link when exactly one resident has that name
                if (count($matches) === 1) {
                    DataStore::update('officials', $o['id'], ['resident_id' => $matches[0]['id']]);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Could not link officials to residents: ' . $e->getMessage());
        }
    }

    /** Re-reads the officials list for the logged-in resident and stores the result in the session. */
    public static function refresh(): void
    {
        $session = session();
        $level   = null;
        $titles  = [];

        if (strcasecmp((string) $session->get('role'), 'Resident') === 0 && $session->get('residentId')) {
            try {
                if (! $session->get('officialsLinked')) {
                    self::ensureOfficialLinks();
                    $session->set('officialsLinked', true);
                }

                foreach (DataStore::where('officials', ['resident_id' => (string) $session->get('residentId')]) as $o) {
                    if (trim((string) ($o['name'] ?? '')) === '') {
                        continue;
                    }
                    $titles[] = (string) $o['position'];
                    $level = ($o['position'] === self::EDIT_POSITION) ? 'edit' : ($level ?? 'view');
                }
            } catch (\Throwable $e) {
                log_message('error', 'Official access check failed: ' . $e->getMessage());
                $level = null;
                $titles = [];
            }
        }

        $session->set([
            'officialLevel'    => $level,
            'officialPosition' => implode(', ', array_unique($titles)),
        ]);
    }

    public static function isOfficial(): bool
    {
        return in_array(session()->get('officialLevel'), ['view', 'edit'], true);
    }

    public static function isViewOnly(): bool
    {
        return session()->get('officialLevel') === 'view';
    }

    /** Text shown under the name in the top bar. */
    public static function label(): string
    {
        $pos = (string) session()->get('officialPosition');
        if ($pos === '') {
            return (string) (session()->get('role') ?? 'Barangay Secretary');
        }

        return $pos . (self::isViewOnly() ? ' · View only' : '');
    }
}
