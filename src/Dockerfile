FROM python:3.11.1-slim-bullseye

RUN apt-get update -y
RUN apt-get install -y lsb-release ca-certificates apt-transport-https software-properties-common gnupg2 \
  curl ffmpeg libsm6 libxext6 python3-dev build-essential \
  git apt-transport-https ca-certificates wget \
  && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
  && sh -c 'echo "deb https://packages.sury.org/php/ bullseye main" > /etc/apt/sources.list.d/php.list' \
  && apt-get update \
  && apt-get install -y php8.1-bcmath php8.1-cli php8.1-curl php8.1-zip php8.1-mbstring php8.1-dom \
  && apt-get clean
RUN apt-get clean -y && update-ca-certificates -f

ENV GROUP_ID=1000 \
    USER_ID=1000

WORKDIR /app

ADD . /app



RUN php composer.phar install

RUN pip install --upgrade pip
RUN pip install setuptools
RUN pip install -r mltoolkit/requirements.txt
