# Genearecords

Genearecords is a self-hosted searchable database of records, mostly for genealogy purposes.

It is still very early, and not recommended for production use as database can change anytime.

## Features

- Import CSV (match database fields and add custom fields)
- Basic search (surname, given names, location and years, exact search only)
- Basic dark mode

## Upcoming features

- [P1] Location add map (geolocation with Nominatim + display with Leaflet)
- [P1] Search - Implement direct query search (without Symfony forms) and paginate results
- [P1] Search results - Add search form as a sidebar
- [P2] Allow user with specific role (“can list records”) to view all records from a specific place in Explore
- [P2] User management, with roles (such as “can contribute”, or “can explore databases”)
- [P2] Make the theme responsive on mobile (navbar)
- [P3] Advanced search (fuzzy with Levenshtein, weighted score according to the relationship to the record)
- [P3] Internationalization (implemented, but needs to be completed)
- [P4] Auto-guess fields in CSV import
- [P5] Customizable pages
- [P6] Find similar names (find Levenshtein distance 1 or 2 of a given name)
- [P7] Registration
- [P8] Export results as CSV (for users with role “can export”)

The ultimate goal would be to have a backend running Elasticsearch to improve fuzzy search speed. Examples of successful implementations include MatchID which could be an inspiration.

Currently, it is not in the works, because:
- I have no knowledge of Elasticsearch, so it means a lot of free time to dedicate learning it
- It requires a new infrastructure that would probably be more costly

However, if someone with knowledge of Elasticsearch wants to step in and help this project, please reach out.

## Installation

### Requirements

- PHP 8.1+
- PostgreSQL. Technically, it should be possible to use MariaDB/MySQL or SQLite, but on a sample test, it showed 10x better performance with PostgreSQL than MariaDB. Additionally, when Levenshtein search will be implemented, it will be only with PostgreSQL.

Please increase the default memory limit of `php.ini`, such as:
```
memory_limit = 512M
```

If you have very big files, it might be necessary to increase even more this value, or split your CSV files into smaller files.