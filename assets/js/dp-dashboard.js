jQuery(document).ready(function($) {

	$('.pf-row input').on('keydown keyup', function (e) {

		var current_value = $(this).val();
		var calculated_value = current_value * 0.25;

		$('.pf-row div.value span').html(calculated_value);
		$('input[name="dp-runtime-days"]').val(current_value);

			// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
			// Allow: Ctrl+A, Command+A
		(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
			// Allow: home, end, left, right, down, up
		(e.keyCode >= 35 && e.keyCode <= 40)) {
			// let it happen, don't do anything
			return;
		}
			// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			e.preventDefault();
		}

	});

	braintree.setup(clientToken, "dropin", {
	  container: "payment-form",
	  onPaymentMethodReceived: function(obj) {
	  	$('input[name="payment-method-nonce"]').val(obj.nonce);
	  	$('#checkout').submit();
	  }
	});

	$('#dp-purchase-runtime-dropdown').on('click', function(event) {

		event.preventDefault();

		$('.dp-purchase-runtime-dropdown').fadeToggle(50);

		return false;

	});

	// views last month
	$('#dp-dashboard-views-last-month').on('click', function() {

		$('#dp-dashboard-views-this-month').removeClass('is-active');
		$(this).addClass('is-active');

		$('#views-col [data-dashboard-view-name="this-month"]').addClass('hidden');
		$('#views-col [data-dashboard-view-name="last-month"]').removeClass('hidden');

	});

	// views this month
	$('#dp-dashboard-views-this-month').on('click', function() {

		$('#dp-dashboard-views-last-month').removeClass('is-active');
		$(this).addClass('is-active');

		$('#views-col [data-dashboard-view-name="last-month"]').addClass('hidden');
		$('#views-col [data-dashboard-view-name="this-month"]').removeClass('hidden');

	});

	// people last month
	$('#dp-dashboard-people-last-month').on('click', function() {

		$('#dp-dashboard-people-this-month').removeClass('is-active');
		$(this).addClass('is-active');

		$('#people-col [data-dashboard-view-name="this-month"]').addClass('hidden');
		$('#people-col [data-dashboard-view-name="last-month"]').removeClass('hidden');

	});

	// people this month
	$('#dp-dashboard-people-this-month').on('click', function() {

		$('#dp-dashboard-people-last-month').removeClass('is-active');
		$(this).addClass('is-active');

		$('#people-col [data-dashboard-view-name="last-month"]').addClass('hidden');
		$('#people-col [data-dashboard-view-name="this-month"]').removeClass('hidden');

	});


	function dp_dashboard_get_values() {

		$.ajax({
			type: 'GET',
			url: document.location.href,
			data: {action: 'dp-dashboard-get-values'}
		}).done(function(response) {

			$('[data-dashboard-view="runtime"]').html(response.runtime);
			$('[data-dashboard-view="space"]').html(response.space + '%');
			$('[data-dashboard-view="views"]').html(response.views);
			$('[data-dashboard-view="previous_views"]').html(response.previous_views);
			$('[data-dashboard-view="people"]').html(response.people);
			$('[data-dashboard-view="previous_people"]').html(response.previous_people);

		}).fail(function(response) {

			// console.log(response);

		});

	}

	dp_dashboard_get_values();
	
	// refresh dashboard every 5 seconds
	setInterval(function() {

		dp_dashboard_get_values();

	}, 5000);

});
