(function(){
  function get(url){ return fetch(url).then(async r=>{ const d=await r.json().catch(()=>({})); if(!r.ok) throw new Error(d.error||'Request failed'); return d; }); }
  function render(item){
    const d = document.getElementById('detailCard');
    if (!d) return;
    const isInst = (item.type === 'institution');
    const rows = [
      ['Name', item.name||''],
      ['Type', item.type||''],
      ['Email', item.email||''],
      ['Phone', item.phone||''],
      ['Website', item.website||''],
      ['Institution Code', item.inst_code||''],
      ['Institution University', item.inst_university||''],
      ['Institution Type', item.inst_type||''],
      ['Organization Type', item.org_type||''],
      ['Address Line 1', item.address_line1||''],
      ['Address Line 2', item.address_line2||''],
      ['City', item.city||''],
      ['State', item.state||''],
      ['District', item.district||''],
      ['Pincode', item.pincode||''],
      ['Country', item.country||''],
      ['Submitted On', item.submitted_at||''],
      ['Approved On', item.created_at||'']
    ].filter(([k,v]) => String(v).trim() !== '');
    d.innerHTML = `
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        ${rows.map(([k,v])=>`
          <div class="space-y-1">
            <div class="text-xs uppercase tracking-wide text-gray-500">${k}</div>
            <div class="text-gray-900 font-semibold">${v}</div>
          </div>
        `).join('')}
      </div>
      ${item.document_path ? `<div class="mt-8">
        <a class="inline-flex items-center px-4 py-2 rounded-md bg-primary-50 text-blue-700 hover:bg-blue-100 font-medium transition" href="${item.document_path}" target="_blank">
          <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-4 w-4 mr-2\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 4v16m8-8H4\" /></svg>
          View Uploaded Document
        </a>
      </div>` : ''}
    `;
  }
  document.addEventListener('DOMContentLoaded', function(){
    const params = new URLSearchParams(location.search);
    const id = params.get('id');
    const type = params.get('type');
    if (!id || !type) { const d=document.getElementById('detailCard'); if(d) d.textContent='Missing parameters'; return; }
    get(`api.php?action=registration_detail&id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}`).then(r=>{
      if (!r || !r.item) throw new Error('Not found');
      render(r.item);
    }).catch(err=>{
      const d=document.getElementById('detailCard');
      if (d) d.textContent = err.message || 'Failed to load details';
    });
  });
})();



