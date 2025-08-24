<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Menu::firstOrCreate(
            ["label_en" => 'Dashboard'],
            [
                "label_fr" => 'Tableau de bord',
                "label_en" => 'Dashboard',
                "status" => 1,
                "icone" => 'dashboard-icon',
                "target" => '/dashboard',
                "menu_id" => 0,
                "position" => 1,
            ]
        );
        Menu::firstOrCreate(
            ["label_en" => 'Documents'],
            [
                "label_fr" => 'Documents',
                "label_en" => 'Documents',
                "status" => 1,
                "icone" => 'documents-icon',
                "target" => '/documents',
                "menu_id" => 0,
                "position" => 2,
            ]
        );
        Menu::firstOrCreate(
            ["label_en" => 'Documents to provide'],
            [
                "label_fr" => 'Pièces à fournir',
                "label_en" => 'Documents to provide',
                "status" => 1,
                "icone" => 'attachments-icon',
                "target" => '/attachments',
                "menu_id" => 0,
                "position" => 3,
            ]
        );
        Menu::firstOrCreate(
            ["label_en" => 'Settings'],
            [
                "label_fr" => 'Paramètres',
                "label_en" => 'Settings',
                "status" => 1,
                "icone" => 'settings-icon',
                "target" => '/settings',
                "menu_id" => 0,
                "position" => 4,
            ]
        );
        Menu::firstOrCreate(
            ["label_en" => 'Profile Management'],
            [
                "label_fr" => 'Gestion Profil',
                "label_en" => 'Profile Management',
                "status" => 1,
                "icone" => 'profile-icon',
                "target" => '/profile',
                "menu_id" => 0,
                "position" => 5,
            ]
        );
        Menu::firstOrCreate(
            ["label_en" => 'User Management'],
            [
                "label_fr" => 'Gestion Utilisateurs',
                "label_en" => 'User Management',
                "status" => 1,
                "icone" => 'user-icon',
                "target" => '/users',
                "menu_id" => 0,
                "position" => 6,
            ]
        );
        Menu::firstOrCreate(
            ["label_en" => 'Appointments'],
            [
                "label_fr" => 'Rendez-vous',
                "label_en" => 'Appointments',
                "status" => 1,
                "icone" => 'appointments-icon',
                "target" => '/appointments',
                "menu_id" => 0,
                "position" => 7,
            ]
        );
        Menu::firstOrCreate(
            ["label_en" => 'Audit trail'],
            [
                "label_fr" => "Piste d'audit",
                "label_en" => 'Audit trail',
                "status" => 1,
                "icone" => 'logs-icon',
                "target" => '/logs',
                "menu_id" => 0,
                "position" => 8,
            ]
        );
    }
}
