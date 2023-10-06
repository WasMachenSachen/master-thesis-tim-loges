# Begleit Repository zur Masterabeit "Konzeption und Entwicklung einer automatisierten Lösung zur Generierung von Beschreibungstexten für Wein im E-Commerce Bereich auf Basis strukturierter Produktdaten"

## Benutzung plugin

Um das Plugin zu testen wird hier eine Beispiel Shopware Installation zur Verfügung gestellt. Diese basiert auf der Entwicklungsumgebung [Dockware](https://docs.dockware.io/).

Der Quellcode des Plugins ist in `./shopware-plugin/AiDescription`. Dieser wird automatisch in den Shopware Development Store injected.

Für die erste Benutzung muss folgender Ablauf durchlaufen werden:

1. Starten des Docker-Containers: `docker-compose up`
2. Mit der Docker-Shell verbinden: `docker exec -it CONTAINER_ID /bin/bash` (`docker ps` für eine Auflistung der Docker-Container)
3. `cd /var/www/html`
4. Installieren und Aktivieren des Plugins: `php bin/console plugin:install --activate AiDescription`
5. Beispiel Produkte einpflegen: `mysql -u root -p shopware < /var/www/html/mysqldata/example-store.dump` (Passwort: root)
6. Shopware Cache Löschen: `php bin/console cache:clear`
7. Login in der Shopware Administration unter `http://localhost:8888/#/login`. Login: admin // Passwort: shopware(Beim ersten Start muss auf das Kompilieren der Plugin-Dateien gewartete werden)
8. OpenAI API Key in der Plugin Konfiguration eintragen: `http://localhost:8888/#/sw/extension/config/AiDescription`

> Der OpenAI API-Key muss für das Modell GPT-4 freigeschaltet sein!

Beispielprodukte sind nach dem Import vorhanden, eine Generierung kann auf der Produkt-Einzelseite gestartet werden: `http://localhost:8888/#/sw/product/detail/adfccca13f3b4c0a82739ad8966adf76/ai`. Die Beispieldaten der SQL-Dump für die MySQL Datenbank liegen in `./shopware-example-store-test-data`.

### Frontend nicht verfügbar

Es sollte automatisch der integrierte watcher von dockware gestartet werden, sollte das Frontend nicht erreichbar sein muss dieser manuell gestartet werden:

1. Mit der Docker-Shell verbinden: `docker exec -it CONATINER_NAME /bin/bash` (`docker ps` für eine Auflistung der Docker-Container)
2. Watcher starten: `cd /var/www && make watch-admin`

### Scripte können nicht ausgeführt werden

Rechte reparieren siehe [docware docs](https://docs.dockware.io/tips-and-tricks/how-to-use-bind-mounting#mac):
`docker exec -it shop bash -c 'sudo chown www-data:www-data /var/www/html -R'`

## Scraper

Die Scripte für das Scraping sind in `./scraper` abgelegt,

## Tuning

Die Scripte für das Tuning sind in `./tuning` abgelegt.
