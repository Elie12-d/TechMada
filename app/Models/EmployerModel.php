<?php 

namespace App\Models;
use CodeIgniter\Model;


class EmployerModel extends Model{
    protected $table = 'employers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom', 'prenom','email','role','departement_id','date_embauche','password','actif'];    
}