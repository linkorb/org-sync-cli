# OrgSync CLI
> Provides users synchronisation between your targets

Basically it's Symfony command line app which wraps [OrgSync](https://github.com/linkorb/org-sync) library

## Installation

Docker:

```sh
docker run -t -i -v=${PWD}:/opt -u $(id -u):$(id -g) --name org-sync-cli composer:latest bash
cd /opt/ && composer install
```

## Usage

There are two commands to perform synchronization between your user's data:
* `linkorb:organization:sync` will perform full synchronization itself (see `bin/console linkorb:organization:sync --help` for details)
* `linkorb:user:set-password` will set password for particular passed username (see `bin/console linkorb:user:set-password --help` for details)

### Examples
Sync organization:

`./bin/console linkorb:organization:sync --organization organization.yaml --targets targets.yaml`

Update password:

`./bin/console linkorb:user:set-password --organization organization.yaml --targets targets.yaml jdoe`
