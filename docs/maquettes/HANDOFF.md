# <Nom du projet> — <Client>

Passation des maquettes UX vers le développement. Ce dossier est la référence visuelle et fonctionnelle validée le <date> (<réunion de cadrage, validation client…>).

## Contenu du dossier

| Chemin | Rôle |
|---|---|
| `maquettes/*.html` | Maquettes statiques, une par écran. S'ouvrent directement dans un navigateur. |
| `captures/NN-*.png` | Capture de chaque écran, numérotée dans l'ordre du parcours. À consulter en priorité pour la mise en page. |
| `tokens.css` | Couleurs, statuts, rayons, police : variables CSS à reprendre telles quelles dans le thème. |

Les maquettes sont des **références**, pas du code à copier : elles utilisent des styles en ligne et des données fictives. Le rendu final reproduit la mise en page, les libellés et les couleurs, en s'appuyant sur `tokens.css`.

## Stack

- WordPress (wp-cubi), framework **Coretik**, ACF + ACF Extended pour la saisie en back-office.
- Services tiers : <paiement, emailing, API…>.

## Écrans

| Capture | Écran | Accès | Notes |
|---|---|---|---|
| `01-…` | | public / connecté / admin | |

## Règles métier

<!-- Une règle par puce, formulée pour être testée : « Un X ne peut pas Y quand Z ». Ce sont elles que les tests
     d'intégration vérifient. -->

## Points ouverts

<!-- Ce qui attend une réponse du client : à vider au fil du projet. -->

## Planning

<!-- Jalons et ouvertures. -->
