<?php

function afficherMenu()
{
    echo "\n=== Jeu Shifumi ===\n";
    echo "1. Nouvelle partie\n";
    echo "2. Historique des parties\n";
    echo "3. Statistiques\n";
    echo "4. Quitter\n";
}

function nouvellePartie()
{
    while (true) {
        echo "\nTapez 'pierre', 'feuille', 'ciseau' ou 'menu' pour revenir au menu : ";
        $choixJoueur = strtolower(trim(fgets(STDIN)));

        if ($choixJoueur === 'menu') {
            return;
        }

        $choixPossibles = ['pierre', 'feuille', 'ciseau'];
        if (!in_array($choixJoueur, $choixPossibles)) {
            echo "Choix invalide. Réessayez.\n";
            continue;
        }

        $choixCPU = $choixPossibles[array_rand($choixPossibles)];
        echo "L'ordinateur a choisi : $choixCPU\n";

        $resultat = determinerResultat($choixJoueur, $choixCPU);
        echo "Résultat : $resultat\n";

        enregistrerPartie($choixJoueur, $choixCPU, $resultat);

        echo "\n1. Rejouer\n2. Retour au menu\nVotre choix : ";
        $suite = trim(fgets(STDIN));
        if ($suite != 1) {
            return;
        }
    }
}

function determinerResultat($joueur, $cpu)
{
    if ($joueur === $cpu) return "Égalité";

    if (
        ($joueur === 'pierre' && $cpu === 'ciseau') ||
        ($joueur === 'feuille' && $cpu === 'pierre') ||
        ($joueur === 'ciseau' && $cpu === 'feuille')
    ) {
        return "Gagné";
    } else {
        return "Perdu";
    }
}

function enregistrerPartie($joueur, $cpu, $resultat)
{
    $fichier = 'data/parties.json';
    if (!file_exists($fichier)) {
        file_put_contents($fichier, json_encode([]));
    }

    $historique = json_decode(file_get_contents($fichier), true);
    $historique[] = [
        'date' => date('Y-m-d H:i:s'),
        'joueur' => $joueur,
        'cpu' => $cpu,
        'résultat' => $resultat
    ];
    file_put_contents($fichier, json_encode($historique, JSON_PRETTY_PRINT));
}

function afficherHistorique()
{
    $fichier = 'data/parties.json';
    if (!file_exists($fichier)) {
        echo "Aucune partie enregistrée.\n";
        return;
    }

    $historique = json_decode(file_get_contents($fichier), true);

    echo "\n=== Historique des parties ===\n";
    printf("%-20s %-10s %-10s %-10s\n", "Date", "Joueur", "CPU", "Résultat");
    echo str_repeat("-", 55) . "\n";

    foreach ($historique as $partie) {
        printf("%-20s %-10s %-10s %-10s\n", $partie['date'], $partie['joueur'], $partie['cpu'], $partie['résultat']);
    }

    echo "\nAppuyez sur Entrée pour retourner au menu.";
    fgets(STDIN);
}

function afficherStatistiques()
{
    $fichier = 'data/parties.json';
    if (!file_exists($fichier)) {
        echo "Aucune partie enregistrée.\n";
        return;
    }

    $historique = json_decode(file_get_contents($fichier), true);
    $total = count($historique);
    $victoires = 0;
    $mains = ['pierre' => 0, 'feuille' => 0, 'ciseau' => 0];
    $gainsParMain = ['pierre' => 0, 'feuille' => 0, 'ciseau' => 0];

    foreach ($historique as $partie) {
        $mains[$partie['joueur']]++;
        if ($partie['résultat'] === "Gagné") {
            $victoires++;
            $gainsParMain[$partie['joueur']]++;
        }
    }

    $taux = $total > 0 ? round(($victoires / $total) * 100, 2) : 0;

    echo "\n=== Statistiques ===\n";
    echo "Nombre total de parties : $total\n";
    echo "Taux de victoire : $taux %\n";

    $mainGagnante = array_keys($gainsParMain, max($gainsParMain))[0];
    echo "Main la plus gagnante : $mainGagnante\n";

    echo "\nDétail des victoires par main :\n";
    foreach ($mains as $main => $nb) {
        $tauxMain = $nb > 0 ? round(($gainsParMain[$main] / $nb) * 100, 2) : 0;
        echo "- $main : $tauxMain % de victoires ($gainsParMain[$main]/$nb)\n";
    }

    echo "\nAppuyez sur Entrée pour retourner au menu.";
    fgets(STDIN);
}