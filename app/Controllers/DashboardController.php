<?php
namespace App\Controllers;
use App\Models\EmployerModel;
use App\Models\DepartementModel;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypesCongeModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $userRole = strtolower(session()->get('role') ?? 'employe');
        if ($userRole === 'responsable') {
            $userRole = 'rh';
        }
        
        // Rediriger RH et admin vers leurs espaces respectifs
        if ($userRole === 'rh') {
            return redirect()->to('/rh/demandes');
        }
        if ($userRole === 'admin') {
            return redirect()->to('/admin/dashboard');
        }
        
        // Espace employé
        $conge = new CongeModel();
        $idEmployer = session()->get('id');
        $data['conges'] = $conge->getCongesByEmploye($idEmployer);
        $data['congeAnnuelle'] = $conge->getCongesByEmployeAndType($idEmployer, 1);
        $data['congeMaladie'] = $conge->getCongesByEmployeAndType($idEmployer, 2);
        $data['congeSpeciale'] = $conge->getCongesByEmployeAndType($idEmployer, 3);
        // $data['congeSansSolde'] = $conge->getCongesByEmployeAndType($idEmployer, 4);
        
        // Comptage des congés par statut
        $data['congesEnAttente'] = $conge->getNbCongeParStatus($idEmployer, 'en_attente');
        $data['congesApprouvees'] = $conge->getNbCongeParStatus($idEmployer, 'approuvee');
        $data['congesRefusees'] = $conge->getNbCongeParStatus($idEmployer, 'refusee');

        return $this->renderPage('employe/dashboard', $data, 'dashboard');
        
    }
}