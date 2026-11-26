# Contribuer

## Branches et versions

```
feat/xxx ──MR──▶ develop ──release:start──▶ release_X.Y.Z ──release:finish──▶ master + tag X.Y.Z
```

- **`master`** (protégée) : uniquement les versions livrées, un merge et un tag par version.
- **`develop`** : la prochaine version, uniquement des fonctionnalités terminées, par merge request.
- **`feat/…`, `fix/…`** : un changement, depuis `develop`.
- **`release_X.Y.Z`** : version en recette, déployée en préproduction (`vendor/bin/robo deploy staging release_X.Y.Z`).
  Les corrections de recette se font dans cette branche.
- **`hotfix_X.Y.Z`** : correction urgente de la production, depuis `master`.

```bash
vendor/bin/robo release:preview   # prochaine version et son CHANGELOG, sans rien modifier
vendor/bin/robo release:start     # crée et pousse release_X.Y.Z (version calculée depuis les commits)
vendor/bin/robo release:finish    # CHANGELOG.md, merge dans master, tag X.Y.Z, merge dans develop, push
vendor/bin/robo deploy production X.Y.Z
vendor/bin/robo hotfix:start / hotfix:finish
```

La fermeture de la release fige la version : le tag contient le `CHANGELOG.md` de la version et son message reprend
les changements (`git show X.Y.Z`).

### Messages de commit

[Conventional Commits](https://www.conventionalcommits.org/fr/) : `type(scope): sujet`.

| Type | Usage | Version |
| --- | --- | --- |
| `feat` | nouvelle fonctionnalité | mineure |
| `fix`, `perf` | correction, performance | patch |
| `refactor`, `docs`, `style`, `test`, `build`, `ci`, `chore`, `revert` | technique | patch |
| `type!:` ou pied `BREAKING CHANGE: …` | rupture de compatibilité | majeure (mineure tant que la version est `0.x`) |

Le hook `commit-msg` (installé par `composer install`) refuse les autres messages, et la CI les vérifie sur chaque merge
request (`vendor/bin/robo commits:check`).

### Protection des branches (GitLab › Settings › Repository)

- `master` : merge et push **Maintainers**, force push désactivé.
- `develop` : merge **Developers + Maintainers**, push **Maintainers**, force push désactivé.
- Tags protégés `*` : création **Maintainers**.

## Tests

```bash
composer test                              # unitaires : pas de WordPress, pas de base
composer test:db                           # conteneur MySQL de test (tmpfs, port 3307) + base wp_cubi_tests
composer test:integration                  # WordPress + thème + plugins, base vidée à chaque run
composer analyse                           # PHPStan
composer lint                              # PHPCS
```

- Le conteneur `coretik-tests-mysql` garde ses données en mémoire : après un redémarrage de Docker, relancer
  `composer test:db`. Plusieurs suites en parallèle : une base chacune (`WP_TESTS_DB_NAME=… composer test:db`).
- Un run bloqué sur « Installing… » vient d'une connexion restée ouverte par un run interrompu :
  `SELECT ID, STATE, INFO FROM information_schema.PROCESSLIST WHERE DB = 'wp_cubi_tests'`, puis `KILL <id>`.
- Chaque changement est livré avec ses tests ; la CI (`.gitlab-ci.yml`) lance tout sur chaque merge request.

## Production

- **WP-Cron** est lancé par la crontab du serveur (`DISABLE_WP_CRON` en préproduction et en production), chaque minute :
  `* * * * * /usr/bin/php8.4 /var/www/<projet>/web/wpcb/wp-cron.php > /dev/null 2>&1`. Sans elle, la file d'attente de
  Coretik (envois d'emails en masse…) et les tâches planifiées ne tournent pas.
- **Sentry** : `SENTRY_DSN` dans `config/environments/production.php` et `staging.php`.
- Premier déploiement : `vendor/bin/robo deploy:setup <env>`, `vendor/bin/robo deploy <env> <version>`, puis
  `deploy:seed` (proposé automatiquement sur une base vide).
