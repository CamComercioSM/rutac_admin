if (CHARTS) 
{
    CHARTS.forEach(item => {
        var chart = new ApexCharts(document.querySelector("#" + item.id), item.options);
        chart.render();
    });
}