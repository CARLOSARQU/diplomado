<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Primero verificar que esté logueado
        if (!$session->get('user_id')) {
            return redirect()->to('/')->with('error', 'Debes iniciar sesión');
        }
        
        // Luego verificar permisos de admin
        $rolId = $session->get('rol_id');
        if (!in_array($rolId, [1, 2])) {
            if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
                return service('response')->setStatusCode(403)->setJSON(['error' => 'Acceso denegado']);
            }
            return redirect()->to('/mi-panel')->with('error', 'No tienes permisos de administrador');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}