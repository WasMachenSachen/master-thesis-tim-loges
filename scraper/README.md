# URL scraper

Für Tests als auch Tuning werden die Daten aus dem Vindor Online Shop benötigt.

## Benutzung Scraper

1. URL Array füllen in ./urls.js
2. `npm install`
3. `node scraper.js`
4. Die Ergebnisse werden als CSV in `./scraped-data/` abgelegt.

## Benutzung Generator

Der Generator erwartet einen Dateinamen einer CSV als Argument und stellt daraufhin anfragen an die LLM-APIs. Die Datei muss in `./scraped-data` liegen. Die Ergebnisse werden in `./results` abgelegt.

1. `npm install`
2. `node generator.js FILE_NAME`
