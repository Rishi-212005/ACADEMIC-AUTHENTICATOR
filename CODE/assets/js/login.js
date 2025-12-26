// Override demo behavior and implement real user login with OTP
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    let loginForm = document.getElementById('loginForm');
    const organizationBtn = document.getElementById('organizationBtn');
    const instituteBtn = document.getElementById('instituteBtn');
    const userTypeHidden = document.getElementById('user_type');
    const otpMessage = document.getElementById('otpMessage');
    const otpSection = document.getElementById('otpSection');
    let verifyOtpBtn = document.getElementById('verifyOtpBtn');
    let emailInput = document.getElementById('email');
    let passwordInput = document.getElementById('password');

    if (!loginForm) return;

    // Neutralize any inline demo listeners that auto-show OTP
    const formClone = loginForm.cloneNode(true);
    loginForm.parentNode.replaceChild(formClone, loginForm);
    const form = document.getElementById('loginForm');
    // re-query inputs and buttons because old references point to removed nodes
    emailInput = document.getElementById('email');
    passwordInput = document.getElementById('password');
    verifyOtpBtn = document.getElementById('verifyOtpBtn');

    // Ensure fields start empty and OTP section hidden
    if (emailInput) { emailInput.value = ''; emailInput.setAttribute('autocomplete','off'); }
    if (passwordInput) { passwordInput.value = ''; passwordInput.setAttribute('autocomplete','off'); }
    if (otpMessage) otpMessage.classList.add('hidden');
    if (otpSection) otpSection.classList.add('hidden');

    const submitBtn = document.querySelector('#loginForm button[type="submit"]') || document.querySelector('#loginForm .btn-primary');

    // Sync toggle with hidden field
    if (organizationBtn) {
      organizationBtn.addEventListener('click', function(){
        organizationBtn.classList.add('active');
        instituteBtn?.classList?.remove('active');
        if (userTypeHidden) userTypeHidden.value = 'organization';
      });
    }
    if (instituteBtn) {
      instituteBtn.addEventListener('click', function(){
        instituteBtn.classList.add('active');
        organizationBtn?.classList?.remove('active');
        if (userTypeHidden) userTypeHidden.value = 'institution';
      });
    }

    function showLoading(isLoading){
      // Button state
      if (submitBtn) {
        if (isLoading) {
          submitBtn.setAttribute('disabled','disabled');
          submitBtn.dataset.__origText = submitBtn.textContent;
          submitBtn.textContent = 'Sending OTP...';
        } else {
          submitBtn.removeAttribute('disabled');
          if (submitBtn.dataset.__origText) submitBtn.textContent = submitBtn.dataset.__origText;
        }
      }
      // Fullscreen lightweight overlay
      let overlay = document.getElementById('login-loading-overlay');
      if (isLoading) {
        if (!overlay) {
          overlay = document.createElement('div');
          overlay.id = 'login-loading-overlay';
          overlay.style.position = 'fixed';
          overlay.style.inset = '0';
          overlay.style.background = 'rgba(0,0,0,0.15)';
          overlay.style.display = 'flex';
          overlay.style.alignItems = 'center';
          overlay.style.justifyContent = 'center';
          overlay.style.zIndex = '9999';
          overlay.innerHTML = '<div style="width:42px;height:42px;border:4px solid #cbd5e1;border-top-color:#2563eb;border-radius:50%;animation:spin 0.9s linear infinite"></div>';
          const style = document.createElement('style');
          style.innerHTML = '@keyframes spin{to{transform:rotate(360deg)}}';
          document.head.appendChild(style);
          document.body.appendChild(overlay);
        } else {
          overlay.style.display = 'flex';
        }
      } else if (overlay) {
        overlay.style.display = 'none';
      }
    }

    function post(url, body){
      return fetch(url, {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)})
        .then(async r=>{ const d = await r.json().catch(()=>({})); if(!r.ok){ throw new Error(d.error||'Request failed'); } return d; });
    }

    form.onsubmit = function(e){
      e.preventDefault();
      const email = (emailInput?.value||'').trim();
      const password = (passwordInput?.value||'').trim();
      if (!email || !password) { alert('Enter email and password'); return; }
      showLoading(true);
      const selectedType = userTypeHidden?.value || (document.getElementById('instituteBtn')?.classList?.contains('active') ? 'institution' : 'organization');
      post('api.php?action=login_start', {email, password, type: selectedType}).then(res => {
        // Show OTP only when credentials are correct
        if (otpMessage) otpMessage.classList.remove('hidden');
        setTimeout(()=>{
          if (otpMessage) otpMessage.classList.add('hidden');
          if (otpSection) otpSection.classList.remove('hidden');
          // Clear OTP inputs
          document.querySelectorAll('.otp-input').forEach(i=> i.value='');
          showLoading(false);
        }, 500);
      }).catch(err => {
        alert(err.message || 'Login failed');
        if (passwordInput) passwordInput.value = '';
        showLoading(false);
      });
    };

    if (verifyOtpBtn) {
      verifyOtpBtn.onclick = function(){
        const digits = Array.from(document.querySelectorAll('.otp-input')).map(i=>i.value).join('');
        if (digits.length !== 6) { alert('Enter 6-digit OTP'); return; }
        post('api.php?action=verify_otp', {code: digits}).then(res => {
          const path = (res && res.redirect) || (res?.type === 'institution' ? 'institute_dashboard.php' : 'organisation_dashboard.php');
          if (path) {
            window.location.href = path;
          } else {
            // Fallback: compute redirect via session on server
            fetch('api.php?action=session_redirect').then(r=>r.json()).then(rj=>{
              if (rj && rj.redirect) {
                window.location.href = rj.redirect;
              } else {
                alert('Login successful, but could not determine destination. Please reload.');
              }
            }).catch(()=>{
              alert('Login successful, but redirect failed. Please reload.');
            });
          }
        }).catch(err => {
          alert(err.message || 'Invalid OTP');
          document.querySelectorAll('.otp-input').forEach(i=> i.value='');
        });
      };
    }
  });
})();


