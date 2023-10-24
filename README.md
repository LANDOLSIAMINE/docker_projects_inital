# Docker Container Drupal 10 

## Installation

Après avoir cloné le dépôt, vous devez créer deux dossiers : `src` et `backup`. Voici comment vous pouvez le faire :

1. Ouvrez un terminal.
2. Naviguez vers le répertoire du projet cloné. Par exemple, si votre projet est dans le répertoire `Documents`, vous pouvez y accéder en tapant `cd Documents/nom_du_projet`.
3. Créez le dossier `src` en tapant `mkdir src`.
4. Créez le dossier `backup` en tapant `mkdir backup`.

Maintenant, vous avez les dossiers `src` et `backup` dans votre répertoire de projet.

## Utilisation

1. Lancer la commande `docker compose build`
2. Lancer la commande `docker compose up -d`
3. Lancer le terminal de docker drupal : `docker exec -it <nom-du-docker>`
4. Dans le terminal du docker, Lancer la commande `git clone` de la repository du projet `/opt/drupal/`. 


## Contribution

Si vous souhaitez contribuer à ce projet, veuillez suivre les directives de contribution.

## Licence

Incluez ici des informations sur la licence.
