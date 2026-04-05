<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Role-based access control filter.
 * Usage in routes: ->filter('role:recruiter') or ->filter('role:admin')
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!empty($arguments)) {
            $userRole     = session()->get('user_role');
            $allowedRoles = $arguments;

            if (!in_array($userRole, $allowedRoles, true)) {
                return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that page.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
