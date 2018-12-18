<div class="card border-dark">
    <table class="table table-sm">
      <tbody>
        <tr>
          <th class="align-middle">R.U.C.</th>
          <td class="align-middle">{{ $voucher->emissionPoint->branch->company->ruc }}</td>
        </tr>
        <tr>
          <th class="align-middle" colspan="2"><center>F A C T U R A</center></th>
        </tr>
        <tr>
          <th class="align-middle">No.</th>
          <td class="align-middle">{{ str_pad(strval($voucher->emissionPoint->branch->establishment), 3, '0', STR_PAD_LEFT) }}-{{ str_pad(strval($voucher->emissionPoint->code), 3, '0', STR_PAD_LEFT) }}-{{ str_pad(strval($voucher->sequential), 9, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
          <th class="align-middle" colspan="2">NÚMERO DE AUTORIZACIÓN</th>
        </tr>
        <tr>
            @php
            $voucherAccessKey = \DateTime::createFromFormat('Y-m-d', $voucher->issue_date)->format('dmY') .
                str_pad(strval(\ElectronicInvoicing\VoucherType::find($voucher->voucher_type_id)->code), 2, '0', STR_PAD_LEFT) .
                $voucher->emissionPoint->branch->company->ruc .
                $voucher->environment->code .
                str_pad(strval($voucher->emissionPoint->branch->establishment), 3, '0', STR_PAD_LEFT) .
                str_pad(strval($voucher->emissionPoint->code), 3, '0', STR_PAD_LEFT) .
                str_pad(strval($voucher->sequential), 9, '0', STR_PAD_LEFT) .
                str_pad(strval($voucher->numeric_code), 8, '0', STR_PAD_LEFT) .
                '1';
            $voucherAccessKey .= \ElectronicInvoicing\Http\Controllers\VoucherController::getCheckDigit($voucherAccessKey);
            @endphp
          <td class="align-middle" colspan="2">{{ $voucherAccessKey }}</td>
        </tr>
        <tr>
          <th class="align-middle">FECHA Y HORA DE AUTORIZACIÓN</th>
          <td class="align-middle">{{ $voucher->authorization_date }}</td>
        </tr>
        <tr>
          <th class="align-middle">AMBIENTE</th>
          <td class="align-middle">{{ \ElectronicInvoicing\Environment::find($voucher->environment_id)->name }}</td>
        </tr>
        <tr>
          <th class="align-middle">EMISIÓN</th>
          <td class="align-middle">NORMAL</td>
        </tr>
        <tr>
          <th class="align-middle" colspan="2">CLAVE DE ACCESO</th>
        </tr>
        <tr>
          <td class="align-middle" colspan="2"><img src='data:image/png;base64,{{ \DNS1D::getBarcodePNG($voucherAccessKey, "C128", 1.3, 60, array(0,0,0), true) }}' alt="barcode"   /></td>
        </tr>
      </tbody>
    </table>
</div>