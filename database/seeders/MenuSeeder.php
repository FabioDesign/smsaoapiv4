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
            ["en" => 'Dashboard'],
            [
                "fr" => 'Tableau de bord',
                "status" => 1,
                "icone" => 'dashboard-icon',
                "target" => '/dashboard',
                "menu_id" => 0,
                "position" => 1,
            ]
        );
        Menu::firstOrCreate(
            ["en" => 'Documents'],
            [
                "fr" => 'Documents',
                "status" => 1,
                "icone" => 'documents-icon',
                "target" => '/documents',
                "menu_id" => 0,
                "position" => 2,
            ]
        );
        Menu::firstOrCreate(
            ["en" => 'Documents to provide'],
            [
                "fr" => 'Pièces à fournir',
                "status" => 1,
                "icone" => 'attachments-icon',
                "target" => '/attachments',
                "menu_id" => 0,
                "position" => 3,
            ]
        );
        Menu::firstOrCreate(
            ["en" => 'Settings'],
            [
                "fr" => 'Paramètres',
                "status" => 1,
                "icone" => 'settings-icon',
                "target" => '/settings',
                "menu_id" => 0,
                "position" => 4,
            ]
        );
        Menu::firstOrCreate(
            ["en" => 'Profile Management'],
            [
                "fr" => 'Gestion des Profils',
                "status" => 1,
                "icone" => 'profile-icon',
                "target" => '/profile',
                "menu_id" => 0,
                "position" => 5,
            ]
        );
        Menu::firstOrCreate(
            ["en" => 'User Management'],
            [
                "fr" => 'Gestion des Utilisateurs',
                "status" => 1,
                "icone" => 'user-icon',
                "target" => '/users',
                "menu_id" => 0,
                "position" => 6,
            ]
        );
        Menu::firstOrCreate(
            ["en" => 'Appointments'],
            [
                "fr" => 'Rendez-vous',
                "status" => 1,
                "icone" => 'appointments-icon',
                "target" => '/appointments',
                "menu_id" => 0,
                "position" => 7,
            ]
        );
        Menu::firstOrCreate(
            ["en" => 'Audit trail'],
            [
                "fr" => "Piste d'audit",
                "status" => 1,
                "icone" => 'audit_trail-icon',
                "target" => '/audit_trail',
                "menu_id" => 0,
                "position" => 8,
            ]
        );
    }
}
