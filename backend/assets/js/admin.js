function toggle(collection, item) {
	var idx = _.indexOf(collection, item);
	if (idx !== -1) {
		collection.splice(idx, 1);
	} else {
		collection.push(item);
	}
}

// Validate
function validateEmail(email) {
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(String(email).toLowerCase());
}

function download_csv(csv, filename) {
	var csvFile;
	var downloadLink;
	// CSV FILE
	csvFile = new Blob([csv], {
		type: "text/csv"
	});
	downloadLink = document.createElement("a");
	downloadLink.download = filename;
	downloadLink.href = window.URL.createObjectURL(csvFile);
	downloadLink.style.display = "none";
	document.body.appendChild(downloadLink);
	downloadLink.click();
	document.body.removeChild(downloadLink);
}

function lsdd_currency_format(number, symbol = false) {
	currency = lsdc_admin.currency;

	let currency_rule = [];
	currency_rule['IDR'] = ['id-ID', 'Rp '];
	currency_rule['MYR'] = ['id-ID', 'RM '];
	currency_rule['SGD'] = ['id-ID', 'S$'];
	currency_rule['USD'] = ['en-US', '$'];

	number = parseInt(number);

	if (isNaN(number))
		return 0; // Return Zero if NaN

	let selected = currency_rule[currency];

	if (symbol) {
		return selected[1] + number.toLocaleString(selected[0]);
		// Expected : Rp 10.000 | $10,000
	} else {
		return number.toLocaleString(selected[0]);
		// Expected : 10.000 | 10,000
	}

}

function lsdd_date_now() {
	var date = new Date();
	var month = ('0' + (date.getMonth() + 1)).slice(-2);
	var day = ('0' + date.getDate()).slice(-2);
	var year = date.getFullYear();
	return year + "-" + month + "-" + day;
}

/**
 * https://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
 * @param string name 
 * @param url url 
 */
function lsdd_get_url_param(name, url = window.location.href) {
	name = name.replace(/[\[\]]/g, '\\$&');
	var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

// For upercase each first character in words
String.prototype.ucwords = function () {
	str = this.toLowerCase();
	return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
		function ($1) {
			return $1.toUpperCase();
		});
}

function lsdd_pad(num, size) {
	num = num.toString();
	while (num.length < size) num = "0" + num;
	return num;
}



