<script type="text/javascript">
$(document).ready(function(){
    @if($action === 'draft')
        var voucher = @json($draftVoucher);
    @elseif($action === 'edit')
        var voucher = {
            "additionaldetail_name": @json($voucher->additionalFields()->get()->pluck('name')),
            "additionaldetail_value": @json($voucher->additionalFields()->get()->pluck('value')),
            "tax": @json(\ElectronicInvoicing\RetentionTaxDescription::where('id', $voucher->retentions()->first()->details()->pluck('retention_tax_description_id'))->get()->pluck('retention_tax_id')),
            "description": @json(\ElectronicInvoicing\RetentionTaxDescription::where('id', $voucher->retentions()->first()->details()->pluck('retention_tax_description_id'))->get()),
        };
    @endif
    function addRowTax() {
        var _token = $('input[name = "_token"]').val();
        $.ajax({
            url: "{{ route('retentionTaxes.taxes') }}",
            method: "POST",
            data: {
                _token: _token
            },
            success: function(result) {
                var options = '';
                var taxes = JSON.parse(result);
                for (var i = 0; i < taxes.length; i++) {
                    options += '<option value="' + taxes[i]['id'] + '">' + taxes[i]['tax'] + '</option>';
                }
                retentionTable.row.add([
                    '<select class="form-control selectpicker" id="tax[]" name="tax[]" data-live-search="true" title="{{ trans_choice(__("view.select_a_model", ["model" => strtolower(__("view.tax"))]), 0) }}">' + options + '</select>',
                    '<select class="form-control selectpicker" id="description[]" name="description[]" data-live-search="true" title="{{ trans_choice(__("view.select_a_model", ["model" => strtolower(__("view.tax_description"))]), 1) }}"></select>',
                    '<input class="form-control" type="number" id="value[]" name="value[]" min="0.00" max="100.00" value="0.00" step="0.01">',
                    '<input class="form-control" type="text" id="tax_base[]" name="tax_base[]" value="0.00">',
                    '<input class="form-control" type="text" id="retained-value[]" name="retained-value[]" value="0.00" readonly>',
                    '<button type="button" class="btn btn-danger btn-sm">&times;</button>',
                ]).draw(false);
                $('select[id *= tax]').selectpicker();
                $('select[id *= description]').selectpicker();
                updateTotal();
            }
        });
    }
    var retentionTable = $('#retention-table').DataTable({
        paging: false,
        dom: 'Bfrtip',
        buttons: [{
            text: '{{ __("view.add_row") }}',
            action: function(e, dt, node, config){
                addRowTax();
            }
        }]
    });
    $('#retention-table tbody').on('changed.bs.select', 'select[id *= tax]', function(){
        var _token = $('input[name = "_token"]').val();
        var reference = $(this);
        var id = reference.closest('tr').find('select[id *= tax]').val();
        if (id != '') {
            $.ajax({
                url: "{{ route('retentionTaxes.taxDescriptions') }}",
                method: "POST",
                data: {
                    _token: _token,
                    id: id,
                },
                success: function(result) {
                    var options = '';
                    var taxDescriptions = JSON.parse(result);
                    for (var i = 0; i < taxDescriptions.length; i++) {
                        options += '<option value="' + taxDescriptions[i]['id'] + '">' + taxDescriptions[i]['code'] + ' - ' + taxDescriptions[i]['description'] + '</option>';
                    }
                    reference.closest('tr').find('select[id *= description]').html(options).selectpicker('refresh');
                    updateTotal();
                }
            });
        }
    });
    $('#retention-table tbody').on('changed.bs.select', 'select[id *= description]', function(){
        var _token = $('input[name = "_token"]').val();
        var reference = $(this);
        var id = reference.closest('tr').find('select[id *= description]').val();
        if (id != '') {
            $.ajax({
                url: "{{ route('retentionTaxDescriptions.taxDescription') }}",
                method: "POST",
                data: {
                    _token: _token,
                    id: id,
                },
                success: function(result) {
                    var options = '';
                    var taxDescription = JSON.parse(result);
                    reference.closest('tr').find('input[id *= value]').attr({
                        "min" : taxDescription[0]['min_rate'],
                        "max" : taxDescription[0]['max_rate'],
                        "value" : taxDescription[0]['rate']
                    });
                    updateRetainedValue(reference);
                }
            });
        }
    });
    function updateRetainedValue(reference) {
        var value = Number(reference.closest('tr').find('input[id *= value]').val());
        value = isNaN(value) ? 0.0 : value;
        var taxBase = Number(reference.closest('tr').find('input[id *= tax_base]').val())
        taxBase = isNaN(taxBase) ? 0.0 : taxBase;
        retainedValue = value * taxBase / 100.0;
        reference.closest('tr').find('input[id *= value]').val(value.toFixed(2));
        reference.closest('tr').find('input[id *= tax_base]').val(taxBase.toFixed(2));
        reference.closest('tr').find('input[id *= retained-value]').val(retainedValue.toFixed(2));
        updateTotal();
    }
    function updateTotal() {
        var retained_values = $.map($('input[id *= retained-value]'), function(option) {
            return Number(option.value);
        });
        var total_retained = retained_values.reduce(function(a, b) {
            return a + b;
        }, 0.0);
        $('#retention_total').val(total_retained.toFixed(2));
    }
    $('#retention-table tbody').on('change', 'input[id *= value]', function(){
        updateRetainedValue($(this));
    });
    $('#retention-table tbody').on('change', 'input[id *= tax_base]', function(){
        updateRetainedValue($(this));
    });
    $('#retention-table tbody').on('click', 'button.btn.btn-danger.btn-sm', function(){
        retentionTable
            .row($(this).parents('tr'))
            .remove()
            .draw();
        updateTotal();
    });
    $('#issue_date').change(function() {
        var issueDate = new Date($('#issue_date').val());
        if (!isNaN(issueDate.getTime())) {
            $('#fiscal_period').val(issueDate.getUTCFullYear() + '/' + ("00" + (issueDate.getUTCMonth() + 1)).slice(-2));
        }
    });
    var issueDate = new Date($('#issue_date').val());
    if (!isNaN(issueDate.getTime())) {
        $('#fiscal_period').val(issueDate.getUTCFullYear() + '/' + ("00" + (issueDate.getUTCMonth() + 1)).slice(-2));
    }
    $('#issue_date_support_document').datepicker({
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        endDate: '0d',
        format: 'yyyy/mm/dd',
        daysOfWeekHighlighted: "0,6"
    });
    var additionalDetailCount = 0;
    function addRowAdditionalDetail() {
        if (additionalDetailCount < 3) {
            additionalDetailTable.row.add([
                '<input class="form-control" type="text" id="additionaldetail_name[]" name="additionaldetail_name[]" value="">',
                '<input class="form-control" type="text" id="additionaldetail_value[]" name="additionaldetail_value[]" value="">',
                '<button type="button" class="btn btn-danger btn-sm">&times;</button>',
            ]).draw(false);
            additionalDetailCount++;
            @if($action === 'edit' || $action === 'draft')
                if ('additionaldetail_name' in voucher) {
                    if ($("input[id ~= 'additionaldetail_name[]']").length == voucher['additionaldetail_name'].length && voucher['additionaldetail_name'].length > 0) {
                        $("input[id ~= 'additionaldetail_name[]']").each(function() {
                            $(this).val(voucher['additionaldetail_name'][0]);
                            voucher['additionaldetail_name'].shift();
                        });
                    }
                }
                if ('additionaldetail_value' in voucher) {
                    if ($("input[id ~= 'additionaldetail_value[]']").length == voucher['additionaldetail_value'].length && voucher['additionaldetail_value'].length > 0) {
                        $("input[id ~= 'additionaldetail_value[]']").each(function() {
                            $(this).val(voucher['additionaldetail_value'][0]);
                            voucher['additionaldetail_value'].shift();
                        });
                    }
                }
            @endif
        }
    }
    var additionalDetailTable = $('#additionaldetail-table').DataTable({
        paging: false,
        dom: 'Bfrtip',
        searching: false,
        buttons: [{
            text: '{{ __("view.add_row") }}',
            action: function(e, dt, node, config){
                addRowAdditionalDetail();
            }
        }]
    });
    $('#additionaldetail-table tbody').on('click', 'button.btn.btn-danger.btn-sm', function(){
        additionalDetailTable
            .row($(this).parents('tr'))
            .remove()
            .draw();
        additionalDetailCount--;
    });
    $('#voucher_type_support_document').selectpicker();
});
</script>
