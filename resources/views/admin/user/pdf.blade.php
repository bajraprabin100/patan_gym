<!DOCTYPE html>
<html>
<head>
    <style>
        table, td, th {
            border: 1px solid #000;
            text-align: left;
            font-size: 12px;
            font-weight: 200;
            padding: 5px;

        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .company h2, p {
            text-align: center;

        }

        div.breakNow {
            page-break-inside: avoid;
            page-break-after: always;
        }

        .table2 td {
            font-size: 12px;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>

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
Date From : <span style="text-transform: uppercase; padding-left: 5px;">{{$attribute['date_from']}}</span>
</li>
<li style="display: inline-block; margin:0; padding-left: 75px;">
Date To : <span style="text-transform: uppercase; padding-left: 5px;">{{$attribute['date_to']}}</span>
</li>
<li style="display: inline-block; margin:0; padding-left: 75px;">
Carrier : <span style="text-transform: uppercase; padding-left: 5px;">&nbsp;</span>
</li>
</ul>
<table style="border: none;">
<thead>
<tr>
<th>SN</th>
<th>Date</th>
<th>Particular</th>
<th>Debit.Amt</th>
<th>Credit.Amt</th>
</tr>
</thead>
<tbody style="border: none;">
<?php $i=1; ?>
@foreach($cash_book as $c)
<tr style="border: none;">
<td >{{$i++}}</td>
<td >{{$c->date}}</td>
<td >{{$c->particular}}</td>
<td >{{$c->debit_amount}}</td>
<td >{{$c->credit_amount}}</td>
</tr>
    @endforeach
</tbody>
</table>
</div>

</body>
</html>