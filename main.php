<?php
require_once 'functions.php';

while (true) {
    afficherMenu();
    echo "Choisissez une option : ";
    $choix = trim(fgets(STDIN));

    switch ($choix) {
        case 1:
            nouvellePartie();
            break;
        case 2:
            afficherHistorique();
            break;
        case 3:
            afficherStatistiques();
            break;
        case 4:
            echo "Au revoir !\n";
            exit;
        default:
            echo "Option invalide. Veuillez réessayer.\n";
    }
}