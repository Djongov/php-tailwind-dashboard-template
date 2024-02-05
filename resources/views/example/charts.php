<?php

use Template\Html;

echo Html::h1('Charts', true);

echo Html::p('This is a chart page. Here is how we can use the charting abilities built into the system.', ['text-center']);

echo HTML::h2('Image charts (Quickchart.io)');

echo HTML::p('We can control which quickchart host we use by setting the QUICKCHART_HOST environment variable. Default is quickchart.io but you can host your own.');

echo HTML::p('shortUrls are used to shorten the URLs for the images so they can be used in emails for example. Also shortURLs are higher quality and are also only available at quickchart.io. ' . HTML::a("Read more", "https://quickchart.io/documentation/usage/short-urls-and-templates/#:~:text=To%20generate%20a%20short%20URL,.io%2Fchart%2Fcreate%20.&text=Go%20to%20the%20URL%20in,URLs%20to%20become%20active%20globally.", $theme, '_blank'));

use Charts\Charts;

echo '<div class="my-12 flex flex-wrap flex-row justify-center items-center">';
    // Pie chart
    echo '<div class="w-full md:w-1/2 lg:w-1/3 p-2">';

        echo HTML::h3('Pie chart');

        echo HTML::p('This is a pie chart.');

        echo Charts::doughnutOrPieChart('pie', 'Pie chart', ['January', 'February', 'March', 'April', 'May', 'June'], [12, 19, 3, 5, 2, 3]);

    echo '</div>';
    // Doughnut chart
    echo '<div class="w-full md:w-1/2 lg:w-1/3 p-2">';

        echo HTML::h3('Doughnut chart');

        echo HTML::p('This is a doughnut chart.');

        echo Charts::doughnutOrPieChart('doughnut', 'Doughnut chart', ['January', 'February', 'March', 'April', 'May', 'June'], [12, 19, 3, 5, 2, 3]);

    echo '</div>';
    // Radial gauge
    echo '<div class="w-full md:w-1/2 lg:w-1/3 p-2">';

        echo HTML::h3('Radial gauge');

        echo HTML::p('This is a Radial gauge.');

        $min = 0;

        $max = 100;

        $randomNumber = rand($min, $max);

        echo Charts::radialGauge('random number ouf of ' . $max, $randomNumber, [$min, $max]);

    echo '</div>';
    // Line chart
    echo '<div class="w-full md:w-1/2 lg:w-1/3 p-2">';

        echo HTML::h3('Line chart');

        echo HTML::p('This is a line chart.');

        $lineChartData = [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June'],
            'datasets' => [
                [
                    'label' => 'User 1 data',
                    'data' => [15, 22, 3, 52, 22, 3]
                ],
                [
                    'label' => 'User 2 data',
                    'data' => [12, 55, 33, 5, 2, 3]
                ]
            ]
        ];

        echo Charts::lineChart('Line chart', $lineChartData, 400, 200, 'svg');

    echo '</div>';
echo '</div>';

echo HTML::h2('Interactive charts (Chart.js)');

echo HTML::p('We can spawn interactive charts using Chart.js. This is a JavaScript library that allows us to create charts and graphs. We are passing the data to the JavaScript by using hidden inputs with the name "autoload".');

echo '<div id="doughnut-limits-holder" class="my-12 flex flex-wrap flex-row justify-center items-center">';
    // initiate an array that will pass the following data into hidden inputs so Javascript can have access to this data on page load and draw the charts
    $chartsArray = [
        [
            'type' => 'doughnut',
            'data' => [
                'parentDiv' => 'doughnut-limits-holder',
                'title' => 'Gauge chart',
                'width' => 180,
                'height' => 180,
                'labels' => ['used', 'unused'],
                'data' => [1, 5]
            ]
        ],
        [
            'type' => 'piechart',
            'data' => [
                'parentDiv' => 'doughnut-limits-holder',
                'title' => 'Pie Chart',
                'width' => 300,
                'height' => 300,
                'labels' => ['January', 'February', 'March', 'April', 'May', 'June'],
                'data' => [12, 19, 3, 5, 2, 3]
            ]
        ]
    ];
    // Now go through them and create an input hidden for each
    foreach ($chartsArray as $array) {
        echo '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
    }
echo '</div>';
