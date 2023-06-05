<?php

use dosamigos\chartjs\ChartJs;
use yii\helpers\Html;


$today = date('d/m/Y');

?>

<div class="utl-dashboard">
    <div class="row">
        <div class="col-lg-12">
            <?php echo  Html::a('ESPORTA XLS', ['export-excel'], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 10px']); ?>
            <?php echo  Html::a('ESPORTA PDF', ['export-pdf'], ['class' => 'btn btn-info', 'style' => 'margin-bottom: 10px', 'target' => '_blank']); ?>
            <h1 class="panel-title">GENERALE</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Eventi aperti il <?= $today; ?> / tipologia</h2>
                </div>
                <div class="panel-content" style="padding: 20px;">
                    <?php
                    echo ChartJs::widget([
                        'type' => 'pie',
                        'id' => 'evt_t',
                        'options' => [
                            'height' => 250,
                            'width' => 400,
                        ],
                        'data' => [
                            'radius' =>  "100%",
                            'labels' => array_map(function ($e) {
                                return $e['tipologia'] . ": " . $e['conteggio'];
                            }, $eventi_attivi_tipologia),
                            'datasets' => [
                                [
                                    'data' => array_map(function ($e) {
                                        return $e['conteggio'];
                                    }, $eventi_attivi_tipologia),
                                    'label' => '',
                                    'backgroundColor' => $colors,
                                    'borderWidth' => 1
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'legend' => [
                                'display' => true,
                                'position' => 'right',
                                'labels' => array_map(function ($e) {
                                    return $e['tipologia'] . ": " . $e['conteggio'];
                                }, $eventi_attivi_tipologia),
                                'labels' => [
                                    'fontSize' => 14,
                                    'fontColor' => "#425062",
                                ]
                            ],
                            'tooltips' => [
                                'enabled' => false,
                                'intersect' => true
                            ],
                            'hover' => [
                                'mode' => true
                            ],
                            'maintainAspectRatio' => false,

                        ],
                        'plugins' => new \yii\web\JsExpression('
                            [{
                                afterDatasetsDraw: function(chart, easing) {
                                    var ctx = chart.ctx;
                                    var totals = 0;
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                if(dataset.data[index] > 0) totals += dataset.data[index];
                                            })
                                        }
                                    })
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                // Draw the text in black, with the specified font
                                                ctx.fillStyle = \'rgb(0, 0, 0)\';

                                                var fontSize = 16;
                                                var fontStyle = \'normal\';
                                                var fontFamily = \'Helvetica\';
                                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                                                // Just naively convert to string for now
                                                var dataString = dataset.data[index];

                                                if(!dataString) return;

                                                // Make sure alignment settings are correct
                                                ctx.textAlign = \'center\';
                                                ctx.textBaseline = \'middle\';

                                                var padding = 5;
                                                var position = element.tooltipPosition();
                                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                            });
                                        }
                                    });
                                }
                            }]')
                    ]);

                    ?>
                </div>
            </div>
        </div>
        <?php if (Yii::$app->FilteredActions->showFieldProvincia) : ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Eventi aperti il <?= $today; ?> / provincia</h2>
                    </div>
                    <div class="panel-content" style="padding: 20px;">
                        <?php
                        echo ChartJs::widget([
                            'type' => 'pie',
                            'id' => 'evt_p',
                            'options' => [
                                'height' => 250,
                                'width' => 400,
                            ],
                            'data' => [
                                'radius' =>  "100%",
                                'labels' => array_map(function ($e) {
                                    return $e['sigla'] . ": " . $e['conteggio'];
                                }, $eventi_attivi_provincia),
                                'datasets' => [
                                    [
                                        'data' => array_map(function ($e) {
                                            return $e['conteggio'];
                                        }, $eventi_attivi_provincia),
                                        'label' => '',
                                        'backgroundColor' => $colors,
                                        'borderWidth' => 1
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => true,
                                    'position' => 'right',
                                    'labels' => array_map(function ($e) {
                                        return $e['sigla'] . ": " . $e['conteggio'];
                                    }, $eventi_attivi_provincia),
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",
                                    ]
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => true
                                ],
                                'maintainAspectRatio' => false,

                            ],
                            'plugins' => new \yii\web\JsExpression('
                            [{
                                afterDatasetsDraw: function(chart, easing) {
                                    var ctx = chart.ctx;
                                    var totals = 0;
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                if(dataset.data[index] > 0) totals += dataset.data[index];
                                            })
                                        }
                                    })
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                // Draw the text in black, with the specified font
                                                ctx.fillStyle = \'rgb(0, 0, 0)\';

                                                var fontSize = 16;
                                                var fontStyle = \'normal\';
                                                var fontFamily = \'Helvetica\';
                                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                                                // Just naively convert to string for now
                                                var dataString = dataset.data[index];

                                                if(!dataString) return;

                                                // Make sure alignment settings are correct
                                                ctx.textAlign = \'center\';
                                                ctx.textBaseline = \'middle\';

                                                var padding = 5;
                                                var position = element.tooltipPosition();
                                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                            });
                                        }
                                    });
                                }
                            }]')

                        ]);

                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php

    $eventi_aperti_oggi = null;
    $evt_a = null;

    foreach ($eventi_aperti as $e) {
        if ($e['ref'] == 'today') {
            $eventi_aperti_oggi = $e;
        } else {
            $evt_a = $e;
        }
    }

    ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Eventi aperti</h2>
                </div>
                <div class="panel-content" style="padding: 20px;">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p class="big_number"><?= $eventi_aperti_oggi['n']; ?></p>
                            <p class="text-center">Eventi aperti il <?= $today; ?></p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p class="main_diff text-success">
                                <?= $evt_a['n']; ?>
                            </p>
                            <p>Eventi attivi alle <?= date('H:i'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Eventi chiusi</h2>
                </div>
                <div class="panel-content" style="padding: 20px;">
                    <div class="row">
                        <div class="col-xs-12 col-sm12 col-md-12 col-lg-12">
                            <p class="big_number"><?= $eventi_chiusi[0]['n']; ?></p>
                            <p class="text-center">Eventi chiusi il <?= $today; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="panel-title">AIB</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Incendi gestiti il <?= $today; ?> / sottotipologia</h2>
                </div>
                <div class="panel-content" style="padding: 20px;">
                    <?php
                    echo ChartJs::widget([
                        'type' => 'pie',
                        'id' => 'inc_t',
                        'options' => [
                            'height' => 250,
                            'width' => 400,
                        ],
                        'data' => [
                            'radius' =>  "100%",
                            'labels' => array_map(function ($e) {
                                return $e['tipologia'] . ": " . $e['conteggio'];
                            }, $incendi_attivi_tipologia),
                            'datasets' => [
                                [
                                    'data' => array_map(function ($e) {
                                        return $e['conteggio'];
                                    }, $incendi_attivi_tipologia),
                                    'label' => '',
                                    'backgroundColor' => $colors,
                                    'borderWidth' => 1
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'legend' => [
                                'display' => true,
                                'position' => 'right',
                                'labels' => array_map(function ($e) {
                                    return $e['tipologia'] . ": " . $e['conteggio'];
                                }, $incendi_attivi_tipologia),
                                'labels' => [
                                    'fontSize' => 14,
                                    'fontColor' => "#425062",
                                ]
                            ],
                            'tooltips' => [
                                'enabled' => false,
                                'intersect' => true
                            ],
                            'hover' => [
                                'mode' => true
                            ],
                            'maintainAspectRatio' => false,

                        ],
                        'plugins' => new \yii\web\JsExpression('
                            [{
                                afterDatasetsDraw: function(chart, easing) {
                                    var ctx = chart.ctx;
                                    var totals = 0;
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                if(dataset.data[index] > 0) totals += dataset.data[index];
                                            })
                                        }
                                    })
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                // Draw the text in black, with the specified font
                                                ctx.fillStyle = \'rgb(0, 0, 0)\';

                                                var fontSize = 16;
                                                var fontStyle = \'normal\';
                                                var fontFamily = \'Helvetica\';
                                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                                                // Just naively convert to string for now
                                                var dataString = dataset.data[index];

                                                if(!dataString) return;

                                                // Make sure alignment settings are correct
                                                ctx.textAlign = \'center\';
                                                ctx.textBaseline = \'middle\';

                                                var padding = 5;
                                                var position = element.tooltipPosition();
                                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                            });
                                        }
                                    });
                                }
                            }]')
                    ]);

                    ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Incendi gestiti il <?= $today; ?> / ente gestore</h2>
                </div>
                <div class="panel-content" style="padding: 20px;">
                    <?php
                    echo ChartJs::widget([
                        'type' => 'pie',
                        'id' => 'inc_g',
                        'options' => [
                            'height' => 250,
                            'width' => 400,
                        ],
                        'data' => [
                            'radius' =>  "100%",
                            'labels' => array_map(function ($e) {
                                return $e['descrizione'] . ": " . $e['conteggio'];
                            }, $incendi_attivi_ente_gestore),
                            'datasets' => [
                                [
                                    'data' => array_map(function ($e) {
                                        return $e['conteggio'];
                                    }, $incendi_attivi_ente_gestore),
                                    'label' => '',
                                    'backgroundColor' => $colors,
                                    'borderWidth' => 1
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'legend' => [
                                'display' => true,
                                'position' => 'right',
                                'labels' => array_map(function ($e) {
                                    return $e['descrizione']; // . ": " . $e['conteggio'];
                                }, $incendi_attivi_ente_gestore),
                                'labels' => [
                                    'fontSize' => 14,
                                    'fontColor' => "#425062",
                                ]
                            ],
                            'tooltips' => [
                                'enabled' => false,
                                'intersect' => true
                            ],
                            'hover' => [
                                'mode' => true
                            ],
                            'maintainAspectRatio' => false,

                        ],
                        'plugins' => new \yii\web\JsExpression('
                            [{
                                afterDatasetsDraw: function(chart, easing) {
                                    var ctx = chart.ctx;
                                    var totals = 0;
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                if(dataset.data[index] > 0) totals += dataset.data[index];
                                            })
                                        }
                                    })
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                // Draw the text in black, with the specified font
                                                ctx.fillStyle = \'rgb(0, 0, 0)\';

                                                var fontSize = 16;
                                                var fontStyle = \'normal\';
                                                var fontFamily = \'Helvetica\';
                                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                                                // Just naively convert to string for now
                                                var dataString = dataset.data[index];

                                                if(!dataString) return;

                                                // Make sure alignment settings are correct
                                                ctx.textAlign = \'center\';
                                                ctx.textBaseline = \'middle\';

                                                var padding = 5;
                                                var position = element.tooltipPosition();
                                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                            });
                                        }
                                    });
                                }
                            }]')

                    ]);

                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php if (Yii::$app->FilteredActions->showFieldProvincia) : ?>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Incendi gestiti il <?= $today; ?> / provincia</h2>
                    </div>
                    <div class="panel-content" style="padding: 20px;">
                        <?php
                        echo ChartJs::widget([
                            'type' => 'pie',
                            'id' => 'inc_p',
                            'options' => [
                                'height' => 250,
                                'width' => 400,
                            ],
                            'data' => [
                                'radius' =>  "100%",
                                'labels' => array_map(function ($e) {
                                    return $e['sigla'] . ": " . $e['conteggio'];
                                }, $incendi_provincia),
                                'datasets' => [
                                    [
                                        'data' => array_map(function ($e) {
                                            return $e['conteggio'];
                                        }, $incendi_provincia),
                                        'label' => '',
                                        'backgroundColor' => $colors,
                                        'borderWidth' => 1
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => true,
                                    'position' => 'right',
                                    'labels' => array_map(function ($e) {
                                        return $e['sigla'] . ": " . $e['conteggio'];
                                    }, $incendi_provincia),
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",
                                    ]
                                ],
                                'tooltips' => [
                                    'enabled' => false,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => true
                                ],
                                'maintainAspectRatio' => false,

                            ],
                            'plugins' => new \yii\web\JsExpression('
                            [{
                                afterDatasetsDraw: function(chart, easing) {
                                    var ctx = chart.ctx;
                                    var totals = 0;
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                if(dataset.data[index] > 0) totals += dataset.data[index];
                                            })
                                        }
                                    })
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                // Draw the text in black, with the specified font
                                                ctx.fillStyle = \'rgb(0, 0, 0)\';

                                                var fontSize = 16;
                                                var fontStyle = \'normal\';
                                                var fontFamily = \'Helvetica\';
                                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                                                // Just naively convert to string for now
                                                var dataString = dataset.data[index];

                                                if(!dataString) return;

                                                // Make sure alignment settings are correct
                                                ctx.textAlign = \'center\';
                                                ctx.textBaseline = \'middle\';

                                                var padding = 5;
                                                var position = element.tooltipPosition();
                                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                            });
                                        }
                                    });
                                }
                            }]')
                        ]);

                        ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Incendi boschivi gestiti il <?= $today; ?> / provincia</h2>
                    </div>
                    <div class="panel-content" style="padding: 20px;">
                        <?php
                        echo ChartJs::widget([
                            'type' => 'pie',
                            'id' => 'inc_b_p',
                            'options' => [
                                'height' => 250,
                                'width' => 400,
                            ],
                            'data' => [
                                'radius' =>  "100%",
                                'labels' => array_map(function ($e) {
                                    return $e['sigla'] . ": " . $e['conteggio'];
                                }, $incendi_boschivi_provincia),
                                'datasets' => [
                                    [
                                        'data' => array_map(function ($e) {
                                            return $e['conteggio'];
                                        }, $incendi_boschivi_provincia),
                                        'label' => '',
                                        'backgroundColor' => $colors,
                                        'borderWidth' => 1
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => true,
                                    'position' => 'right',
                                    'labels' => array_map(function ($e) {
                                        return $e['sigla'] . ": " . $e['conteggio'];
                                    }, $incendi_boschivi_provincia),
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",
                                    ]
                                ],
                                'tooltips' => [
                                    'enabled' => false,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => true
                                ],
                                'maintainAspectRatio' => false,

                            ],
                            'plugins' => new \yii\web\JsExpression('
                            [{
                                afterDatasetsDraw: function(chart, easing) {
                                    var ctx = chart.ctx;
                                    var totals = 0;
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                if(dataset.data[index] > 0) totals += dataset.data[index];
                                            })
                                        }
                                    })
                                    chart.data.datasets.forEach(function (dataset, i) {
                                        var meta = chart.getDatasetMeta(i);
                                        if (!meta.hidden) {
                                            meta.data.forEach(function(element, index) {
                                                // Draw the text in black, with the specified font
                                                ctx.fillStyle = \'rgb(0, 0, 0)\';

                                                var fontSize = 16;
                                                var fontStyle = \'normal\';
                                                var fontFamily = \'Helvetica\';
                                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                                                // Just naively convert to string for now
                                                var dataString = dataset.data[index];

                                                if(!dataString) return;

                                                // Make sure alignment settings are correct
                                                ctx.textAlign = \'center\';
                                                ctx.textBaseline = \'middle\';

                                                var padding = 5;
                                                var position = element.tooltipPosition();
                                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                            });
                                        }
                                    });
                                }
                            }]')
                        ]);

                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row">
        <?php if (Yii::$app->FilteredActions->showFieldProvincia) : ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Mezzi impiegati (pickup / autobotti)</h2>
                    </div>
                    <div class="panel-content" style="padding: 20px;">
                        <?php
                        $n = 0;
                        foreach ($mezzi_impiegati as $mezzo) {

                        ?>
                            <p>
                                <span class="" style="width: 36px; height: 10px; display: inline-block; background-color: <?= $colors[$n]; ?>"></span>
                                <?= $mezzo['sigla'] . ": " . $mezzo['conteggio']; ?>
                            </p>
                        <?php
                            $n++;
                        }
                        ?>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Incendi con intervento del mezzo aereo</h2>
                    </div>
                    <div class="panel-content" style="padding: 20px;">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <p class="big_number"><?= $incendi_mezzo_aereo[0]['conteggio']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (Yii::$app->FilteredActions->showElicottero) : ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Lanci elicottero</h2>
                    </div>
                    <div class="panel-content" style="padding: 20px;">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <p class="big_number"><?= $lanci_elicottero[0]['numero_lanci']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Ore di volo</h2>
                    </div>
                    <div class="panel-content" style="padding: 20px;">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <p class="big_number"><?= $ore_di_volo[0]['ore_di_volo']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>