<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('auth/login'));
        }

        $userRole = (string) session()->get('role');
        $allowedRoles = is_array($arguments) ? $arguments : [];

        if ($allowedRoles === [] || in_array($userRole, $allowedRoles, true)) {
            return;
        }

        return redirect()->to(site_url('dashboard'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No-op
    }
}
