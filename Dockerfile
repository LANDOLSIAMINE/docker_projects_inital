FROM drupal:10

RUN apt-get update && \
    apt-get install -y git && \
    php -r "readfile('http://files.drush.org/drush.phar');" > drush && \
    chmod +x drush && \
    mv drush /usr/local/bin

WORKDIR /opt/drupal