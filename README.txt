SmartSafe API – Mini-Projekt

Dieses Projekt ist eine kleine REST-API in PHP zur Verwaltung von SmartSafes.

Technik:
- PHP 8 (Laragon)
- Microsoft SQL Server (PDO sqlsrv)
- REST API (JSON)
- Getestet mit Postman

Aktueller Entwicklungsstand:
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

