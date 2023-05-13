$(document).ready(function () {
	var windowH = $(window).height();
	var footerH = $("footer").innerHeight();
	console.log(footerH);
	$("#login_page").height(windowH - footerH);
});

//adds extra table rows
var i = $('table tr')['length'];
$('.addmore')['on']('click', function() {
    html = '<tr>';
    html += '<td><input class="case" type="checkbox"/></td>';
	//html += '<td>' + i + '</td>';
    html += '<td><input type="hidden" name="data[Invoice]['+(i-1)+'][itemId]" id="itemId_' + i + '" class="from-control"><input type="text" data-type="productCode" name="data[Invoice]['+(i-1)+'][itemNo]" id="itemNo_' + i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td><input type="text" data-type="productName" name="data[Invoice]['+(i-1)+'][itemName]" id="itemName_' + i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td><input type="text" name="data[Invoice]['+(i-1)+'][price]" id="price_' + i + '" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
    html += '<td><input type="text" name="data[Invoice]['+(i-1)+'][quantity]" id="quantity_' + i + '" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';
    html += '<td><input type="text" name="data[Invoice]['+(i-1)+'][total]" id="total_' + i + '" class="form-control totalLinePrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
    html += '</tr>';
    $('table')['append'](html);
    i++;
});
$('.delete')['on']('click', function() {
    $('.case:checkbox:checked')['parents']('tr')['remove']();
    $('#check_all')['prop']('checked', false);
});
$(document)['on']('change', '#check_all', function() {
    $('input[class=case]:checkbox')['prop']('checked', $(this)['is'](':checked'))
});
$('.delete')['on']('click', function() {
    $('.case:checkbox:checked')['parents']('tr')['remove']();
    $('#check_all')['prop']('checked', false);
    calculateTotal();
});
var prices = ['S10_1678|1969 Harley Davidson Ultimate Chopper|48.81', 'S10_1949|1952 Alpine Renault 1300|98.58', 'S10_2016|1996 Moto Guzzi 1100i|68.99', 'S10_4698|2003 Harley-Davidson Eagle Drag Bike|91.02', 'S10_4757|1972 Alfa Romeo GTA|85.68', 'S10_4962|1962 LanciaA Delta 16V|103.42', 'S12_1099|1968 Ford Mustang|95.34', 'S12_1108|2001 Ferrari Enzo|95.59', 'S12_1666|1958 Setra Bus|77.9', 'S12_2823|2002 Suzuki XREO|66.27', 'S12_3148|1969 Corvair Monza|89.14', 'S12_3380|1968 Dodge Charger|75.16', 'S12_3891|1969 Ford Falcon|83.05', 'S12_3990|1970 Plymouth Hemi Cuda|31.92', 'S12_4473|1957 Chevy Pickup|55.7', 'S12_4675|1969 Dodge Charger|58.73', 'S18_1097|1940 Ford Pickup Truck|58.33', 'S18_1129|1993 Mazda RX-7|83.51', 'S18_1342|1937 Lincoln Berline|60.62', 'S18_1367|1936 Mercedes-Benz 500K Special Roadster|24.26', 'S18_1589|1965 Aston Martin DB5|65.96', 'S18_1662|1980s Black Hawk Helicopter|77.27', 'S18_1749|1917 Grand Touring Sedan|86.7', 'S18_1889|1948 Porsche 356-A Roadster|53.9', 'S18_1984|1995 Honda Civic|93.89', 'S18_2238|1998 Chrysler Plymouth Prowler|101.51', 'S18_2248|1911 Ford Town Car|33.3', 'S18_2319|1964 Mercedes Tour Bus|74.86', 'S18_2325|1932 Model A Ford J-Coupe|58.48', 'S18_2432|1926 Ford Fire Engine|24.92', 'S18_2581|P-51-D Mustang|49', 'S18_2625|1936 Harley Davidson El Knucklehead|24.23', 'S18_2795|1928 Mercedes-Benz SSK|72.56', 'S18_2870|1999 Indy 500 Monte Carlo SS|56.76', 'S18_2949|1913 Ford Model T Speedster|60.78', 'S18_2957|1934 Ford V8 Coupe|34.35', 'S18_3029|1999 Yamaha Speed Boat|51.61', 'S18_3136|18th Century Vintage Horse Carriage|60.74', 'S18_3140|1903 Ford Model A|68.3', 'S18_3232|1992 Ferrari 360 Spider red|77.9', 'S18_3233|1985 Toyota Supra|57.01', 'S18_3259|Collectable Wooden Train|67.56', 'S18_3278|1969 Dodge Super Bee|49.05', 'S18_3320|1917 Maxwell Touring Car|57.54', 'S18_3482|1976 Ford Gran Torino|73.49', 'S18_3685|1948 Porsche Type 356 Roadster|62.16', 'S18_3782|1957 Vespa GS150|32.95', 'S18_3856|1941 Chevrolet Special Deluxe Cabriolet|64.58', 'S18_4027|1970 Triumph Spitfire|91.92', 'S18_4409|1932 Alfa Romeo 8C2300 Spider Sport|43.26', 'S18_4522|1904 Buick Runabout|52.66', 'S18_4600|1940s Ford truck|84.76', 'S18_4668|1939 Cadillac Limousine|23.14', 'S18_4721|1957 Corvette Convertible|69.93', 'S18_4933|1957 Ford Thunderbird|34.21', 'S24_1046|1970 Chevy Chevelle SS 454|49.24', 'S24_1444|1970 Dodge Coronet|32.37', 'S24_1578|1997 BMW R 1100 S|60.86', 'S24_1628|1966 Shelby Cobra 427 S/C|29.18', 'S24_1785|1928 British Royal Navy Airplane|66.74', 'S24_1937|1939 Chevrolet Deluxe Coupe|22.57', 'S24_2000|1960 BSA Gold Star DBD34|37.32', 'S24_2011|18th century schooner|82.34', 'S24_2022|1938 Cadillac V-16 Presidential Limousine|20.61', 'S24_2300|1962 Volkswagen Microbus|61.34', 'S24_2360|1982 Ducati 900 Monster|47.1', 'S24_2766|1949 Jaguar XK 120|47.25', 'S24_2840|1958 Chevy Corvette Limited Edition|15.91', 'S24_2841|1900s Vintage Bi-Plane|34.25', 'S24_2887|1952 Citroen-15CV|72.82', 'S24_2972|1982 Lamborghini Diablo|16.24', 'S24_3151|1912 Ford Model T Delivery Wagon|46.91', 'S24_3191|1969 Chevrolet Camaro Z28|50.51', 'S24_3371|1971 Alpine Renault 1600s|38.58', 'S24_3420|1937 Horch 930V Limousine|26.3', 'S24_3432|2002 Chevy Corvette|62.11', 'S24_3816|1940 Ford Delivery Sedan|48.64', 'S24_3856|1956 Porsche 356A Coupe|98.3', 'S24_3949|Corsair F4U ( Bird Cage)|29.34', 'S24_3969|1936 Mercedes Benz 500k Roadster|21.75', 'S24_4048|1992 Porsche Cayenne Turbo Silver|69.78', 'S24_4258|1936 Chrysler Airflow|57.46', 'S24_4278|1900s Vintage Tri-Plane|36.23', 'S24_4620|1961 Chevrolet Impala|32.33'];

