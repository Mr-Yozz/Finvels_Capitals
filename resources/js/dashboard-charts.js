import Chart from 'chart.js/auto';

// =====================
// Animated Counters
// =====================
document.querySelectorAll('.counter').forEach(el => {
    const target = parseFloat(el.dataset.count);
    if (!isNaN(target)) {
        let count = 0;
        const increment = target / 100;
        const interval = setInterval(() => {
            count += increment;
            if (count >= target) {
                el.innerText = target.toLocaleString();
                clearInterval(interval);
            } else {
                el.innerText = Math.floor(count).toLocaleString();
            }
        }, 15);
    }
});

// =====================
// Mini Sparklines
// =====================
document.querySelectorAll('.sparkline').forEach(el => {
    const values = JSON.parse(el.dataset.values || '[]');
    if(values.length){
        new Chart(el, {
            type: 'line',
            data: {
                labels: values.map((_,i)=>i+1),
                datasets: [{
                    data: values,
                    borderColor: 'rgba(255,255,255,0.9)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x:{ display:false }, y:{ display:false } }
            }
        });
    }
});

// =====================
// Main Charts
// =====================

// Monthly Loan Disbursement
const loanChartEl = document.getElementById('loanChart');
if (loanChartEl) {
    const labels = JSON.parse(loanChartEl.dataset.labels || '[]');
    const data = JSON.parse(loanChartEl.dataset.values || '[]');
    new Chart(loanChartEl, {
        type: 'bar',
        data: { labels, datasets: [{ label:'Disbursed Amount', data, backgroundColor:'rgba(78,115,223,0.7)', borderRadius: 4 }] },
        options: { responsive: true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true, ticks:{ callback:v=>'₹'+v.toLocaleString() } } } }
    });
}

// Branch-wise Loan Distribution
const branchChartEl = document.getElementById('branchChart');
if(branchChartEl){
    const labels = JSON.parse(branchChartEl.dataset.labels || '[]');
    const data = JSON.parse(branchChartEl.dataset.values || '[]');
    new Chart(branchChartEl, {
        type: 'doughnut',
        data: { labels, datasets:[{ data, backgroundColor:['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'] }] },
        options:{ responsive:true, plugins:{ legend:{ position:'bottom' } } }
    });
}

// Repayment Trend
const repaymentChartEl = document.getElementById('repaymentChart');
if(repaymentChartEl){
    const labels = JSON.parse(repaymentChartEl.dataset.labels || '[]');
    const expected = JSON.parse(repaymentChartEl.dataset.expected || '[]');
    const actual = JSON.parse(repaymentChartEl.dataset.actual || '[]');

    new Chart(repaymentChartEl, {
        type:'line',
        data: {
            labels,
            datasets:[
                { label:'Expected', data:expected, borderColor:'#1cc88a', tension:0.3, fill:false },
                { label:'Actual', data:actual, borderColor:'#e74a3b', tension:0.3, fill:false }
            ]
        },
        options:{ responsive:true }
    });
}
