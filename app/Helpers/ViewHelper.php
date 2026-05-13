<?php

/**
 * ====================================================================
 * HELPER FUNCTIONS FOR VIEW DATA PREPARATION
 * ====================================================================
 * Functions to prepare sidebar, header, and other shared data
 * for views in the TechMada RH application
 */

/**
 * Prepare sidebar data based on user role
 */
function prepareSidebar($userRole = 'employe', $user = null, $activeMenu = '')
{
    $sidebarData = [];
    
    if ($userRole === 'employe') {
        $sidebarData = [
            'sidebarIcon' => 'bi bi-briefcase',
            'sidebarSubtitle' => 'Espace employé',
            'menuItems' => [
                [
                    'url' => base_url('employe/dashboard'),
                    'icon' => 'bi bi-grid-1x2',
                    'label' => 'Tableau de bord',
                    'active' => $activeMenu === 'dashboard'
                ],
                [
                    'url' => base_url('employe/demandes/create'),
                    'icon' => 'bi bi-plus-circle',
                    'label' => 'Nouvelle demande',
                    'active' => $activeMenu === 'create'
                ],
                [
                    'url' => base_url('employe/demandes'),
                    'icon' => 'bi bi-calendar3',
                    'label' => 'Mes demandes',
                    'badge' => ['text' => '2', 'class' => 'alert'],
                    'active' => $activeMenu === 'demandes'
                ],
                [
                    'url' => base_url('employe/profil'),
                    'icon' => 'bi bi-person',
                    'label' => 'Mon profil',
                    'active' => $activeMenu === 'profil'
                ],
            ]
        ];
    } elseif ($userRole === 'rh') {
        $sidebarData = [
            'sidebarIcon' => 'bi bi-person-check',
            'sidebarSubtitle' => 'Espace responsable',
            'menuItems' => [
                [
                    'url' => base_url('rh/dashboard'),
                    'icon' => 'bi bi-grid-1x2',
                    'label' => 'Tableau de bord',
                    'active' => $activeMenu === 'dashboard'
                ],
                [
                    'url' => base_url('rh/demandes'),
                    'icon' => 'bi bi-inbox',
                    'label' => 'Demandes à traiter',
                    'badge' => ['text' => '4', 'class' => 'alert'],
                    'active' => $activeMenu === 'demandes'
                ],
                [
                    'url' => base_url('rh/historique'),
                    'icon' => 'bi bi-archive',
                    'label' => 'Historique',
                    'active' => $activeMenu === 'historique'
                ],
                [
                    'url' => base_url('rh/soldes'),
                    'icon' => 'bi bi-people',
                    'label' => 'Soldes employés',
                    'active' => $activeMenu === 'soldes'
                ],
            ]
        ];
    } elseif ($userRole === 'admin') {
        $sidebarData = [
            'sidebarIcon' => 'bi bi-shield-check',
            'sidebarSubtitle' => 'Administration',
            'menuItems' => [
                [
                    'url' => base_url('admin/dashboard'),
                    'icon' => 'bi bi-speedometer2',
                    'label' => 'Vue d\'ensemble',
                    'active' => $activeMenu === 'dashboard'
                ],
                [
                    'url' => base_url('admin/demandes'),
                    'icon' => 'bi bi-inbox',
                    'label' => 'Toutes les demandes',
                    'badge' => ['text' => '4', 'class' => 'alert'],
                    'active' => $activeMenu === 'demandes'
                ],
                [
                    'url' => base_url('admin/employes'),
                    'icon' => 'bi bi-people',
                    'label' => 'Employés',
                    'active' => $activeMenu === 'employes'
                ],
                [
                    'url' => base_url('admin/departements'),
                    'icon' => 'bi bi-building',
                    'label' => 'Départements',
                    'active' => $activeMenu === 'departements'
                ],
                [
                    'url' => base_url('admin/types-conge'),
                    'icon' => 'bi bi-tags',
                    'label' => 'Types de congé',
                    'active' => $activeMenu === 'types'
                ],
                [
                    'url' => base_url('admin/soldes'),
                    'icon' => 'bi bi-sliders',
                    'label' => 'Soldes annuels',
                    'active' => $activeMenu === 'soldes'
                ],
            ]
        ];
    }
    
    // Add user info
    if ($user) {
        $sidebarData['user'] = [
            'name' => $user['prenom'] . ' ' . $user['nom'] ?? 'Utilisateur',
            'role' => $user['role_libelle'] ?? ucfirst($userRole),
            'initials' => strtoupper(substr($user['prenom'] ?? '', 0, 1)) . strtoupper(substr($user['nom'] ?? '', 0, 1)),
            'avatarClass' => $user['avatar_class'] ?? 'av-green'
        ];
    }
    
    return $sidebarData;
}

