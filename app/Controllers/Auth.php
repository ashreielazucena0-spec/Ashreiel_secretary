<?php

namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\DataStore;

class Auth extends BaseController
{
    public function login()
    {
        // Always show the login form when this page is visited directly —
        // clear out any existing session instead of bouncing to the dashboard.
        if (session()->get('isLoggedIn')) {
            session()->destroy();
        }

        return view('auth/login');
    }

    /**
     * Passwords created by the system are hashed. Older accounts that were
     * saved as plain text (e.g. the demo admin) still work, and are upgraded
     * to a hash the first time they log in.
     */
    protected function passwordMatches(string $stored, string $input): bool
    {
        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2')) {
            return password_verify($input, $stored);
        }

        return hash_equals($stored, $input);
    }

    public function doLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $user = null;
        foreach (DataStore::all('users') as $row) {
            if (strcasecmp($row['username'], $username) === 0) {
                $user = $row;   
                break;
            }
        }

        if (! $user || ! $this->passwordMatches((string) $user['password'], $password)) {
            return redirect()->to(site_url('login'))
                ->with('loginError', 'Incorrect username or password.');
        }

        if (($user['status'] ?? 'Active') === 'Inactive') {
            return redirect()->to(site_url('login'))
                ->with('loginError', 'This account has been deactivated. Contact the barangay office.');
        }

        // upgrade legacy plain-text passwords
        if (! str_starts_with((string) $user['password'], '$2y$') && ! str_starts_with((string) $user['password'], '$argon2')) {
            DataStore::update('users', $user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        session()->set([
            'isLoggedIn'   => true,
            'userId'       => $user['id'],
            'username'     => $user['username'],
            'name'         => $user['name'],
            'role'         => $user['role'],
            'residentId'   => $user['resident_id'] ?? null,
            'tempPassword' => (int) ($user['temp_password'] ?? 0) === 1,
        ]);

        DataStore::log($user['username'], 'Logged into the system');

        if ($user['role'] === 'Resident') {
            Access::refresh();

            // residents elected to a position get the officials interface
            return redirect()->to(site_url(Access::isOfficial() ? 'barangay/dashboard' : 'resident/dashboard'));
        }

        return redirect()->to(site_url('barangay/dashboard'));
    }

    public function logout()
    {
        $username = session()->get('username');
        if ($username) {
            DataStore::log($username, 'Logged out of the system');
        }
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}
    