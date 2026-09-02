<?php

namespace App\Controllers;

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

        return view('auth/login', ['activeTab' => 'login']);
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

        if (! $user || $user['password'] !== $password) {
            return redirect()->to(site_url('login'))
                ->with('activeTab', 'login')
                ->with('loginError', 'Incorrect username or password.');
        }

        if (($user['status'] ?? 'Active') === 'Inactive') {
            return redirect()->to(site_url('login'))
                ->with('activeTab', 'login')
                ->with('loginError', 'This account has been deactivated. Contact an administrator.');
        }

        session()->set([
            'isLoggedIn' => true,
            'userId'     => $user['id'],
            'username'   => $user['username'],
            'name'       => $user['name'],
            'role'       => $user['role'],
        ]);

        DataStore::log($user['username'], 'Logged into the system');

        return redirect()->to(site_url('barangay/dashboard'));
    }

    public function doRegister()
    {
        $name     = trim((string) $this->request->getPost('name'));
        $username = trim((string) $this->request->getPost('username'));
        $role     = (string) $this->request->getPost('role') ?: 'Secretary';
        $password = (string) $this->request->getPost('password');
        $password2 = (string) $this->request->getPost('password2');

        if ($name === '' || $username === '' || $password === '') {
            return redirect()->to(site_url('login'))
                ->with('activeTab', 'register')
                ->with('registerError', 'Please fill in all required fields.');
        }

        if (strlen($password) < 6) {
            return redirect()->to(site_url('login'))
                ->with('activeTab', 'register')
                ->with('registerError', 'Password must be at least 6 characters.');
        }

        if ($password !== $password2) {
            return redirect()->to(site_url('login'))
                ->with('activeTab', 'register')
                ->with('registerError', 'Passwords do not match.');
        }

        foreach (DataStore::all('users') as $row) {
            if (strcasecmp($row['username'], $username) === 0) {
                return redirect()->to(site_url('login'))
                    ->with('activeTab', 'register')
                    ->with('registerError', 'That username is already taken.');
            }
        }

        $id = DataStore::nextId('users', 'U-');
        DataStore::insert('users', [
            'id'       => $id,
            'username' => $username,
            'name'     => $name,
            'role'     => $role,
            'status'   => 'Active',
            'password' => $password,
        ]);

        DataStore::log($username, 'Created a new account (' . $role . ')');

        return redirect()->to(site_url('login'))
            ->with('activeTab', 'login')
            ->with('registerSuccess', 'Account created — you can log in now.');
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