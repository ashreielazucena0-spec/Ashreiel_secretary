<?php

namespace App\Filters;

use App\Libraries\Access;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Gate for the staff area (/barangay/*) and for shared downloads.
 * Not logged in  -> login page.
 * Resident role  -> sent to the resident portal, unless the resident holds an
 *                    official position (then view-only, or full access for the Secretary).
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please log in to continue.');
        }

        $isResident = strcasecmp((string) $session->get('role'), 'Resident') === 0;
        $inStaffArea = str_starts_with(ltrim($request->getUri()->getPath(), '/'), 'barangay')
            || str_contains($request->getUri()->getPath(), '/barangay/');

        if ($isResident) {
            Access::refresh();

            if ($inStaffArea) {
                // only residents who hold an official position may enter
                if (! Access::isOfficial()) {
                    return redirect()->to(site_url('resident/dashboard'));
                }

                // view-only officials can look but not change anything
                if (Access::isViewOnly() && ! in_array(strtolower($request->getMethod()), ['get', 'head'], true)) {
                    return redirect()->back()
                        ->with('error', 'Your account is view-only. Only the Barangay Secretary can make changes.');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing to do
    }
}
