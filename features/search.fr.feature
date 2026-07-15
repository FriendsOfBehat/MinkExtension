# language: fr
Fonctionnalité: Recherche
  Afin de voir la définition d'un mot
  En tant qu'utilisateur du site
  Je dois pouvoir rechercher un mot

  Scénario: Recherche d'une page qui existe
    Etant donné que je suis sur "/wiki/Main_Page"
    Quand je remplis "search" avec "Behavior Driven Development"
    Et je presse "Search"
    Alors je devrais voir "agile software development"

  Scénario: Recherche d'une page qui n'existe pas
    Etant donné que je suis sur "/wiki/Main_Page"
    Quand je remplis "search" avec "Glory Driven Development"
    Et je presse "Search"
    Alors je devrais voir "Search results"
