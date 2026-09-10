# Naturheilpraxis Svenja Michaelsen — Relaunch-Entwurf

Statische Website, reines HTML und CSS. Kein Build-Schritt, kein Framework,
keine Abhängigkeiten. Zum Bearbeiten genügt ein Texteditor.

## Struktur

```
index.html        Startseite
therapien.html    Alle 8 Behandlungsschwerpunkte auf einer Seite (mit Sprungmarken)
ueber-mich.html   Person, Arbeitsweise, Zusammenarbeit, Kosten, Links
kontakt.html      Kontaktdaten und Formular (ohne Funktion)
impressum.html    Impressum (Entwurf, Pflichtangaben teils offen)
datenschutz.html  Datenschutzerklaerung (Entwurf)
css/style.css     Gesamtes Layout
img/              Bilder
```

## Bewusste Entscheidungen

- **Keine externen Ressourcen.** Keine Google Fonts, kein CDN, kein Tracking.
  Die Seite laedt nichts von fremden Servern. Deshalb ist auch kein
  Cookie-Banner noetig.
- **Blaupalette.** Auf Wunsch der Praxis ausschliesslich Blautoene, von
  Marineblau (#16324f) bis zu sehr hellem Blaugrau (#eef3f8). Alle Farben
  liegen als CSS-Variablen im `:root`-Block von `css/style.css` - eine
  Farbanpassung braucht nur dort geaendert zu werden. Saemtliche
  Text-Hintergrund-Kombinationen erfuellen WCAG AA (mindestens 4.5:1).
- **Systemschriften.** Serifenschrift fuer Ueberschriften, Systemschrift fuer
  Fliesstext. Faellt sauber zurueck, wenn eine Schrift fehlt.
- **Kontaktformular ohne Funktion.** Die Felder sind `disabled`, es gibt kein
  `action`-Ziel. Nichts wird uebertragen oder gespeichert.
- **Zugaenglichkeit.** Sprungmarke zum Inhalt, sichtbarer Fokus, semantische
  Ueberschriftenhierarchie, Alt-Texte, `prefers-reduced-motion` beruecksichtigt.

## Offene Punkte

Siehe `RELAUNCH-NOTIZEN.md` im Projektordner.

## Lokal ansehen

```
python -m http.server 8080
```

Dann http://localhost:8080 im Browser oeffnen.
