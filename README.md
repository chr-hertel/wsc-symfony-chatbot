# Web Summer Camp Symfony Chatbot

Simple Symfony based Chat Bot UI on top of OpenAI's GPT.
It was build as demo for a workshop at [Web Summer Camp 2024](https://websummercamp.com/2024/workshop/custom-chatbots-with-gpt-and-symfony-php-framework).

Before we start, let's have a brief look at the technologies, setup and basic architecture of this demo.

## Requirements

What you need to run this demo:

* Internet Connection
* Terminal & Browser
* [Git](https://git-scm.com/) & [GitHub Account](https://github.com)
* [Docker](https://www.docker.com/) with [Docker Compose Plugin](https://docs.docker.com/compose/)
* Your Favorite IDE or Editor

## Technology

This small demo sits on top of following technologies:

* [PHP >= 8.3](https://www.php.net/releases/8.2/en.php)
* [Symfony 7.1 incl. Twig, Asset Mapper & UX](https://symfony.com/)
* [Bootstrap 5](https://getbootstrap.com/docs/5.0/getting-started/introduction/)
* [OpenAI's GPT & Embeddings](https://platform.openai.com/docs/overview)
* [ChromaDB Vector Store](https://www.trychroma.com/)

## Setup

The setup is split into two parts, the Symfony application and the OpenAI configuration.

### 1. Symfony App

Checkout the repository, start the docker environment and install dependencies:
```shell
git clone git@github.com:chr-hertel/wsc-symfony-chatbot.git
cd wsc-symfony-chatbot
docker compose up -d
docker compose exec app composer install
```

Now you should be able to open https://localhost:8080 in your browser,
and the chatbot UI should be available for you to start chatting.

### 2. OpenAI Configuration

For using GPT and embedding models from OpenAI, you need to configure the `OPENAI_API_KEY` env variable.

This is done by copying the provided `dev.decrypt.private.php` file into `config/secrets/dev/` directory.

Verify the success of this step by running the following command:
```shell
docker compose exec app bin/console secrets:list [--reveal]
```

You should see the `OPENAI_API_KEY` in the list of secrets.

**Don't forget to set up the project in your favorite IDE or editor.** 

## Functionality

* The chatbot application is a simple and small Symfony 7.1 application.
* The UI is coupled to a Twig LiveComponent, that integrates a `Chat` implementation on top of the user's session.
* You can reset the chat context by hitting the `Reset` button in the top right corner.
* As part of this workshop, we will connect the `Chat` with GPT, a vector store and other tools.

**The challenges of this workshop are documented in the [CHALLENGES.md](CHALLENGES.md) file.**

## Helpers

This repository comes with some tools for quality assurance installed, and a small wrapper script.

### Execute all quality checks at once

```shell
bin/check
```

### Composer

```shell
docker compose exec app composer install
docker compose exec app composer validate
```

### PHP CS Fixer

```shell
docker compose exec app vendor/bin/php-cs-fixer fix
```

### PHPStan

```shell
docker compose exec app vendor/bin/phpstan analyse
```

### PHPUnit

```shell
docker compose exec app vendor/bin/phpunit
```
