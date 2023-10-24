FROM drupal:10

RUN apt-get update && \
    apt-get install -y git 

RUN rm -rf *

RUN composer require drush/drush

# Clone your repository
RUN git clone  https://yourusername:yourtoken@github.com/username/reponame.git .

WORKDIR /opt/drupal
