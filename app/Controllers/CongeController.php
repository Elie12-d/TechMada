<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\TypesCongeModel;

class CongeController extends BaseController
{
    public function index()
    {
        $employeId = session()->get('id');
        $congeModel = new CongeModel();

        $data['demandes'] = $congeModel->getCongesByEmploye($employeId);
        return $this->renderPage('employe/index', $data, 'demandes');
    }

    public function create()
    {
        $employeId = session()->get('id');
        $typeModel = new TypesCongeModel();
        $congeModel = new CongeModel();

        $types = $typeModel->findAll();
        $soldes = [];

        foreach ($types as $type) {
            $taken = $congeModel
                ->selectSum('nb_jours', 'total_jours')
                ->where('employe_id', $employeId)
                ->where('type_conge_id', $type['id'])
                ->where('statut', 'approuvee')
                ->first();

            $joursPris = $taken['total_jours'] ?? 0;
            $total = $type['jours_annuels'] ?? 0;

            $soldes[] = [
                'type' => $type['libelle'],
                'restants' => max(0, $total - $joursPris),
                'total' => $total
            ];
        }

        $data['typesConge'] = $types;
        $data['soldes'] = $soldes;
        return $this->renderPage('employe/create', $data, 'create');
    }

    public function store()
    {
        $rules = [
            'type_conge_id' => 'required|integer',
            'date_debut' => 'required|valid_date[Y-m-d]',
            'date_fin' => 'required|valid_date[Y-m-d]' 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        if (strtotime($dateFin) < strtotime($dateDebut)) {
            return redirect()->back()->withInput()->with('error', 'La date de fin doit être postérieure à la date de début.');
        }

        $nbJours = (int) ((strtotime($dateFin) - strtotime($dateDebut)) / (60 * 60 * 24)) + 1;

        $congeModel = new CongeModel();
        $congeModel->insert([
            'employe_id' => session()->get('id'),
            'type_conge_id' => $this->request->getPost('type_conge_id'),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $this->request->getPost('motif'),
            'statut' => 'en_attente',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/dashboard')->with('success', 'Votre demande de congé a été créée et est en attente de validation.');
    }
}
