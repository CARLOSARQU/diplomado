<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface 
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Verificar si está logueado
        if (!$session->get('user_id')) {
            if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
                return service('response')->setStatusCode(401)->setJSON(['error' => 'No autenticado']);
            }
            return redirect()->to('/')->with('error', 'Debes iniciar sesión');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}