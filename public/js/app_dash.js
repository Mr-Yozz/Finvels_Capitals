<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const loanCtx = document.getElementById('loanChart');
    new Chart(loanCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_map(fn($m) => date("M", mktime(0,0,0,$m,1)), range(1, 12))) !!},
            datasets: [{
                label: 'Disbursed (₹)',
                data: {!! json_encode(array_values($monthlyDisbursed)) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    const branchCtx = document.getElementById('branchChart');
    new Chart(branchCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($branchWise->pluck('branch.name')) !!},
            datasets: [{
                data: {!! json_encode($branchWise->pluck('total')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)'
                ]
            }]
        }
    });

    const repayCtx = document.getElementById('repaymentChart');
    new Chart(repayCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_map(fn($m) => date("M", mktime(0,0,0,$m,1)), range(1, 12))) !!},
            datasets: [
                {
                    label: 'Due (₹)',
                    data: {!! json_encode($repaymentTrend->pluck('total_due')) !!},
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: 'Paid (₹)',
                    data: {!! json_encode($repaymentTrend->pluck('total_paid')) !!},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        }
    });
</script>