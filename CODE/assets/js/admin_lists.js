(function(){
  function get(url){ return fetch(url).then(async r=>{ const d=await r.json().catch(()=>({})); if(!r.ok) throw new Error(d.error||'Request failed'); return d; }); }
  document.addEventListener('DOMContentLoaded', function(){
    const isInstitutions = /admin_institutions\.php$/i.test(location.pathname);
    get('api.php?action=admin_summary').then(res => {
      let items = isInstitutions ? (res.lists?.institutions||[]) : (res.lists?.organizations||[]);
      const body = document.getElementById('listBody');
      const badge = document.getElementById('countBadge');
      if (badge) badge.textContent = `${items.length} total approved`;
      if (!body) return;
      function render(list){
        body.innerHTML = list.map(r=>`
        <tr>
          <td class="px-6 py-4 whitespace-nowrap">${r.name}</td>
          <td class="px-6 py-4 whitespace-nowrap">${r.email||''}</td>
          <td class="px-6 py-4 whitespace-nowrap">${r.created_at ? new Date(r.created_at).toLocaleString() : ''}</td>
          <td class="px-6 py-4 whitespace-nowrap"><button class="view-btn text-blue-600" data-id="${r.id||''}" data-email="${r.email}" data-type="${isInstitutions ? 'institution' : 'organization'}">View</button></td>
        </tr>
        `).join('');
      }
      render(items);
      // Search filter
      const search = document.getElementById('searchInput');
      if (search) {
        search.addEventListener('input', function(){
          const q = this.value.toLowerCase();
          const filtered = items.filter(r => (r.name||'').toLowerCase().includes(q) || (r.email||'').toLowerCase().includes(q));
          if (badge) badge.textContent = `${filtered.length} of ${items.length} match`;
          render(filtered);
          wireViewButtons();
        });
      }
      // Wire view buttons to fetch details
      function wireViewButtons(){
        body.querySelectorAll('.view-btn').forEach(btn => {
          btn.addEventListener('click', function(){
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            const target = type === 'institution' ? 'admin_institution_detail.php' : 'admin_organization_detail.php';
            if (id && type) {
              window.location.href = `${target}?id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}`;
            }
          });
        });
      }
      wireViewButtons();
    }).catch(()=>{
      const body = document.getElementById('listBody');
      if (body) body.innerHTML = '<tr><td class="px-6 py-4 text-red-600" colspan="4">Failed to load data</td></tr>';
    });
  });
})();



