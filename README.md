# Begleit Repository zur Masterabeit "Konzeption und Entwicklung einer automatisierten Lösung zur Generierung von Beschreibungstexten für Wein im E-Commerce Bereich auf Basis strukturierter Produktdaten"

docker-compose up -d

connect to container shell

## docware watcher

use this! next one not working. to lazy to debug

```bash
cd /var/www
make watch-admin
```

watcher preview is running at port 8888! not 8080

https://docs.dockware.io/development/watchers

## Shopware watcher

./bin/watch-administration.sh

## install and activae

```bash
php bin/console plugin:install AiDescription

php bin/console plugin:activate AiDescription
```

# Tuning

Die Scripte für das Tuning sind in `./tuning` abgelegt.

## Vorbereitung der Tuningdaten

Die für das Tuning benötigten Daten werden durch das script `./tuning/data-prep.js` generiert.

**Benutzung:**

1. `cd /tuning`
2. `.env` anlegen und folgende Variablen füllen:
   1. `OPENAI_API_KEY`
3. `npm install`
4. `node data-prep.js`
5. Name der erstellten JSONL Datei in `./tuning/tuning.js` eintragen (TRAINING_FILE). Die Funktionen im nächsten Ablauf müssen aus und einkommentiert werden. Es ist nicht sinnvoll erschienen, einen zusätzlichen Aufwand für eine CLI Abfrage zu betreiben.
   1. `uploadToOpenAI()` ausführen.
   2. `TRAINING_FILE_ID` eintragen (kommt mit dem Ergebnis aus `uploadToOpenAI()`).
   3. `initTraining()` ausführen.
   4. `getTrainingStatus()` ausführen um den Status zu verfolgen.
6. Nach erfolgreichem Training wird der Initiator per E-Mail benachrichtigt

### Tuningdaten

Die genutzten Trainingsdaten sind in `./tuning/trainind-data` abgelegt. Die Unterordner sind nach der Anzahl der Menge an Beispielen bennant. Die tatsächliche Anzahl der Beispiele ist geringer, da es vorkommen kann, dass Produkte in der URL-Liste nicht mehr vorhanden waren zum Zeitpunkt des Scrapings.
