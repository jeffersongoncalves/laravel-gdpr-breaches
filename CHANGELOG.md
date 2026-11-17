# Changelog

All notable changes to this project will be documented in this file.

## 1.0.0 - 2026-11-17

First release: GDPR personal data breach register for Laravel, part of the laravel-gdpr suite.

- A 72-hour deadline to notify the supervisory authority, counted from awareness (Art. 33(1)). A late notice requires the reasons for the delay.
- Risk assessment: `unlikely` (documented only), `risk` (notify the authority) or `high` (also tell the data subjects, Art. 34(1)).
- Phased notification, which tracks the information still owed (Art. 33(4)).
- Exemptions from the data subject communication, with a recorded reason (Art. 34(3)).
- The `pending` and `overdue` scopes, the `BreachOpened` and `BreachOverdue` events, and `gdpr:breaches-overdue`.

## [Unreleased]
