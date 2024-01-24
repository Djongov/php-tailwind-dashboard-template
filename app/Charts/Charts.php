<?php

namespace Charts;

use App\General;
use Charts\QuickChart;

class Charts
{
    // Radial Gauge good for measuring percentages or values out of max values
    public static function radialGauge($width, $height, $format, $label, $data, $range = [0, 100], $shortUrl = false)
    {
        $chart = new QuickChart([
            'width' => $width,
            'height' => $height,
            'format' => $format,
        ]);
        $background = 'getGradientFillHelper("horizontal", ["green", "lime"])';
        if ($data === $range[1]) {
            $background = '"red"';
        }
        $chart->setConfig('{
        type: "radialGauge",
        data: {
            datasets: [{
            data: [' . $data . '],
            backgroundColor: ' . $background . ',
            borderWidth: 0,
            label: "' . $label . '",
            }]
        },
        options: {
            // See https://github.com/pandameister/chartjs-chart-radial-gauge#options
            domain: [' . implode(',', $range) . '],
            trackColor: "rgb(204, 221, 238)",
            roundedCorners: false,
            legend: {},
            title: {
                display: true,
                text: "' . $label . '"
            },
            centerPercentage: 80,
            centerArea: {
                fontSize: 18,
                displayText: true,
                text: (val) => val + "/" + ' . $range[1] . ',
                subText: "",
                padding: 4,
                fontColor: \'#777\',
            },
            responsive: true,
            title: {
                display: true,
                fontSize: 18,
                text: \'' . $label . '\',
                color: \'#777\',
                align: \'center\',
                position: \'top\',
                fullSize: false
            },
            legend: {
                display: false,
                position: \'right\',
                align: \'top\',
                labels: {
                    fontColor: \'#777\',
                    fontStyle: \'bold\',
                    fontSize: 14,
                    padding: 12
                }
            },
        }
    }');
        return ($shortUrl) ?  '<figure class="m-1"><img src="' . $chart->getShortUrl() . '" title="' . $label . '" alt="' . $label . '" /></figure>' : '<figure class="m-1"><img src="' . $chart->getUrl() . '" title="' . $label . '" alt="' . $label . '" /></figure>';
    }
    // Donut or Pie chart in one
    public static function doughnutOrPieChart(string $type, string $title, array $labels, array $data, string|int $width = 300, string|int $height = 300, string $format = 'prng', bool $shortUrl = false)
    {
        $chart = new QuickChart([
            'width' => $width,
            'height' => $height,
            'format' => $format,
        ]);

        $var = '';
        foreach ($labels as $entry) {
            $var .= "'$entry',";
        }
        // Customize a little bit the colors based on stuff
        if ($title === 'maliciousConfidences') {
            $background_color_string = '[';
            foreach ($labels as $entry) {
                if ($title === 'maliciousConfidences') {
                    if ($entry <= 0) {
                        $background_color_string .= '\'lime\'' . PHP_EOL;
                    } elseif ($entry > 0 && $entry < 50) {
                        $background_color_string .= '\'green\'' . PHP_EOL;
                    } elseif ($entry >= 50 && $entry < 75) {
                        $background_color_string .= '\'orange\'' . PHP_EOL;
                    } elseif ($entry >= 75 && $entry <= 80) {
                        $background_color_string .= '\'crimson\'' . PHP_EOL;
                    } elseif (
                        $entry > 80 && $entry <= 100
                    ) {
                        $background_color_string .= '\'red\'' . PHP_EOL;
                    } else {
                        $background_color_string .= '\'blue\'' . PHP_EOL;
                    }
                    if (array_key_last($data)) {
                        $background_color_string .= ',';
                    }
                }
            }
            $background_color_string .= ']';
        } else {
            $background_color_string = '[
                \'rgba(54, 162, 235, 1)\', // blue
                \'rgba(75, 192, 192, 1)\', // green
                \'rgba(255, 99, 132, 1)\', // red
                \'rgba(255, 159, 64, 1)\', // orange
                \'rgba(153, 102, 255, 1)\', // purple
                \'rgba(255, 206, 86, 1)\', // yellow
                \'rgba(255, 0, 0, 1)\', // bright red
                \'rgba(0, 255, 255, 1)\', // cyan
                \'rgba(255, 0, 255, 1)\', // magenta
                \'rgba(128, 128, 128, 1)\' // grey
            ]';
        }

        $chart->setConfig('{
            type: "' . $type . '",
            data: {
                labels: [' . $var . '],
                datasets: [{
                    label: "' . $title . '",
                    backgroundColor: ' . $background_color_string . ',
                    data: [' . implode(",", $data) . '],
                    borderColor: \'rgba(0,0,0, 0.95)\',
                    borderWidth: 0,
                    weight: 600,
                    pointBackgroundColor: function(context) {
                        var index = context.dataIndex;
                        var value = context.dataset.data[index];
                        return value === \'DenyList\' ? \'green\' : \'blue\'
                    }
                }]
            },
            options: {
                    responsive: true,
                    title: {
                        display: true,
                        fontSize: 20,
                        text: \'' . $title . '\',
                        color: \'#777\',
                        align: \'center\',
                        position: \'top\',
                        fullSize: true
                    },
                    legend: {
                        display: true,
                        position: \'top\',
                        align: \'top\',
                        labels: {
                            fontColor: \'#777\',
                            fontStyle: \'bold\',
                            fontSize: 12,
                            padding: 12
                        }
                    },
                    plugins: {
                        doughnutlabel: {
                            labels: [
                                {
                                    text: \'' . array_sum($data) . '\',
                                    font: {
                                        size: \'30\',
                                        family: \'Arial, Helvetica, sans-serif\',
                                        weight: \'bold\'
                                    },
                                    backgroundColor: \'green\',
                                    color: \'#777\'
                                }
                            ]
                        },
                        datalabels: {
                            anchor: "center",
                            align: "center",
                            color: "white",
                            backgroundColor: "black",
                            borderColor: "black",
                            borderWidth: 1,
                            borderRadius: 6,
                            font: {
                                weight: \'bold\',
                                size: 12,
                            }
                        }
                    },
                }
            }');

