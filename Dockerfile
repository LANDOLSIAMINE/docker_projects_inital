FROM drupal:10

RUN apt-get update && \
    apt-get install -y git 

RUN rm -rf *

RUN composer require drush/drush

# Clone your repository
RUN git clone  https://LANDOLSIAMINE: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@github.com/LANDOLSIAMINE/formalogistics.git .

WORKDIR /opt/drupal