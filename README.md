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

Organization structure example:

```yaml
name: LinkORB
users:
  user1:
    email: user1.name@gmail.com
    displayName: Test User
    avatar: https://example.com/user.gif
    properties:
      githubId: user123
      skypeId: t.user
      firstName: Test
      lastName: User
  user2:
    email: user2@example.com
    properties:
      firstName: John
      lastName: Doe
  user3:
    email: user3@example.com
    properties:
      firstName: Billie
      lastName: Doe

groups:
  team:
    displayName: the whole team
  developers:
    parent: team
    displayName: Developers
    avatar: https://example.com/devs.png
    members:
      - user1
      - user2
    properties:
      hello: world
    targets:
      - camunda1
```

Targets list example:
```yaml
camunda1:
    type: 'camunda'
    baseUrl: 'http://172.17.0.1:8080/engine-rest/'
    adminUsername: 'root'
    adminPassword: 'root'
camunda2:
    type: 'camunda'
    baseUrl: 'http://172.17.0.1:8081/engine-rest/'
```
