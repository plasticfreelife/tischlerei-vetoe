/* Tischlerei Christian Vetö – Verhalten der Seiten. Alles funktioniert auch ohne dieses Skript. */
(function () {
  'use strict';
  var doc = document;

  /* Menü auf dem Telefon */
  var kopf = doc.querySelector('.kopf');
  var menueKnopf = doc.querySelector('.menue-knopf');
  if (kopf && menueKnopf) {
    menueKnopf.addEventListener('click', function () {
      var offen = kopf.classList.toggle('offen');
      menueKnopf.setAttribute('aria-expanded', offen ? 'true' : 'false');
      menueKnopf.textContent = offen ? 'Schließen' : 'Menü';
    });
  }

  /* Leistungsliste: das Bild rechts wechselt mit dem Bereich */
  var bereiche = doc.querySelector('[data-bereiche]');
  if (bereiche) {
    var zeigen = function (id, link) {
      bereiche.querySelectorAll('[data-id]').forEach(function (el) {
        el.classList.toggle('sichtbar', el.getAttribute('data-id') === id);
      });
      bereiche.querySelectorAll('.bereich').forEach(function (b) { b.classList.toggle('aktiv', b === link); });
    };
    bereiche.querySelectorAll('.bereich').forEach(function (link) {
      var id = link.getAttribute('data-bild');
      link.addEventListener('mouseenter', function () { zeigen(id, link); });
      link.addEventListener('focus', function () { zeigen(id, link); });
    });
  }

  /* Auftritt beim Hineinscrollen. Ohne Skript oder bei reduzierter Bewegung bleibt alles einfach sichtbar. */
  var ruhig = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var auftritte = doc.querySelectorAll('.auf');
  if (!ruhig && 'IntersectionObserver' in window) {
    var beobachter = new IntersectionObserver(function (eintraege) {
      eintraege.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('los'); beobachter.unobserve(e.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    auftritte.forEach(function (el) { beobachter.observe(el); });
  }

  /* Anfrage: freie Beschreibung; wer ein Hochbett sucht, bekommt den Hinweis auf Horst */
  var anfrage = doc.getElementById('anfrage-form');
  if (anfrage) {
    var horstHinweis = doc.querySelector('[data-anfrage-horst]');
    var istHochbett = function () { return /hochbett/i.test(anfrage.elements.vorhaben.value); };
    var pruefeHorst = function () { if (horstHinweis) horstHinweis.hidden = !istHochbett(); };
    anfrage.elements.vorhaben.addEventListener('input', pruefeHorst);
    pruefeHorst();

    var status = doc.getElementById('anfrage-status');
    var knopf = anfrage.querySelector('button[type="submit"]');
    var zeige = function (text, fehler) {
      status.textContent = text;
      status.classList.toggle('fehler', !!fehler);
      status.hidden = false;
      status.scrollIntoView({ block: 'center' });
    };
    try {
      var adresse = new URLSearchParams(location.search);
      if (adresse.get('gesendet')) zeige('Danke für Ihre Anfrage. Wir melden uns innerhalb von zwei Werktagen.', false);
      if (adresse.get('fehler')) zeige('Die Anfrage konnte nicht gesendet werden. Bitte schreiben Sie uns an office@tischler-vetoe.at.', true);
    } catch (e) { /* ältere Browser */ }

    /* Ohne PHP (z. B. in der Vorschau) wird die Anfrage als E-Mail vorbereitet */
    var alsEmail = function () {
      var f = anfrage.elements;
      var zeilen = [
        'Was soll entstehen: ' + f.vorhaben.value,
        'Maße: ' + (f.masse.value || 'noch offen'),
        'Ort: ' + (f.ort.value || '-'),
        'Zeitraum: ' + (f.zeitraum.value || 'noch offen'),
        'Rahmen: ' + (f.budget.value || 'im Gespräch klären'),
        '',
        f.nachricht.value,
        '',
        f.name.value,
        f.telefon.value ? 'Telefon: ' + f.telefon.value : '',
        'E-Mail: ' + f.email.value
      ];
      var betreff = 'Projektanfrage: ' + f.vorhaben.value + (f.ort.value ? ', ' + f.ort.value : '');
      var text = zeilen.join('\n');
      doc.getElementById('anfrage-text').value = 'An: office@tischler-vetoe.at\nBetreff: ' + betreff + '\n\n' + text;
      var meldung = doc.getElementById('anfrage-meldung');
      meldung.hidden = false;
      meldung.scrollIntoView({ block: 'nearest' });
      location.href = 'mailto:office@tischler-vetoe.at?subject=' + encodeURIComponent(betreff) + '&body=' + encodeURIComponent(text);
    };

    anfrage.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!anfrage.reportValidity()) return;
      var dateien = anfrage.elements['f-dateien'] ? anfrage.elements['f-dateien'].files : [];
      var summe = 0;
      if (dateien.length > 5) { zeige('Bitte höchstens 5 Dateien anhängen.', true); return; }
      for (var i = 0; i < dateien.length; i++) {
        if (dateien[i].size > 10 * 1024 * 1024) { zeige('Die Datei ' + dateien[i].name + ' ist größer als 10 MB.', true); return; }
        summe += dateien[i].size;
      }
      if (summe > 20 * 1024 * 1024) { zeige('Die Dateien sind zusammen größer als 20 MB.', true); return; }

      knopf.disabled = true;
      knopf.textContent = 'Wird gesendet …';
      fetch(anfrage.action, { method: 'POST', body: new FormData(anfrage), headers: { Accept: 'application/json' } })
        .then(function (antwort) {
          var typ = antwort.headers.get('Content-Type') || '';
          if (typ.indexOf('application/json') === -1) throw new Error('kein Formular-Server');
          return antwort.json();
        })
        .then(function (ergebnis) {
          zeige(ergebnis.text, !ergebnis.ok);
          if (ergebnis.ok) anfrage.reset();
        })
        .catch(function () { alsEmail(); })
        .then(function () { knopf.disabled = false; knopf.textContent = 'Anfrage absenden'; });
    });
    var kopieren = doc.getElementById('anfrage-kopieren');
    if (kopieren) kopieren.addEventListener('click', function () {
      var ausgabe = doc.getElementById('anfrage-text');
      var fertig = function () { kopieren.textContent = 'Kopiert'; };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(ausgabe.value).then(fertig, function () { ausgabe.select(); });
      } else { ausgabe.select(); }
    });
  }
})();
