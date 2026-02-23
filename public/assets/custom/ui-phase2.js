document.addEventListener('DOMContentLoaded', () => {
  const uri = (document.body.dataset.uri || '').toLowerCase();

  const tables = document.querySelectorAll('table.table');
  tables.forEach((table) => {
    if (!table.closest('.table-responsive')) {
      const wrapper = document.createElement('div');
      wrapper.className = 'table-responsive';
      table.parentNode.insertBefore(wrapper, table);
      wrapper.appendChild(table);
    }

    table.classList.add('enable-mobile-cards');

    const headers = Array.from(table.querySelectorAll('thead th')).map((th) =>
      (th.textContent || '').trim() || 'Field'
    );

    if (!headers.length) {
      const firstRow = table.querySelector('tbody tr');
      if (firstRow) {
        const cells = firstRow.querySelectorAll('td');
        cells.forEach((_, idx) => headers.push('Kolom ' + (idx + 1)));
      }
    }

    table.querySelectorAll('tbody tr').forEach((row) => {
      row.querySelectorAll('td').forEach((td, idx) => {
        if (!td.dataset.label) {
          td.dataset.label = headers[idx] || 'Field';
        }
      });
    });
  });

  const syncMobileTableMode = () => {
    const mobile = window.innerWidth <= 991.98;
    document.querySelectorAll('table.enable-mobile-cards').forEach((table) => {
      table.classList.toggle('table-mobile-card', mobile);
    });
  };

  syncMobileTableMode();
  window.addEventListener('resize', syncMobileTableMode);

  document.querySelectorAll('img').forEach((img) => {
    if (!img.getAttribute('loading')) {
      img.setAttribute('loading', 'lazy');
    }
  });

  const quickFilterInput = document.querySelector('[data-quick-filter-input]');
  if (quickFilterInput) {
    quickFilterInput.addEventListener('input', () => {
      const q = quickFilterInput.value.trim().toLowerCase();

      document.querySelectorAll('table tbody tr').forEach((row) => {
        const text = (row.textContent || '').toLowerCase();
        row.style.display = !q || text.includes(q) ? '' : 'none';
      });

      document.querySelectorAll('.card .list-item').forEach((item) => {
        const text = (item.textContent || '').toLowerCase();
        item.style.display = !q || text.includes(q) ? '' : 'none';
      });
    });
  }

  if (uri.includes('rekap-absensi') || uri.includes('murid') || uri.includes('guru')) {
    const firstInput = document.querySelector('input[type="search"], input[name="q"], #searchMurid');
    if (firstInput && !firstInput.placeholder) {
      firstInput.placeholder = 'Cari data...';
    }
  }
});