(function ($) {
	'use strict';

	// $(document).on('ready', function (event) {
	// 	$(".selecttwo").select2({
	// 		allowClear: true
	// 	});
	// });

	/**
	 * Only Load in Tab Apperance
	 * Checking Google Font Cache
	 */
	if (lsdd_get_url_param('tab') == 'appearance') {
		if (localStorage.getItem("lsdd_font_cache") == null || localStorage.getItem("lsdd_font_cache") == '') {
			jQuery.getJSON("https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCoDdOKhPem_sbA-bDgJ_-4cVhJyekWk-U", function (fonts) {
				var lsdd_font_cache = {};
				for (var i = 0; i < fonts.items.length; i++) {
					lsdd_font_cache[fonts.items[i].family] = fonts.items[i].files.regular;
				}
				localStorage.setItem("lsdd_font_cache", JSON.stringify(lsdd_font_cache));
			});
		} else {
			var lsdd_font_cache = JSON.parse(localStorage.getItem("lsdd_font_cache"));
			var selectedfont = jQuery('#selectedfont').text();
			jQuery.each(lsdd_font_cache, function (index, value) {
				jQuery('#fontlist')
					.remove("option")
					.append(jQuery((index == selectedfont) ? "<option selected></option>" : "<option></option>")
						.attr("value", index)
						.attr("style", "font-family:" + index + "; font-size: 16px")
						.text(index));
			});
		}
	}

	$(function () {
		$('.lsdd-color-picker').wpColorPicker();
		$(".lsdd-email-picker").wpColorPicker({
			change: function (event, ui) {
				var element = event.target;
				var color = ui.color.toString();
				var type = $(element).attr('data-type');
				$('#lsdd-editor-' + type).find('table[role="presentation"]:first').css('background', color);
			}
		});
	});
	//=============== Admin - General ===============//

	/**
	 * Input Currency Formatting
	 */
	$(document).on('keyup', 'input.currency', function (event) {
		// skip for arrow keys
		if (event.which >= 37 && event.which <= 40) return;

		let separator = ".";
		if (lsdc_admin.currency == 'USD') separator = ",";

		// currency_validate
		$(this).val(function (index, value) {
			return value
				.replace(/\D/g, "")
				.replace(/^0+/, '') // Removing Leading by Zero
				.replace(/\B(?=(\d{3})+(?!\d))/g, separator);
		});
	});

	// DeepLink Tab
	$(document).ready(function () {

		let url = location.href.replace(/\/$/, "");
		if (location.hash) {
			const hash = url.split("#"); //split url
			const querystring = url.split("tab=");

			if (querystring) {
				let indentify = querystring.pop().split('#')[0];
				if (indentify) {
					$('#' + indentify).find('input[name="sections"]').prop('checked', false); // reset
					$('#' + indentify).find('input[name="sections"]#' + hash[1]).prop('checked', true); //set
					url = location.href.replace(/\/#/, "#");
					history.replaceState(null, null, url);
					setTimeout(() => {
						$(window).scrollTop(0);
					}, 400);
				}
			}

		} else { // Set Default Tab
			if (url.split("tab=")[1]) {
				$('#' + url.split("tab=")[1] + '.verticaltab').find('input:first').prop('checked', true); // reset
			}
		}

		// Handle Click Tab Deeplinking
		$(document).on("click", ".verticaltab .tablabel", function (e) {
			let newUrl;
			const hash = $(this).attr("data-linking");
			if (hash == "#home") {
				newUrl = url.split("#")[0];
			} else {
				newUrl = url.split("#")[0] + '#' + hash;
			}
			newUrl += "/";
			history.replaceState(null, null, newUrl);
		});

	});

	//=============== Admin - Store ===============//
	$(document).on("change", "#country", function (e) {

		$.get(lsdc_admin.plugin_url + 'assets/cache/' + $(this).val() + '-states.json', function (data, status) {
			// alert("Data: " + data + "\nStatus: " + status);
			console.log(data);
			$("#states option").remove();
			$("#cities option").remove();
			$.each(data, function (i, value) {
				var option = $('<option value="' + value.province_id + '">' + value.province + '</option>');
				$("#states").append(option);
			});

		});
	});

	$(document).on("change", "#states", function (e) {
		$.get(lsdc_admin.plugin_url + 'assets/cache/' + $('#country').find(":selected").val() + '-cities.json', function (data, status) {
			$("#cities option").remove();
			$.each(data, function (i, value) {
				if ($('#states').find(":selected").val() == value.province_id) {
					var option = $('<option value="' + value.city_id + '">' + value.type + ' ' + value.city_name + '</option>');
					$("#cities").append(option);
				}
			});
		});
	});

	$(document).on("click", "#lsdc_admin_store_save", function (e) {
		$(this).addClass('loading');
		var that = this;

		let store = {};
		store['lsdd_store_country'] = $('#country').find(":selected").val();
		store['lsdd_store_state'] = $('#states').find(":selected").val();
		store['lsdd_store_city'] = $('#cities').find(":selected").val();
		store['lsdd_store_address'] = $('#address').val();
		store['lsdd_store_postalcode'] = $('#postalcode').val();
		store['lsdd_store_currency'] = $('#currency').find(":selected").val();

		// $.post( lsdc_admin.ajax_url, { 
		// 	action : 'lsdc_admin_store_save',
		// 	store : store,
		// 	security : lsdc_admin.ajax_nonce,
		// 	}, function( response ){
		// 		if( response.trim() == 'action_success' ){
		// 			$(that).removeClass('loading');
		// 		}
		// 	}).fail(function(){
		// 		alert('Please check your internet connection');
		// 	});
	});

	//=============== Admin - Appearance ===============//

	$(document).on("click", "#lsdd-admin-apperance-save", function (e) {
		$(this).addClass('loading');
		var that = this;

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdc_admin_appearance_save',
			appearance: $("#appearance form").serialize(),
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				$(that).removeClass('loading');
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	//=============== Admin - Payments ===============//

	// Enabled
	$(document).on("change", ".lsdd-payment-status", function (e) {

		let id = $(this).find('input[type="checkbox"]').attr('id');
		let state = ($(this).find('input[type="checkbox"]').is(":checked")) ? 'on' : 'off';

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdc_admin_payment_status',
			id: id,
			state: state,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				// give feedback
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});

	});

	// Manage
	$(document).on("click", ".lsdd-payment-manage", function (e) {
		if ($('form#' + $(this).attr('id') + '_form').length == 0) { // Checking Cache DOM
			let html = $('#' + $(this).attr('id') + '_content').html();
			// Manipulate InnerHTML
			var $html = $('<div />', {
				html: html
			});
			$html.find('form').attr("id", $(this).attr('id') + '_form'); // Change ID
			$('#payment-editor').html($html.html());


			// $(".selectlive" ).select2({
			// 	allowClear: true,
			// 	width: '100%',

			// }); 

		}

		$('#payment-editor').closest('div.column').show();
		$('#payment-editor').closest('div.column').css('z-index', '9999');
	});

	// Close Panel
	$(document).on("click", ".panel-close", function (e) {
		$('#payment-editor').closest('div.column').hide();
		$('#payment-editor').closest('div.column').css('z-index', '0');
		$('#payment-editor').html('');

		// $(".selectlive").select2('destroy');
	});

	// Payment Save
	$(document).on("click", ".lsdd-payment-save", function (e) {
		e.preventDefault();
		$(this).addClass('loading');
		var that = this;
		var serialize = $(this).closest('#payment-editor').find('.panel-body form').serialize();
		var id = $(this).attr('id').replace('_payment', '');
		var method = $(this).attr('method');


		$.post(lsdc_admin.ajax_url, {
			action: 'lsdd_payment_settings',
			method: method,
			id: id,
			serialize: serialize,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				// $(that).removeClass('loading');
				// $('#payment-editor').closest('div.column').hide();
				// $('#payment-editor').closest('div.column').css('z-index','1');
				location.reload();
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	// Handle Upload Image
	var file_frame;
	var attachment;
	$(document).on("click", ".lsdc_admin_upload", function (event) {
		event.preventDefault();
		var that = this;
		var frame = file_frame;
		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media.frames.frame = wp.media({
			// title: 'Upload Image',
			// 	button: {
			// 	text: 'Choose Image'
			// },
			multiple: false
		});

		frame.on('select', function () {
			attachment = frame.state().get('selection').first().toJSON();
			$(that).prev().prev().attr('src', attachment.url);
			$(that).prev().attr('value', attachment.url);
		});

		frame.open();
	});

	//=============== Admin - Notification ===============//
	// Enabled
	$(document).on("change", ".lsdd-notification-status", function (e) {

		let id = $(this).find('input[type="checkbox"]').attr('id');
		let state = ($(this).find('input[type="checkbox"]').is(":checked")) ? 'on' : 'off';

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdc_admin_notification_status',
			id: id,
			state: state,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				// give feedback
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});

	});

	//=============== Admin - Shipping ===============//
	// Enabled
	$(document).on("change", ".lsdd-shipping-status", function (e) {

		let id = $(this).find('input[type="checkbox"]').attr('id');
		let state = ($(this).find('input[type="checkbox"]').is(":checked")) ? 'on' : 'off';

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdc_admin_shipping_status',
			id: id,
			state: state,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				// give feedback
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});

	});

	//=============== Admin - Settings ===============//

	$(document).on("click", "#lsdc_admin_settings_save", function (e) {
		e.preventDefault();
		$(this).addClass('loading');
		var that = this;

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdc_admin_settings_save',
			settings: $("#settings form").serialize(),
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				$(that).removeClass('loading');
			} else {
				location.reload();
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	// ============= Admin - General Save Settings ============= //
	$(document).on("click", ".lsdc_admin_option_save", function (e) {
		e.preventDefault();
		$(this).addClass('loading');
		var that = this;
		var option = $(this).attr('option');
		var data = $(this).closest('form').serialize();
		var block = $(this).closest('form').attr('block');

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdc_admin_option_save',
			option: option,
			settings: data,
			block: block,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				$(that).removeClass('loading');
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	// =================== Institution Settings =================== //
	$(document).on("click", "#lsdd_institution_settings_save", function (e) {
		e.preventDefault();
		$(this).addClass('loading');
		var that = this;
		console.log($("#settings form").serialize());

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdd_institution_settings_save',
			settings: $("#settings form").serialize(),
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			if (response.trim() == 'action_success') {
				$(that).removeClass('loading');
			} else {
				location.reload();
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	// ------------------- Notification ----------------------//

	// ------------------- Report - Complete ----------------------//
	$(document).on("click", ".lsdd_report_complete", function () {
		let id = $(this).closest('.action').attr('id');
		$(this).addClass('loading');
		var that = $(this);

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdd_report_action',
			act: 'completed',
			id: id,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			console.log(response);
			if (response.trim() == 'action_success') {
				that.closest('.action').prev().find('span').addClass('label-success').removeClass('label-warning').text('Completed');
				that.remove();
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});
	// ------------------- Report - Delete ----------------------//

	$(document).on("click", ".lsdd_report_delete", function () {
		if (confirm(lsdc_admin.translation.delete_report)) {
			let id = $(this).closest('.action').attr('id');
			$(this).addClass('loading');
			var that = $(this);

			$.post(lsdc_admin.ajax_url, {
				action: 'lsdd_report_action',
				act: 'delete',
				id: id,
				security: lsdc_admin.ajax_nonce,
			}, function (response) {
				if (response.trim() == 'action_success') {
					that.closest('tr').remove();
				}
			}).fail(function () {
				alert('Please check your internet connection');
			});
		}
	});
	// ------------------- Report - Edit ----------------------//
	// Edit Entry Data v.2.1.0
	$(document).on("click", ".lsdd_report_edit", function (e) {
		let report_editor = $('#report_editor');
		//Call Panel
		report_editor.closest('div.column').show();
		report_editor.closest('div.column').css('z-index', '9999');
		let id = $(this).closest('.action').attr('id');

		$('#lsdd_report_update').attr('data-id', id);

		if (id) {
			$('.panel-title').text('Edit Report #' + id);
		}

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdd_report_action',
			act: 'read',
			id: id,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			var data = $.parseJSON(response)[0];

			report_editor.find('.panel-body #name').val(data.name);
			report_editor.find('.panel-body #phone').val(data.phone);
			report_editor.find('.panel-body #total').val(data.total);
			report_editor.find('.panel-body #status').val(data.status);

			var date = new Date(data.date);
			var month = ('0' + (date.getMonth() + 1)).slice(-2);
			var day = ('0' + date.getDate()).slice(-2);
			var year = date.getFullYear();
			var htmlDate = year + '-' + month + '-' + day;

			report_editor.find('.panel-body #date').val(htmlDate);
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});


	$(document).on("click", ".panel-close", function (e) {
		$('.panel').closest('div.column').hide();
	});

	// ------------------- Report - Update Report ----------------------//	
	// Update Entry Data v.2.1.0
	$(document).on("click", "#lsdd_report_update", function (e) {
		let id = $(this).attr('data-id');
		let report_editor = $('#report_editor');
		$(this).addClass('loading');


		$.post(lsdc_admin.ajax_url, {
			action: 'lsdd_report_action',
			act: 'update',
			id: id,
			update_data: {
				'name': report_editor.find('.panel-body #name').val(),
				'phone': report_editor.find('.panel-body #phone').val(),
				'status': report_editor.find('.panel-body #status').val(),
				'date': report_editor.find('.panel-body #date').val()
			},
			security: lsdc_admin.ajax_nonce,
		}, function (response) {

			if (response.trim() == 'action_success') {
				location.reload();
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	/* Bulk Action */
	let stackID = [];

	function selectAll(item) {
		$('thead.multiple_action').show();

		$("#selectall").prop('indeterminate', true);
		$('.selectedId').prop('checked', true);

		// Set All ID
		$('.selectedId').each(function () {
			if ($(this).attr('id')) toggle(stackID, $(this).attr('id'));
		});
		$('.bulk-action').attr('data-badge', stackID.length);
		$('.bulk-action').attr('data-id', stackID);
		console.log(stackID);

	}

	function unselectAll() {
		$("#selectall").prop('indeterminate', false);
		$("#selectall").prop('checked', false);
		$('.selectedId').prop('checked', false);
		stackID = [];
		$('.bulk-action').attr('data-badge', '');
		$('.bulk-action').attr('data-id', '');
		$('thead.multiple_action').hide();
	}

	$(document).on("click", "#selectall", function (e) {
		return (this.tog = !this.tog) ? selectAll() : unselectAll();
	});

	$(document).on("change", ".selectedId", function (e) {
		var check = ($('.selectedId').filter(":checked").length == $('.selectedId').length);
		$('#selectall').prop("checked", check);
		$("#selectall").prop('indeterminate', false);
		$("#selectall").prop('checked', true);
		// Set All ID Selected.
		if ($(this).attr('id')) toggle(stackID, $(this).attr('id'));
		$('.bulk-action').attr('data-badge', stackID.length);
		$('.bulk-action').attr('data-id', stackID);
		$('thead.multiple_action').show();
		// Stack Null
		if (stackID.length == 0) {
			$("#selectall").prop('checked', false);
			$('.bulk-action').attr('data-badge', '');
			$('thead.multiple_action').hide();
		}
	});

	// ------------------- Report -  Bulk Action ----------------------//	
	// Bulk Action
	$(document).on("click", ".bulk-action", function (e) {
		$(this).text('...');
		var that = this;

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdd_report_bulk_action',
			act: $(this).attr('id'),
			data: $(this).attr('data-id'),
			security: lsdc_admin.ajax_nonce,
		}, function (response) {

			if (response.trim() == 'action_success') {
				location.reload();
			}
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	// ------------------- Report -  Export  ----------------------//	
	$(document).on("click", "#lsdd_export", function (e) {
		$(this).addClass('loading');
		let filter = $(this).prev('select').find(":selected").val();
		var that = this;

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdd_report_export_action',
			filter: filter,
			security: lsdc_admin.ajax_nonce,
		}, function (response) {
			var data = $.parseJSON(response);
			console.log(data);
			let ExportStack = [data.header];

			$.each(data.content, function (i, row) {
				var newrow = [];
				$.each(row, function (i, cell) {
					newrow.push(cell);
				});
				ExportStack.push(Object.values(newrow));
			});
			download_csv(ExportStack.join("\n"), data.name + '.csv');
			$(that).removeClass('loading');
		}).fail(function () {
			alert('Please check your internet connection');
		});
	});

	/**
	 * LSDDonation > Reports
	 * Reports > Import
	 * 3.0.4 : Passed and Tested Manual
	 */
	$(document).on("click", "#import-click", function (w) {
		let filename = $("#import-data").val().toLowerCase();
		filename = filename.replace(/ /g, '');
		filename = filename.replace(/ *\([^)]*\) */g, "");
		let that = this;

		var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv)$/;
		if (regex.test(filename)) {
			if (typeof (FileReader) != "undefined") {
				$(this).addClass('loading'); //

				var reader = new FileReader(); //Read File

				reader.onload = function (e) {

					var rows = e.target.result.split("\n"); //getting rows
					var newRows = []; // setup object

					for (var i = 1; i < rows.length; i++) { // Start From Data, 0 is Header
						var item = rows[i].split(","); // item is array
						var newRow = [];


						if (item.length > 1) { //count item, and more than 1 = valid

							// Itenerate the item
							for (var position = 0; position < item.length; position++) { // itenerate Cell
								if (item[0] != "") { // checking report id
									if (position == 4) { //on Cell Phone
										// == Adjust Phone
										if (item[4][0] != 0) { // Check : First Char in Value == 0
											newRow[4] = '0' + item[4]; // Add Zero
										} else {
											newRow[4] = item[4]; // Skip
										}
										// == Adjust Phone
									} else {
										newRow[position] = item[position].replace(/(\r\n|\n|\r)/gm, ""); //Trim
									}

								}
							}
						}
						newRows.push(newRow); // Put Row 
					}
					// Sending Data Import
					$.post(lsdc_admin.ajax_url, {
						action: 'lsdd_report_import_action',
						data: JSON.stringify(newRows),
						security: lsdc_admin.ajax_nonce,
					}, function (response) {

						response = JSON.parse(response);
						$('#TB_closeWindowButton').click();
						lsd_push_notification('LSDDonation Report - Import', response.message);
						$(that).removeClass('loading');
						window.location.reload();
					}).fail(function () {
						alert('Please check your internet connection');
					});
				}

				reader.readAsText($("#import-data")[0].files[0]);
			} else {
				alert("This browser does not support HTML5.");
			}
		} else {
			alert("File not Supported !!!");
		}
	});

	// ------------------- Product - Recommend Image Size  ----------------------//	
	$(document).ready(function () {
		$('body.post-type-lsdd_program #postimagediv .inside').append('<p class="recommended" id="donation-recommended">Recommended image size 392 x 210px</p>');
	});

	$(document).on('change', 'input[name="program_type"]', function () {
		$('body.post-type-lsdd_program #postimagediv .recommended').hide()
		if ($('input[name="program_type"]:checked').val()) {
			$('body.post-type-lsdd_program #postimagediv #' + $('input[name="program_type"]:checked').val().trim() + '-recommended').show();
		}
	});


	$(document).on('click', '.modal-click', function () {
		$('#import-db').addClass('active');
	});

	$(document).on('click', '.modal-close', function () {
		$('#import-db').removeClass('active');
	});

	-
	/******************************************/
	/* Register and Unregister License Key to Server Update
	/******************************************/
	$(document).on("click", ".lsdd-license-register", function (e) {
		var that = this;
		var inputKey = $(this).closest('.card-header').find('input.lsdd-license-key');
		$(that).closest('.card').css('border', 'none');

		if (inputKey.val() != '') {
			$(this).addClass('loading');
			$.post(lsdc_admin.ajax_url, {

				action: 'lsdd_license_register',
				key: $(this).closest('.card-header').find('input.lsdd-license-key').val(),
				type: $(this).attr('data-type'),
				id: $(this).attr('data-id'),
				security: lsdc_admin.ajax_nonce,

			}, function (response) {

				response = JSON.parse(response);
				$(that).closest('.card-header').find('#msg').text(response.messsage);
				$(that).removeClass('loading');

				if (response.status == 'failed') {
					$(that).closest('.card').css('border', '1px solid red');
					$(that).closest('.card-header').find('input').val("");
				} else {
					location.reload();
				}

			}).fail(function () {
				alert('Please check your internet connection');
			});
		} else {
			inputKey.css('border', '1px solid red');
		}
	});

	// -------------------- Invoice -----------------------//	
	$(document).on('click', '.lsdd_download_invoice', function () {
		$('#modal-invoice').addClass('active');
		$('.invoice-box').addClass('loading');

		let id = $(this).parent().siblings().val();

		$.post(lsdc_admin.ajax_url, {
			action: 'lsdc_admin_get_invoice',
			id: id,
			security: lsdc_admin.ajax_nonce,
		}, function (res) {
			var name = res.data[0].name.toLowerCase();
			$('#pad_invoice').html(lsdd_pad(res.data[0].report_id, 7));
			$('#name_invoice').html(name.ucwords());
			$('#email_invoice').html(res.data[0].email);
			$('#status_invoice').html(res.data[0].status);
			$('#item_invoice').html(res.data[1]);
			$('.total_invoice').html(lsdd_currency_format(2000, true));
			jQuery('.invoice-box').removeClass('loading');
		}).fail(function () {
			alert('Please check your internet connection');
		}).done(function () {
			setTimeout(() => {
				window.print();
			}, 500);
		});
	})


})(jQuery);