        return ($shortUrl) ?  '<figure class="m-2"><img src="' . $chart->getShortUrl() . '" title="' . $title . '" alt="' . $title . '" width="' . $width . '" height="' . $height . '" /></figure>' : '<figure class="m-2"><img src="' . $chart->getUrl() . '" title="' . $title . '" alt="' . $title . '" width="' . $width . '" height="' . $height . '" /></figure>';
    }
    public static function lineChart(string|int $width, string|int $height, string $format, string $title, array $labels, array $data, bool $shortUrl = false): string
    {
        $chart = new QuickChart([
            'width' => $width,
            'height' => $height,
            'format' => $format,
        ]);
        $backgroundColorArray = [
            'rgba(54, 162, 235, 1)',    // blue
            'rgba(75, 192, 192, 1)',    // green
            'rgba(255, 99, 132, 1)',    // red
            'rgba(255, 159, 64, 1)',    // orange
            'rgba(153, 102, 255, 1)',   // purple
            'rgba(255, 206, 86, 1)',    // yellow
            'rgba(255, 0, 0, 1)',       // bright red
            'rgba(0, 255, 255, 1)',     // cyan
            'rgba(255, 0, 255, 1)',     // magenta
            'rgba(128, 128, 128, 1)',   // grey
            'rgba(0, 128, 0, 1)',       // greenish
            'rgba(255, 165, 0, 1)',     // orange-yellow
            'rgba(0, 0, 255, 1)',       // pure blue
            'rgba(255, 140, 0, 1)',     // dark orange
            'rgba(148, 0, 211, 1)',     // dark violet
            'rgba(255, 69, 0, 1)',      // red-orange
            'rgba(0, 255, 0, 1)',       // pure green
            'rgba(255, 215, 0, 1)',     // gold
            'rgba(0, 255, 127, 1)',     // spring green
            'rgba(255, 20, 147, 1)',    // deep pink
        ];

        // we need to figure out the target, it will be the first key of the $data[0] array
        $target = array_key_first($data[0]);
        // now we need to figure the count, it will be third key of the $data[0] array
        $count = array_key_last($data[0]);
        $datasets = '';
        foreach ($data as $index => $dataset_array) {
            foreach ($dataset_array as $key => $value) {
                if ($key === $target) {
                    $label_name = $value;
                }
                if ($key === $count) {
                    $count_string = $value;
                }
            }
            // we also want to make sure that the background color is the same for the same array entry
            if ($index >= 0 && $index < count($backgroundColorArray)) {
                $background_color = $backgroundColorArray[$index];
            } else {
                // Generate a random color
                $random_color = 'rgba(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ', 1)';
                $background_color = $random_color;
            }
            $datasets .= '
                {
                    label: \'' . $label_name . '\',
                    backgroundColor: \'' . $background_color . '\',
                    borderColor: \'' . $background_color . '\',
                    data: ' . $count_string . ',
                    fill: false,
                    tension: 0.1
                },
            ';
        }
        $chart->setConfig('{
            type: "line",
            data: {
                labels: [' . implode(",", $labels) . '],
                datasets: [
                ' . $datasets . '
            ]
            },
            options: {
                    responsive: true,
                    title: {
                        display: true,
                        fontSize: 20,
                        text: \'' . $title . '\',
                        color: \'black\',
                        align: \'center\',
                        position: \'top\',
                        fullSize: true
                    },
                    legend: {
                        display: true,
                        position: \'right\',
                        align: \'start\',
                        fontSize: 9
                    },
                }
        }');
        return ($shortUrl) ?  '<figure class="m-1"><img src="' . $chart->getShortUrl() . '" title="' . $title . '" alt="' . $title . '" /></figure>' : '<figure class="m-1"><img src="' . $chart->getUrl() . '" title="' . $title . '" alt="' . $title . '" /></figure>';
    }
    public static function formatTimeLineArrayForLineChart(array $array, string $format = 'm-d h:m'): array
    {
        $timeline = $array[0]["TimeGenerated"];

        $timeline = str_replace("[", "", $timeline);
        $timeline = str_replace("]", "", $timeline);
        $timeline = str_replace("\"", "", $timeline);

        $timelineArray = explode(",", $timeline);

        // Let's format the dates. Let's run all the timelineArray values through the GeneralMethods::convertToUTC() method
        foreach ($timelineArray as $index => $date) {
            $date = "' " . General::convertToUTC($date, $format) . "'";
            $timelineArray[$index] = $date;
        }
        return $timelineArray;
    }
}
