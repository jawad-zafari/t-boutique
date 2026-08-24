<?php

/**
 * Contrôleur Page
 * Gère l'affichage des pages statiques (Mentions légales, CGU, Confidentialité...)
 */
class Page extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // Si l'utilisateur tape juste /Page/ dans l'URL, on le renvoie à l'accueil
    public function index()
    {
        header('Location: ' . URL . 'Index/index');
        exit;
    }

    // Page: Politique de confidentialité
    public function privacy()
    {
        $this->view('page/privacy');
    }

    // Page: Mentions légales
    public function legal()
    {
        $this->view('page/legal');
    }

    // Page: Conditions générales (CGU/CGV)
    public function terms()
    {
        $this->view('page/terms');
    }

    // Page: Conditions d'inscription (utilisé dans register)
    public function conditions()
    {
        $this->view('page/conditions');
    }

    // Page: Retours et remboursements
    public function returns()
    {
        $this->view('page/returns');
    }

    // Page: Foire Aux Questions
    public function faq()
    {
        $this->view('page/faq');
    }
}
?>