<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'created_at',
        'traite_par'
    ];

    public function getCongesByEmploye($employeId)
    {
        return $this->where('employe_id', $employeId)->orderBy('date_debut', 'DESC')->findAll();
    }

    public function getCongesByEmployeAndType($employeId, $typeCongeId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('conges.*, types_conge.libelle as type_conge_libelle')
                    ->where('employe_id', $employeId)
                    ->join('types_conge', 'types_conge.id = conges.type_conge_id')
                    ->where('type_conge_id', $typeCongeId)
                    ->orderBy('date_debut', 'DESC')
                    ->get();
        return $builder->get()->getResult();
    }

    public function getNbCongeParStatus($employeId)
    {
        return $this->select('statut')
                    ->selectSum('nb_jours', 'total_jours')
                    ->where('employe_id', $employeId)
                    ->groupBy('statut')
                    ->findAll();
    }
}