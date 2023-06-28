<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Resumo Rifa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <style>
        .quadro {
            width: 25%;
            border: solid;
            border-width: thin;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            color: white;
            font-weight: bold;
        }

        .f-12 {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <center>
        <h3>{{ $rifa->name }}</h3>
    </center>
    <br>
    <div>
        <div class="quadro" style="float: left; background-color: #3366cc;">Disponível:
            {{ $rifa->qtdNumerosDisponiveis() }}</div>
        <div class="quadro" style="margin-left: 35%; background-color: #ccbd33;">Reservados:
            {{ $rifa->qtdNumerosReservados() }}</div>
        <div class="quadro" style="margin-left: 70%; background-color: #5ecc33;margin-top: -100px;">Pagos:
            {{ $rifa->qtdNumerosPagos() }}</div>
    </div>

    <br>

    @if ($rifa->participantes()->count() === 0)
        <center>
            <h4>Nenhum participante foi encontrado!</h4>
        </center>
    @else
        <table class="table table-striped f-12">
            <thead class="">
                <tr>
                    <th scope="col">Cota</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Celular</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rifa->numbersRelatorio() as $cota)
                    <tr>
                        <td class="text-center">{{ $cota->number }}</td>
                        <td class="text-center">{{ $cota->participante()->name }}</td>
                        <td class="text-center">{{ $cota->participante()->telephone }}</td>
                        <td class="text-center">{{ $cota->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
