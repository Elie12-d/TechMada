<?php

if (!function_exists('prepareSidebar')) {
    function prepareSidebar($userRole = null, $user = null, $activeMenu = '')
    {
        $sidebarData = [];
        $userRole = strtolower($userRole ?? session()->get('role') ?? 'employe');
        if ($userRole === 'responsable') {
            $userRole = 'rh';
        }

        if (empty($activeMenu)) {
            $currentPath = trim(service('uri')->getPath(), '/');
            switch ($currentPath) {
                case 'dashboard':
                case 'employe/dashboard':
                case 'rh/dashboard':
                case 'admin/dashboard':
                    $activeMenu = 'dashboard';
                    break;
                case 'conges/create':
                case 'employe/demandes/create':
                    $activeMenu = 'create';
                    break;
                case 'conges':
                case 'employe/demandes':
                case 'rh/demandes':
                case 'admin/demandes':
                    $activeMenu = 'demandes';
                    break;
                case 'profil':
                case 'employe/profil':
                    $activeMenu = 'profil';
                    break;
                case 'rh/historique':
                    $activeMenu = 'historique';
                    break;
                case 'rh/soldes':
                case 'admin/soldes':
                    $activeMenu = 'soldes';
                    break;
                case 'admin/employes':
                    $activeMenu = 'employes';
                    break;
                case 'admin/departements':
                    $activeMenu = 'departements';
                    break;
                case 'admin/types-conge':
                    $activeMenu = 'types';
                    break;
            }
        }
        
        if ($userRole === 'employe') {
            $sidebarData = [
                'sidebarIcon' => 'bi bi-briefcase',
                'sidebarSubtitle' => 'Espace employé',
                'menuItems' => [
                    [
                        'url' => base_url('/dashboard'),
                        'icon' => 'bi bi-grid-1x2',
                        'label' => 'Tableau de bord',
                        'active' => $activeMenu === 'dashboard'
                    ],
                    [
                        'url' => base_url('/conges/create'),
                        'icon' => 'bi bi-plus-circle',
                        'label' => 'Nouvelle demande',
                        'active' => $activeMenu === 'create'
                    ],
                    [
                        'url' => base_url('/conges'),
                        'icon' => 'bi bi-calendar3',
                        'label' => 'Mes demandes',
                        'badge' => ['text' => '2', 'class' => 'alert'],
                        'active' => $activeMenu === 'demandes'
                    ],
                    [
                        'url' => base_url('/profil'),
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
        
        if ($user) {
            $sidebarData['user'] = [
                'name' => ($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''),
                'role' => $user['role_libelle'] ?? ucfirst($userRole),
                'initials' => strtoupper(substr($user['prenom'] ?? '', 0, 1)) . strtoupper(substr($user['nom'] ?? '', 0, 1)),
                'avatarClass' => $user['avatar_class'] ?? 'av-green'
            ];
        }
        
        return $sidebarData;
    }
}

if (!function_exists('prepareHeader')) {
    function prepareHeader($pageTitle = 'Tableau de bord', $breadcrumbs = [], $actions = [])
    {
        return [
            'pageTitle' => $pageTitle,
            'breadcrumbs' => $breadcrumbs,
            'actions' => $actions
        ];
    }
}

if (!function_exists('getBreadcrumbs')) {
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
}

if (!function_exists('getStatusBadge')) {
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
}

if (!function_exists('getLeaveTypeBadge')) {
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
}

if (!function_exists('getUrgencyClass')) {
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
}
