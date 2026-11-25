<?php

namespace App\Controllers;

use App\Libraries\DataStore;
use CodeIgniter\HTTP\RedirectResponse;

class Barangay extends BaseController
{
    protected const PUROKS = ['Purok Acasia', 'Purok Malipayon', 'Purok Paradise', 'Purok Subis', 'Purok Basakan', 'Purok Falcata', 'Purok Kalapukan'];

    /** File types accepted for ordinances / resolutions. */
    protected const DOC_EXTENSIONS = ['doc', 'docx', 'xls', 'xlsx', 'pdf'];

    /**
     * Field/table schema for every module. This drives the generic
     * list + add/edit modal + delete views (see barangay/module.php)
     * the same way the original prototype drove its UI off one seed object.
     */
    protected function schemas(): array
    {
        $residentNameOptions = function () {
            $opts = [];
            foreach (DataStore::all('residents') as $r) {
                $full = trim($r['first'] . ' ' . $r['last']);
                if ($full !== '') {
                    $opts[$r['id']] = $full . ' (' . $r['id'] . ')';
                }
            }
            asort($opts, SORT_NATURAL | SORT_FLAG_CASE);
            return $opts;
        };
        $residentOptions = fn () => $this->optionMap('residents', fn ($r) => trim($r['first'] . ' ' . $r['last']) . ' (' . $r['id'] . ')');
        $typeOptions     = fn () => array_column(DataStore::all('certificate_types'), 'name', 'name');

        $puroks = self::PUROKS;

        return [
            'residents' => [
                'title' => 'Residents',
                'icon'  => 'bi-people',
                'idPrefix' => 'R-',
                'fields' => [
                    'first'      => ['label' => 'First Name', 'type' => 'text', 'required' => true],
                    'middle'     => ['label' => 'Middle Name', 'type' => 'text'],
                    'last'       => ['label' => 'Last Name', 'type' => 'text', 'required' => true],
                    'birth'      => ['label' => 'Birthdate', 'type' => 'date'],
                    'sex'        => ['label' => 'Sex', 'type' => 'select', 'options' => ['Male', 'Female']],
                    'civil'      => ['label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated']],
                    'contact'    => ['label' => 'Contact No.', 'type' => 'text'],
                    'purok'      => ['label' => 'Purok', 'type' => 'select', 'options' => $puroks],
                    'occupation' => ['label' => 'Occupation', 'type' => 'text'],
                    // checkboxes: ticked = 'checked' value, unticked = 'unchecked' value
                    'voter'      => ['label' => 'Registered Voter', 'type' => 'checkbox', 'checked' => 'Registered', 'unchecked' => 'Not Registered'],
                    'senior'     => ['label' => 'Senior Citizen', 'type' => 'checkbox', 'checked' => 'Yes', 'unchecked' => 'No'],
                    'pwd'        => ['label' => 'PWD', 'type' => 'checkbox', 'checked' => 'Yes', 'unchecked' => 'No'],
                    'infant'     => ['label' => 'Infant', 'type' => 'checkbox', 'checked' => 'Yes', 'unchecked' => 'No'],
                ],
                'columns' => ['first', 'last', 'sex', 'civil', 'purok', 'house_label', 'contact'],
                'columnLabels' => ['house_label' => 'House No.'],
            ],
            'households' => [
                'title' => 'Households',
                'icon'  => 'bi-house',
                'idPrefix' => 'HH-',
                'fields' => [
                    'house_no'    => ['label' => 'House No.', 'type' => 'text', 'required' => true],
                    'head'        => ['label' => 'Household Head', 'type' => 'select', 'options' => $residentOptions, 'required' => true],
                    'purok'       => ['label' => 'Purok', 'type' => 'select', 'options' => $puroks],
                    'street'      => ['label' => 'Street Name', 'type' => 'text'],
                    'in_building' => ['label' => 'Living in a building?', 'type' => 'select', 'options' => ['No', 'Yes']],
                    'building'    => ['label' => 'Building Name', 'type' => 'text', 'showWhen' => 'in_building=Yes'],
                    'floor'       => ['label' => 'Floor', 'type' => 'text', 'showWhen' => 'in_building=Yes'],
                    'members'     => ['label' => 'No. of Members', 'type' => 'number'],
                ],
                'columns' => ['house_no', 'head', 'purok', 'street', 'building', 'floor', 'members'],
            ],
            'certificates' => [
                'title' => 'Released Certificates',
                'icon'  => 'bi-file-earmark-text',
                'idPrefix' => 'CERT-',
                'fields' => [
                    'resident' => ['label' => 'Resident', 'type' => 'select', 'options' => $residentOptions],
                    'type'     => ['label' => 'Certificate Type', 'type' => 'select', 'options' => $typeOptions],
                    'purpose'  => ['label' => 'Purpose', 'type' => 'text'],
                    'date'     => ['label' => 'Date Issued', 'type' => 'date'],
                    'amount'   => ['label' => 'Amount (₱)', 'type' => 'number'],
                ],
                'columns' => ['resident', 'type', 'purpose', 'date', 'amount'],
            ],
            'certificate_types' => [
                'title' => 'Certificate Types',
                'icon'  => 'bi-patch-plus',
                'idPrefix' => 'CT-',
                'fields' => [
                    'name'        => ['label' => 'Certificate Name', 'type' => 'text', 'required' => true],
                    'fee'         => ['label' => 'Fee (₱)', 'type' => 'number', 'required' => true],
                    'description' => ['label' => 'Description / Requirements', 'type' => 'text'],
                ],
                'columns' => ['name', 'fee', 'description'],
            ],
            'requests' => [
                'title' => 'Certificate Requests',
                'icon'  => 'bi-card-checklist',
                'idPrefix' => 'REQ-',
                'fields' => [
                    'resident' => ['label' => 'Resident', 'type' => 'select', 'options' => $residentOptions],
                    'type'     => ['label' => 'Certificate Type', 'type' => 'select', 'options' => $typeOptions],
                    'purpose'  => ['label' => 'Purpose', 'type' => 'text'],
                    'date'     => ['label' => 'Date Requested', 'type' => 'date'],
                    'status'   => ['label' => 'Status', 'type' => 'select', 'options' => ['Pending', 'Processing', 'Ready', 'Released']],
                ],
                'columns' => ['resident', 'type', 'purpose', 'date', 'status'],
            ],
            'cases' => [
                'title' => 'Complaints / Blotter',
                'icon'  => 'bi-shield-exclamation',
                'idPrefix' => 'B-' . date('Y') . '-',
                'fields' => [
                    'complainant' => ['label' => 'Complainant', 'type' => 'select', 'options' => $residentOptions],
                    'respondent'  => ['label' => 'Respondent', 'type' => 'select', 'options' => $residentOptions],
                    'nature'      => ['label' => 'Nature of Complaint', 'type' => 'text'],
                    'date'        => ['label' => 'Date Filed', 'type' => 'date'],
                    'status'      => ['label' => 'Status', 'type' => 'select', 'options' => ['Pending', 'Resolved']],
                    'details'     => ['label' => 'Details', 'type' => 'textarea'],
                    'action'      => ['label' => 'Action Taken', 'type' => 'textarea'],
                ],
                'columns' => ['complainant', 'respondent', 'nature', 'date', 'status'],
            ],
            'meetings' => [
                'title' => 'Meetings & Events',
                'icon'  => 'bi-calendar-event',
                'idPrefix' => 'M-',
                'fields' => [
                    'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
                    'date'  => ['label' => 'Date', 'type' => 'date'],
                    'time'  => ['label' => 'Time', 'type' => 'time'],
                    'venue' => ['label' => 'Venue', 'type' => 'text'],
                ],
                'columns' => ['title', 'date', 'time', 'venue'],
            ],
            'documents' => [
                'title' => 'Ordinances & Resolutions',
                'icon'  => 'bi-journal-text',
                'idPrefix' => 'DOC-',
                'fields' => [
                    'number' => ['label' => 'Document No.', 'type' => 'text'],
                    'title'  => ['label' => 'Title', 'type' => 'text', 'required' => true],
                    'type'   => ['label' => 'Type', 'type' => 'select', 'options' => ['Resolution', 'Ordinance']],
                    'date'   => ['label' => 'Date Approved', 'type' => 'date'],
                    'file'   => ['label' => 'Digital Copy (Word, Excel or PDF)', 'type' => 'file', 'skip' => true],
                ],
                'columns' => ['number', 'title', 'type', 'date', 'file'],
            ],
            'payments' => [
                'title' => 'Payments',
                'icon'  => 'bi-cash-stack',
                'idPrefix' => 'OR-',
                'fields' => [
                    'resident' => ['label' => 'Resident', 'type' => 'select', 'options' => $residentOptions],
                    'type'     => ['label' => 'Payment For', 'type' => 'text'],
                    'amount'   => ['label' => 'Amount (₱)', 'type' => 'number'],
                    'date'     => ['label' => 'Date Paid', 'type' => 'date'],
                ],
                'columns' => ['resident', 'type', 'amount', 'date'],
            ],
            'officials' => [
                'title' => 'Officials',
                'icon'  => 'bi-person-badge',
                'idPrefix' => 'O-',
                'fields' => [
                    // name: must be picked from the registered residents (value = resident id)
                    'name'     => ['label' => 'Full Name', 'type' => 'select', 'options' => $residentNameOptions],
                    // position: fixed slot, never editable (several rows share a position, e.g. Kagawad)
                    'position' => ['label' => 'Position', 'type' => 'select', 'options' => ['Punong Barangay', 'Barangay Secretary', 'Barangay Treasurer', 'Kagawad', 'SK Chairperson'], 'readonly' => true],
                    'term'     => ['label' => 'Term', 'type' => 'text'],
                    'contact'  => ['label' => 'Contact No.', 'type' => 'text'],
                ],
                'columns' => ['name', 'position', 'term', 'contact'],
            ],
            'users' => [
                'title' => 'User Management',
                'icon'  => 'bi-person-lock',
                'idPrefix' => 'U-',
                'fields' => [
                    'username' => ['label' => 'Username', 'type' => 'text', 'required' => true],
                    'name'     => ['label' => 'Full Name', 'type' => 'text', 'required' => true],
                    'role'     => ['label' => 'Role', 'type' => 'select', 'options' => ['Admin', 'Secretary']],
                    'status'   => ['label' => 'Status', 'type' => 'select', 'options' => ['Active', 'Inactive']],
                    'password' => ['label' => 'Password', 'type' => 'password', 'required' => true],
                ],
                'columns' => ['username', 'name', 'role', 'status'],
            ],
        ];
    }

    /**
     * Defense-in-depth guard for admin-only actions (user management,
     * backup & restore). The 'admin' route filter is the primary gate;
     * this catches anything that reaches these methods some other way.
     */
    protected function requireAdmin(): ?RedirectResponse
    {
        if (strcasecmp((string) session()->get('role'), 'Admin') !== 0) {
            return redirect()->to(site_url('barangay/dashboard'))
                ->with('error', 'That area is restricted to administrator accounts.');
        }

        return null;
    }

    protected function optionMap(string $table, callable $labelFn): array
    {
        $options = [];
        foreach (DataStore::all($table) as $row) {
            $options[$row['id']] = $labelFn($row);
        }
        return $options;
    }

    protected function residentName(string $id): string
    {
        $r = DataStore::find('residents', $id);
        return $r ? trim($r['first'] . ' ' . $r['last']) : $id;
    }

    /**
     * Maps a module key to its own dedicated view file. Every view listed
     * here is a standalone copy of the same generic list/add/edit/delete
     * markup (they all read off $schema/$module/$rows), split out so each
     * module can be customized independently going forward. Falls back to
     * the shared barangay/module view for anything not listed.
     */
    protected function moduleViews(): array
    {
        return [
            'residents'    => 'barangay/residents',
            'households'   => 'barangay/households',
            'certificates' => 'barangay/certificates',
            'requests'     => 'barangay/requests',
            'cases'        => 'barangay/complaints',
            'meetings'     => 'barangay/meetings',
            'documents'    => 'barangay/documents',
            'payments'     => 'barangay/payments',
            'officials'    => 'barangay/officials',
            'users'        => 'barangay/users',
        ];
    }

    public function dashboard()
    {
        DataStore::ensureSchema();
        $requests = DataStore::all('requests');

        $data = [
            'pageTitle' => 'Dashboard',
            'active'    => 'dashboard',
            'stats'     => [
                'residents'    => DataStore::count('residents'),
                'households'   => DataStore::count('households'),
                'requests'     => count(array_filter($requests, fn ($r) => ($r['status'] ?? '') === 'Pending')),
                'cases'        => DataStore::count('cases'),
            ],
            'recentRequests' => array_slice(array_reverse($requests), 0, 5),
            'recentCertificates' => array_slice(array_reverse(DataStore::all('certificates')), 0, 4),
            'upcomingMeetings' => array_slice(DataStore::all('meetings'), 0, 4),
            'recentLogs' => array_slice(DataStore::all('logs'), 0, 6),
            'residentNameFn' => fn ($id) => $this->residentName($id),
        ];

        return view('barangay/dashboard', $data);
    }

    /* ---------------------------------------------------------------
     * Reports — every card opens the data behind it, ready to print
     * ------------------------------------------------------------- */

    protected function ageOf(?string $birth): string
    {
        if (! $birth || ! strtotime($birth)) {
            return '';
        }
        $diff = (new \DateTime($birth))->diff(new \DateTime('today'));

        return $diff->y >= 1 ? (string) $diff->y : $diff->m . ' mo.';
    }

    protected function fullName(array $r, bool $lastFirst = true): string
    {
        $mid = trim((string) ($r['middle'] ?? ''));
        return $lastFirst
            ? trim($r['last'] . ', ' . $r['first'] . ($mid !== '' ? ' ' . mb_substr($mid, 0, 1) . '.' : ''))
            : trim($r['first'] . ' ' . ($mid !== '' ? $mid . ' ' : '') . $r['last']);
    }

    public function reports()
    {
        DataStore::ensureSchema();
        $schemas = $this->schemas();

        // Wording used on the Reports page only.
        $labels = [
            'residents'    => 'Registered Residents',
            'households'   => 'Registered Households',
            'payments'     => 'Collections',
            'certificates' => 'Released Certificates',
        ];

        $counts = [];
        foreach ($schemas as $key => $schema) {
            if ($key === 'users' && strcasecmp((string) session()->get('role'), 'Admin') !== 0) {
                continue; // staff accounts are admin-only
            }

            if ($key === 'users') {
                // resident logins are not staff accounts — don't count them under User Management
                $count = count(array_filter(DataStore::all('users'), fn ($u) => ($u['role'] ?? '') !== 'Resident'));
            } elseif ($key === 'certificates') {
                $count = count($this->releasedCertificates());
            } else {
                $count = DataStore::count($key);
            }

            $counts[$key] = ['title' => $labels[$key] ?? $schema['title'], 'icon' => $schema['icon'], 'count' => $count];
        }

        $residents   = DataStore::all('residents');
        $purokCounts = [];
        foreach ($residents as $r) {
            $name = ($r['purok'] ?? '') !== '' ? $r['purok'] : 'Unspecified';
            $purokCounts[$name] = ($purokCounts[$name] ?? 0) + 1;
        }
        ksort($purokCounts);

        $groups = [
            'voter'  => ['title' => 'Registered Voters', 'icon' => 'bi-check2-square', 'count' => count(array_filter($residents, fn ($r) => ($r['voter'] ?? '') === 'Registered'))],
            'senior' => ['title' => 'Senior Citizens',   'icon' => 'bi-person-heart',  'count' => count(array_filter($residents, fn ($r) => ($r['senior'] ?? '') === 'Yes'))],
            'pwd'    => ['title' => 'PWD',               'icon' => 'bi-person-wheelchair', 'count' => count(array_filter($residents, fn ($r) => ($r['pwd'] ?? '') === 'Yes'))],
            'infant' => ['title' => 'Infants',           'icon' => 'bi-emoji-smile',   'count' => count(array_filter($residents, fn ($r) => ($r['infant'] ?? '') === 'Yes'))],
        ];

        return view('barangay/reports', [
            'pageTitle'   => 'Reports',
            'active'      => 'reports',
            'counts'      => $counts,
            'purokCounts' => $purokCounts,
            'groups'      => $groups,
        ]);
    }

    /** A printable report for one module (residents, households, released certificates, ...). */
    public function reportModule(string $module)
    {
        DataStore::ensureSchema();
        $schemas = $this->schemas();
        if (! isset($schemas[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if ($module === 'users' && ($block = $this->requireAdmin())) {
            return $block;
        }

        $schema = $schemas[$module];
        foreach ($schema['fields'] as $k => $f) {
            if (isset($f['options']) && is_callable($f['options'])) {
                $schema['fields'][$k]['options'] = ($f['options'])();
            }
        }

        $get      = $this->request->getGet();
        $from     = (string) ($get['from'] ?? '');
        $to       = (string) ($get['to'] ?? '');
        $purok    = (string) ($get['purok'] ?? '');
        $filter   = (string) ($get['filter'] ?? '');
        $status   = (string) ($get['status'] ?? '');
        $type     = (string) ($get['type'] ?? '');
        $names    = $this->optionMap('residents', fn ($r) => $this->fullName($r));
        $residentKeys = ['resident', 'head', 'complainant', 'respondent'];

        $title   = [
            'residents'    => 'Registered Residents',
            'households'   => 'Registered Households',
            'payments'     => 'Collections',
            'certificates' => 'Released Certificates',
        ][$module] ?? $schema['title'];
        $subtitle = [];
        $filters  = []; // which filter controls to show
        $columns  = [];
        $rows     = [];
        $total    = null; // [label, amount]
        $rowLink  = null; // optional: fn(row) => [url, label]

        if ($module === 'residents') {
            $filters = ['purok', 'filter'];
            $columns = ['id' => 'ID', 'name' => 'Name', 'sex' => 'Sex', 'age' => 'Age', 'civil' => 'Civil Status', 'purok' => 'Purok', 'house' => 'House No.', 'contact' => 'Contact', 'voter' => 'Voter', 'senior' => 'Senior', 'pwd' => 'PWD', 'infant' => 'Infant'];

            $houses = array_column(DataStore::all('households'), null, 'id');
            $list   = DataStore::all('residents');
            usort($list, fn ($a, $b) => strcasecmp($a['last'] . $a['first'], $b['last'] . $b['first']));

            $groupsTitle = ['voter' => 'Registered Voters', 'senior' => 'Senior Citizens', 'pwd' => 'Persons with Disability (PWD)', 'infant' => 'Infants', 'male' => 'Male Residents', 'female' => 'Female Residents'];
            foreach ($list as $r) {
                if ($purok !== '' && ($r['purok'] ?? '') !== $purok) {
                    continue;
                }
                if ($filter === 'voter' && ($r['voter'] ?? '') !== 'Registered') continue;
                if ($filter === 'senior' && ($r['senior'] ?? '') !== 'Yes') continue;
                if ($filter === 'pwd' && ($r['pwd'] ?? '') !== 'Yes') continue;
                if ($filter === 'infant' && ($r['infant'] ?? '') !== 'Yes') continue;
                if ($filter === 'male' && ($r['sex'] ?? '') !== 'Male') continue;
                if ($filter === 'female' && ($r['sex'] ?? '') !== 'Female') continue;

                $hh = $houses[$r['household_id'] ?? ''] ?? null;
                $rows[] = [
                    'id' => $r['id'], 'name' => $this->fullName($r), 'sex' => $r['sex'] ?? '', 'age' => $this->ageOf($r['birth'] ?? null),
                    'civil' => $r['civil'] ?? '', 'purok' => $r['purok'] ?? '', 'house' => $hh['house_no'] ?? '', 'contact' => $r['contact'] ?? '',
                    'voter' => ($r['voter'] ?? '') === 'Registered' ? '✔' : '', 'senior' => ($r['senior'] ?? '') === 'Yes' ? '✔' : '',
                    'pwd' => ($r['pwd'] ?? '') === 'Yes' ? '✔' : '', 'infant' => ($r['infant'] ?? '') === 'Yes' ? '✔' : '',
                ];
            }
            if ($purok !== '') {
                $title = 'Residents of ' . $purok;
            }
            if ($filter !== '' && isset($groupsTitle[$filter])) {
                $title = $groupsTitle[$filter] . ($purok !== '' ? ' — ' . $purok : '');
            }
        } elseif ($module === 'households') {
            $filters = ['purok'];
            $columns = ['house_no' => 'House No.', 'head' => 'Household Head', 'purok' => 'Purok', 'street' => 'Street', 'building' => 'Building', 'floor' => 'Floor', 'members' => 'Members'];
            foreach (DataStore::all('households') as $h) {
                if ($purok !== '' && ($h['purok'] ?? '') !== $purok) {
                    continue;
                }
                $rows[] = [
                    'id' => $h['id'], 'house_no' => $h['house_no'] ?? '', 'head' => $names[$h['head']] ?? $h['head'], 'purok' => $h['purok'] ?? '',
                    'street' => $h['street'] ?? '', 'building' => $h['building'] ?? '', 'floor' => $h['floor'] ?? '', 'members' => (int) ($h['members'] ?? 0),
                ];
            }
            usort($rows, fn ($a, $b) => strnatcasecmp((string) $a['house_no'], (string) $b['house_no']));
            if ($purok !== '') {
                $title = 'Registered Households — ' . $purok;
            }
            $rowLink = fn ($row) => [base_url('barangay/reports/household/' . $row['id']), 'View members'];
        } else {
            // generic: date range / status / type filters where the module has those fields
            $hasDate   = isset($schema['fields']['date']);
            $hasStatus = isset($schema['fields']['status']) && isset($schema['fields']['status']['options']);
            $hasType   = isset($schema['fields']['type']['options']);
            $filters   = array_values(array_filter([$hasDate ? 'dates' : null, $hasStatus ? 'status' : null, $hasType ? 'type' : null]));

            $source = $module === 'certificates' ? $this->releasedCertificates()
                : ($module === 'users' ? array_filter(DataStore::all('users'), fn ($u) => ($u['role'] ?? '') !== 'Resident') : DataStore::all($module));

            foreach ($schema['columns'] as $col) {
                $columns[$col] = $schema['fields'][$col]['label'] ?? ucfirst($col);
            }
            if ($module === 'certificates') {
                $columns = ['id' => 'Certificate No.'] + $columns;
            }

            foreach ($source as $r) {
                if ($hasDate && $from !== '' && ($r['date'] ?? '') < $from) continue;
                if ($hasDate && $to !== '' && ($r['date'] ?? '') > $to) continue;
                if ($hasStatus && $status !== '' && ($r['status'] ?? '') !== $status) continue;
                if ($hasType && $type !== '' && ($r['type'] ?? '') !== $type) continue;

                $out = ['id' => $r['id']];
                foreach ($columns as $col => $label) {
                    $v = $r[$col] ?? '';
                    if (in_array($col, $residentKeys, true) && isset($names[$v])) {
                        $v = $names[$v];
                    }
                    if ($col === 'amount' && $v !== '') {
                        $v = '₱' . number_format((float) $v, 2);
                    }
                    if ($col === 'date' && $v !== '' && strtotime((string) $v)) {
                        $v = date('M d, Y', strtotime((string) $v));
                    }
                    if ($col === 'file') {
                        $v = ! empty($r['file_name']) ? $r['file_name'] : '';
                    }
                    $out[$col] = $v;
                }
                $rows[] = $out;
            }

            if (isset($columns['amount'])) {
                $sum = 0.0;
                foreach ($source as $r) {
                    if ($hasDate && $from !== '' && ($r['date'] ?? '') < $from) continue;
                    if ($hasDate && $to !== '' && ($r['date'] ?? '') > $to) continue;
                    if ($hasType && $type !== '' && ($r['type'] ?? '') !== $type) continue;
                    $sum += (float) ($r['amount'] ?? 0);
                }
                $total = ['Total amount', '₱' . number_format($sum, 2)];
            }

            if ($hasDate && ($from !== '' || $to !== '')) {
                $subtitle[] = 'Date: ' . ($from !== '' ? date('M d, Y', strtotime($from)) : 'start') . ' to ' . ($to !== '' ? date('M d, Y', strtotime($to)) : 'today');
            }
            if ($hasStatus && $status !== '') $subtitle[] = 'Status: ' . $status;
            if ($hasType && $type !== '') $subtitle[] = 'Type: ' . $type;
        }

        if ($module === 'residents' || $module === 'households') {
            if ($purok !== '') $subtitle[] = 'Purok: ' . $purok;
        }

        return view('barangay/report_detail', [
            'pageTitle' => $title . ' — Report',
            'active'    => 'reports',
            'module'    => $module,
            'title'     => $title,
            'subtitle'  => $subtitle,
            'columns'   => $columns,
            'rows'      => $rows,
            'total'     => $total,
            'rowLink'   => $rowLink,
            'filters'   => $filters,
            'values'    => compact('from', 'to', 'purok', 'filter', 'status', 'type'),
            'puroks'    => self::PUROKS,
            'statuses'  => $schema['fields']['status']['options'] ?? [],
            'types'     => isset($schema['fields']['type']['options']) ? array_values((array) $schema['fields']['type']['options']) : [],
            'groupOptions' => ['voter' => 'Registered voters', 'senior' => 'Senior citizens', 'pwd' => 'PWD', 'infant' => 'Infants', 'male' => 'Male', 'female' => 'Female'],
        ]);
    }

    /** Printable list of the people living in one household. */
    public function reportHousehold(string $id)
    {
        DataStore::ensureSchema();
        $hh = DataStore::find('households', $id);
        if (! $hh) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rows = [];
        $list = DataStore::where('residents', ['household_id' => $id]);
        usort($list, fn ($a, $b) => strcasecmp($a['last'] . $a['first'], $b['last'] . $b['first']));
        foreach ($list as $r) {
            $rows[] = [
                'id' => $r['id'], 'name' => $this->fullName($r) . ($r['id'] === $hh['head'] ? ' (Head)' : ''), 'sex' => $r['sex'] ?? '', 'age' => $this->ageOf($r['birth'] ?? null),
                'civil' => $r['civil'] ?? '', 'contact' => $r['contact'] ?? '', 'voter' => ($r['voter'] ?? '') === 'Registered' ? '✔' : '',
                'senior' => ($r['senior'] ?? '') === 'Yes' ? '✔' : '', 'pwd' => ($r['pwd'] ?? '') === 'Yes' ? '✔' : '', 'infant' => ($r['infant'] ?? '') === 'Yes' ? '✔' : '',
            ];
        }

        $where = trim(($hh['purok'] ?? '') . (($hh['street'] ?? '') !== '' ? ', ' . $hh['street'] : ''), ', ');

        return view('barangay/report_detail', [
            'pageTitle' => 'Household members — Report',
            'active'    => 'reports',
            'module'    => 'households',
            'title'     => 'Members of House No. ' . (($hh['house_no'] ?? '') !== '' ? $hh['house_no'] : $hh['id']),
            'subtitle'  => array_filter([$where]),
            'columns'   => ['id' => 'ID', 'name' => 'Name', 'sex' => 'Sex', 'age' => 'Age', 'civil' => 'Civil Status', 'contact' => 'Contact', 'voter' => 'Voter', 'senior' => 'Senior', 'pwd' => 'PWD', 'infant' => 'Infant'],
            'rows'      => $rows,
            'total'     => null,
            'rowLink'   => null,
            'filters'   => [],
            'values'    => ['from' => '', 'to' => '', 'purok' => '', 'filter' => '', 'status' => '', 'type' => ''],
            'puroks'    => [], 'statuses' => [], 'types' => [], 'groupOptions' => [],
            'backUrl'   => base_url('barangay/reports/households'),
        ]);
    }

    public function activityLogs()
    {
        return view('barangay/activity_logs', [
            'pageTitle' => 'Activity Logs',
            'active'    => 'activity_logs',
            'logs'      => DataStore::all('logs'),
        ]);
    }

    public function backup()
    {
        if ($block = $this->requireAdmin()) {
            return $block;
        }

        return view('barangay/backup', [
            'pageTitle' => 'Backup & Restore',
            'active'    => 'backup',
        ]);
    }

    public function backupExport()
    {
        if ($block = $this->requireAdmin()) {
            return $block;
        }

        DataStore::log(session()->get('username') ?? 'unknown', 'Exported a system backup');
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="barangay-sumadat-backup-' . date('Y-m-d') . '.json"')
            ->setBody(DataStore::export());
    }

    public function backupImport()
    {
        if ($block = $this->requireAdmin()) {
            return $block;
        }

        $file = $this->request->getFile('backupFile');
        if (! $file || ! $file->isValid()) {
            return redirect()->to(site_url('barangay/backup'))->with('error', 'Please choose a valid backup file.');
        }

        $json = file_get_contents($file->getTempName());
        if (! DataStore::import($json)) {
            return redirect()->to(site_url('barangay/backup'))->with('error', 'That file is not a valid backup.');
        }

        DataStore::log(session()->get('username') ?? 'unknown', 'Restored the system from a backup file');
        return redirect()->to(site_url('barangay/backup'))->with('success', 'Backup restored successfully.');
    }

    public function backupReset()
    {
        if ($block = $this->requireAdmin()) {
            return $block;
        }

        DataStore::reset();
        DataStore::log(session()->get('username') ?? 'unknown', 'Reset the system to demo data');
        return redirect()->to(site_url('barangay/backup'))->with('success', 'System data has been reset to the demo dataset.');
    }

    /* ---------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------- */

    protected function actor(): string
    {
        return session()->get('username') ?? 'unknown';
    }

    protected function schemaOr404(string $module): array
    {
        $schemas = $this->schemas();
        if (! isset($schemas[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $schemas[$module];
    }

    /** Values for every real (non-"skip") field of a module, as posted. */
    protected function postedFields(array $schema, bool $forUpdate = false): array
    {
        $row = [];
        foreach ($schema['fields'] as $key => $field) {
            if (! empty($field['skip'])) {
                continue;
            }

            if ($forUpdate && ! empty($field['readonly'])) {
                continue; // fixed fields (e.g. an official's position) can't be changed
            }

            $val = $this->request->getPost($key);

            if ($field['type'] === 'checkbox') {
                // an unticked box posts nothing at all, so absence means "unchecked"
                $row[$key] = ($val !== null && $val !== '' && $val !== '0') ? $field['checked'] : $field['unchecked'];
                continue;
            }

            if ($field['type'] === 'password') {
                if ($val === null || $val === '') {
                    if ($forUpdate) {
                        continue; // don't overwrite the password with a blank
                    }
                    $val = '';
                } else {
                    $val = password_hash((string) $val, PASSWORD_DEFAULT);
                }
            }

            $row[$key] = $val ?? '';
        }

        return $row;
    }

    protected function randomPassword(int $length = 8): string
    {
        // no 0/O/1/l/I — easy to read out loud or copy from paper
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $out;
    }

    protected function uniqueUsername(string $first, string $last): string
    {
        $clean = fn (string $v) => preg_replace('/[^a-z0-9]/', '', mb_strtolower($v));
        $base  = trim($clean($first) . '.' . $clean($last), '.');
        if ($base === '') {
            $base = 'resident';
        }

        $taken = array_map('strtolower', array_column(DataStore::all('users'), 'username'));
        $name  = $base;
        $n     = 2;
        while (in_array($name, $taken, true)) {
            $name = $base . $n++;
        }

        return $name;
    }

    /**
     * Creates the resident's login (or, if they already have one, issues a new
     * temporary password). The plain password is only ever returned here —
     * it is stored hashed — so the caller must show it to the secretary now.
     */
    protected function provisionLogin(array $resident): array
    {
        $password = $this->randomPassword();
        $fullName = trim($resident['first'] . ' ' . $resident['last']);
        $existing = DataStore::where('users', ['resident_id' => $resident['id']])[0] ?? null;

        if ($existing) {
            DataStore::update('users', $existing['id'], [
                'password'      => password_hash($password, PASSWORD_DEFAULT),
                'temp_password' => 1,
                'status'        => 'Active',
                'name'          => $fullName,
            ]);
            $username = $existing['username'];
        } else {
            $username = $this->uniqueUsername($resident['first'], $resident['last']);
            DataStore::insert('users', [
                'id'            => DataStore::nextId('users', 'U-'),
                'username'      => $username,
                'name'          => $fullName,
                'role'          => 'Resident',
                'status'        => 'Active',
                'password'      => password_hash($password, PASSWORD_DEFAULT),
                'resident_id'   => $resident['id'],
                'temp_password' => 1,
            ]);
        }

        return ['name' => $fullName, 'username' => $username, 'password' => $password];
    }

    protected function adjustMembers(?string $householdId, int $delta): void
    {
        if (! $householdId) {
            return;
        }

        $hh = DataStore::find('households', $householdId);
        if ($hh) {
            DataStore::update('households', $householdId, ['members' => max(0, (int) $hh['members'] + $delta)]);
        }
    }

    /**
     * Census-style lookup: finds the registered household for a typed house
     * number. When the same number exists in several puroks, the purok picks one.
     *
     * @return array{0:?array,1:?string} [household|null, errorMessage|null]
     */
    protected function findHouseholdByNumber(string $houseNo, string $purok = ''): array
    {
        $needle  = mb_strtolower(trim($houseNo));
        $matches = array_values(array_filter(
            DataStore::all('households'),
            fn ($h) => mb_strtolower(trim((string) ($h['house_no'] ?? ''))) === $needle
        ));

        if (count($matches) > 1 && $purok !== '') {
            $matches = array_values(array_filter($matches, fn ($h) => ($h['purok'] ?? '') === $purok));
        }

        if (count($matches) > 1) {
            return [null, 'House No. ' . $houseNo . ' is registered in more than one purok. Please choose the Purok too.'];
        }

        return [$matches[0] ?? null, null];
    }

    /** Birthdate decides Infant (under 1 year) and Senior Citizen (60+). No birthdate: leave what was ticked. */
    protected function applyAgeFlags(array $row): array
    {
        $birth = trim((string) ($row['birth'] ?? ''));
        if ($birth === '') {
            return $row;
        }

        try {
            $born = new \DateTimeImmutable($birth);
        } catch (\Throwable $e) {
            return $row;
        }

        $age = $born->diff(new \DateTimeImmutable('today'))->y;
        if ($born > new \DateTimeImmutable('today')) {
            return $row;
        }
        $row['senior'] = $age >= 60 ? 'Yes' : 'No';
        $row['infant'] = $age < 1 ? 'Yes' : 'No';

        return $row;
    }

    /** Households offered by the "Registered house" picker on the resident form. */
    protected function householdChoices(): array
    {
        $names = $this->optionMap('residents', fn ($r) => trim($r['first'] . ' ' . $r['last']));
        $list  = array_map(fn ($h) => [
            'id'       => (string) $h['id'],
            'no'       => trim((string) ($h['house_no'] ?? '')),
            'purok'    => (string) ($h['purok'] ?? ''),
            'street'   => (string) ($h['street'] ?? ''),
            'building' => ($h['in_building'] ?? 'No') === 'Yes'
                ? trim((string) ($h['building'] ?? '') . ' ' . (($h['floor'] ?? '') !== '' ? '(' . $h['floor'] . ')' : '')) : '',
            'head'     => (string) ($names[$h['head'] ?? ''] ?? ''),
            'members'  => (int) ($h['members'] ?? 0),
        ], DataStore::all('households'));

        usort($list, fn ($a, $b) => strnatcasecmp($a['no'] !== '' ? $a['no'] : 'zzz' . $a['id'], $b['no'] !== '' ? $b['no'] : 'zzz' . $b['id']));

        return $list;
    }

    /** True when a house number is already registered in that purok. */
    protected function houseNumberTaken(string $houseNo, string $purok, ?string $exceptId = null): bool
    {
        $needle = mb_strtolower(trim($houseNo));
        foreach (DataStore::all('households') as $h) {
            if (($h['id'] ?? '') !== $exceptId
                && mb_strtolower(trim((string) ($h['house_no'] ?? ''))) === $needle
                && ($h['purok'] ?? '') === $purok) {
                return true;
            }
        }

        return false;
    }

    /** Certificate for a request (created the first time it is needed). */
    protected function ensureCertificateForRequest(string $requestId): ?array
    {
        $existing = DataStore::where('certificates', ['request_id' => $requestId])[0] ?? null;
        if ($existing) {
            return $existing;
        }

        $req = DataStore::find('requests', $requestId);
        if (! $req) {
            return null;
        }

        $type = DataStore::where('certificate_types', ['name' => $req['type']])[0] ?? null;
        $cert = [
            'id'         => DataStore::nextId('certificates', 'CERT-'),
            'resident'   => $req['resident'],
            'type'       => $req['type'],
            'purpose'    => $req['purpose'],
            'date'       => date('Y-m-d'),
            'amount'     => $type['fee'] ?? 0,
            'request_id' => $requestId,
        ];
        DataStore::insert('certificates', $cert);

        return $cert;
    }

    /** Certificates that were actually released: issued directly, or from a request marked Released. */
    protected function releasedCertificates(): array
    {
        $statuses = array_column(DataStore::all('requests'), 'status', 'id');

        return array_values(array_filter(DataStore::all('certificates'), function ($c) use ($statuses) {
            $linked = $c['request_id'] ?? null;
            return ! $linked || ($statuses[$linked] ?? '') === 'Released';
        }));
    }

    protected function createHouseholdFor(array $resident): string
    {
        $in = $this->request->getPost('hh_in_building') === 'Yes';
        $id = DataStore::nextId('households', 'HH-');

        DataStore::insert('households', [
            'id'          => $id,
            'house_no'    => trim((string) $this->request->getPost('house_no')),
            'head'        => $resident['id'],
            'purok'       => $resident['purok'] ?? '',
            'street'      => trim((string) $this->request->getPost('hh_street')),
            'in_building' => $in ? 'Yes' : 'No',
            'building'    => $in ? trim((string) $this->request->getPost('hh_building')) : null,
            'floor'       => $in ? trim((string) $this->request->getPost('hh_floor')) : null,
            'members'     => 1,
        ]);

        return $id;
    }

    protected function normalizeHousehold(array $row): array
    {
        $in = ($row['in_building'] ?? 'No') === 'Yes';
        $row['in_building'] = $in ? 'Yes' : 'No';
        $row['building']    = $in ? ($row['building'] ?? '') : null;
        $row['floor']       = $in ? ($row['floor'] ?? '') : null;
        $row['members']     = max(1, (int) ($row['members'] ?? 1));

        return $row;
    }

    /** Validates + saves an uploaded ordinance/resolution file. Returns [name, path] or an error string. */
    protected function saveDocumentFile()
    {
        $file = $this->request->getFile('file');
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null; // nothing uploaded
        }

        if (! $file->isValid()) {
            return 'The file could not be uploaded (' . $file->getErrorString() . ').';
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, self::DOC_EXTENSIONS, true)) {
            return 'Only Word (.doc, .docx), Excel (.xls, .xlsx) and PDF files can be uploaded.';
        }

        if ($file->getSizeByUnit('mb') > 10) {
            return 'That file is larger than 10 MB.';
        }

        $dir = WRITEPATH . 'uploads/documents';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $stored = bin2hex(random_bytes(12)) . '.' . $ext;
        $file->move($dir, $stored);

        return [$file->getClientName(), $stored];
    }

    protected function deleteDocumentFile(?string $stored): void
    {
        if ($stored) {
            $path = WRITEPATH . 'uploads/documents/' . basename($stored);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /* ---------------------------------------------------------------
     * Resident photos (camera capture or file upload)
     * ------------------------------------------------------------- */

    /** Adds residents.photo on databases created before this feature existed. */
    protected function ensurePhotoColumn(): void
    {
        try {
            $db = db_connect();
            if (! $db->fieldExists('photo', 'residents')) {
                $db->query('ALTER TABLE `residents` ADD COLUMN `photo` VARCHAR(100) NULL DEFAULT NULL');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Could not add residents.photo: ' . $e->getMessage());
        }
    }

    /**
     * Reads a posted photo — either the camera/cropped image sent as a
     * data URL (photo_data) or a plain uploaded file (photo) — checks it is
     * really an image, shrinks it to at most 800px and saves it as JPEG.
     *
     * @return array{0:?string,1:?string} [storedFileName|null, errorMessage|null]
     */
    protected function savePhoto(): array
    {
        $maxBytes = 8 * 1024 * 1024;
        $raw      = null;

        $data = (string) $this->request->getPost('photo_data');
        if ($data !== '') {
            if (! preg_match('#^data:image/(jpeg|png|webp);base64,#', $data)) {
                return [null, 'The captured photo could not be read. Please retake it.'];
            }
            $raw = base64_decode(substr($data, strpos($data, ',') + 1), true);
            if ($raw === false) {
                return [null, 'The captured photo could not be read. Please retake it.'];
            }
        } else {
            $file = $this->request->getFile('photo');
            if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                return [null, null]; // no photo this time
            }
            if (! $file->isValid()) {
                return [null, 'The photo could not be uploaded (' . $file->getErrorString() . ').'];
            }
            $raw = file_get_contents($file->getTempName());
        }

        if ($raw === false || $raw === '' || strlen($raw) > $maxBytes) {
            return [null, 'The photo is empty or larger than 8 MB.'];
        }

        $info = @getimagesizefromstring($raw);
        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return [null, 'The photo must be a JPG, PNG or WebP image.'];
        }

        $dir = WRITEPATH . 'uploads/residents';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $stored = bin2hex(random_bytes(12)) . '.jpg';
        $path   = $dir . '/' . $stored;

        // Re-encode: strips anything hidden in the file and keeps storage small.
        if (function_exists('imagecreatefromstring') && ($img = @imagecreatefromstring($raw))) {
            if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
                $exif = @exif_read_data('data://image/jpeg;base64,' . base64_encode($raw));
                $rot  = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
                if ($rot !== 0) {
                    $img = imagerotate($img, $rot, 0) ?: $img;
                }
            }
            $w = imagesx($img);
            $h = imagesy($img);
            $scale = min(1, 800 / max($w, $h));
            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));
            $dst = imagecreatetruecolor($nw, $nh);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
            $ok = imagejpeg($dst, $path, 85);
            imagedestroy($img);
            imagedestroy($dst);
            if (! $ok) {
                return [null, 'The photo could not be saved. Please try again.'];
            }
        } elseif ($info[2] === IMAGETYPE_JPEG) {
            file_put_contents($path, $raw); // no GD on this server: keep the JPEG as-is
        } else {
            return [null, 'This server can only accept JPG photos. Please choose a JPG image.'];
        }

        return [$stored, null];
    }

    protected function deleteResidentPhoto(?string $stored): void
    {
        if ($stored) {
            $path = WRITEPATH . 'uploads/residents/' . basename($stored);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /* ---------------------------------------------------------------
     * Generic module CRUD (residents, households, certificates, ...)
     * ------------------------------------------------------------- */

    public function index(string $module)
    {
        DataStore::ensureSchema();
        $schema = $this->schemaOr404($module);

        if ($module === 'users' && ($block = $this->requireAdmin())) {
            return $block;
        }

        // resolve any option callbacks now that we're in a request context
        foreach ($schema['fields'] as $key => $field) {
            if (isset($field['options']) && is_callable($field['options'])) {
                $schema['fields'][$key]['options'] = ($field['options'])();
            }
        }

        if ($module === 'residents') {
            $this->ensurePhotoColumn();
        }
        if ($module === 'officials') {
            \App\Libraries\Access::ensureOfficialLinks();
        }

        $search = trim((string) $this->request->getGet('q'));
        $rows   = DataStore::all($module);

        // Resident logins are managed from the Residents page, not here.
        if ($module === 'users') {
            $rows = array_filter($rows, fn ($u) => ($u['role'] ?? '') !== 'Resident');
        }

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $rows = array_filter($rows, function ($row) use ($needle) {
                foreach ($row as $v) {
                    if (is_string($v) && str_contains(mb_strtolower($v), $needle)) {
                        return true;
                    }
                }
                return false;
            });
        }

        // residents: show / edit the census-style house number of the household they live in
        if ($module === 'residents') {
            $houses = array_column(DataStore::all('households'), null, 'id');
            $rows = array_map(function ($r) use ($houses) {
                $hh = $houses[$r['household_id'] ?? ''] ?? null;
                $r['house_no']    = $hh['house_no'] ?? '';
                $r['house_label'] = ($hh['house_no'] ?? '') !== '' ? $hh['house_no'] : ($hh ? '— (' . $hh['id'] . ')' : '');
                return $r;
            }, $rows);
        }

        $viewName = $this->moduleViews()[$module] ?? 'barangay/module';

        $accounts = [];
        if ($module === 'residents') {
            foreach (DataStore::all('users') as $u) {
                if (! empty($u['resident_id'])) {
                    $accounts[$u['resident_id']] = $u['username'];
                }
            }
        }

        $receipts = [];
        if ($module === 'requests') {
            foreach (DataStore::all('payments') as $pay) {
                if (! empty($pay['request_id'])) {
                    $receipts[$pay['request_id']] = $pay['id'];
                }
            }
        }

        $printed = [];
        if ($module === 'requests') {
            foreach (DataStore::all('certificates') as $c) {
                if (! empty($c['request_id']) && ! empty($c['printed_at'])) {
                    $printed[$c['request_id']] = true;
                }
            }
        }

        return view($viewName, [
            'printed'   => $printed,
            'receipts'  => $receipts,
            'pageTitle' => $schema['title'],
            'active'    => $module,
            'module'    => $module,
            'schema'    => $schema,
            'rows'      => array_reverse($rows),
            'search'    => $search,
            'accounts'  => $accounts,
            'houseHints' => $module === 'residents' ? $this->householdChoices() : [],
            'residentLookup' => $module !== 'residents' ? $this->optionMap('residents', fn ($r) => trim($r['first'] . ' ' . $r['last'])) : [],
        ]);
    }

    public function store(string $module)
    {
        DataStore::ensureSchema();
        $schema = $this->schemaOr404($module);
        $back   = redirect()->to(site_url('barangay/' . $module));

        if ($module === 'users' && ($block = $this->requireAdmin())) {
            return $block;
        }

        if ($module === 'officials') {
            return $back->with('error', 'Every official position is already listed — edit the entry and use "Start New Term" when a new term begins.');
        }

        if ($module === 'residents') {
            return $this->storeResident($schema);
        }

        if ($module === 'requests') {
            return $this->storeWalkInRequest($schema);
        }

        $row = ['id' => DataStore::nextId($module, $schema['idPrefix'])] + $this->postedFields($schema);

        if ($module === 'households') {
            $row['house_no'] = trim((string) ($row['house_no'] ?? ''));
            if (($row['head'] ?? '') === '' || $row['house_no'] === '') {
                return $back->with('error', 'Please enter the House No. and choose the household head.');
            }
            [$dup, $err] = $this->findHouseholdByNumber($row['house_no'], (string) ($row['purok'] ?? ''));
            if ($err || ($dup && ($dup['purok'] ?? '') === ($row['purok'] ?? ''))) {
                return $back->with('error', 'House No. ' . $row['house_no'] . ' is already registered' . (($row['purok'] ?? '') !== '' ? ' in ' . $row['purok'] : '') . '.');
            }
            $row = $this->normalizeHousehold($row);
        }

        if ($module === 'certificate_types') {
            $row['fee'] = max(0, (float) ($row['fee'] ?? 0));
            if (trim((string) $row['name']) === '') {
                return $back->with('error', 'Please enter the certificate name.');
            }
            foreach (DataStore::all('certificate_types') as $t) {
                if (strcasecmp($t['name'], trim($row['name'])) === 0) {
                    return $back->with('error', 'That certificate type already exists.');
                }
            }
        }

        if ($module === 'documents') {
            $saved = $this->saveDocumentFile();
            if (is_string($saved)) {
                return $back->with('error', $saved);
            }
            $row['file_name'] = $saved[0] ?? null;
            $row['file_path'] = $saved[1] ?? null;
        }

        DataStore::insert($module, $row);

        // the household head belongs to the household he/she heads
        if ($module === 'households') {
            $head = DataStore::find('residents', $row['head']);
            if ($head && empty($head['household_id'])) {
                DataStore::update('residents', $head['id'], ['household_id' => $row['id'], 'purok' => $row['purok'] ?? ($head['purok'] ?? '')]);
            }
        }

        DataStore::log($this->actor(), 'Added a new record in ' . $schema['title']);

        return $back->with('success', $schema['title'] . ' record added.');
    }

    protected function storeResident(array $schema)
    {
        $back = redirect()->to(site_url('barangay/residents'));

        $first   = trim((string) $this->request->getPost('first'));
        $last    = trim((string) $this->request->getPost('last'));
        $mode    = $this->request->getPost('hh_mode') === 'new' ? 'new' : 'existing';
        $houseNo = trim((string) $this->request->getPost('house_no'));
        $purok   = trim((string) $this->request->getPost('purok'));

        if ($first === '' || $last === '') {
            return $back->with('error', 'First name and last name are required.');
        }

        // Either a house that is already registered, or a brand-new household made on the spot.
        $household = null;
        if ($mode === 'existing') {
            $household = DataStore::find('households', (string) $this->request->getPost('household_id'));
            if (! $household) {
                return $back->with('error', 'Please choose a registered house, or switch to "New household".');
            }
            $houseNo = (string) ($household['house_no'] ?? '');
        } else {
            if ($houseNo === '' || $purok === '') {
                return $back->with('error', 'Please enter the House No. and choose the Purok for the new household.');
            }
            if ($this->houseNumberTaken($houseNo, $purok)) {
                return $back->with('error', 'House No. ' . $houseNo . ' is already registered in ' . $purok . ' — pick it from the "Registered house" list instead.');
            }
        }

        $this->ensurePhotoColumn();
        [$photo, $photoError] = $this->savePhoto();
        if ($photoError) {
            return $back->with('error', $photoError);
        }

        $row = ['id' => DataStore::nextId('residents', $schema['idPrefix'])] + $this->postedFields($schema);
        $row = $this->applyAgeFlags($row);
        $row['first'] = $first;
        $row['last']  = $last;
        if ($photo) {
            $row['photo'] = $photo;
        }

        // living in a registered household: the household's purok is the resident's purok
        $row['purok'] = $household ? ($household['purok'] ?? '') : $purok;

        $db = db_connect();
        $db->transStart();

        $row['household_id'] = null;
        DataStore::insert('residents', $row);

        if ($household) {
            $householdId = $household['id'];
            $this->adjustMembers($householdId, +1);
        } else {
            $householdId = $this->createHouseholdFor($row);
        }
        DataStore::update('residents', $row['id'], ['household_id' => $householdId]);
        $row['household_id'] = $householdId;

        $creds = $this->provisionLogin($row);

        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->deleteResidentPhoto($photo ?? null);
            return $back->with('error', 'The resident could not be saved. Please try again.');
        }

        DataStore::log($this->actor(), 'Registered a resident (' . $row['id'] . ') at House No. ' . ($houseNo !== '' ? $houseNo : $householdId) . ' (' . $householdId . ') and created a login');

        return $back
            ->with('success', 'Resident registered' . ($houseNo !== '' ? ' at House No. ' . $houseNo : '') . ($household ? ' (' . ($row['purok'] ?: 'no purok') . ').' : ' — new household created.'))
            ->with('credentials', $creds);
    }

    public function update(string $module, string $id)
    {
        DataStore::ensureSchema();
        $schema = $this->schemaOr404($module);
        $back   = redirect()->to(site_url('barangay/' . $module));

        if ($module === 'users' && ($block = $this->requireAdmin())) {
            return $block;
        }

        if ($module === 'residents') {
            return $this->updateResident($schema, $id);
        }

        $changes = $this->postedFields($schema, true);

        $newLogin = null;
        if ($module === 'officials') {
            \App\Libraries\Access::ensureOfficialLinks();

            $current = DataStore::find('officials', $id);
            if (! $current) {
                return $back->with('error', 'That record could not be found.');
            }

            $posted = trim((string) ($changes['name'] ?? ''));
            if ($posted === '__keep__') {
                unset($changes['name']); // leave an old, unlinked name as it is
            } elseif ($posted === '') {
                $changes['name']        = '';
                $changes['resident_id'] = null; // vacant seat -> that resident loses official access
            } else {
                $res = DataStore::find('residents', $posted);
                if (! $res) {
                    return $back->with('error', 'The official must be a registered resident — please choose a name from the list.');
                }
                $changes['name']        = trim($res['first'] . ' ' . $res['last']);
                $changes['resident_id'] = $res['id'];

                // make sure the new official can log in (new residents already have a login)
                if (! DataStore::where('users', ['resident_id' => $res['id']])) {
                    $newLogin = $this->provisionLogin($res);
                }
                DataStore::log($this->actor(), 'Assigned ' . $changes['name'] . ' (' . $res['id'] . ') as ' . $current['position']);
            }
        }

        if ($module === 'households') {
            $changes['house_no'] = trim((string) ($changes['house_no'] ?? ''));
            if ($changes['house_no'] === '') {
                return $back->with('error', 'The House No. is required.');
            }
            foreach (DataStore::all('households') as $other) {
                if ($other['id'] !== $id
                    && mb_strtolower(trim((string) ($other['house_no'] ?? ''))) === mb_strtolower($changes['house_no'])
                    && ($other['purok'] ?? '') === ($changes['purok'] ?? '')) {
                    return $back->with('error', 'House No. ' . $changes['house_no'] . ' is already registered in that purok.');
                }
            }
            $changes = $this->normalizeHousehold($changes);
        }

        if ($module === 'certificate_types') {
            $changes['fee'] = max(0, (float) ($changes['fee'] ?? 0));
        }

        if ($module === 'documents') {
            $saved = $this->saveDocumentFile();
            if (is_string($saved)) {
                return $back->with('error', $saved);
            }
            if (is_array($saved)) {
                $old = DataStore::find('documents', $id);
                $this->deleteDocumentFile($old['file_path'] ?? null);
                $changes['file_name'] = $saved[0];
                $changes['file_path'] = $saved[1];
            }
        }

        DataStore::update($module, $id, $changes);
        DataStore::log($this->actor(), 'Updated a record (' . $id . ') in ' . $schema['title']);

        // everyone living in the household takes the household's purok
        if ($module === 'households' && isset($changes['purok'])) {
            foreach (DataStore::where('residents', ['household_id' => $id]) as $member) {
                if (($member['purok'] ?? '') !== $changes['purok']) {
                    DataStore::update('residents', $member['id'], ['purok' => $changes['purok']]);
                }
            }
        }

        if ($module === 'requests' && ($changes['status'] ?? '') === 'Released') {
            $this->ensureCertificateForRequest($id);
            $payment = $this->ensurePaymentForRequest($id);
            return $back->with('success', 'Request released and the payment was recorded.')->with('receiptId', $payment['id'] ?? null);
        }

        if ($newLogin) {
            return $back->with('success', 'Official updated. A resident login was created for ' . $newLogin['name'] . '.')
                ->with('credentials', $newLogin);
        }

        return $back->with('success', $schema['title'] . ' record updated.');
    }

    protected function updateResident(array $schema, string $id)
    {
        $back     = redirect()->to(site_url('barangay/residents'));
        $resident = DataStore::find('residents', $id);
        if (! $resident) {
            return $back->with('error', 'That record could not be found.');
        }

        $changes = $this->applyAgeFlags($this->postedFields($schema, true));
        $mode    = $this->request->getPost('hh_mode') === 'new' ? 'new' : 'existing';
        $houseNo = trim((string) $this->request->getPost('house_no'));
        $purok   = trim((string) $this->request->getPost('purok'));
        $old     = $resident['household_id'] ?? null;
        $oldHh   = $old ? DataStore::find('households', $old) : null;

        $target   = null;   // existing household to be in
        $newHouse = false;  // register a brand-new household
        if ($mode === 'new') {
            if ($houseNo === '' || $purok === '') {
                return $back->with('error', 'Please enter the House No. and choose the Purok for the new household.');
            }
            if ($this->houseNumberTaken($houseNo, $purok)) {
                return $back->with('error', 'House No. ' . $houseNo . ' is already registered in ' . $purok . ' — pick it from the "Registered house" list instead.');
            }
            $newHouse = true;
            $changes['purok'] = $purok;
        } else {
            $chosen = (string) $this->request->getPost('household_id');
            $target = $chosen !== '' ? DataStore::find('households', $chosen) : $oldHh; // blank: stay where they are
            if ($chosen !== '' && ! $target) {
                return $back->with('error', 'That house could not be found.');
            }
            if ($target) {
                $changes['purok'] = $target['purok'] ?? ($resident['purok'] ?? ''); // the household's purok is the resident's purok
            } else {
                unset($changes['purok']);
            }
        }

        $this->ensurePhotoColumn();
        [$photo, $photoError] = $this->savePhoto();
        if ($photoError) {
            return $back->with('error', $photoError);
        }
        if ($photo) {
            $this->deleteResidentPhoto($resident['photo'] ?? null);
            $changes['photo'] = $photo;
        } elseif ((string) $this->request->getPost('remove_photo') === '1') {
            $this->deleteResidentPhoto($resident['photo'] ?? null);
            $changes['photo'] = null;
        }

        $db = db_connect();
        $db->transStart();

        DataStore::update('residents', $id, $changes);

        if ($newHouse) {
            $new = $this->createHouseholdFor($changes + $resident);
            $this->adjustMembers($old, -1);
            DataStore::update('residents', $id, ['household_id' => $new]);
        } elseif ($target && $target['id'] !== $old) {
            $this->adjustMembers($old, -1);
            $this->adjustMembers($target['id'], +1);
            DataStore::update('residents', $id, ['household_id' => $target['id']]);
        }

        // keep the login's display name in step with the resident
        $account = DataStore::where('users', ['resident_id' => $id])[0] ?? null;
        if ($account) {
            DataStore::update('users', $account['id'], [
                'name' => trim(($changes['first'] ?? $resident['first']) . ' ' . ($changes['last'] ?? $resident['last'])),
            ]);
        }

        $db->transComplete();

        DataStore::log($this->actor(), 'Updated a record (' . $id . ') in Residents');

        return $back->with('success', 'Residents record updated.');
    }

    /**
     * Certificate Requests > Add New is for WALK-INS: the resident is at the
     * desk, so the request skips the wait list — the certificate is created,
     * the payment is recorded, the request is marked Released and the
     * certificate opens straight in the print dialog.
     */
    protected function storeWalkInRequest(array $schema)
    {
        $back     = redirect()->to(site_url('barangay/requests'));
        $resident = trim((string) $this->request->getPost('resident'));
        $type     = trim((string) $this->request->getPost('type'));
        $purpose  = trim((string) $this->request->getPost('purpose'));

        if ($resident === '' || ! DataStore::find('residents', $resident)) {
            return $back->with('error', 'Please choose the resident.');
        }
        $validType = DataStore::where('certificate_types', ['name' => $type])[0] ?? null;
        if (! $validType) {
            return $back->with('error', 'Please choose the certificate type.');
        }

        $id = DataStore::nextId('requests', $schema['idPrefix']);
        DataStore::insert('requests', [
            'id'       => $id,
            'resident' => $resident,
            'type'     => $validType['name'],
            'purpose'  => $purpose,
            'date'     => date('Y-m-d'),
            'status'   => 'Released',
        ]);

        $cert    = $this->ensureCertificateForRequest($id);
        $payment = $this->ensurePaymentForRequest($id);
        DataStore::log($this->actor(), 'Walk-in request ' . $id . ': printed ' . $validType['name'] . ' (' . ($cert['id'] ?? '') . ') for ' . $resident);

        return redirect()->to(site_url('barangay/requests/print/' . $id));
    }

    public function resetResidentLogin(string $id)
    {
        $resident = DataStore::find('residents', $id);
        if (! $resident) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $creds = $this->provisionLogin($resident);
        DataStore::log($this->actor(), 'Created/reset the login for resident ' . $id);

        return redirect()->to(site_url('barangay/residents'))
            ->with('success', 'A new temporary password was created for ' . $creds['name'] . '.')
            ->with('credentials', $creds);
    }

    public function newOfficialsTerm()
    {
        $term = trim((string) $this->request->getPost('term'));
        if ($term === '') {
            return redirect()->to(site_url('barangay/officials'))->with('error', 'Please enter the new term (for example 2026-2029).');
        }

        // a new term empties every seat: names, contact numbers and the link to
        // each resident account, so all former officials return to the resident portal
        \App\Libraries\Access::ensureOfficialLinks();
        DataStore::updateAll('officials', [
            'term'        => $term,
            'name'        => '',
            'resident_id' => null,
            'contact'     => '',
        ]);
        DataStore::log($this->actor(), 'Started a new officials term (' . $term . ') and cleared all positions');

        return redirect()->to(site_url('barangay/officials'))
            ->with('success', 'New term ' . $term . ' started. Every position is now vacant and the former officials are back on their resident accounts — edit each position to enter the new official.');
    }

    /** Shown instead of the certificate when it was already printed once. */
    protected function alreadyPrinted(array $cert)
    {
        $to = ! empty($cert['request_id']) ? 'barangay/requests' : 'barangay/certificates';

        return redirect()->to(site_url($to))->with('error',
            'Certificate ' . $cert['id'] . ' was already printed on ' . date('M j, Y g:i A', strtotime((string) $cert['printed_at']))
            . ' and cannot be printed again. Only the receipt can be reprinted.');
    }

    /** Opens the certificate for a request (creating it the first time) and marks the request "Ready". */
    public function printRequest(string $id)
    {
        $req = DataStore::find('requests', $id);
        if (! $req) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        DataStore::ensureSchema();
        $cert = $this->ensureCertificateForRequest($id);

        // a certificate can be printed only once — only the receipt can be reprinted
        if (! empty($cert['printed_at'])) {
            return $this->alreadyPrinted($cert);
        }
        DataStore::update('certificates', $cert['id'], ['printed_at' => date('Y-m-d H:i:s')]);

        if (in_array($req['status'], ['Pending', 'Processing'], true)) {
            DataStore::update('requests', $id, ['status' => 'Ready']);
        }

        DataStore::log($this->actor(), 'Printed certificate (' . $cert['id'] . ') for request ' . $id);

        $payment = DataStore::where('payments', ['request_id' => $id])[0] ?? null;

        return view('barangay/certificate_print', [
            'cert'      => $cert,
            'resident'  => DataStore::find('residents', $cert['resident'] ?? ''),
            'receiptId' => $payment['id'] ?? null,
        ]);
    }

    /**
     * One payment (collection) per released request. Amount comes from the
     * printed certificate, or the certificate type's fee. Safe to call twice.
     */
    protected function ensurePaymentForRequest(string $requestId): ?array
    {
        $existing = DataStore::where('payments', ['request_id' => $requestId])[0] ?? null;
        if ($existing) {
            return $existing;
        }

        $req = DataStore::find('requests', $requestId);
        if (! $req) {
            return null;
        }

        $cert = DataStore::where('certificates', ['request_id' => $requestId])[0] ?? null;
        $type = DataStore::where('certificate_types', ['name' => $req['type']])[0] ?? null;

        $payment = [
            'id'         => DataStore::nextId('payments', 'OR-'),
            'resident'   => $req['resident'],
            'type'       => $req['type'],
            'amount'     => $cert['amount'] ?? $type['fee'] ?? 0,
            'date'       => date('Y-m-d'),
            'request_id' => $requestId,
        ];
        DataStore::insert('payments', $payment);
        DataStore::log($this->actor(), 'Recorded payment (' . $payment['id'] . ') for request ' . $requestId);

        return $payment;
    }

    public function releaseRequest(string $id)
    {
        $req = DataStore::find('requests', $id);
        if (! $req) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        DataStore::update('requests', $id, ['status' => 'Released']);
        $this->ensureCertificateForRequest($id);
        $payment = $this->ensurePaymentForRequest($id);
        DataStore::log($this->actor(), 'Released the certificate for request ' . $id);

        return redirect()->to(site_url('barangay/requests'))
            ->with('success', 'Request ' . $id . ' released and the payment was recorded.')
            ->with('receiptId', $payment['id'] ?? null);
    }

    public function printReceipt(string $id)
    {
        $payment = DataStore::find('payments', $id);
        if (! $payment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $cert = ! empty($payment['request_id'])
            ? (DataStore::where('certificates', ['request_id' => $payment['request_id']])[0] ?? null)
            : null;

        DataStore::log($this->actor(), 'Printed receipt (' . $id . ')');

        return view('barangay/receipt_print', [
            'payment'  => $payment,
            'resident' => DataStore::find('residents', $payment['resident'] ?? ''),
            'cert'     => $cert,
            'receivedBy' => session()->get('name') ?? '',
            'officials'  => DataStore::where('officials', ['position' => 'Barangay Treasurer']),
        ]);
    }

    public function printCertificate(string $id)
    {
        $cert = DataStore::find('certificates', $id);
        if (! $cert) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        DataStore::ensureSchema();
        $cert = DataStore::find('certificates', $id);

        if (! empty($cert['printed_at'])) {
            return $this->alreadyPrinted($cert);
        }
        DataStore::update('certificates', $id, ['printed_at' => date('Y-m-d H:i:s')]);

        $resident = DataStore::find('residents', $cert['resident'] ?? '');

        // a certificate that came from a resident's request: printing it means it's ready to claim
        if (! empty($cert['request_id'])) {
            $req = DataStore::find('requests', $cert['request_id']);
            if ($req && in_array($req['status'], ['Pending', 'Processing'], true)) {
                DataStore::update('requests', $req['id'], ['status' => 'Ready']);
            }
        }

        DataStore::log($this->actor(), 'Printed certificate (' . $id . ')');

        return view('barangay/certificate_print', [
            'cert'     => $cert,
            'resident' => $resident,
        ]);
    }

    public function toggleUserStatus(string $id)
    {
        if ($block = $this->requireAdmin()) {
            return $block;
        }

        $user = DataStore::find('users', $id);
        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($id === session()->get('userId')) {
            return redirect()->to(site_url('barangay/users'))->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = ($user['status'] ?? 'Active') === 'Active' ? 'Inactive' : 'Active';
        DataStore::update('users', $id, ['status' => $newStatus]);

        $verb = $newStatus === 'Inactive' ? 'Deactivated' : 'Activated';
        DataStore::log($this->actor(), $verb . ' user account (' . $id . ')');

        return redirect()->to(site_url('barangay/users'))->with('success', 'User account ' . strtolower($verb) . '.');
    }

    public function delete(string $module, string $id)
    {
        $schema = $this->schemaOr404($module);

        if ($module === 'users' && ($block = $this->requireAdmin())) {
            return $block;
        }

        if ($module === 'officials') {
            return redirect()->to(site_url('barangay/officials'))
                ->with('error', 'Official positions can\'t be deleted — edit the entry instead.');
        }

        if ($module === 'users' && $id === session()->get('userId')) {
            return redirect()->to(site_url('barangay/users'))->with('error', 'You cannot delete your own account.');
        }

        $existing = DataStore::find($module, $id);

        if (! DataStore::delete($module, $id)) {
            if (DataStore::$lastDeleteBlockedByReference) {
                return redirect()->to(site_url('barangay/' . $module))
                    ->with('error', 'This ' . rtrim($schema['title'], 's') . ' record is still linked to other records (e.g. certificates, households, or requests) and cannot be deleted until those are removed first.');
            }

            return redirect()->to(site_url('barangay/' . $module))->with('error', 'That record could not be found.');
        }

        if ($module === 'documents' && $existing) {
            $this->deleteDocumentFile($existing['file_path'] ?? null);
        }

        if ($module === 'residents' && $existing) {
            $this->adjustMembers($existing['household_id'] ?? null, -1);
            $this->deleteResidentPhoto($existing['photo'] ?? null);
        }

        DataStore::log($this->actor(), 'Deleted a record (' . $id . ') from ' . $schema['title']);

        return redirect()->to(site_url('barangay/' . $module))->with('success', $schema['title'] . ' record deleted.');
    }
}