// Override demo behavior and implement real admin login with OTP
+(function(){
  // Wait for DOM to be ready
  document.addEventListener('DOMContentLoaded', function() {
    const loginBtn = document.getElementById('loginBtn');
    const loginForm = document.getElementById('loginForm');
    const otpForm = document.getElementById('otpForm');
    const loginError = document.getElementById('loginError');
    const verifyBtn = document.getElementById('verifyBtn');
    const otpError = document.getElementById('otpError');
    const otpSuccess = document.getElementById('otpSuccess');
    const adminEmail = document.getElementById('adminEmail');
    const adminPassword = document.getElementById('adminPassword');

    if (!loginBtn || !loginForm || !otpForm) return;

    // Neutralize inline demo listeners by cloning the button
    const loginBtnClone = loginBtn.cloneNode(true);
    loginBtn.parentNode.replaceChild(loginBtnClone, loginBtn);
    const freshLoginBtn = document.getElementById('loginBtn');

    // Hide OTP form initially
    otpForm.style.display = 'none';
    loginError.style.display = 'none';

    // Clear email and password fields on page load and disable autocomplete
    if (adminEmail) {
      adminEmail.value = '';
      adminEmail.setAttribute('autocomplete','off');
    }
    if (adminPassword) {
      adminPassword.value = '';
      adminPassword.setAttribute('autocomplete','off');
    }

    function post(url, body) {
      return fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(body)
      }).then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok) {
          throw new Error(data.error || 'Request failed');
        }
        return data;
      });
    }

    // Loading overlay and button state while waiting for OTP
    const submitBtn = document.getElementById('loginBtn');
    function showLoading(isLoading){
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
      let overlay = document.getElementById('admin-login-overlay');
      if (isLoading) {
        if (!overlay) {
          overlay = document.createElement('div');
          overlay.id = 'admin-login-overlay';
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

    // Override the demo login handler
    freshLoginBtn.onclick = function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const email = adminEmail.value.trim();
      const password = adminPassword.value.trim();
      
      if (!email || !password) {
        loginError.textContent = 'Please enter both email and password.';
        loginError.style.display = 'block';
        return;
      }

      // Clear any previous errors
      loginError.style.display = 'none';
      
      showLoading(true);
      post('api.php?action=admin_login', {email, password})
        .then(res => {
          if (res && res.ok && res.otp) {
            // Valid credentials - show OTP form
            loginForm.style.display = 'none';
            otpForm.style.display = 'block';
            // Clear OTP inputs
            for (let i = 1; i <= 6; i++) {
              const digit = document.getElementById('digit' + i);
              if (digit) digit.value = '';
            }
            showLoading(false);
          } else {
            throw new Error('Invalid response');
          }
        })
        .catch(err => {
          // Invalid credentials or error
          loginError.textContent = 'Invalid credentials. Please try again.';
          loginError.style.display = 'block';
          // Clear password field
          adminPassword.value = '';
          showLoading(false);
        });
    };

    // Override the demo OTP handler
    if (verifyBtn) {
      verifyBtn.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const digits = [];
        for (let i = 1; i <= 6; i++) {
          const digit = document.getElementById('digit' + i);
          if (digit) digits.push(digit.value);
        }
        const code = digits.join('');
        
        if (code.length !== 6) {
          otpError.textContent = 'Please enter all 6 digits.';
          otpError.style.display = 'block';
          return;
        }

        otpError.style.display = 'none';
        
        post('api.php?action=admin_verify_otp', {code})
          .then(res => {
            if (res && res.ok) {
              otpSuccess.style.display = 'block';
              setTimeout(() => {
                window.location.href = 'admin_dashboard.php';
              }, 1000);
            } else {
              throw new Error('Invalid OTP');
            }
          })
          .catch(err => {
            otpError.textContent = 'Invalid OTP. Please try again.';
            otpError.style.display = 'block';
            // Clear OTP inputs
            for (let i = 1; i <= 6; i++) {
              const digit = document.getElementById('digit' + i);
              if (digit) digit.value = '';
            }
          });
      };
    }
  });
})();