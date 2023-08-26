# Shopware Beispiel Store

Um das Plugin zu testen wird hier eine Beispiel Shopware Installation zur Verfügung gestellt.

## Benutzung

```
docker compose up -d
```

## Troubleshoting Benutzung

### Frontend nicht verfügbar

Es wird automatisch der integrierte watcher von docware gestartet, sollte das Frontend nicht erreichbar sein muss dieser manuell gestartet werden:

1. Mit Container verbinden `docker exec -it <CONTAINER_ID> /bin/sh`
2. Watcher starten: `cd /var/www && make watch-admin`

### Scripte können nicht ausgeführt werden

Rechte reparieren siehe [docware docs](https://docs.dockware.io/tips-and-tricks/how-to-use-bind-mounting#mac):
`docker exec -it shop bash -c 'sudo chown www-data:www-data /var/www/html -R'`
