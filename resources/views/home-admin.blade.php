@extends('layouts.admin')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Home</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <style>
        .dashboard-itens {
            display: flex;
            justify-content: space-between;
        }

        .dashboard-item {
            position: relative;
            width: 23%;
            height: 135px;
            background-color: #292727;
            border-radius: 10px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .dashboard-itens {
                flex-direction: column;
            }

            .dashboard-item {
                width: 100%;
                margin-bottom: 20px;
            }
        }

        

        .dashboard-item.profit {
            background-color: #0b400073;
            border: 1px solid #0e6495;
        }

        .dashboard-item.request {
            background-color: #234667;
            border: 1px solid #0e6495;
        }

        .dashboard-item.pending_request {
            background-color: #2e3b46;
            border: 1px solid #0e6495;
        }

        .dashboard-item.pending_entry {
            background-color: #1f3349;
            border: 1px solid #0e6495;
        }

        .dashboard-item-body {
            padding: 10px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-size: 1.5rem;
        }

        .dashboard-item-body p {
            margin-right: 10px;
            color: #f5f5f5;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;

        }

        .dashboard-item-body p:first-child {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .dashboard-item-body p:nth-child(2) {
            font-size: 1.1rem;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .dashboard-item-body i {
            position: absolute;
            font-size: 6rem;
            top: 20px;
            right: 11px;
            opacity: .2;
            color: #fff;
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <div class="dashboard-itens">
                <div class="dashboard-item profit block-copy">
                    <div class="dashboard-item-body">
                        <p>Lucro</p>
                        <p>R$ {{ number_format($cotasPagas->sum('valor'), 2, ",", ".") }}</p>
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                </div>

                <div class="dashboard-item request block-copy">
                    <div class="dashboard-item-body">
                        <p>Pedidos</p>
                        <p>{{ $pedidos->count() }}</p>
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                <div class="dashboard-item pending_request block-copy">
                    <div class="dashboard-item-body">
                        <p>Aguardando Pgto.</p>
                        <p>{{ $aguardandoPgto->count() }}</p>
                        <i class="fa-solid fa-hourglass"></i>
                    </div>
                </div>

                <div class="dashboard-item pending_entry block-copy">
                    <div class="dashboard-item-body">
                        <p>Entrada Pendente</p>
                        <p>R$ {{ number_format($aguardandoPgto->sum('valor'), 2, ",", ".") }}</p>
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                </div>
            </div>



            {{-- <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="bi bi-trophy"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Sorteios</span>
                        <span class="info-box-number">{{$countSweepstakes}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div> --}}
            <!-- /.row -->

            <!--<div class="card">
                                                        <div class="card-header ui-sortable-handle" style="cursor: move;">
                                                            <h3 class="card-title">
                                                                <i class="fas fa-chart-pie mr-1"></i>
                                                                VENDAS
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="myChart" height="10vh" width="20vw"></canvas>
                                                        </div>-->
        </div>
        </div><!-- /.container-fluid -->
    </section>

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"
        integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                                            <script>
                                                const DATA_COUNT = 7;
                                                const NUMBER_CFG = {
                                                    count: DATA_COUNT,
                                                    min: 0,
                                                    max: 100
                                                };
                                                const data = {
                                                    labels: ([<?php echo @$resultDate; ?>]),
                                                    datasets: [{
                                                        label: 'VENDAS',
                                                        data: ([<?php echo @$resultPayment; ?>]),
                                                        fill: true,
                                                        borderWidth: 1,
                                                        pointStyle: 'rectRot',
                                                        backgroundColor: 'rgb(1, 254, 0)',
                                                        pointRadius: 5,
                                                        pointBorderColor: 'rgb(0, 0, 0)'
                                                    }, ]
                                                };

                                                const config = {
                                                    type: 'line',
                                                    data: data,
                                                    options: {
                                                        plugins: {
                                                            legend: {
                                                                labels: {
                                                                    usePointStyle: true,
                                                                },
                                                            }
                                                        }
                                                    }
                                                };

                                                const actions = [{
                                                    name: 'Toggle Point Style',
                                                    handler(chart) {
                                                        chart.options.plugins.legend.labels.usePointStyle = !chart.options.plugins.legend.labels
                                                            .usePointStyle;
                                                        chart.update();
                                                    }
                                                }, ];

                                                const myChart = new Chart(
                                                    document.getElementById('myChart'),
                                                    config,
                                                );
                                            </script>-->
@endsection
