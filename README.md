# FROQ Pimcore | Setting up local environment

A FROQ Pimcore application  
Developed by the Youwe Pimcore team.

## Getting the files
```bash
git clone ssh://git@source.youwe.nl:7999/froq/froq-pimcore.git
cd froq-pimcore
```

## Dev Configuration

### env.local
Create the `env.local` file
```yaml
###> symfony/framework-bundle ###
APP_ENV=dev
APP_DEBUG=true
###< symfony/framework-bundle ###

###> pimcore/pimcore ###
PIMCORE_DEV_MODE=true
###< pimcore/pimcore ###

#General
FROQ_DB_NAME=pimcore
FROQ_DB_USER=pimcore
FROQ_DB_PWD=pimcore
FROQ_DB_HOST=ddev-froq-pimcore-db
FROQ_DB_PORT=3306
FROQ_DB_VERSION='10.7.3-MariaDB-1:10.7.3+maria~focal'
```

To make sure that the host's value is correct:

1. Run ``docker ps``
2. Verify if the db container name is equal to "ddev-froq-pimcore-db". Otherwise, update the host's value in doctrine.yaml to make it similar to the db's container name.

## DDEV Setup

1. You *need* to have [`ddev`](https://ddev.readthedocs.io/en/stable/#installation) installed to run this stack.
2. Run `ddev start`.
3. Froq Pimcore project is now available at the URLs described by the ddev CLI output.
4. Optional: If you need to import the DB you can download the fresh dump from the server and use `ddev import-db` command to import it

## Docker Setup

### Prerequisites

* You must have docker-compose installed.
* You must have Git installed.

### Start the containers
Run `docker-compose up -d`

-----------------------------------------------------------------

# Froq UI

## Requirements

NodeJS >= v14

## Local installation

run this command
```shell
npm install
```

## Compilation

**For development and watching the changes:**

```shell
npm run watch
```

**For dev env:**
- This command mixes all the js and css files without minimizing them.

```shell
npm run dev
```

**For prod env:**
- This command mixes all the js and css files and minimize them.

```shell
npm run prod
```

Create index:
```bash
bin/console elasticsearch:synchronous-create-colour-guideline-index
bin/console youwe:pimcore-elasticsearch:populate

bin/console pimcore:deployment:classes-rebuild --create-classes
bin/console doctrine:migrations:migrate
```