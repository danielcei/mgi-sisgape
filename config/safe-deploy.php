<?php

declare(strict_types=1);

use App\Models\User;

return [
    'default_user_model' => env('SAFE_DEPLOY_DEFAULT_USER_MODEL', User::class),

    'migrations_connection' => env('SAFE_DEPLOY_MIGRATIONS_CONNECTION', 'non_persistent'),

    'user_stamp_columns' => [
        'created_by' => env('SAFE_DEPLOY__USER_STAMP_CREATED_BY_COLUMN', 'created_by'),
        'updated_by' => env('SAFE_DEPLOY_M_USER_STAMP_UPDATED_BY_COLUMN', 'updated_by'),
        'deleted_by' => env('SAFE_DEPLOY__USER_STAMP_DELETED_BY_COLUMN', 'deleted_by'),
    ],

    'icons' => [
        // Overrides
        'panels::sidebar.collapse-button' => 'phosphor-dots-three-outline-vertical',
        'panels::sidebar.expand-button' => 'phosphor-list',

        // Resource Groups
        'safe-deploy-icon::group.administrativo' => 'phosphor-gear',
        'safe-deploy-icon::group.analise' => 'phosphor-chart-bar',
        'safe-deploy-icon::group.associado' => 'phosphor-building-office',
        'safe-deploy-icon::group.faturamento' => 'phosphor-currency-dollar',
        'safe-deploy-icon::group.venda' => 'phosphor-shopping-cart',
        'safe-deploy-icon::group.simulador' => 'phosphor-flask',
        'safe-deploy-icon::group.funcoes-e-permissoes' => 'phosphor-key',


        // DAF
        'safe-deploy-icon::resource.business-unit' => 'phosphor-puzzle-piece',
        'safe-deploy-icon::resource.budgets' => 'phosphor-file',
        'safe-deploy-icon::resource.clients' => 'phosphor-building',
        'safe-deploy-icon::resource.credit-notes' => 'phosphor-coins',
        'safe-deploy-icon::resource.guarantee-and-retention' => 'phosphor-folder-simple-lock',
        'safe-deploy-icon::resource.monthly-expected-budget' => 'phosphor-file-text',
        'safe-deploy-icon::resource.person-cost-history' => 'phosphor-currency-dollar-simple',
        'safe-deploy-icon::resource.project-expense' => 'phosphor-invoice',
        'safe-deploy-icon::resource.project-invoice-alarm' => 'phosphor-bell',
        'safe-deploy-icon::resource.project-invoice' => 'phosphor-file-text',
        'safe-deploy-icon::resource.return-cost' => 'phosphor-coins',

        // Human Resources
        'safe-deploy-icon::resource.business-unit-person-delegated' => 'phosphor-hand-coins',
        'safe-deploy-icon::resource.person' => 'phosphor-person',
        'safe-deploy-icon::resource.holiday' => 'phosphor-cheers',
        'safe-deploy-icon::resource.perquisites' => 'phosphor-hand-coins',
        'safe-deploy-icon::resource.person-expenses' => 'phosphor-printer',

        // Manager
        'safe-deploy-icon::resource.internal-costs' => 'phosphor-currency-dollar-simple',

        // Project Management
        'safe-deploy-icon::resource.documents' => 'phosphor-file',
        'safe-deploy-icon::resource.internal-revenue-note' => 'phosphor-code',
        'safe-deploy-icon::resource.internal-revenues' => 'phosphor-arrows-left-right',
        'safe-deploy-icon::resource.projects' => 'phosphor-laptop',

        // Resources
        'safe-deploy-icon::resource.ability' => 'phosphor-code',
        'safe-deploy-icon::resource.absence-type' => 'phosphor-arrows-left-right',
        'safe-deploy-icon::resource.activity-sector' => 'phosphor-building',
        'safe-deploy-icon::resource.approval-state-type' => 'phosphor-check',
        'safe-deploy-icon::resource.bank-account-type' => 'phosphor-money',
        'safe-deploy-icon::resource.bank-entity' => 'phosphor-bank',
        'safe-deploy-icon::resource.bill-type' => 'phosphor-invoice',
        'safe-deploy-icon::resource.category-ability' => 'phosphor-code',
        'safe-deploy-icon::resource.certification' => 'phosphor-certificate',
        'safe-deploy-icon::resource.check-type' => 'phosphor-arrows-left-right',
        'safe-deploy-icon::resource.contact-type' => 'phosphor-at',
        'safe-deploy-icon::resource.contract-category' => 'phosphor-file-text',
        'safe-deploy-icon::resource.contract-type' => 'phosphor-file-text',
        'safe-deploy-icon::resource.cost-center' => 'phosphor-coins',
        'safe-deploy-icon::resource.cost-status' => 'phosphor-coins',
        'safe-deploy-icon::resource.cost-type' => 'phosphor-coins',
        'safe-deploy-icon::resource.country' => 'phosphor-globe',
        'safe-deploy-icon::resource.course' => 'phosphor-book-open-user',
        'safe-deploy-icon::resource.delivered-invoice-type' => 'phosphor-printer',
        'safe-deploy-icon::resource.document-type' => 'phosphor-identification-card',
        'safe-deploy-icon::resource.educational-institution' => 'phosphor-graduation-cap',
        'safe-deploy-icon::resource.expense-type' => 'phosphor-shopping-cart-simple',
        'safe-deploy-icon::resource.gender' => 'phosphor-gender-intersex',
        'safe-deploy-icon::resource.guarantee-and-retention-type' => 'phosphor-hand-coins',
        'safe-deploy-icon::resource.holiday-database' => 'phosphor-database',
        'safe-deploy-icon::resource.internal-revenue-state' => 'phosphor-arrows-left-right',
        'safe-deploy-icon::resource.job-area' => 'phosphor-briefcase',
        'safe-deploy-icon::resource.marital-status' => 'phosphor-intersect',
        'safe-deploy-icon::resource.occupation' => 'phosphor-briefcase',
        'safe-deploy-icon::resource.payment-state-type' => 'phosphor-check',
        'safe-deploy-icon::resource.payment-type' => 'phosphor-money',
        'safe-deploy-icon::resource.perquisite-frequency' => 'phosphor-clock',
        'safe-deploy-icon::resource.perquisite-type' => 'phosphor-hand-coins',
        'safe-deploy-icon::resource.person-expense-status' => 'phosphor-printer',
        'safe-deploy-icon::resource.person-expense-type' => 'phosphor-printer',
        'safe-deploy-icon::resource.person-type' => 'phosphor-user-gear',
        'safe-deploy-icon::resource.phc-company' => 'phosphor-puzzle-piece',
        'safe-deploy-icon::resource.priority-type' => 'phosphor-warning-circle',
        'safe-deploy-icon::resource.professional-status' => 'phosphor-briefcase',
        'safe-deploy-icon::resource.project-document-type' => 'phosphor-file-text',
        'safe-deploy-icon::resource.project-role' => 'phosphor-tree-structure',
        'safe-deploy-icon::resource.project-type' => 'phosphor-chart-scatter',
        'safe-deploy-icon::resource.rate-period' => 'phosphor-clock',
        'safe-deploy-icon::resource.scholarity' => 'phosphor-pencil-ruler',
        'safe-deploy-icon::resource.sending-state-type' => 'phosphor-share',
        'safe-deploy-icon::resource.state-type' => 'phosphor-power',
        'safe-deploy-icon::resource.suggestion-status' => 'phosphor-lightbulb',
        'safe-deploy-icon::resource.task-type' => 'phosphor-list-checks',
        'safe-deploy-icon::resource.tax' => 'phosphor-percent',
        'safe-deploy-icon::resource.vacation-rejection-reason' => 'phosphor-arrows-left-right',
        'safe-deploy-icon::resource.variable-movement-status' => 'phosphor-briefcase',
        'safe-deploy-icon::resource.variable-movement-type' => 'phosphor-arrows-left-right',
        'safe-deploy-icon::resource.user' => 'phosphor-user',

        // company
        'safe-deploy-icon::page.project-pipeline' => 'phosphor-building',
        'safe-deploy-icon::page.project-profit' => 'phosphor-building',

        // Other
        'safe-deploy-icon::resource.person-addresses' => 'phosphor-house-line',
        'safe-deploy-icon::resource.person-certificates' => 'phosphor-certificate',

        'safe-deploy-icon::calendar' => 'phosphor-calendar-blank',
        'safe-deploy-icon::currency-euro-symbol' => 'phosphor-currency-eur',
        'safe-deploy-icon::cost-type' => 'phosphor-building',
        'safe-deploy-icon::details' => 'phosphor-file-magnifying-glass',
        'safe-deploy-icon::documents' => 'phosphor-identification-card',
        'safe-deploy-icon::bank-account' => 'phosphor-bank',
        'safe-deploy-icon::contacts' => 'phosphor-address-book',
        'safe-deploy-icon::projects' => 'phosphor-laptop',
        'safe-deploy-icon::salary-history' => 'phosphor-currency-dollar-simple',
        'safe-deploy-icon::attachments' => 'phosphor-paperclip',
        'safe-deploy-icon::download' => 'phosphor-cloud-arrow-down',
        'safe-deploy-icon::expenses' => 'phosphor-printer',
        'safe-deploy-icon::perquisites' => 'phosphor-hand-coins',
        'safe-deploy-icon::vacationes' => 'phosphor-island',
        'safe-deploy-icon::business-unit' => 'phosphor-puzzle-piece',
        'safe-deploy-icon::tasks' => 'phosphor-list-checks',
        'safe-deploy-icon::info' => 'phosphor-info',
        'safe-deploy-icon::members' => 'phosphor-users-three',
        'safe-deploy-icon::reported-hours' => 'phosphor-clock-user',
        'safe-deploy-icon::user' => 'phosphor-user',
        'safe-deploy-icon::clock' => 'phosphor-clock',
        'safe-deploy-icon::notes' => 'phosphor-note',
    ],

    'resources_ordering' => [
        // Administrative
        //AuditResource::class,
        //PermissionResource::class,
        //RoleResource::class,
        //UserResource::class,
    ],
];
