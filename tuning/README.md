# Vorbereitung der Tuningdaten

Die für das Tuning benötigten Daten werden durch das script `./tuning/data-prep.js` generiert.

**Benutzung:**

1. `cd /tuning`
2. `.env` Anlegen und folgende Variablen füllen:
   1. `OPENAI_API_KEY`
3. `npm install`
4. Starten mit: `node data-prep.js`
5. Name der erstellten JSONL Datei in `./tuning/tuning.js` eintragen (TRAINING_FILE). Die Funktionen im nächsten Ablauf müssen aus und einkommentiert werden. Es ist nicht sinnvoll erschienen, einen zusätzlichen Aufwand für eine CLI Abfrage zu betreiben.
   1. `uploadToOpenAI()` ausführen.
   2. `TRAINING_FILE_ID` eintragen (kommt mit dem Ergebnis aus `uploadToOpenAI()`).
   3. `initTraining()` ausführen.
   4. `getTrainingStatus()` ausführen um den Status zu verfolgen.
6. Nach erfolgreichem Training wird der Initiator per E-Mail benachrichtigt

## Tuningdaten

Die genutzten Trainingsdaten sind in `./tuning/trainind-data` abgelegt. Die Unterordner sind nach der Anzahl der Menge an Beispielen bennant. Die tatsächliche Anzahl der Beispiele ist geringer, da es vorkommen kann, dass Produkte in der URL-Liste nicht mehr vorhanden waren zum Zeitpunkt des Scrapings.
