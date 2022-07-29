<?php
namespace Deployer;

require 'recipe/symfony.php';

// --------------------------
// modification des paramètres par défaut
// --------------------------

// on demande à inclure les packages de dev
set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');

// Paramètres de notre application
set("env_database", "mysql://yoann:guizmo@127.0.0.1:3306/oflix-simple?serverVersion=mariadb-10.3.34");


// ---------------------------------------------------------------------------
// Paramètres de connexion au serveur distant
// ---------------------------------------------------------------------------

// Nom du fichier contenant la clé SSH permettant de s'authentifier auprès
// du serveur distant (va chercher dans votre répertoire local ~/.ssh de
// l'utilisateur courant).
// Généralement requis pour se connecter à un serveur mais pas nécessaire
// pour se connecter à notre VM Kourou.
// set('ssh_key_filename', 'nom_du_fichier_contenant_la_cle_ssh.pem');

// Adresse du serveur distant (adresse IP ou DNS public)
// set('remote_server_url','adresse_ip_ou_dns_public_du_serveur');
set('remote_server_url','yoann-chaperon-server.eddi.cloud');

// Nom du compte utilisateur sur le serveur distant/
// C'est cet utilisateur qui exécutera les commandes distantes.
// set('remote_server_user','nom_utilisateur_distant');
set('remote_server_user','student');

// ---------------------------------------------------------------------------
// Paramètres de déploiement spécifiques à notre projet
// ---------------------------------------------------------------------------

// Répertoire cible (sur le serveur distant) où le code source sera déployé
// => le répertoire sera créé s'il n'existe pas
set('remote_server_target_repository', '/var/www/html/obaby');

// Adresse du dépôt Github contenant le code source du projet 
set('repository', 'git@github.com:O-clock-Curie/projet-03-obaby.git');

// Nom de la branche à déployer
set('repository_target_branch', 'Develop');

// ---------------------------------------------------------------------------
// Autres paramètres concernant le déploiement
// ---------------------------------------------------------------------------

// [Optional]
// Ce paramètre permet d'avoir le retour de la commande "git clone"
set('git_tty', true); 

// On ne veut pas envoyer de statistiques à Deployer.org (même de façon anonyme)
set('allow_anonymous_stats', false);

// Nombre de releases à conserver (5 par défaut, -1 pour illimité)
set('keep_releases', 3);

// ---------------------------------------------------------------------------
// Définition des paramètres de déploiement pour le serveur de 'production'
// ---------------------------------------------------------------------------

host('prod')
    // On précise l'adresse du serveur distant.
    // Les doubles accolades {{my_parameter}} permettent de récupérer
    // la valeur d'un paramètre défini avec set('my_parameter','my_value');
    ->set('hostname', '{{remote_server_url}}')
    // Précise le chemin absolu (sur la machine distante) du répertoire
    // dans lequel le code sera déployé.
    // par exemple : /var/www/html/mywebsite
    ->set('deploy_path', '{{remote_server_target_repository}}')
    // Si la branche n'est pas spécifiée, Deployer utilise le nom de la branche
    // actuelle du dépôt Git local dans lequel on se trouve.
    ->set('branch', '{{repository_target_branch}}')
    // Précise le nom de l'utilisateur (sur la machine distante) qui sera utilisé
    // pour établir la connexion SSH et exécuter les commandes.
    ->set('remote_user', '{{remote_server_user}}');
    // Chemin du fichier (sur votre machine locale) contenant la clé SSH permettant
    // d'établir la connexion SSH.
    // Généralement requis pour se connecter à un serveur mais pas nécessaire
    // pour se connecter à notre VM Kourou.
    // ->set('identity_file','~/.ssh/{{ssh_key_filename}}')

// ---------------------------------------------------------------------------
// Définition des tâches (tasks)
// ---------------------------------------------------------------------------

desc('Création de la base de données');
task('init:database', function() {
    run('{{bin/console}} doctrine:database:create');
});

desc('Supression base de données');
task('init:database:drop', function() {
    run('{{bin/console}} doctrine:database:drop --if-exists --no-interaction --force');
});

desc('mise à jour des posters des films');
task('kustom:command:poster', function(){
    run('{{bin/console}} omdb:fetch:poster');
});

desc('Deploy project');
task('first_deploy', [
    // Affiche le nom de la branche déployée
    'deploy:info',

    // Préparation du serveur distant.
    'deploy:setup',
    
    // Ajoute un fichier deploy.lock dans le répertoire .dep
    // pour indiquer qu'un déploiement est en cours. C'est juste
    // un indicateur qui n'impacte aucunement le fonctionnement du site.
    'deploy:lock', 

    // Ecrit les informations dans .dep/releases pour associer un identifiant
    // au déploiement en cours.
    // Nettoie également la dernière release si elle a échouée
    'deploy:release',

    // Détermine et clone la branche du dépôt Git contenant le code source
    // à déployer.
    'deploy:update_code',

    // Modifie le lien symbolique 'current' pour qu'il pointe vers la nouvelle
    // release.
    // C'est à ce moment précis, que notre site bascule sur la nouvelle version du code,
    // cela se fait en un clin d'oeil et c'est grâce à ça qu'il est inutile de passer
    // le site en mode maintenance.
    'deploy:symlink',

    // Supprime le fichier deploy.lock, le déploiement du code est terminé
    // on peut enlever cet indicateur.
    'deploy:unlock',

    // Si la paramètre 'keep_releases' a été défini,
    // on ne garde que les N dernières releases, dans notre cas, les 3 dernières.
    // Les répertoires des releases trop anciennes situées dans
    // 'releases/' sont supprimés mais le fichier .dep/releases
    // conserve les références à ces anciennes releases.
    'deploy:cleanup',

    // Si on arrive jusque là, on affiche un message de succès du déploiement.
    'deploy:success'
]);

// Facultatif, en cas d'échec du déploiement on force la suppression
// du fichier 'deploy.lock' présent dans le répertoire '.dep' qui sert
// d'indicateur de 'déploiement en cours'
after('deploy:failed', 'deploy:unlock');