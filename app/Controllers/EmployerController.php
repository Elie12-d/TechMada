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
        return view('auth/login');
    }
}