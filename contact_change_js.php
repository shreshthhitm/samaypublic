<script type="text/javascript">
var otpVerified = 0; //global variable. default value 0
	//var isConfPass = 0;
	//var otpVerifiedEmail = 0;
var form_phone2 = 0;
<?php
$php_self = htmlspecialchars($_SERVER['PHP_SELF']);
if(isset($_GET['edit'])){
	$edit = htmlspecialchars($_GET['edit']);
?>
var form_phone2 = 1;
<?php } ?>
	$("form[name='contact_form']").on('submit', function (e) {
		//e.preventDefault();
		var formSubmit = false;
		//var clikedForm = this; // Select Form
		//sendContact(clickedForm);
		//if($(this).hasClass('no_pass')){
			//if(otpVerified == 1 && otpVerifiedEmail == 1){
			if(otpVerified == 1 || $(this).find("#form_phone2").val() == ''){
				
				if(form_phone2 == 1){	// || $(this).find("#form_phone2").val() == ''
				
					sendContact(this);
				}else{
				//$("form[name='contact_form']")[0].submit();
				////$("form[name='contact_form']").submit();
				//console.log($("#form_phone").val());
				//$("#form_phone2").val($("#form_phone").val());
					formSubmit = true;
				///e.currentTarget.submit();
				
				}
				return formSubmit;
				//unset otp session below
				<?php $_SESSION['verify_otp'] = 0; $_SESSION['verify_otp_email'] = 0; ?>
			}else{
				/*if( otpVerified == 0 && otpVerifiedEmail == 0){
					$(".formError").html("Please verify Email & Mobile OTP first!");
				}else if(otpVerified == 0){
					$(".formError").html("Please verify Email OTP first!");
				}else if(otpVerifiedEmail == 0){*/
					$(".formError").html("Please verify Mobile OTP first!");
				//}
				$(".formError").show();
				return false;
			}
		/*}else{
			var pass = $('input[name="form_password"]').val();
			var confpass = $('input[name="form_password1"]').val();
 
			//just to make sure once again during submit
			//if both are true then only allow submit
			if(pass == confpass){
				isConfPass = 1;
				$(".confPassError").hide();
			}
			
			console.log("OTP result = "+otpVerified+", "+otpVerifiedEmail);
			if(otpVerified == 1 && otpVerifiedEmail == 1 && isConfPass == 1){
				sendContact(this);
				<?php $_SESSION['verify_otp'] = 0; $_SESSION['verify_otp_email'] = 0; ?>
			}else{
				if(isConfPass ==0){
					$(".confPassError").html("Passwords do not match!");
					$(".confPassError").show();
				}
				if( otpVerified == 0 && otpVerifiedEmail == 0){
					$(".formError").html("Please verify Email & Mobile OTP first!");
					$(".formError").show();
				}else if(otpVerified == 0){
					$(".formError").html("Please verify Email OTP first!");
					$(".formError").show();
				}else if(otpVerifiedEmail == 0){
					$(".formError").html("Please verify Mobile OTP first!");
					$(".formError").show();
				}
				return false;
			}
		}*/
	});

	$('body').on('shown.bs.modal', '#enter-otp', function () {
        $('input:visible:enabled:first', this).focus();
    });
	
	$("#enter-otp").on('hide.bs.modal', function () {
        $("form[name='otp']")[0].reset();
    });
	function sendOTP(elemClass,otpType,otpSelf) {
		var $elemClassObj = elemClass;
		//console.log("elemClassObj: "+elemClass);
		var otpElem = $(elemClass);
		var valid1 = false;
		if(otpType == 'email'){
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			var inputId = "form_email";
			var otpVal = $("#form_email").val();
			if (emailReg.test(otpVal) && otpVal != '') {
				//console.log("Checking Email");
				valid1 = true;
			} else {
				valid1 = false;
			}
		}else if(otpType == 'sms'){
			var inputId = "form_phone";
			var otpVal = $("#form_phone").val();
			if (otpVal.length == 10 && otpVal != null) {
				//console.log("Checking Number");
				valid1 = true;
			} else {
				valid1 = false;
			}
		}
			
		if(valid1 == true){
			otpElem.parent().siblings(".sendOtpError").html("").hide();
			var data = {
                            //action: 'my_action',
                            /*value: {
                				        "mobile_number" : number,
                				        "action" : "send_otp"
                			        }*/
                		    //mobile_number: number,
							otp_type: otpType,
							otp_val: otpVal,
                			otp_action: "send_otp",
                			otp_self: otpSelf
                        };
			$.ajax({
			    //url: ajaxurl,
			    url: '<?=SITEURL;?>/otp-process.php',
			    data: data,
			    type : 'POST',
			    //dataType : "json",
			    success: function(response) {
			        //alert(response);
                    //console.log("Otp Sent Ajax Success");
    				//$("#myModal").modal('hide');
					var response = JSON.parse(response);
					if(response.type == 'success'){
						$("#enter-otp").modal('show');
						//$("#userotp").focus();
						//console.log("elemClassafterotp: "+elemClass);
						$("#verify").attr("onClick","verifyOTP('"+inputId+"','"+otpType+"','"+otpSelf+"')");
						$("#resend").attr("onClick","resendOTP('"+inputId+"','"+otpType+"','"+otpSelf+"')");
						var otpHtml = "A six-digit OTP has been sent to your ";
						if(otpType == 'email'){
							otpHtml += "Email-ID";
						}else{
							otpHtml += "Mobile No.";
						}
							otpHtml += "("+response.receiver+").";
							//otpHtml += " "+response.message;	//Display the Actual OTP Message on frontend
						$("." + response.type).html(otpHtml);
						$("." + response.type).show();
					}else{
						otpElem.parent().siblings(".sendOtpError").html('<small>'+response.message+'</small>')
						otpElem.parent().siblings(".sendOtpError").show();
					}
                },
                error : function(jqXHR, exception) {
					//console.log(jqXHR);
					if (jqXHR.status === 0) {
					alert('Not connect.\n Verify Network.');
					} else if (jqXHR.status == 404) {
					alert('Requested page not found. [404]');
					} else if (jqXHR.status == 500) {
					alert('Internal Server Error [500].');
					} else if (exception === 'parsererror') {
					alert('Requested JSON parse failed.');
					} else if (exception === 'timeout') {
					alert('Time out error.');
					} else if (exception === 'abort') {
					alert('Ajax request aborted.');
					} else {
					alert('Uncaught Error.\n' + jqXHR.responseText);
					}
				}
            });
		}else{
			var errorTxt = '<small>Please enter a valid ';
			if(otpType == 'email'){
				errorTxt += 'email';
			}
			if(otpType == 'sms'){
				errorTxt += 'number';
			}
			errorTxt += '!</small>';
			otpElem.parent().siblings(".sendOtpError").html(errorTxt).show();
		}
	}
	
	function verifyOTP(elemId,otpTypeVerify,otpSelf) {
		$(".error").html("").hide();
		$(".success").html("").hide();
		var otpElem1 = $("#"+elemId).next();
		//console.log("otpElem1: "+otpElem1);
		var otpElem1Prev = $(otpElem1).prev();
		var otp = $("#userotp").val();
		//console.log("User OTP: "+otp);
		/*var input = {
            		    action: 'my_action',
            			"otp" : otp,
            			"otp_type" : otpTypeVerify,
            			"otp_action" : "verify_otp"
            		};*/
		var data = {
						otp: otp,
						otp_type: otpTypeVerify,
						otp_action: "verify_otp"
					};
		if (otp.length == 6 && otp != null) {
			$.ajax({
				//url: ajaxurl,
				url: '<?=SITEURL;?>/otp-process.php',
			    data: data,
				type: 'POST',
				dataType : "json",
				//data : input,
				success : function(response) {
					$("." + response.type).html(response.message);
					$("." + response.type).show();
					if(response.type == "success"){
						if(otpTypeVerify == 'email'){
							otpVerifiedEmail = 1;
						}else{
							otpVerified = 1;
						}
    					otpElem1.remove();
    					otpElem1Prev.after('<button type="button" class="btn btn-success otpBtn verifiedBtn" style="cursor: auto;">Verified</button>');
    					$(".formError").hide();
						<?php if(!isset($edit)){ ?>
							$("#form_phone2").val($("#form_phone").val());
						<?php } ?>
    					otpElem1Prev.prop( "disabled", true );
    					setTimeout(function() {
    						$("#enter-otp").modal('hide');
    						//$("#myModal").modal('show');
    					}, 2000);
					}
				},
				error : function(ss) {
					alert(ss.type);
				}
			});
		} else {
			$(".error").html('You have entered wrong OTP.')
			$(".error").show();
		}
	}
	
	function resendOTP(elemId,otpType,otpSelf){
		//console.log("OTP Resent");
		$(".error").html("").hide();
		$(".success").html("").hide();
		var otpVal1 = $("#"+elemId).val();
			/*var input = {
				"mobile_number" : number,
				"action" : "send_otp"
			};*/
		if(otpVal1 != ''){
			var data = {
							otp_type: otpType,
							otp_val: otpVal1,
                			otp_action: "send_otp",
							otp_self: otpSelf
                        };
			$.ajax({
			    //url: ajaxurl,
				url: '<?=SITEURL;?>/otp-process.php',
			    data: data,
			    type : 'POST',
				dataType : "json",
			    success: function(response) {
    				//$("#myModal").modal('hide');
    				//$("#enter-otp").modal('show');
    				//$("#userotp").focus();
					var otpHtml = "A six-digit OTP has been sent again to your ";
						if(otpType == 'email'){
							otpHtml += "Email-ID";
						}else{
							otpHtml += "Mobile No.";
						}
							otpHtml += "("+response.receiver+").";
							//otpHtml += " "+response.message;
					$("." + response.type).html(otpHtml);
    				$("." + response.type).show();
                },
                error : function(ss) {
					alert(ss);
				}
            });
		} else {
			var errorTxt = '<small>Please enter a valid ';
			if(otpType == 'email'){
				errorTxt += 'email';
			}
			if(otpType == 'sms'){
				errorTxt += 'number';
			}
			errorTxt += '!</small>';
			$("#"+elemId).closest(".input-group").siblings(".sendOtpError").html(errorTxt).show();
			//$(".sendOtpError").html('<small>Please enter a valid number!</small>')
			//$(".sendOtpError").show();
		}
	}
	
	var countSubmission = 0;
    function sendContact(elem) {
    	var valid = true;
    	var formElem = $(elem);
    	var formData = new FormData(elem);
    	/*for (var pair of formData.entries()) {
            console.log(pair[0]+ ', ' + pair[1]); 
        }*/
		
		formData.append('form_phone', $(formElem).find("#form_phone").val());
        //formData.append('form_email', $(formElem).find("#form_email").val());

		if(valid) {
    	    //console.log("valid");
    	    /*var data = '';
    	    var name = $(formElem).find("input[name=form_name]").val();
    	    var mobile = $(formElem).find("input[name=form_phone]").val();
			var city = $(formElem).find("input[name=form_city]").val();
			var pincode = $(formElem).find("input[name=form_pincode]").val();
    	    var email = $(formElem).find("input[name=form_email]").val();
    	    var service = $(formElem).find("select[name=form_service]").val();
    	    var message = $(formElem).find("textarea[name=form_message]").val();
    	    var attach = $(formElem).find("input[name=form_attachment]").val();
    	    console.log("name = "+name+", mobile = "+mobile);
    	    if(name){
    	        data += 'name='+name;
    	    }
    	    if(mobile){
    	        data += '&mobile='+mobile;
    	    }
			if(city){
    	        data += '&city='+city;
    	    }
			if(pincode){
    	        data += '&pincode='+pincode;
    	    }
    	    if(email){
    	        data += '&email='+email;
    	        console.log("email="+email);
    	    }
    	    if(service){
    	        data += '&service='+service;
    	        console.log("service="+service);
    	    }
    	    if(message){
    	        data += '&message='+message;
    	        console.log("message="+message);
    	    }
    	    if(attach){
    	        data += '&form_attachment='+attach;
    	        console.log("form_attachment="+attach);
    	    }*/
    	    countSubmission++;
			
    		jQuery.ajax({
        		url: '<?=SITEURL;?>/update_contact.php',
        		crossDomain: true,
        		//data: data+'&subject=&countSubmission='+countSubmission,
        		data: formData,
        		processData: false,
                contentType: false,
        		type: "POST",
        		success:function(data){
        		    var result = JSON.parse(data);
        		    //console.log(result);
        		    $("#myModal").modal('hide');
        		    //console.log("countSubmission: "+countSubmission);
        		    //$("#mail-status").html(data);
        		    if(result.type == 'success'){
        		        $("#thankModal .modal-header .register-form h4").html('<i class="fa fa-check" aria-hidden="true"></i> Thank You!').addClass("form-success").removeClass("form-failure");
        		        $("#thankModal .modal-body .register-form div").html('The contact number you provided have been successfully updated with us.<br/><div class="text-success">'+result.message1+'</div><div class="text-danger">'+result.message2+'</div>');
        		        $("#thankModal").modal('show');
						//$("#thankModal .modal-body .form-signin #inputEmail").val(result.username);
						//$("#thankModal .modal-body .form-signin #inputPassword").val(result.password);
        		        setTimeout(function() {
							//$('#thankModal').fadeOut();
                            //$('#thankModal').modal('hide');
                            //$("#carouselModal").modal('show');
							//$(formElem)[0].reset();
							//$("form#auto_login")[0].submit();
							//location.reload(true);
							window.location.href= "<?=$php_self.(isset($edit) ? '?edit='.$edit.'&update=1&ph=y' : '');?>";
                        }, 3000);
        		    }else if(result.type == 'failure'){
        		        console.log("Mail not sent");
        		        $("#thankModal .modal-header .register-form h4").html('<i class="fa fa-times" aria-hidden="true"></i> Sorry!').addClass("form-failure").removeClass("form-success");
        		        $("#thankModal .modal-body .register-form div").html('Some Error occured while saving your details!');
        		        $("#thankModal").modal('show');
        		    }else{
						
					}
        		},
        		error:function (data){ }
    		});
			
    	}
    }
</script>
