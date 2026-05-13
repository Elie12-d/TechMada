<?
namespace App\Controllers;
use App\Models\EmployerModel;
use App\Models\DepartementModel;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypesCongeModel;

class EmployerController extends BaseController
{
    public function index()
    {
        
        $conge = new CongeModel();
        $idEmployer = session()->get('id');
        $data['conges'] = $conge->getCongesByEmploye($idEmployer);
        $data['congeAnnuelle'] = $conge->getCongesByEmployeAndType($idEmployer, 1);
        $data['congeMaladie'] = $conge->getCongesByEmployeAndType($idEmployer, 2);
        $data['congeSpeciale'] = $conge->getCongesByEmployeAndType($idEmployer, 3);
        // $data['congeSansSolde'] = $conge->getCongesByEmployeAndType($idEmployer, 4);
        $data['nbConges'] = $conge->getNbCongeParStatus($idEmployer);

        return view('employer/dashboard', $data);
        
    }
}