# itp-prototype

## Software
* xampp -> https://sourceforge.net/projects/xampp/files/latest/download?source=files
* git bash -> https://gitforwindows.org/
* composer -> https://getcomposer.org/download/
* Github desktop -> https://desktop.github.com/

## Installation/Setup für den Testbetrieb

1. Clone dieses Projekt
2. Führe ``composer install`` aus (git bash funzt hier gut)
3. Erstelle Nutzer in Datenbank mit Passwort
4. Öffne die Datei __.env__ und editiere die Datenbankverbindung: z.B. DATABASE_URL=mysql://Micha:123456@127.0.0.1:3306/itp
5. Führe ``php bin/console doctrine:database:create`` aus
6. Führe ``php bin/console doctrine:migrations:migrate`` aus
