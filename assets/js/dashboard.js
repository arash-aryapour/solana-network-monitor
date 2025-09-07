document.addEventListener('DOMContentLoaded', function() {
    // Refresh button functionality
    const refreshBtn = document.getElementById('refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }

    // Activity type chart
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
        const activityChart = new Chart(activityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Add Liquidity', 'Remove Liquidity', 'Create Token', 'Token Swap'],
                datasets: [{
                    data: [12, 19, 3, 5],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 206, 86, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Wallet activity chart
    const walletCtx = document.getElementById('walletChart');
    if (walletCtx) {
        const walletChart = new Chart(walletCtx, {
            type: 'bar',
            data: {
                labels: ['Wallet 1', 'Wallet 2', 'Wallet 3', 'Wallet 4'],
                datasets: [{
                    label: 'Activity Count',
                    data: [12, 19, 3, 5],
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Auto-refresh every 30 seconds
    setInterval(function() {
        fetch('api/get_wallet_data.php?wallet_id=1')
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    // Update last update time
                    const lastUpdateEl = document.getElementById('last-update');
                    if (lastUpdateEl) {
                        lastUpdateEl.textContent = new Date().toLocaleString();
                    }
                    
                    // Update activities table
                    const tableBody = document.getElementById('activities-table');
                    if (tableBody) {
                        // Clear existing rows
                        tableBody.innerHTML = '';
                        
                        // Add new rows
                        data.activities.forEach(activity => {
                            const row = document.createElement('tr');
                            
                            const typeMap = {
                                'ACTIVITY_TOKEN_ADD_LIQ': 'Add Liquidity',
                                'ACTIVITY_TOKEN_REMOVE_LIQ': 'Remove Liquidity',
                                'ACTIVITY_SPL_INIT_MINT': 'Create Token',
                                'ACTIVITY_AGG_TOKEN_SWAP': 'Token Swap'
                            };
                            
                            row.innerHTML = `
                                <td>${data.wallet.address.substring(0, 10)}...</td>
                                <td>${typeMap[activity.activity_type] || activity.activity_type}</td>
                                <td>${activity.token_name}</td>
                                <td>$${parseFloat(activity.value).toFixed(2)}</td>
                                <td>${new Date(activity.created_at).toLocaleString()}</td>
                                <td>
                                    <a href="https://solscan.io/account/${data.wallet.address}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> View
                                    </a>
                                </td>
                            `;
                            
                            tableBody.appendChild(row);
                        });
                    }
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }, 30000);
});