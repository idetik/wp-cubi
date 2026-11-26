# Instructions du projet

- Les maquettes UX validées sont dans `docs/maquettes/`. Lire `docs/maquettes/HANDOFF.md` avant toute tâche front ou
  back-office, et respecter `docs/maquettes/tokens.css` pour les couleurs.
- Thème : `web/app/themes/starter` (framework Coretik : `coretik()->schema()`, modèles, requêtes dans `src/Queries`,
  jamais `get_posts()` / `WP_Query` en direct). Documentation Coretik : `vendor/idetik/coretik/README.md`.
- Chaque changement est livré avec ses tests : `composer test` (unitaires), `composer test:db && composer
  test:integration` (WordPress + MySQL), `composer analyse` (PHPStan), `composer lint` (PHPCS). Voir `CONTRIBUTING.md`.
- Commits en Conventional Commits (`feat(scope): …`, `fix: …`) : ils calculent la version et le CHANGELOG. Une branche
  par changement, merge request vers `develop`. Ne jamais pousser sur `master`.