/**
 * Prepare header data
 */
function prepareHeader($pageTitle = 'Tableau de bord', $breadcrumbs = [], $actions = [])
{
    return [
        'pageTitle' => $pageTitle,
        'breadcrumbs' => $breadcrumbs,
        'actions' => $actions
    ];
}

/**
 * Get common page title for different sections
 */
function getPageTitle($userRole, $page)
{
    $titles = [
        'employe' => [
            'dashboard' => 'Tableau de bord',
            'create' => 'Nouvelle demande de congé',
            'demandes' => 'Mes demandes de congé',
            'profil' => 'Mon profil'
        ],
        'rh' => [
            'dashboard' => 'Vue d\'ensemble',
            'demandes' => 'Demandes à traiter',
            'historique' => 'Historique des demandes',
            'soldes' => 'Soldes des employés'
        ],
        'admin' => [
            'dashboard' => 'Vue d\'ensemble',
            'demandes' => 'Toutes les demandes',
            'employes' => 'Gestion des employés',
            'departements' => 'Gestion des départements',
            'types' => 'Types de congé',
            'soldes' => 'Soldes annuels'
        ]
    ];
    
    return $titles[$userRole][$page] ?? 'TechMada RH';
}

/**
 * Get breadcrumbs for navigation
 */
function getBreadcrumbs($userRole, $page)
{
    $home = ['label' => 'Accueil', 'url' => base_url($userRole . '/dashboard')];
    
    if ($page === 'dashboard') {
        return [$home];
    }
    
    $breadcrumbs = [
        'employe' => [
            'create' => [$home, ['label' => 'Nouvelle demande']],
            'demandes' => [$home, ['label' => 'Mes demandes']],
            'profil' => [$home, ['label' => 'Mon profil']]
        ],
        'rh' => [
            'demandes' => [$home, ['label' => 'Demandes à traiter']],
            'historique' => [$home, ['label' => 'Historique']],
            'soldes' => [$home, ['label' => 'Soldes']]
        ],
        'admin' => [
            'demandes' => [$home, ['label' => 'Toutes les demandes']],
            'employes' => [$home, ['label' => 'Employés']],
            'departements' => [$home, ['label' => 'Départements']],
            'types' => [$home, ['label' => 'Types de congé']],
            'soldes' => [$home, ['label' => 'Soldes']]
        ]
    ];
    
    return $breadcrumbs[$userRole][$page] ?? [$home];
}

/**
 * Format status badge in French
 */
function getStatusBadge($status)
{
    $statuses = [
        'en_attente' => ['label' => 'en attente', 'class' => 's-attente'],
        'approuvee' => ['label' => 'approuvée', 'class' => 's-approuvee'],
        'refusee' => ['label' => 'refusée', 'class' => 's-refusee'],
        'annulee' => ['label' => 'annulée', 'class' => 's-annulee']
    ];
    
    return $statuses[$status] ?? ['label' => $status, 'class' => ''];
}

/**
 * Format leave type badge in French
 */
function getLeaveTypeBadge($type)
{
    $types = [
        'annuel' => 't-annuel',
        'maladie' => 't-maladie',
        'special' => 't-special',
        'sans_solde' => 't-sans-solde'
    ];
    
    return $types[strtolower(str_replace(' ', '_', $type))] ?? '';
}

/**
 * Calculate urgency class based on remaining days
 */
function getUrgencyClass($remaining, $total)
{
    $percentage = ($remaining / $total) * 100;
    
    if ($percentage <= 25) {
        return 'danger';
    } elseif ($percentage <= 50) {
        return 'warn';
    }
    
    return '';
}
