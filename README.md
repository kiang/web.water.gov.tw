# Water Supply Information System

This project processes and converts water supply information data from the Taiwan Water Corporation's website.

## Features

- Fetches water supply interruption information
- Converts coordinates from Web Mercator (EPSG:3857) to WGS84 (EPSG:4326)
- Processes both polygon areas and point locations
- Organizes data by year and case number

## Directory Structure

```
.
├── docs/               # Processed and converted data
├── raw/               # Raw data from API
│   └── cases/        # Individual case files organized by year
└── scripts/          # Processing scripts
    ├── 01_fetch.php  # Data fetching script
    └── 02_convert.php # Coordinate conversion script
```

## Usage

1. Fetch data:
```bash
php scripts/01_fetch.php
```

2. Convert coordinates:
```bash
php scripts/02_convert.php
```

## Data Format

The system processes two main types of data:
1. Water supply interruption cases (polygon areas)
2. Water supply points (point locations)

All coordinates are converted from Web Mercator (EPSG:3857) to WGS84 (EPSG:4326) for compatibility with most mapping services.

## License

MIT License - see LICENSE file for details 