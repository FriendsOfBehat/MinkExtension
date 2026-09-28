# language: de
Funktionalität: Translated steps recognize a quote escaped with a backslash
  As a developer writing scenarios in German
  I want translated step definitions to be found
  even when an argument contains an escaped quote

  Szenario: Argument without an escaped quote
    Angenommen ich bin auf "/wiki/Main_Page"
    Wenn ich gebe in das Feld "search" "Behavior Driven Development" ein
    Und ich drücke "Search"
    Dann ich sollte "agile software development" sehen

  Szenario: Argument with an escaped quote
    Angenommen ich bin auf "/wiki/Main_Page"
    Wenn ich gebe in das Feld "search" "Bruce sagt \"Ich bin Batman\"" ein
    Und ich drücke "Search"
    Dann ich sollte "Search results" sehen
