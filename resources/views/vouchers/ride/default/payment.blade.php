<div style="padding: 2px 2px 0px 2px" class="card border-dark mb-3">
    <table class="table table-sm">
        <thead>
            <th class="align-bottom">FORMA DE PAGO</th>
            <th class="align-bottom"><center>VALOR</center></th>
            <th class="align-bottom">UNIDAD DE TIEMPO</th>
            <th class="align-bottom"><center>PLAZO</center></th>
        </thead>
      <tbody>
        @foreach ($voucher->payments as $payment)
            <tr>
              <td class="align-middle">{{ \ElectronicInvoicing\PaymentMethod::find($payment->payment_method_id)->name }}</td>
              <td class="text-right align-middle">{{ number_format($payment->total, 2, '.', '') }}</td>
              <td class="align-middle">{{ \ElectronicInvoicing\TimeUnit::find($payment->time_unit_id)->name }}</td>
              <td class="text-right align-middle">{{ number_format($payment->term, 2, '.', '') }}</td>
            </tr>
        @endforeach
      </tbody>
    </table>
</div>
