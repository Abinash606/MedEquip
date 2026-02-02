<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * BaseController provides a convenient spot for loading common dependencies
 * and performing functionality that should be available to all controllers.
 */
class BaseController extends Controller
{
    /**
     * Automatic helper loading
     */
    protected $helpers = ['url', 'form'];

    /**
     * Initialize controller: sets up services and session handling
     */
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        // Start the session if not already
        $this->session = session();
    }
}