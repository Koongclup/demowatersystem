document.addEventListener('DOMContentLoaded', function() {
    fetchUsers();
    fetchWaterUsage();
    fetchBills();
    fetchUserCount();
    fetchWaterUsageData();
    fetchBillData();
});

function fetchUsers() {
    $.ajax({
        url: '../controllers/UserController.php',
        type: 'POST',
        data: { action: 'fetchAll' },
        dataType: 'json',
        success: function(response) {
            let rows = '';
            if (response) {
                response.forEach(user => {
                    rows += `<tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                    </tr>`;
                });
                $('#usersBody').html(rows);
                // Initialize DataTable after populating users
                $('#usersTable').DataTable();
            }
        }
    });
}

function fetchWaterUsage() {
    $.ajax({
        url: '../controllers/WaterUsageController.php',
        type: 'POST',
        data: { action: 'fetchAll' },
        dataType: 'json',
        success: function(response) {
            let rows = '';
            if (response) {
                response.forEach(usage => {
                    rows += `<tr>
                        <td>${usage.id}</td>
                        <td>${usage.user_id}</td>
                        <td>${usage.usage_date}</td>
                        <td>${usage.amount}</td>
                    </tr>`;
                });
                $('#waterUsageBody').html(rows);
                // Initialize DataTable after populating water usage
                $('#waterUsageTable').DataTable();
            }
        }
    });
}

function fetchBills() {
    $.ajax({
        url: '../controllers/BillController.php',
        type: 'POST',
        data: { action: 'fetchAll' },
        dataType: 'json',
        success: function(response) {
            let rows = '';
            if (response) {
                response.forEach(bill => {
                    rows += `<tr>
                        <td>${bill.id}</td>
                        <td>${bill.user_id}</td>
                        <td>${bill.billing_date}</td>
                        <td>${bill.total_amount}</td>
                        <td>${bill.status}</td>
                    </tr>`;
                });
                $('#billsBody').html(rows);
                // Initialize DataTable after populating bills
                $('#billsTable').DataTable();
            }
        }
    });
}

function fetchUserCount() {
    $.ajax({
        url: '../controllers/UserController.php',
        type: 'POST',
        data: { action: 'fetchCount' },
        dataType: 'json',
        success: function(response) {
            $('#userCount').html(`<h2>Total Users: ${response.count}</h2>`);
        }
    });
}

function fetchWaterUsageData() {
    $.ajax({
        url: '../controllers/WaterUsageController.php',
        type: 'POST',
        data: { action: 'fetchUsageData' },
        dataType: 'json',
        success: function(response) {
            const dates = response.map(item => item.usage_date);
            const totalUsage = response.map(item => item.total_usage);
            renderWaterUsageChart(dates, totalUsage);
        }
    });
}

function fetchBillData() {
    $.ajax({
        url: '../controllers/BillController.php',
        type: 'POST',
        data: { action: 'fetchBillData' },
        dataType: 'json',
        success: function(response) {
            const billingDates = response.map(item => item.billing_date);
            const totalBilled = response.map(item => item.total_billed);
            renderBillChart(billingDates, totalBilled);
        }
    });
}

function renderWaterUsageChart(dates, totalUsage) {
    const options = {
        chart: {
            type: 'line',
            height: 300 
        },
        series: [{
            name: 'Water Usage',
            data: totalUsage
        }],
        xaxis: {
            categories: dates
        }
    };
    const chart = new ApexCharts(document.querySelector("#waterUsageChart"), options);
    chart.render();
}

function renderBillChart(billingDates, totalBilled) {
    const options = {
        chart: {
            type: 'bar',
            height: 300 
        },
        series: [{
            name: 'Total Billed',
            data: totalBilled
        }],
        xaxis: {
            categories: billingDates
        }
    };
    const chart = new ApexCharts(document.querySelector("#billChart"), options);
    chart.render();
}
