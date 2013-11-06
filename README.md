Installation
============
* Copier les fichiers sur un serveur PHP/Mysql
* Renommer le fichier admin2/include/connect.default.php en connect.php et l'éditer pour refléter les informations de connexions à votre base de données
* Importer la structure de la base de données sur votre serveur à partir du fichier db_planning.sql
* Ajouter un utilisateur dans la table utilisateurs (le mot de passe doit être codé en MD5)

Utilisation
===========
Une fois l'outil installée, la configuration des sections et horaires se fait dans la base de données directement.

La création de nouveaux utilisateurs et la gestion des plages de travail se fait directement depuis l'interface.

Démo
====
Une version démo de l'outil est disponible à l'adresse suivante : [http://www.geobib.fr/demo/planning](http://www.geobib.fr/demo/planning "")

Administration complète : 
* Login : admin
* Mot de passe : admin

Administration light (pas d'accès aux statistiques) :
* Login : pro
* Mot de passe : pro


Capture d'écran
===============
![Capture d'écran](https://github.com/symac/planning/blob/master/img/capture.png?raw=true "Capture d'écran")

Crédits
=======
Ce logiciel a été développé : 
* Par Alban Espeut (mairie de Perpignan) pour la version originale
* Par Sylvain Machefert (université Bordeaux 3) pour les adaptations au contexte de la bibliothèque

L'utilisation de ce logiciel est libre pour un usage non commercial.