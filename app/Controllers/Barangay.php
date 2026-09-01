<?php

namespace App\Controllers;

use App\Libraries\DataStore;
use CodeIgniter\HTTP\RedirectResponse;

class Barangay extends BaseController
{
    /**
     * Field/table schema for every module. This drives the generic
     * list + add/edit modal + delete views (see barangay/module.php)
     * the same way the original prototype drove its UI off one seed object.
     */
    protected function schemas(): array
    {
        $residentOptions = fn () => $this->optionMap('residents', fn ($r) => trim($r['first'] . ' ' . $r['last']) . ' (' . $r['id'] . ')');

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
                    'purok'      => ['label' => 'Purok', 'type' => 'select', 'options' => ['Purok Acasia', 'Purok Malipayon', 'Purok Paradise', 'Purok Subis', 'Purok Basakan', 'Purok Falcata', 'Purok Kalapukan']],
                    'occupation' => ['label' => 'Occupation', 'type' => 'text'],
                    'voter'      => ['label' => 'Voter Status', 'type' => 'select', 'options' => ['Registered', 'Not Registered']],
                    'senior'     => ['label' => 'Senior Citizen', 'type' => 'select', 'options' => ['Yes', 'No']],
                    'pwd'        => ['label' => 'PWD', 'type' => 'select', 'options' => ['Yes', 'No']],
                ],
                'columns' => ['first', 'last', 'sex', 'civil', 'purok', 'contact'],
            ],
            'households' => [
                'title' => 'Households',
                'icon'  => 'bi-house',
                'idPrefix' => 'HH-',
                'fields' => [
                    'head'    => ['label' => 'Household Head', 'type' => 'select', 'options' => $residentOptions],
                    'purok'   => ['label' => 'Purok', 'type' => 'select', 'options' => ['Purok Acasia', 'Purok Malipayon', 'Purok Paradise', 'Purok Subis', 'Purok Basakan', 'Purok Falcata', 'Purok Kalapukan']],
                    'members' => ['label' => 'No. of Members', 'type' => 'number'],
                ],
                'columns' => ['head', 'purok', 'members'],
            ],
            'certificates' => [
                'title' => 'Certificates',
                'icon'  => 'bi-file-earmark-text',
                'idPrefix' => 'CERT-',
                'fields' => [
                    'resident' => ['label' => 'Resident', 'type' => 'select', 'options' => $residentOptions],
                    'type'     => ['label' => 'Certificate Type', 'type' => 'select', 'options' => ['Barangay Clearance', 'Certificate of Residency', 'Certificate of Indigency', 'Business Clearance', 'Barangay Certificate']],
                    'purpose'  => ['label' => 'Purpose', 'type' => 'text'],
                    'date'     => ['label' => 'Date Issued', 'type' => 'date'],
                    'amount'   => ['label' => 'Amount (₱)', 'type' => 'number'],
                ],
                'columns' => ['resident', 'type', 'purpose', 'date', 'amount'],
            ],
            'requests' => [
                'title' => 'Requests',
                'icon'  => 'bi-card-checklist',
                'idPrefix' => 'REQ-',
                'fields' => [
                    'resident' => ['label' => 'Resident', 'type' => 'select', 'options' => $residentOptions],
                    'type'     => ['label' => 'Request Type', 'type' => 'text'],
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
                ],
                'columns' => ['number', 'title', 'type', 'date'],
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
                    'name'     => ['label' => 'Full Name', 'type' => 'text', 'required' => true],
                    'position' => ['label' => 'Position', 'type' => 'select', 'options' => ['Punong Barangay', 'Barangay Secretary', 'Barangay Treasurer', 'Kagawad', 'SK Chairperson']],
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
        $data = [
            'pageTitle' => 'Dashboard',
            'active'    => 'dashboard',
            'stats'     => [
                'residents'    => DataStore::count('residents'),
                'households'   => DataStore::count('households'),
                'requests'     => DataStore::count('requests'),
                'cases'        => DataStore::count('cases'),
            ],
            'recentRequests' => array_slice(array_reverse(DataStore::all('requests')), 0, 5),
            'upcomingMeetings' => array_slice(DataStore::all('meetings'), 0, 4),
            'recentLogs' => array_slice(DataStore::all('logs'), 0, 6),
            'residentNameFn' => fn ($id) => $this->residentName($id),
        ];

        return view('barangay/dashboard', $data);
    }

    public function reports()
    {
        $schemas = $this->schemas();
        $counts  = [];
        foreach ($schemas as $key => $schema) {
            $counts[$key] = ['title' => $schema['title'], 'icon' => $schema['icon'], 'count' => DataStore::count($key)];
        }

        $purokCounts = [];
        foreach (DataStore::all('residents') as $r) {
            $purokCounts[$r['purok'] ?? 'Unspecified'] = ($purokCounts[$r['purok'] ?? 'Unspecified'] ?? 0) + 1;
        }

        return view('barangay/reports', [
            'pageTitle' => 'Reports',
            'active'    => 'reports',
            'counts'    => $counts,
            'purokCounts' => $purokCounts,
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
        return view('barangay/backup', [
            'pageTitle' => 'Backup & Restore',
            'active'    => 'backup',
        ]);
    }

    public function backupExport()
    {
        DataStore::log(session()->get('username') ?? 'unknown', 'Exported a system backup');
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="barangay-sumadat-backup-' . date('Y-m-d') . '.json"')
            ->setBody(DataStore::export());
    }

    public function backupImport()
    {
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
        DataStore::reset();
        DataStore::log(session()->get('username') ?? 'unknown', 'Reset the system to demo data');
        return redirect()->to(site_url('barangay/backup'))->with('success', 'System data has been reset to the demo dataset.');
    }

    /* ---------------------------------------------------------------
     * Generic module CRUD (residents, households, certificates, ...)
     * ------------------------------------------------------------- */

    public function index(string $module)
    {
        $schemas = $this->schemas();
        if (! isset($schemas[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $schema = $schemas[$module];
        // resolve any option callbacks now that we're in a request context
        foreach ($schema['fields'] as $key => $field) {
            if (isset($field['options']) && is_callable($field['options'])) {
                $schema['fields'][$key]['options'] = ($field['options'])();
            }
        }

        $search = trim((string) $this->request->getGet('q'));
        $rows   = DataStore::all($module);

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

        $viewName = $this->moduleViews()[$module] ?? 'barangay/module';

        return view($viewName, [
            'pageTitle' => $schema['title'],
            'active'    => $module,
            'module'    => $module,
            'schema'    => $schema,
            'rows'      => array_reverse($rows),
            'search'    => $search,
            'residentLookup' => $module !== 'residents' ? $this->optionMap('residents', fn ($r) => trim($r['first'] . ' ' . $r['last'])) : [],
        ]);
    }

    public function store(string $module)
    {
        $schemas = $this->schemas();
        if (! isset($schemas[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $schema = $schemas[$module];

        $row = ['id' => DataStore::nextId($module, $schema['idPrefix'])];
        foreach ($schema['fields'] as $key => $field) {
            $row[$key] = $this->request->getPost($key) ?? '';
        }

        DataStore::insert($module, $row);
        DataStore::log(session()->get('username') ?? 'unknown', 'Added a new record in ' . $schema['title']);

        return redirect()->to(site_url('barangay/' . $module))->with('success', $schema['title'] . ' record added.');
    }

    public function update(string $module, string $id)
    {
        $schemas = $this->schemas();
        if (! isset($schemas[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $schema = $schemas[$module];

        $changes = [];
        foreach ($schema['fields'] as $key => $field) {
            $val = $this->request->getPost($key);
            if ($field['type'] === 'password' && ($val === null || $val === '')) {
                continue; // don't overwrite password with blank
            }
            $changes[$key] = $val ?? '';
        }

        DataStore::update($module, $id, $changes);
        DataStore::log(session()->get('username') ?? 'unknown', 'Updated a record (' . $id . ') in ' . $schema['title']);

        return redirect()->to(site_url('barangay/' . $module))->with('success', $schema['title'] . ' record updated.');
    }

    public function printCertificate(string $id)
    {
        $cert = DataStore::find('certificates', $id);
        if (! $cert) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $resident = DataStore::find('residents', $cert['resident'] ?? '');

        DataStore::log(session()->get('username') ?? 'unknown', 'Printed certificate (' . $id . ')');

        return view('barangay/certificate_print', [
            'cert'     => $cert,
            'resident' => $resident,
        ]);
    }

    public function toggleUserStatus(string $id)
    {
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
        DataStore::log(session()->get('username') ?? 'unknown', $verb . ' user account (' . $id . ')');

        return redirect()->to(site_url('barangay/users'))->with('success', 'User account ' . strtolower($verb) . '.');
    }

    public function delete(string $module, string $id)
    {
        $schemas = $this->schemas();
        if (! isset($schemas[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $schema = $schemas[$module];

        if ($module === 'users' && $id === session()->get('userId')) {
            return redirect()->to(site_url('barangay/users'))->with('error', 'You cannot delete your own account.');
        }

        DataStore::delete($module, $id);
        DataStore::log(session()->get('username') ?? 'unknown', 'Deleted a record (' . $id . ') from ' . $schema['title']);

        return redirect()->to(site_url('barangay/' . $module))->with('success', $schema['title'] . ' record deleted.');
    }
}