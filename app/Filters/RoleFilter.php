<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * RoleFilter checks that an authenticated user has the required role.
 * The required role is passed as a parameter when configuring the filter
 * in the route definition (e.g. 'role:super_admin'). If the role does
 * not match the current user the request is redirected to a 403 page.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (! $session->has('user_id')) {
            return redirect()->to('/login');
        }
        if (empty($arguments)) {
            return null;
        }
        $requiredRole = $arguments[0];
        $userRole = $session->get('role');
        if ($userRole !== $requiredRole) {
            // Optionally you could redirect to the user's dashboard
            return redirect()->to('/login')->with('error', 'You do not have permission to access this resource.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing required
    }
}