/* Luma Agenda Public Display Renderer */
(function () {
  'use strict';

  var DEFAULT_THEME = {
    'la-bg': '#f8f4eb',
    'keynote-badge': '#c8b5ec', 'keynote-text': '#3c1875',
    'panel-badge': '#ffa69b',   'panel-text': '#891a0e',
    'fireside-badge': '#fcdb82','fireside-text': '#7a5000',
    'demo-badge': '#9acbf5',    'demo-text': '#1a3f7a',
    'workshop-badge': '#95dee3','workshop-text': '#0f5c63',
    'neutral-badge': '#dddcde', 'neutral-text': '#3d3b3f'
  };

  var TYPES = {
    keynote: 'KEYNOTE', panel: 'PANEL', fireside_chat: 'FIRESIDE CHAT',
    demo: 'DEMO', workshop: 'WORKSHOP', welcome: 'WELCOME', closing: 'CLOSING',
    announcement: 'ANNOUNCEMENT', registration: 'REGISTRATION', break: 'BREAK', lunch: 'LUNCH'
  };

  function el(tag, cls) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    return e;
  }

  function initials(n) {
    return (n || '?').trim().split(/\s+/).map(function (w) { return w[0] || ''; }).join('').slice(0, 2).toUpperCase();
  }

  function fmt(m) {
    var h = Math.floor(m / 60) % 24, mn = m % 60;
    var ap = h >= 12 ? 'PM' : 'AM', dh = h > 12 ? h - 12 : (h === 0 ? 12 : h);
    return dh + ':' + (mn < 10 ? '0' : '') + mn + ' ' + ap;
  }

  function avColors(type) {
    var map = {
      keynote:      ['var(--keynote-tint)',  'var(--keynote-text)'],
      panel:        ['var(--panel-tint)',    'var(--panel-text)'],
      fireside_chat:['var(--fireside-tint)', 'var(--fireside-text)'],
      demo:         ['var(--demo-tint)',     'var(--demo-text)'],
      workshop:     ['var(--workshop-tint)', 'var(--workshop-text)']
    };
    return map[type] || ['var(--neutral-tint)', 'var(--neutral-text)'];
  }

  function hexToTint(hex) {
    if (!hex || hex.length < 7) return '#f5f3f0';
    var r = parseInt(hex.slice(1, 3), 16) * 0.22 + 255 * 0.78;
    var g = parseInt(hex.slice(3, 5), 16) * 0.22 + 255 * 0.78;
    var b = parseInt(hex.slice(5, 7), 16) * 0.22 + 255 * 0.78;
    return 'rgb(' + Math.round(r) + ',' + Math.round(g) + ',' + Math.round(b) + ')';
  }

  function calcTimes(S) {
    var parts = (S.baseTime || '16:30').split(':');
    var cur = parseInt(parts[0]) * 60 + parseInt(parts[1]);
    return (S.sessions || []).map(function (slot) {
      var start = cur, end = start + (slot.duration || 0);
      cur = end + (slot.buffer || 0);
      return { id: slot.id, duration: slot.duration, buffer: slot.buffer, parallel: slot.parallel, startMin: start, endMin: end };
    });
  }

  function applyTheme(container, theme) {
    var merged = Object.assign({}, DEFAULT_THEME, theme);
    Object.keys(merged).forEach(function (k) {
      container.style.setProperty('--' + k, merged[k]);
    });
    ['keynote', 'panel', 'fireside', 'demo', 'workshop', 'neutral'].forEach(function (t) {
      var badgeKey = t + '-badge';
      container.style.setProperty('--' + t + '-tint', hexToTint(merged[badgeKey]));
    });
  }

  function buildHeader(S) {
    var hdr = el('div', 'la-header');

    if (S.org) {
      var orgEl = el('div', 'la-org');
      orgEl.textContent = S.org;
      hdr.appendChild(orgEl);
    }

    var titleEl = el('h3', 'la-title');
    titleEl.textContent = S.title || 'Agenda';
    hdr.appendChild(titleEl);

    if (S.date) {
      var dateWrap = el('div', 'la-date-row');
      if (S.eventUrl) {
        var dateLink = document.createElement('a');
        dateLink.href = S.eventUrl;
        dateLink.target = '_blank';
        dateLink.rel = 'noopener noreferrer';
        dateLink.className = 'la-date-link';
        dateLink.textContent = S.date;
        dateWrap.appendChild(dateLink);
      } else {
        dateWrap.textContent = S.date;
      }
      hdr.appendChild(dateWrap);
    }

    var timeStr = (S.timeStart || '') + ' ' + (S.timeStartPeriod || 'PM') + ' – ' + (S.timeEnd || '') + ' ' + (S.timeEndPeriod || 'PM');
    if (S.eventDate) timeStr += ', ' + S.eventDate;
    if (S.timeStart) {
      var timeRow = el('div', 'la-time-row');
      var timeIcon = el('span', 'material-icons-outlined la-time-icon');
      timeIcon.textContent = 'schedule';
      timeRow.appendChild(timeIcon);
      timeRow.appendChild(document.createTextNode(timeStr.trim()));
      hdr.appendChild(timeRow);
    }

    if (S.location) {
      var locRow = el('div', 'la-loc-row');
      var locIcon = el('span', 'material-icons-outlined la-loc-icon');
      locIcon.textContent = 'place';
      locRow.appendChild(locIcon);
      locRow.appendChild(document.createTextNode(S.location));
      hdr.appendChild(locRow);
    }

    if (S.mc) {
      var mcEl = el('div', 'la-mc');
      mcEl.textContent = 'MC: ' + S.mc;
      hdr.appendChild(mcEl);
    }

    if (S.description) {
      var descEl = el('div', 'la-description');
      descEl.textContent = S.description;
      hdr.appendChild(descEl);
    }

    return hdr;
  }

  function buildCard(card) {
    var cardEl = el('div', 'la-card la-card--' + card.type);

    if (card.track) {
      var metaRow = el('div', 'la-card-meta');
      metaRow.textContent = card.track;
      cardEl.appendChild(metaRow);
    }

    var typeRow = el('div', 'la-card-type-row');
    var badge = el('span', 'la-badge la-badge--' + card.type);
    badge.textContent = TYPES[card.type] || card.type.toUpperCase().replace(/_/g, ' ');
    typeRow.appendChild(badge);

    if (card.location) {
      var locWrap = el('div', 'la-card-loc-wrap');
      var locIcon = el('span', 'material-icons-outlined la-card-loc-icon');
      locIcon.textContent = 'place';
      locWrap.appendChild(locIcon);
      var locEl = el('span', 'la-card-loc');
      locEl.textContent = card.location;
      locWrap.appendChild(locEl);
      typeRow.appendChild(locWrap);
    }
    cardEl.appendChild(typeRow);

    var titleEl = el('div', 'la-card-title');
    titleEl.textContent = card.title;
    cardEl.appendChild(titleEl);

    if (card.speakers && card.speakers.length) {
      var colors = avColors(card.type);
      var spkGrid = el('div', 'la-speakers');
      card.speakers.forEach(function (sp) {
        var row = el('div', 'la-spk');
        var av = el('div', 'la-spk-av');
        av.style.background = colors[0];
        av.style.color = colors[1];
        av.textContent = initials(sp.name);
        row.appendChild(av);
        var info = el('div', 'la-spk-info');
        var nameEl = el('span', 'la-spk-name');
        nameEl.textContent = sp.name;
        info.appendChild(nameEl);
        var co = sp.company + (sp.role ? ' (' + sp.role + ')' : '');
        if (co.trim()) {
          var coEl = el('span', 'la-spk-co');
          coEl.textContent = co;
          info.appendChild(coEl);
        }
        row.appendChild(info);
        spkGrid.appendChild(row);
      });
      cardEl.appendChild(spkGrid);
    }

    if (card.notes) {
      var notesEl = el('div', 'la-card-notes');
      notesEl.textContent = card.notes;
      cardEl.appendChild(notesEl);
    }

    if (card.url) {
      var link = document.createElement('a');
      link.className = 'la-card-link';
      link.href = card.url;
      link.target = '_blank';
      link.rel = 'noopener noreferrer';
      var ico = el('span');
      ico.style.cssText = 'font-size:11px;margin-right:3px;line-height:1';
      ico.textContent = '↗';
      link.appendChild(ico);
      link.appendChild(document.createTextNode(' Learn more'));
      cardEl.appendChild(link);
    }

    return cardEl;
  }

  function renderAgenda(container, S, theme) {
    applyTheme(container, theme);

    var app = el('div', 'la-app');

    app.appendChild(buildHeader(S));

    var tl = el('div', 'la-tl');
    var times = calcTimes(S);

    times.forEach(function (slotTime, i) {
      var slot = S.sessions[i];
      if (!slot) return;

      var row = el('div', 'la-row');

      var tc = el('div', 'la-time');
      var tv = el('div', 'la-tv');
      tv.textContent = fmt(slotTime.startMin);
      tc.appendChild(tv);
      var durLabel = slot.buffer > 0 ? (slot.duration + ' + ' + slot.buffer + ' m') : (slot.duration + ' min');
      var dv = el('div', 'la-dv');
      dv.textContent = durLabel;
      tc.appendChild(dv);
      row.appendChild(tc);

      var spine = el('div', 'la-spine');
      var dot = el('div', 'la-dot');
      var vl = el('div', 'la-vl');
      spine.appendChild(dot);
      spine.appendChild(vl);
      row.appendChild(spine);

      var cc = el('div', 'la-cc');
      var parWrap = el('div', 'la-par-wrap');
      (slot.parallel || []).forEach(function (card) {
        var pw = el('div', 'la-par-card');
        pw.appendChild(buildCard(card));
        parWrap.appendChild(pw);
      });
      cc.appendChild(parWrap);
      row.appendChild(cc);
      tl.appendChild(row);
    });

    if (times.length) {
      var last = times[times.length - 1];
      var endMin = last.endMin + (last.buffer || 0);
      var endRow = el('div', 'la-end');

      var etcEl = el('div', 'la-time');
      var etvEl = el('div', 'la-tv');
      etvEl.textContent = fmt(endMin);
      etcEl.appendChild(etvEl);
      var edvEl = el('div', 'la-dv');
      edvEl.textContent = 'End';
      etcEl.appendChild(edvEl);
      endRow.appendChild(etcEl);

      var espEl = el('div', 'la-spine');
      var evlEl = document.createElement('div');
      evlEl.style.cssText = 'width:1px;background:var(--la-line);height:15px;flex-shrink:0';
      espEl.appendChild(evlEl);
      var edtEl = el('div', 'la-dot');
      edtEl.style.cssText = 'background:var(--la-text);width:10px;height:10px;border-radius:50%;flex-shrink:0';
      espEl.appendChild(edtEl);
      endRow.appendChild(espEl);
      tl.appendChild(endRow);
    }

    app.appendChild(tl);
    container.appendChild(app);
  }

  function init() {
    var displays = document.querySelectorAll('.luma-agenda-display');
    for (var i = 0; i < displays.length; i++) {
      var container = displays[i];
      if (container.dataset.rendered) continue;
      container.dataset.rendered = '1';
      try {
        var agendaData = JSON.parse(container.getAttribute('data-agenda') || '{}');
        var themeData  = JSON.parse(container.getAttribute('data-theme') || '{}');
        if (agendaData && Array.isArray(agendaData.sessions) && agendaData.sessions.length) {
          renderAgenda(container, agendaData, themeData);
        } else {
          container.innerHTML = '<p style="padding:24px;color:#888">No agenda sessions found.</p>';
        }
      } catch (e) {
        container.innerHTML = '<p style="padding:24px;color:#888">Could not load agenda.</p>';
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
