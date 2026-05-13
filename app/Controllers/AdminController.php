<?php

namespace App\Controllers;

use App\Models\EmployerModel;
use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\TypesCongeModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $employeModel = new EmployerModel();
        $congeModel = new CongeModel();
        $deptModel = new DepartementModel();

        // Employés actifs
        $employes_actifs = $employeModel
            ->where('actif', 1)
            ->countAllResults();

        // Augmentation employés ce mois
        $debut_mois = date('Y-m-01');
        $fin_mois = date('Y-m-t');
        $employes_ce_mois = $employeModel
            ->where('actif', 1)
            ->where('date_embauche >=', $debut_mois)
            ->where('date_embauche <=', $fin_mois)
            ->countAllResults();

        // Demandes en attente
        $demandes_attente = $congeModel
            ->where('statut', 'en_attente')
            ->countAllResults();

        // Approuvées ce mois
        $approuvees_mois = $congeModel
            ->where('statut', 'approuvee')
            ->where('DATE(created_at) >=', $debut_mois)
            ->where('DATE(created_at) <=', $fin_mois)
            ->countAllResults();

        // Approuvées mois dernier
        $debut_mois_dernier = date('Y-m-01', strtotime('-1 month'));
        $fin_mois_dernier = date('Y-m-t', strtotime('-1 month'));
        $approuvees_mois_dernier = $congeModel
            ->where('statut', 'approuvee')
            ->where('DATE(created_at) >=', $debut_mois_dernier)
            ->where('DATE(created_at) <=', $fin_mois_dernier)
            ->countAllResults();

        $augmentation_approuvees = $approuvees_mois - $approuvees_mois_dernier;

        // Départements
        $nb_departements = $deptModel->countAllResults();

        // Absents aujourd'hui
        $aujourd_hui = date('Y-m-d');
        $conges_actifs = $congeModel
            ->select('c.*, e.prenom, e.nom, tc.libelle as type')
            ->from('conges c')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('types_conge tc', 'tc.id = c.type_conge_id')
            ->where('c.statut', 'approuvee')
            ->where('c.date_debut <=', $aujourd_hui)
            ->where('c.date_fin >=', $aujourd_hui)
            ->findAll();

        $absents = [];
        foreach ($conges_actifs as $conge) {
            $initials = substr($conge['prenom'], 0, 1) . substr($conge['nom'], 0, 1);
            $absents[] = [
                'name' => $conge['prenom'] . ' ' . $conge['nom'],
                'type' => $conge['type'],
                'date_fin' => $conge['date_fin'],
                'initials' => $initials,
                'avatar_class' => 'av-green'
            ];
        }

        // Demandes récentes (avec infos employé et type)
        $demandesRecentes = $congeModel
            ->select('c.*, e.prenom, e.nom, tc.libelle as type, c.statut')
            ->from('conges c')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('types_conge tc', 'tc.id = c.type_conge_id')
            ->orderBy('c.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $demandesFormatees = [];
        foreach ($demandesRecentes as $demande) {
            $initials = substr($demande['prenom'], 0, 1) . substr($demande['nom'], 0, 1);
            $demandesFormatees[] = [
                'user_name' => $demande['prenom'] . ' ' . $demande['nom'],
                'type' => $demande['type'],
                'duree' => (int)$demande['nb_jours'],
                'statut' => $demande['statut'],
                'user_initials' => $initials,
                'avatar_class' => 'av-green'
            ];
        }

        // Soldes critiques (< 2 jours)
        $soldesCritiques = $congeModel
            ->select('e.prenom, e.nom, s.jours_pris, s.jours_attribues, tc.libelle')
            ->from('soldes s')
            ->join('employes e', 'e.id = s.employe_id')
            ->join('types_conge tc', 'tc.id = s.type_conge_id')
            ->where('s.jours_attribues - s.jours_pris <=', 2)
            ->findAll();

        $data['metriques'] = [
            'employes_actifs' => $employes_actifs,
            'augmentation_employes' => $employes_ce_mois > 0 ? '+' . $employes_ce_mois : '+0',
            'demandes_attente' => $demandes_attente,
            'approuvees_mois' => $approuvees_mois,
            'augmentation_approuvees' => $augmentation_approuvees >= 0 ? '+' . $augmentation_approuvees : (string)$augmentation_approuvees,
            'departements' => $nb_departements,
            'absents_aujourd_hui' => count($absents)
        ];

        $data['demandesRecentes'] = $demandesFormatees;
        $data['absents'] = $absents;
        $data['soldesCritiques'] = $soldesCritiques;

        return $this->renderPage('admin/dashboard', $data, 'dashboard');
    }

    public function employes()
    {
        $employeModel = new EmployerModel();
        $deptModel = new DepartementModel();
        $db = \Config\Database::connect();

        // Récupérer tous les employés avec infos département et soldes
        $employes = $db->table('employes e')
            ->select('e.*, d.nom as dept_libelle, SUM(s.jours_attribues - s.jours_pris) as solde_annuel')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('soldes s', 's.employe_id = e.id AND s.annee = ' . date('Y'), 'left')
            ->groupBy('e.id')
            ->orderBy('e.nom', 'ASC')
            ->get()
            ->getResultArray();

        // Ajouter des données de formatage et statut
        $employes_formatted = [];
        foreach ($employes as $emp) {
            $emp['avatar_class'] = 'av-green';
            $emp['statut'] = $emp['actif'] == 1 ? 'actif' : 'inactif';
            $emp['solde_annuel'] = max(0, round($emp['solde_annuel'] ?? 0, 1));
            $employes_formatted[] = $emp;
        }

        $departements = $deptModel->findAll();

        $data['employes'] = $employes_formatted;
        $data['departements'] = $departements;

        return $this->renderPage('admin/employes', $data, 'employes');
    }

    public function store()
    {
        $rules = [
            'prenom' => 'required|string|min_length[2]',
            'nom' => 'required|string|min_length[2]',
            'email' => 'required|valid_email|is_unique[employes.email]',
            'password' => 'required|string|min_length[6]',
            'date_embauche' => 'required|valid_date[Y-m-d]',
            'role' => 'required|in_list[employe,rh,admin]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $employeModel = new EmployerModel();
        $typeModel = new TypesCongeModel();

        // Hash password
        $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        // Insérer employé
        $employeModel->insert([
            'prenom' => $this->request->getPost('prenom'),
            'nom' => $this->request->getPost('nom'),
            'email' => $this->request->getPost('email'),
            'password' => $password,
            'role' => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id') ?: null,
            'date_embauche' => $this->request->getPost('date_embauche'),
            'actif' => 1
        ]);

        $nouvelEmployeId = $employeModel->insertID();

        // Initialiser soldes de congés pour l'année actuelle
        $types = $typeModel->findAll();
        $db = \Config\Database::connect();
        $annee = date('Y');
        
        foreach ($types as $type) {
            $db->table('soldes')->insert([
                'employe_id' => $nouvelEmployeId,
                'type_conge_id' => $type['id'],
                'annee' => $annee,
                'jours_attribues' => $type['jours_annuels'],
                'jours_pris' => 0
            ]);
        }

        return redirect()->to('/admin/employes')->with('success', 'Employé créé avec succès et soldes initialisés.');
    }

    public function edit($id)
    {
        $employeModel = new EmployerModel();
        $deptModel = new DepartementModel();

        $employe = $employeModel->find($id);
        if (!$employe) {
            return redirect()->to('/admin/employes')->with('error', 'Employé non trouvé.');
        }

        $departements = $deptModel->findAll();
        $data['employe'] = $employe;
        $data['departements'] = $departements;

        return $this->renderPage('admin/employes-edit', $data, 'employes');
    }

    public function update($id)
    {
        $rules = [
            'prenom' => 'required|string|min_length[2]',
            'nom' => 'required|string|min_length[2]',
            'email' => 'required|valid_email',
            'date_embauche' => 'required|valid_date[Y-m-d]',
            'role' => 'required|in_list[employe,rh,admin]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $employeModel = new EmployerModel();
        $employe = $employeModel->find($id);

        if (!$employe) {
            return redirect()->to('/admin/employes')->with('error', 'Employé non trouvé.');
        }

        $data = [
            'prenom' => $this->request->getPost('prenom'),
            'nom' => $this->request->getPost('nom'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id') ?: null,
            'date_embauche' => $this->request->getPost('date_embauche'),
            'actif' => $this->request->getPost('actif') ? 1 : 0
        ];

        $employeModel->update($id, $data);

        return redirect()->to('/admin/employes')->with('success', 'Employé mis à jour avec succès.');
    }

    public function delete($id)
    {
        $employeModel = new EmployerModel();
        $employe = $employeModel->find($id);

        if (!$employe) {
            return redirect()->to('/admin/employes')->with('error', 'Employé non trouvé.');
        }

        $employeModel->delete($id);

        return redirect()->to('/admin/employes')->with('success', 'Employé supprimé avec succès.');
    }
}
