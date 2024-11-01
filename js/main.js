(function($){
	
	$().ready(function() {

		$("#customer-crm-form").validate({
			ignore:"",
			rules: {
				sccrm_customer_name: "required",
				sccrm_customer_email: "required",
				sccrm_customer_phone: { required:true,number:true },
				sccrm_customer_budget: "required",
				sccrm_customer_message: "required",
				sccrm_customer_create_date: "required",
				
			},
			messages: {
				sccrm_customer_name: "Please enter your name",
				sccrm_customer_email: "Please enter a valid email id",
				sccrm_customer_phone: "Please enter valid phone no.",
				sccrm_customer_budget: "Please enter your budget",
				sccrm_customer_message: "Please enter message",				
				sccrm_customer_create_date: "Something went wrong",				
			}
		});
	});
	$.validator.setDefaults({
		submitHandler: function() {
			var formData=$("#customer-crm-form").serialize();
			var data = {action: 'submitCustomerForm',formData:formData};
			$.post(sccrmsettings.ajaxurl,data,function(res)
			{
				$("#customer-crm-form").trigger('reset');
				$("#sccrm_msg").html('You data has been captured successfully..').fadeOut(3000);
			});	
		}
	});

	$( document ).ajaxStart(function() {
	  $('.ajax-loader').removeClass('hide');
	});
	$( document ).ajaxStop(function() {
	  $('.ajax-loader').addClass('hide');
	});
	$( document ).ajaxError(function() {
	  $( "#sccrm_msg" ).text( "Triggered ajaxError handler." );
	});

})(jQuery);/* =========== DOCUMENT READY ends ======================= */