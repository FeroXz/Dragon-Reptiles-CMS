# Dragon Reptiles CMS

Ein leichtgewichtiges, zukunftsorientiertes CMS für Wissensaufbereitung rund um Reptilienarten wie *Pogona vitticeps*, *Heloderma suspectum* und viele mehr. Das System kombiniert ein modernes Headless-inspiriertes UI mit einer schnellen SQLite-Datenbasis und legt den Fokus auf Erweiterbarkeit für Haltungsguides, Tierbestandsverwaltung, Genetik-Tools und weitere kommende Module.

## Features

- 🚀 **Moderne Administrationsoberfläche** auf Tailwind-Basis mit Darkmode-Design.
- 🧭 **Flexible Seitengestaltung** inkl. Startseite, individuellen Seiten und frei editierbarem HTML-Inhalt.
- 📰 **News-/Blog-System** mit Entwurfs- und Veröffentlichungsfunktion.
- 👥 **Nutzerverwaltung** für Administratoren und Redakteure mit sicherer Passwort-Hashing-Strategie (Argon2id).
- 🛠️ **Branding- & Layout-Settings** (Logo, Header, Footer, Sidebar, Menüstruktur) direkt im Adminbereich editierbar.
- 🪄 **Automatisches Setup** mit SQLite, inklusive Migrationen und Initialdaten.
- 📈 **Versionstracking**: Versionsnummer wird im Footer angezeigt und basiert auf der in der Datenbank gepflegten Release-Historie.

## Systemvoraussetzungen

- PHP 8.2 oder höher (entwickelt und getestet mit PHP 8.2+)
- SQLite3 (in PHP integriert)
- Optional: Eingebauter PHP-Webserver für lokale Entwicklung

## Installation & Start

1. Repository klonen oder entpacken.
2. Abhängigkeiten sind nicht erforderlich – es wird kein Composer benötigt.
3. Einen Webserver auf das `public/`-Verzeichnis zeigen lassen, z. B. lokal:
   ```bash
   php -S 0.0.0.0:8000 -t public
   ```
4. Beim ersten Aufruf wird automatisch die SQLite-Datenbank `storage/database.sqlite` erzeugt und migriert.

## Admin-Zugang

- URL: `/admin/login.php`
- Standard-Zugangsdaten:
  - **E-Mail:** `admin@example.com`
  - **Passwort:** `ChangeMe123!`
- Bitte nach dem ersten Login sofort über die Benutzerverwaltung ändern.

## Versionierung

Die Versionsnummer wird aus der Tabelle `version_history` ermittelt und im Footer angezeigt. Neue Releases sollten über die `VersionManager::record()`-Methode gepflegt werden (z. B. in neuen Migrationen oder Setup-Skripten), damit Erweiterungen/Fixes automatisch die Versionsnummer erhöhen.

## Tests

Nach Codeänderungen sollte folgendes Linting ausgeführt werden:

```bash
find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
```

## Feature-Checkliste

- [x] Administrationsbereich mit moderner UI und Login
- [x] Einstellungsmodul für Logo, Header, Footer, Sidebar und Menüstruktur
- [x] Seitenverwaltung inklusive Startseiten-Definition
- [x] News/Blog-Verwaltung mit Veröffentlichungsoption
- [x] Benutzerverwaltung mit Rollen (Admin, Editor)
- [ ] Module für Tierbestandsverwaltung
- [ ] Tierabgabe- & Tierprofil-Module
- [ ] Genetik-Rechner & Zuchtplanung
- [ ] Haltungsguides & Genetik-Guides

## Lizenz

Projektspezifische Lizenzierung – bitte bei Bedarf ergänzen.
