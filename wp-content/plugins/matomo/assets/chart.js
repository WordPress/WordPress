jQuery(document).ready(function(){
    jQuery('.matomo-table[data-chart]').each(function() {
       let $this = jQuery(this);
       let $postbox = $this.parents('div.postbox');
       let $table = $postbox.find('table');
       $table.hide();
       let $canvas = jQuery('<canvas/>',{'id':$this.attr('data-chart')});
       $canvas.insertAfter($table);
       let data = [];
       let labels = [];
       let title = $postbox.find('h2').text();
       let $row;
       let value;
       $table.find('tr').each(function() {
           $row = jQuery(this);
           value = $row.find('td:nth-child(2)').text();
           if ( '-' === value ) {
               value = 0;
           }
           data.push(value);
           labels.push($row.find('td:nth-child(1)').text());
       });

        var myChart = new Chart($canvas, {
            type: 'line',
            data: {
                labels: labels.reverse(),
                datasets: [{
                    label: title,
                    data: data.reverse(),
                    borderColor: "#55bae7",
                    pointBackgroundColor: "#55bae7",
                    pointBorderColor: "#55bae7",
                    pointHoverBackgroundColor: "#55bae7",
                    pointHoverBorderColor: "#55bae7",
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
});
