// Inject approval listing into existing dashboard UI controls
(function(){
  console.log('Admin dashboard script loaded!');
  
  function get(url){ return fetch(url).then(async r=>{ const d=await r.json().catch(()=>({})); if(!r.ok) throw new Error(d.error||'Request failed'); return d; }); }
  function post(url, body){ return fetch(url,{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)}).then(r=>r.json()); }

  // Hook into nav clicks already present; also expose functions globally if needed.
  window.fetchPending = function(type){
    return get('api.php?action=pending&type='+(type||'both'));
  }
  window.approveItem = function(id){ return post('api.php?action=approve', {id}); }
  window.rejectItem = function(id){ return post('api.php?action=reject', {id}); }

  // Dashboard Data Functions - Simple and direct
  function updateDashboardData() {
    console.log('Fetching dashboard data from API...');
    
    // Fetch data from admin_summary API
    fetch('api.php?action=admin_summary', {
      credentials: 'same-origin'
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('API request failed');
        }
        return response.json();
      })
      .then(data => {
        console.log('API Response:', data);
        
        // Update pending badge
        if (data.counts && data.counts.pending_total !== undefined) {
          const badge = document.getElementById('pending-approvals-badge');
          if (badge) {
            badge.textContent = data.counts.pending_total;
            badge.style.display = data.counts.pending_total > 0 ? 'flex' : 'none';
          }
        }

        // Update stat cards with direct counts from users table
        if (data.counts) {
          updateStatCards({
            approved_institutions: data.counts.approved_institutions || 0,
            approved_organizations: data.counts.approved_organizations || 0,
            total_verifications: data.counts.total_verifications || 0
          });
        }
        
        // Update charts
        if (data.status_breakdown || data.monthly) {
          updateCharts(data);
        }
      })
      .catch(error => {
        console.error('Error loading dashboard data:', error);
      });
  }

  // Recent Verification Activity Functions (for table display only)
  function populateRecentActivity() {
    console.log('Populating Recent Verification Activity...');
    const tableBody = document.getElementById('recentActivityTableBody');
    if (!tableBody) {
      console.error('Recent activity table body not found!');
      return;
    }

    // Show loading state
    tableBody.innerHTML = `
      <tr>
        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
          <div class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-500"></div>
            <span class="ml-2">Loading recent activity...</span>
          </div>
        </td>
      </tr>
    `;

    // Fetch recent pending data from dedicated endpoint
    fetch('get_recent_approvals.php')
      .then(response => response.json())
      .then(data => {
        console.log('Recent approvals response:', data);
        
        const recentPending = Array.isArray(data) ? data : [];
        console.log('Recent pending items:', recentPending);

        if (recentPending.length === 0) {
          tableBody.innerHTML = `
            <tr>
              <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                  <p>No recent verification activity</p>
                  <p class="text-sm text-gray-400">All applications have been processed</p>
                </div>
              </td>
            </tr>
          `;
          return;
        }

        // Populate table with recent pending items
        tableBody.innerHTML = recentPending.map(item => {
          const typeIcon = item.type.toLowerCase() === 'institution' ? 'fas fa-university' : 'fas fa-building';
          const typeColor = item.type.toLowerCase() === 'institution' ? 'text-blue-600' : 'text-green-600';
          const typeLabel = item.type; // Already capitalized from backend
          
          return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <i class="${typeIcon} ${typeColor} mr-2"></i>
                  <span class="font-medium text-gray-900 dark:text-gray-100">${item.name}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type.toLowerCase() === 'institution' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'}">
                  ${typeLabel}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                ${item.contact}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                ${new Date(item.submitted_at).toLocaleDateString('en-US', {
                  year: 'numeric',
                  month: 'short',
                  day: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit'
                })}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                  <i class="fas fa-clock mr-1"></i>
                  ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button 
                  class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                  onclick="viewPendingItem(${item.id}, '${item.type.toLowerCase()}')"
                >
                  <i class="fas fa-eye mr-1"></i>
                  View
                </button>
              </td>
            </tr>
          `;
        }).join('');

      })
      .catch(error => {
        console.error('Error fetching recent activity:', error);
        tableBody.innerHTML = `
          <tr>
            <td colspan="6" class="px-6 py-8 text-center text-red-500">
              <div class="flex flex-col items-center">
                <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-2"></i>
                <p>Failed to load recent activity</p>
                <p class="text-sm text-gray-400 mb-2">Error: ${error.message || 'Unknown error'}</p>
                <button onclick="populateRecentActivity()" class="mt-2 text-sm text-primary-600 hover:text-primary-800">
                  <i class="fas fa-redo mr-1"></i>
                  Retry
                </button>
              </div>
            </td>
          </tr>
        `;
      });
  }

  // Update stat cards - Simple direct update
  function updateStatCards(counts) {
    if (!counts) {
      console.error('No counts provided');
      return;
    }
    
    // Get institutions count
    const instCount = parseInt(counts.approved_institutions) || 0;
    const instEl = document.getElementById('total-institutions-count');
    if (instEl) {
      instEl.textContent = instCount;
      console.log('Updated institutions count:', instCount);
    }
    
    // Get organizations count
    const orgCount = parseInt(counts.approved_organizations) || 0;
    const orgEl = document.getElementById('total-organizations-count');
    if (orgEl) {
      orgEl.textContent = orgCount;
      console.log('Updated organizations count:', orgCount);
      }
    
    // Get verifications count
    const verCount = parseInt(counts.total_verifications) || 0;
    const verEl = document.getElementById('total-verifications-count');
    if (verEl) {
      verEl.textContent = verCount;
      console.log('Updated verifications count:', verCount);
      }
  }

  // Update charts
  function updateCharts(response) {
    const statusBreakdown = response.status_breakdown || { valid: 0, suspicious: 0, invalid: 0 };
    const monthly = response.monthly || { labels: [], data: [] };

    // Update verification status chart
    if (window.verificationChart) {
      window.verificationChart.data.datasets[0].data = [
        statusBreakdown.valid || 0,
        statusBreakdown.suspicious || 0,
        statusBreakdown.invalid || 0
      ];
      window.verificationChart.update();
    }

    // Update monthly trend chart
    if (window.monthlyTrendChart) {
      window.monthlyTrendChart.data.labels = monthly.labels || [];
      window.monthlyTrendChart.data.datasets[0].data = monthly.data || [];
      window.monthlyTrendChart.update();
    }
  }

  // View pending item function
  window.viewPendingItem = function(id, type) {
    const target = type === 'institution' ? 'admin_pending_institution.php' : 'admin_pending_organization.php';
    window.location.href = `${target}?id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}`;
  };

  // View All button handler
  window.viewAllRecent = function() {
    // Navigate to pending approvals page
    const pendingLink = document.querySelector('.nav-item[href*="pending"], .nav-item:contains("Pending Approvals")');
    if (pendingLink) {
      pendingLink.click();
    } else {
      // Fallback: show pending content
      if (window.showPendingContent) {
        window.showPendingContent('both');
      }
    }
  };

  // On dashboard load
  document.addEventListener('DOMContentLoaded', function(){
    console.log('Admin dashboard script starting...');
    console.log('Checking for stat card elements...');
    
    // Verify elements exist
    const instEl = document.getElementById('total-institutions-count');
    const orgEl = document.getElementById('total-organizations-count');
    const verEl = document.getElementById('total-verifications-count');
    console.log('Elements found:', {
      institutions: !!instEl,
      organizations: !!orgEl,
      verifications: !!verEl
    });
    
    // Update dashboard data (sidebar badges, stat cards, charts) - but NOT the table
    updateDashboardData();
    
    // Set up View All button
    const viewAllBtn = document.getElementById('viewAllButton');
    if (viewAllBtn) {
      viewAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.viewAllRecent();
      });
    }

    // Auto-refresh dashboard data every 30 seconds (but not the table)
    setInterval(updateDashboardData, 30000);
  });

  // Approved lists view (Institutions/Organizations)
  window.showApprovedList = function(kind){
    const mainContent = document.querySelector('.main-content');
    if (!mainContent) return;

    const title = kind === 'institutions' ? 'Institutions List - Approved' : 'Organizations List - Approved';
    mainContent.innerHTML = '';
    const header = document.createElement('header');
    header.className = 'flex justify-between items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow mb-6';
    header.innerHTML = `
      <h2 class="text-xl font-semibold">${title}</h2>
      <div class="flex items-center space-x-4">
        <button id="darkModeToggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700"><i class="fas fa-moon"></i></button>
      </div>`;
    mainContent.appendChild(header);

    get('api.php?action=admin_summary').then(res => {
      const items = (kind === 'institutions' ? res.lists?.institutions : res.lists?.organizations) || [];
      const nameCol = kind === 'institutions' ? 'Institution Name' : 'Organization Name';
      const rows = items.map(r => `
        <tr>
          <td class="px-6 py-4 whitespace-nowrap">${r.name}</td>
          <td class="px-6 py-4 whitespace-nowrap">${r.email||''}</td>
          <td class="px-6 py-4 whitespace-nowrap">${r.created_at ? new Date(r.created_at).toLocaleString() : ''}</td>
          <td class="px-6 py-4 whitespace-nowrap">
            <button class="text-primary-500 hover:text-primary-600 text-sm font-medium">View</button>
          </td>
        </tr>`).join('');
      const section = document.createElement('div');
      section.className = 'bg-white dark:bg-gray-800 rounded-lg shadow mb-6 overflow-hidden';
      section.innerHTML = `
        <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
          <h3 class="font-semibold">Approved ${kind === 'institutions' ? 'Institutions' : 'Organizations'}</h3>
          <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">${items.length} total</span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">${nameCol}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approved On</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">${rows}</tbody>
          </table>
        </div>`;
      mainContent.appendChild(section);
    }).catch(()=>{
      const err = document.createElement('div');
      err.className = 'p-4 bg-red-50 text-red-700 rounded-lg';
      err.textContent = 'Failed to load approved list.';
      mainContent.appendChild(err);
    });
  }

  // Override the demo showPendingContent with dynamic data from API
  window.showPendingContent = function(type){
    const mainContent = document.querySelector('.main-content');
    if (!mainContent) return;

    // Header
    let title = 'Pending Approvals';
    if (type === 'institutions') title = 'Institutions List - Pending Approvals';
    if (type === 'organizations') title = 'Organizations List - Pending Approvals';

    mainContent.innerHTML = '';
    const header = document.createElement('header');
    header.className = 'flex justify-between items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow mb-6';
    header.innerHTML = `
      <h2 class="text-xl font-semibold">${title}</h2>
      <div class="flex items-center space-x-4">
        <button id="darkModeToggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700"><i class="fas fa-moon"></i></button>
      </div>`;
    mainContent.appendChild(header);

    // Helper to build a section
    function sectionMarkup(kind, items){
      const label = kind === 'institutions' ? 'Pending Institutions' : 'Pending Organizations';
      const count = items.length;
      const nameCol = kind === 'institutions' ? 'Institution Name' : 'Organization Name';
      const rows = items.map(r => `
        <tr>
          <td class="px-6 py-4 whitespace-nowrap">${r.name}</td>
          <td class="px-6 py-4 whitespace-nowrap">${r.email}</td>
          <td class="px-6 py-4 whitespace-nowrap">${new Date(r.created_at).toLocaleString()}</td>
          <td class="px-6 py-4 whitespace-nowrap">
            <button class="view-pending text-blue-600" data-id="${r.id}" data-type="${kind==='institutions' ? 'institution':'organization'}">View</button>
          </td>
        </tr>`).join('');
      return `
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
          <h3 class="font-semibold">${label}</h3>
          <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">${count} pending</span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">${nameCol}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">${rows}</tbody>
          </table>
        </div>
      </div>`;
    }

    // Fetch needed lists
    const needInst = (type === 'both' || type === undefined || type === 'institutions');
    const needOrg = (type === 'both' || type === undefined || type === 'organizations');

    const promises = [];
    if (needInst) promises.push(fetchPending('institutions').then(r=>({kind:'institutions', items:r.items||[]})));
    if (needOrg) promises.push(fetchPending('organizations').then(r=>({kind:'organizations', items:r.items||[]})));

    Promise.all(promises).then(results => {
      results.forEach(res => {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = sectionMarkup(res.kind, res.items);
        mainContent.appendChild(wrapper.firstElementChild);
      });

      // Wire view buttons to navigate to pending detail page
      mainContent.querySelectorAll('.view-pending').forEach(btn => {
        btn.addEventListener('click', function(){
          const id = parseInt(this.getAttribute('data-id'), 10);
          const typeK = this.getAttribute('data-type');
          const target = typeK === 'institution' ? 'admin_pending_institution.php' : 'admin_pending_organization.php';
          window.location.href = `${target}?id=${encodeURIComponent(id)}&type=${encodeURIComponent(typeK)}`;
        });
      });
    }).catch(() => {
      const err = document.createElement('div');
      err.className = 'p-4 bg-red-50 text-red-700 rounded-lg';
      err.textContent = 'Failed to load pending items.';
      mainContent.appendChild(err);
    });
  }
})();


