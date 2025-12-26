// Wire existing forms to backend without changing HTML
(function(){
  function collectOrg() {
    const form = document.getElementById('organization-form');
    if (!form) return null;
    const data = new FormData();
    data.append('type','organization');
    data.append('name', document.getElementById('org-name').value);
    data.append('email', document.getElementById('org-email').value);
    data.append('org_type', document.getElementById('org-type').value);
    data.append('phone', document.getElementById('org-phone').value);
    data.append('website', document.getElementById('org-website').value);
    data.append('address_line1', document.getElementById('address-line1').value);
    data.append('address_line2', document.getElementById('address-line2').value);
    data.append('city', document.getElementById('city').value);
    data.append('state', document.getElementById('state').value);
    data.append('district', document.getElementById('district').value);
    data.append('pincode', document.getElementById('pincode').value);
    data.append('country', document.getElementById('country').value);
    data.append('password', document.getElementById('org-password').value);
    const file = document.getElementById('org-file')?.files?.[0];
    if (file) data.append('document', file);
    return data;
  }
  function collectInst() {
    const form = document.getElementById('institution-form');
    if (!form) return null;
    const data = new FormData();
    data.append('type','institution');
    data.append('name', document.getElementById('inst-name').value);
    data.append('inst_type', document.getElementById('inst-type').value);
    data.append('inst_code', document.getElementById('inst-code').value);
    data.append('inst_university', document.getElementById('inst-university').value);
    data.append('email', document.getElementById('inst-email').value);
    data.append('phone', document.getElementById('inst-phone').value);
    data.append('website', document.getElementById('inst-website').value);
    data.append('address_line1', document.querySelector('#inst-form #address-line1').value);
    data.append('address_line2', document.querySelector('#inst-form #address-line2').value);
    data.append('city', document.querySelector('#inst-form #city').value);
    data.append('state', document.querySelector('#inst-form #state').value);
    data.append('district', document.querySelector('#inst-form #district').value);
    data.append('pincode', document.querySelector('#inst-form #pincode').value);
    data.append('country', document.querySelector('#inst-form #country').value);
    data.append('password', document.getElementById('inst-password').value);
    const file = document.getElementById('inst-file')?.files?.[0];
    if (file) data.append('document', file);
    return data;
  }

  function submit(formData) {
    return fetch('api.php?action=register', { method:'POST', body: formData })
      .then(r=>r.json());
  }

  const orgForm = document.getElementById('organization-form');
  if (orgForm) {
    orgForm.addEventListener('submit', function(e){
      e.preventDefault();
      submit(collectOrg()).then(res => {
        if (res.ok) {
          alert('Registration submitted. Await admin approval.');
          window.location.href = 'login.php';
        } else {
          alert('Registration failed: ' + (res.error || 'Unknown error'));
        }
      });
    });
  }

  const instForm = document.getElementById('institution-form');
  if (instForm) {
    instForm.addEventListener('submit', function(e){
      e.preventDefault();
      submit(collectInst()).then(res => {
        if (res.ok) {
          alert('Registration submitted. Await admin approval.');
          window.location.href = 'login.php';
        } else {
          alert('Registration failed: ' + (res.error || 'Unknown error'));
        }
      });
    });
  }
})();


