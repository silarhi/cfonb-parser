# 6.5

- CFONB 120: an amount with 0 decimals (position 20) is now parsed as an integer instead of a garbage value
- CFONB 240: a `34` or `39` record after a `39` total and before the next `31` header now throws a `ParseException` instead of being attached to the closed transfer
- CFONB 240: a `34` or `39` record before any `31` header now reports the missing header
- An out-of-range date (e.g. day 32 or month 13) now throws a `ParseException` instead of overflowing into the next month
- Support `\r` (classic Mac OS) line endings

# 6.0

Drop support for php 8.1

# 5.0

Add a strict mode parameter

# 4.0

Bump minimal php version to 8.1

# 3.0

Bump minimal php version to 8.0
