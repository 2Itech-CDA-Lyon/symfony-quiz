# M2i Quiz

## Routes

| Méthode HTTP | Nom | Chemin | Contrôleur | Méthode | Description |
|---|---|---|---|---|---|
| `GET` | home | `/` | `MainController` | `home` | Page d'accueil |
| `GET` | quiz_list | `/quiz` | `QuizController` | `list` | Liste de tous les quiz disponibles auxquels on peut jouer |
| `GET` | quiz_single | `/quiz/{id}` | `QuizController` | `single` | Détails d'un quiz / bouton "jouer" |
| `GET` | question_single | `/question/{id}` | `QuestionController` | `single` | Jouer à un quiz / répondre à une question |
| `GET` | quiz_create | `/quiz/create` | `QuizController` | `create` | Liste des quiz que l'on peut modifier / ajouter un nouveau quiz |
| `GET,POST` | quiz_new | `/quiz/{id}/edit` | `QuizController` | `edit` | Créer un nouveau quiz |
| `GET,POST` | quiz_edit | `/quiz/{id}/edit` | `QuizController` | `edit` | Modifier / supprimer un quiz déjà existant / Liste des questions dans le quiz / Ajouter des questions dans le quiz / Réordonner les questions dans le quiz |
| `POST` | quiz_delete | `/quiz/{id}/delete` | `QuizController` | `delete` | Action de suppression d'un quiz |
| `GET,POST` | question_new | `/question/new/{quiz_id}` | `QuestionController` | `new` | Créer une nouvelle question |
| `GET,POST` | question_edit | `/question/{id}/edit` | `QuestionController` | `edit` | Modifier / supprimer une question déjà existante / Liste des réponses dans la question / Ajouter des réponses à la question / Supprimer des réponses de la question |
| `POST` | question_delete | `/question/{id}/delete` | `QuestionController` | `delete` | Action de suppression d'une question |
| `GET,POST` | answer_new | `/answer/new/{question_id}` | `AnswerController` | `new` | Ajouter une réponse |
| `GET,POST` | answer_edit | `/answer/{id}/edit` | `AnswerController` | `edit` | Modifier une réponse |
| `POST` | answer_delete | `/answer/{id}/delete` | `AnswerController` | `delete` | Action de suppression d'une réponse |
