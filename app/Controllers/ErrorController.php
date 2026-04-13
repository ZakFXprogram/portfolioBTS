<?php
/**
 * Contrôleur des erreurs
 */
class ErrorController extends Controller
{
    public function notFound()
    {
        http_response_code(404);
        $this->view('errors/404', [
            'pageTitle' => 'Page non trouvée'
        ]);
    }
}
