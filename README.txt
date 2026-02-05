SmartSafe API – Mini-Projekt

Dieses Projekt ist eine kleine REST-API in PHP zur Verwaltung von SmartSafes.

Technik:
- PHP 8 (Laragon)
- Microsoft SQL Server (PDO sqlsrv)
- REST API (JSON)
- Getestet mit Postman

Aktueller Entwicklungsstand:

- Neuer Stand -05-02-2026 (Repository-Pattern):

- Der direkte SQL-Zugriff wurde aus der index.php ausgelagert.
- Datenbankzugriffe erfolgen jetzt über ein eigenes Repository (SafeRepository).

- Vorteile:
- Trennung von HTTP-Logik und Datenbank-Logik
- Bessere Wartbarkeit
- Vorbereitung auf eine saubere MVC-Struktur
- Zentrale Stelle für SQL-Zugriffe

Die index.php kümmert sich nur noch um:
- Routing
- Validierung
- HTTP-Responses

- Health-Endpoint zum Prüfen, ob die API läuft
- POST /api/safes zum Anlegen eines SmartSafes
- Validierung der Eingabedaten
- Speicherung in einer MSSQL-Datenbank
- Schutz vor SQL-Injection durch Prepared Statements
- Fehlerbehandlung mit sinnvollen HTTP-Statuscodes
- Validierung aller Pflichtfelder
- Unterstützung von Geldbeträgen mit zwei Nachkommastellen (DECIMAL)
- UNIQUE-Constraint auf safe_code
- Rückgabe von HTTP 409 bei doppeltem safe_code
- Saubere JSON-Fehlermeldungen

Ziel:

Ein SmartSafe kann z.B. darstellen:
- einen Tresor
- einen Geldautomaten 
- oder ein Bargeldsystem in einer Filiale

Langfristig könnte das System:
- SmartSafes verwalten
- Bargeldbestände speichern
- Events (Einzahlung, Entnahme, Warnungen) verarbeiten

