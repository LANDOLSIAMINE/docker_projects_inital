FROM drupal:10

RUN apt-get update && \
    apt-get install -y git 

RUN rm -rf *

RUN composer require drush/drush

# Clone your repository
RUN git clone  https://username:token@github.com/LANDOLSIAMINE/formalogistics.git .

WORKDIR /opt/drupal