$(document)['on']('focus', '.autocomplete_txt', function() {
    type = $(this)['data']('type');
	var type_name = '';
    if (type == 'productCode') {
        autoTypeNo = 0;
		type_name = 'code';
    };
    if (type == 'productName') {
        autoTypeNo = 1;
		type_name = 'name';
    };
    $(this)['autocomplete']({
        source: function(request, response) {
            /*var products = $['map'](prices, function(item) {
                var code = item['split']('|');
                return {
                    label: code[autoTypeNo],
                    value: code[autoTypeNo],
                    data: item
                };
            });
			//console.log("shreshth");
            response($['ui']['autocomplete']['filter'](products, request['term']));*/
			$.ajax({
				url : 'product_add_invoice.php',
				dataType: "json",
				//method: 'post',
				data: {
					term: request.term,
					type: type_name
				},
				success: function( data ) {
					response( $.map( data, function( item ) {
						console.log(data);
						var code = item.split("|");
							return {
								label: code[autoTypeNo],
								value: code[autoTypeNo],
								data : item
							}
						}));
					}
				});
        },
        autoFocus: true,
        minLength: 1,
        select: function(event, ui) {
            var product = ui['item']['data']['split']('|');
            id_arr = $(this)['attr']('id');
            id = id_arr['split']('_');
            element_id = id[id['length'] - 1];
            $('#itemId_' + element_id)['val'](product[4]);
            $('#itemNo_' + element_id)['val'](product[0]);
            $('#itemName_' + element_id)['val'](product[1]);
            $('#quantity_' + element_id)['val'](1);
            $('#price_' + element_id)['val'](parseFloat(product[2])['toFixed'](2));
            $('#total_' + element_id)['val']((1 * product[2])['toFixed'](2));
            calculateTotal();
        }
    });
});
$(document)['on']('change keyup blur', '.changesNo', function() {
    id_arr = $(this)['attr']('id');
    id = id_arr['split']('_');
    quantity = $('#quantity_' + id[1])['val']();
    price = $('#price_' + id[1])['val']();
    if (quantity != '' && price != '') {
        $('#total_' + id[1])['val']((parseFloat(price) * parseFloat(quantity))['toFixed'](2))
    };
    calculateTotal();
});
/*$(document)['on']('change keyup blur', '#tax', function() {
    calculateTotal()
});*/

