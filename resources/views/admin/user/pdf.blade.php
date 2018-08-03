<!DOCTYPE html>
<html>
<head>

</head>
<body>
<style>
    table, th, td {
        border: 1px solid black;
    }
</style>
<div>
    <div>
        <h2 style="width: 90%; font-weight: 500; text-transform: uppercase;font-size: 12px; margin: 0;">
            Patan Gym Rep By <span style="text-transform: uppercase;">(Patan Gym)</span>
        </h2>
        <div style="width: 90%; margin-top: 5px;">
            <div style="width: 50%; display: inline-block; font-size: 10px; font-weight: 300;">
                Gwarko, Lalitpur, Nepal
            </div>
            <div style="width: 50%; display: inline-block; font-size: 12px; font-weight: 300;">
                Cash book Entry <br>
                1 / 1
            </div>
        </div>
    </div>
    <div style="width: 100%; height: 1px; background-color: #000; margin: 3px 0;"></div>
    <ul style="font-size: 12px; font-weight: 300; list-style: none; padding-left: 0px; margin: 8px 0 0 0;">
        <li style="display: inline-block; margin:0; padding-left: 0px;">
            Date From : <span style="text-transform: uppercase; padding-left: 5px;">{{$attribute['year']}}</span>
        </li>
        <li style="display: inline-block; margin:0; padding-left: 75px;">
            Date To : <span style="text-transform: uppercase; padding-left: 5px;">{{$attribute['month']}}</span>
        </li>
        <li style="display: inline-block; margin:0; padding-left: 75px;">
            Carrier : <span style="text-transform: uppercase; padding-left: 5px;">&nbsp;</span>
        </li>
    </ul>


</div>
<div>
    <table id="tblOne" style="width:50%; float:left;border:1px solid;">
        <tr style="border:none; ">
            <td></td>
            <td></td>
            <td>Debit</td>
        </tr>
        <tr>
            <th>Date</th>
            <th>Particular</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody style="border: none;">
        <?php $i = 1; ?>
        <?php $debit_amount = 0; ?>
        @foreach($cash_book as $c)
            @if($c->debit_amount!=0)
                <tr style="border:1px solid;">
                    <td>{{$c->date}}</td>
                    <td>{{$c->particular}}</td>
                    <td>{{$c->debit_amount}}</td>
                </tr>
                <?php $debit_amount += $c->debit_amount; ?>
            @endif
        @endforeach
        <tr style="border:1px solid;">
            <td>Total</td>
            <td></td>
            <td>{{$debit_amount}}</td>
        </tr>
        </tbody>
    </table>
    <table id="tblTwo" style="width:50%; float:left;">
        <thead>
        <tr style="border:none; ">
            <td></td>
            <td></td>
            <td>Credit</td>
        </tr>
        <tr>
            <th>Date</th>
            <th>Particular</th>
            <th>Amount</th>
        </thead>
        <tbody style="border: none;">
        <?php $i = 1; ?>
        <?php $credit_amount = 0; ?>
        @foreach($cash_book as $c)
            @if($c->credit_amount!=0)

                <tr>
                    <td>{{$c->date}}</td>
                    <td>{{$c->particular}}</td>
                    <td>{{$c->credit_amount}}</td>
                </tr>
                <?php $credit_amount += $c->credit_amount; ?>
                @endif
                @endforeach
                </tr>
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td>{{$credit_amount}}</td>
                </tr>
        </tbody>
    </table>
</div>
</body>
</html>