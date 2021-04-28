# M2i Quiz

## Routes

| Méthode HTTP | Nom | Chemin | Contrôleur | Méthode | Description |
|---|---|---|---|---|---|
| `GET` | home | `/` | `MainController` | `home` | Page d'accueil |
| `GET` | quiz_list | `/quiz` | `QuizController` | `list` | Liste de tous les quiz disponibles auxquels on peut jouer |
| `GET` | quiz_single | `/quiz/{id}` | `QuizController` | `single` | Détails d'un quiz / bouton "jouer" |
| `GET` | question_single | `/question/{id}` | `QuestionController` | `single` | Jouer à un quiz / répondre à une question |
| `GET` | quiz_create | `/quiz/create` | `QuizController` | `create` | Liste des quiz que l'on peut modifier / ajouter un nouveau quiz |
| `GET` | quiz_edit | `/quiz/{id}/edit` | `QuizController` | `edit` | Modifier / supprimer un quiz déjà existant / Liste des questions dans le quiz / Ajouter des questions dans le quiz / Réordonner les questions dans le quiz |
| `GET` | question_edit | `/question/{id}/edit` | `QuestionController` | `edit` | Modifier / supprimer une question déjà existante / Liste des réponses dans la question / Ajouter des réponses à la question / Supprimer des réponses de la question |