function calculateTotal() {
    subTotal = 0;
    total = 0;
	total_with_cgst = 0;
    $('.totalLinePrice')['each'](function() {
        if ($(this)['val']() != '') {
            subTotal += parseFloat($(this)['val']());
        }
    });
    $('#subTotal')['val'](subTotal['toFixed'](2));
    cg_tax = $('#cgstTax')['val']();
    if (cg_tax != '' && typeof(cg_tax) != 'undefined') {
        cgstAmount = subTotal * (parseFloat(cg_tax) / 100);
        $('#cgstAmount')['val'](cgstAmount['toFixed'](2));
        total = subTotal + cgstAmount;
    } else {
        $('#cgstAmount')['val'](0);
        total = subTotal;
    };
	total_with_cgst = total;
	sg_tax = $('#sgstTax')['val']();
    if (sg_tax != '' && typeof(sg_tax) != 'undefined') {
        sgstAmount = subTotal * (parseFloat(sg_tax) / 100);
        $('#sgstAmount')['val'](sgstAmount['toFixed'](2));
        total = total_with_cgst + sgstAmount;
    } else {
        $('#sgstAmount')['val'](0);
        total = total_with_cgst;
    };
    $('#totalAftertax')['val'](total['toFixed'](2));
    calculateAmountDue();
}
$(document)['on']('change keyup blur', '#amountPaid', function() {
    calculateAmountDue()
});

function calculateAmountDue() {
    amountPaid = $('#amountPaid')['val']();
    total = $('#totalAftertax')['val']();
    if (amountPaid != '' && typeof(amountPaid) != 'undefined') {
        amountDue = parseFloat(total) - parseFloat(amountPaid);
        $('.amountDue')['val'](amountDue['toFixed'](2));
    } else {
        total = parseFloat(total)['toFixed'](2);
        $('.amountDue')['val'](total);
    };
}
var specialKeys = new Array();
specialKeys['push'](8, 46);

function IsNumeric(key) {
    var key_code = key['which'] ? key['which'] : key['keyCode'];
    console['log'](key_code);
    var rt_key = ((key_code >= 48 && key_code <= 57) || specialKeys['indexOf'](key_code) != -1);
    return rt_key;
}
/*$(function() {
    $['fn']['datepicker']['defaults']['format'] = 'dd-mm-yyyy';
    $('#invoiceDate')['datepicker']({
        startDate: '-3d',
        autoclose: true,
        clearBtn: true,
        todayHighlight: true
    });
});*/

/*$('#invoice-form')['submit'](function() {
    $('.txt')['each'](function(key, value) {
        newText = $(this)['val']();
        $(this)['val'](newText['replace'](/\r?\n/g, '<br />'));
    })
});*/

/*$('#clientCompanyName').autocomplete({
	source: function( request, response ) {
		$.ajax({
			url : 'ajax.php',
			dataType: "json",
			method: 'post',
			data: {
				name_startsWith: request.term,
				type: 'customerName'
			},
			success: function( data ) {
				response( $.map( data, function( item ) {
					var code = item.split("|");
						return {
							label: code[1],
							value: code[1],
							data : item
						}
					}));
				}
			});
	},
	autoFocus: true,	      	
	minLength: 1,
	select: function( event, ui ) {
		var names = ui.item.data.split("|");
		$(this).val(names[1]);
		getClientAddress(names[0]);
	}		      	
});
function getClientAddress(id){
	
	 $.ajax({
		 url: "ajax.php",
		 method: 'post', 
		 data:{id:id, type:'clientAddress'},
		 success: function(result){
			$("#clientAddress").html(result);
		}
	});
}*/