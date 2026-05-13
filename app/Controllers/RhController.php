<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployerModel;
use App\Models\TypesCongeModel;

class RhController extends BaseController
{
    public function demandes()
    {
        // Vérifier le rôle de l'utilisateur
        $userRole = strtolower(session()->get('role') ?? 'employe');
        if ($userRole === 'responsable') {
            $userRole = 'rh';
        }

        if ($userRole !== 'rh' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Accès refusé.');
        }

        $congeModel = new CongeModel();
        $departementModel = new DepartementModel();

        // Récupérer toutes les demandes avec les informations des employés
        $demandes = $congeModel->select('
            conges.id,
            conges.date_debut,
            conges.date_fin,
            conges.nb_jours,
            conges.statut,
            conges.motif,
            conges.commentaire_rh,
            employes.prenom,
            employes.nom,
            employes.departement_id,
            departements.nom as departement_nom,
            types_conge.libelle as type
        ')
        ->join('employes', 'employes.id = conges.employe_id', 'left')
        ->join('departements', 'departements.id = employes.departement_id', 'left')
        ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
        ->orderBy('conges.date_debut', 'DESC')
        ->findAll();

        // Formater les données pour la vue
        $demandesFormatted = [];
        foreach ($demandes as $demande) {
            $prenom = trim((string)($demande['prenom'] ?? ''));
            $nom = trim((string)($demande['nom'] ?? ''));
            $userName = $prenom . ' ' . $nom;
            $userName = trim($userName) !== '' ? trim($userName) : 'Employé';

            $demandesFormatted[] = [
                'id' => $demande['id'],
                'user_name' => $userName,
                'prenom' => $prenom,
                'nom' => $nom,
                'user_dept' => $demande['departement_nom'] ?? 'Département',
                'user_initials' => strtoupper(mb_substr($prenom, 0, 1)) . strtoupper(mb_substr($nom, 0, 1)),
                'avatar_class' => 'av-green',
                'type' => $demande['type'] ?? 'Congé',
                'type_libelle' => $demande['type'] ?? 'Congé',
                'date_debut' => $demande['date_debut'],
                'date_fin' => $demande['date_fin'],
                'duree' => $demande['nb_jours'] ?? 0,
                'solde_dispo' => $demande['nb_jours'] ?? 0,
                'statut' => $demande['statut'],
                'motif' => $demande['motif'],
                'commentaire_rh' => $demande['commentaire_rh']
            ];
        }

        // Récupérer tous les départements pour le filtre
        $departements = $departementModel->findAll() ?? [];

        $data['demandes'] = $demandesFormatted;
        $data['departements'] = $departements;
        $data['userRole'] = $userRole;

        return $this->renderPage('rh/index', $data, 'demandes');
    }

    public function approuver($id)
    {
        $userRole = strtolower(session()->get('role') ?? 'employe');
        if ($userRole === 'responsable') {
            $userRole = 'rh';
        }

        if ($userRole !== 'rh' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Accès refusé.');
        }

        $congeModel = new CongeModel();
        $demande = $congeModel->find($id);

        if (!$demande) {
            return redirect()->back()->with('error', 'Demande introuvable.');
        }

        $congeModel->update($id, [
            'statut' => 'approuvee',
            'traite_par' => session()->get('id'),
            'commentaire_rh' => null
        ]);

        return redirect()->to('rh/demandes')->with('success', 'Demande approuvée avec succès.');
    }

    public function refuser($id)
    {
        $userRole = strtolower(session()->get('role') ?? 'employe');
        if ($userRole === 'responsable') {
            $userRole = 'rh';
        }

        if ($userRole !== 'rh' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Accès refusé.');
        }

        $congeModel = new CongeModel();
        $demande = $congeModel->find($id);

        if (!$demande) {
            return redirect()->back()->with('error', 'Demande introuvable.');
        }

        $commentaire = $this->request->getPost('commentaire') ?? '';

        $congeModel->update($id, [
            'statut' => 'refusee',
            'traite_par' => session()->get('id'),
            'commentaire_rh' => $commentaire
        ]);

        return redirect()->to('rh/demandes')->with('success', 'Demande refusée.');
    }
}
