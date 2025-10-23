<?php

use ZakharovAndrew\shop\Module;
use yii\helpers\Html;
use ZakharovAndrew\shop\assets\ShopAssets;
ShopAssets::register($this);

/** @var yii\web\View $this */

$this->title = Module::t('Dashboard');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (Yii::$app->getModule('shop')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

<div class="flex flex-col md-flex-row gap-12 md-gap-6">
    <div class="w-full md-w-1/3">
        <div class="flex flex-col gap-3">
            <div class="flex-box">
                <div class="flex flex-col gap-y-1.5 p-4">
                    <h3>Добавленные товары</h3>
                </div>
                <div class="flex flex-col gap-y-1.5 p-4 pt-0">
                    <canvas id="productsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="w-full md-w-1/3">
        <div class="flex flex-col gap-3">
            <div class="flex-box">
                <div class="flex flex-col gap-y-1.5 p-4">
                    <h3>Обновленные товары</h3>
                </div>
                <div class="flex flex-col gap-y-1.5 p-4 pt-0">
                    <canvas id="updateProductsChart" width="400" height="200"></canvas>
                </div>
            </div>     
        </div>
    </div>
    
    <div class="w-full md-w-1/3">
        <div class="flex flex-col gap-3">
            <div class="flex-box">
                <div class="flex flex-col gap-y-1.5 p-4">
                    <h3>Title 3</h3>
                </div>
            </div>           
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('productsChart').getContext('2d');
    const ctx2 = document.getElementById('updateProductsChart').getContext('2d');
    
    const hours = Array.from({length: 24}, (_, i) => i);
    
    const addedData = new Array(24).fill(0);
    const updatedData = new Array(24).fill(0);
    
    <?php foreach ($addedProduct as $item): ?>
        addedData[<?= $item['h'] ?>] = <?= $item['cnt'] ?>;
    <?php endforeach; ?>
    
    <?php foreach ($updatedProduct as $item): ?>
        updatedData[<?= $item['h'] ?>] = <?= $item['cnt'] ?>;
    <?php endforeach; ?>
    
    // Создаем график
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: hours.map(h => h + ':00'),
            datasets: [
                {
                    label: 'Добавленные товары',
                    data: addedData,
                    borderColor: '#0088fe',
                    backgroundColor: '#5eb4fe8c',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                x: {
                    title: {
                        display: false,
                        text: 'Часы дня'
                    }
                },
                y: {
                    title: {
                        display: false,
                        text: 'Количество товаров'
                    },
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false,
                    text: 'Статистика товаров за сегодня'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            animation: {
                easing: 'linear'
            }
        }
    });
    
        // Создаем график
    const chart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: hours.map(h => h + ':00'),
            datasets: [
                {
                    label: 'Обновленные товары',
                    data: updatedData,
                    borderColor: '#0088fe',
                    backgroundColor: '#5eb4fe8c',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'nearest'
            },
            scales: {
                x: {
                    title: {
                        display: false,
                        text: 'Часы дня'
                    }
                },
                y: {
                    title: {
                        display: false,
                        text: 'Количество товаров'
                    },
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false,
                    text: 'Статистика товаров за сегодня'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            animation: {
                easing: 'linear' // Линейная анимация
            }
        }
    });
});
</script>