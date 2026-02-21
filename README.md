# Time Zone Overlap

> Part of the [URLCV](https://urlcv.com) free tools suite — built for recruiters and remote teams.

**Live tool:** [urlcv.com/tools/timezone-overlap](https://urlcv.com/tools/timezone-overlap)

---

## What it does

Pick any two cities from a library of 85+ locations and instantly see a visual 24-hour overlap chart showing which working hours both cities share — fully DST-aware.

### Features

- **85+ cities** across every major timezone, searchable by city name, country, or IANA identifier
- **Visual timeline** — two colour-coded rows showing each city's working day on the same scale; overlap highlighted in primary colour
- **DST-correct** — uses the browser's `Intl` API with the IANA timezone database, so summer/winter clock changes are handled automatically
- **Date picker** — check overlap for any future or past date (useful for planning calls around DST transitions)
- **Configurable working hours** — defaults to 09:00–17:00, adjustable per use case
- **Swap cities** — flip the two selections with one click
- **Quick presets** — London ↔ New York, London ↔ Singapore, New York ↔ Tokyo, and more
- **Live clock** — current local time in each city, updated every 30 seconds
- **DST notice** — flags when the selected date is during Daylight Saving Time for either city

### Supported timezones include

| Region | Examples |
|--------|---------|
| Americas | Honolulu, Anchorage, Los Angeles, Denver, Chicago, New York, Toronto, São Paulo, Buenos Aires |
| Europe | London, Paris, Berlin, Amsterdam, Madrid, Rome, Stockholm, Moscow |
| Africa / Middle East | Cairo, Nairobi, Johannesburg, Dubai, Riyadh, Tel Aviv |
| Asia | Mumbai (+5:30), Kathmandu (+5:45), Singapore, Tokyo, Shanghai, Seoul |
| Oceania | Perth, Sydney, Melbourne, Auckland |

---

## Technical details

- **Type:** Frontend-only (Alpine.js) — no server round-trip, no data stored
- **DST handling:** `Intl.DateTimeFormat.prototype.formatToParts()` with IANA timezone identifiers — uses the browser's built-in tz database
- **Framework integration:** Laravel package with Blade view
- **Namespace:** `URLCV\TimezoneOverlap`
- **Service provider:** `URLCV\TimezoneOverlap\Laravel\TimezoneOverlapServiceProvider`

### How DST is handled

For each date, the tool calculates UTC offsets using `Intl.DateTimeFormat` at noon on that date. Because the browser's `Intl` implementation uses the full IANA timezone database (updated with browser/OS releases), clock changes for all countries — including unusual cases like half-hour offsets (India +5:30, Nepal +5:45) and reversed DST (Morocco) — are handled correctly.

---

## Installation (via the main URLCV app)

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/urlcv/timezone-overlap.git" }
],
"require": {
    "urlcv/timezone-overlap": "dev-main"
}
```

```bash
composer update urlcv/timezone-overlap
php artisan tools:sync
```

---

## Part of URLCV

[URLCV](https://urlcv.com) automates CV parsing, candidate scoring, and shortlist generation — so recruiters can place more candidates, faster.

[Start a free trial →](https://urlcv.com/register)
