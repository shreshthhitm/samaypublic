
    <!--enter otp-->
	<div class="modal fade" id="enter-otp" >
		<div class="modal-dialog ">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
					<div class="row register-form">
						<h4 class="modal-title">Verify OTP</h4>
						<button type="button" class="close" data-dismiss="modal">×</button>
					</div>
				</div>
				<div class="modal-body">
					<div class="error"></div>
					<div class="success"></div>
					
					<form action="" method="post" name="otp" enctype="multipart/form-data" novalidate="novalidate">
						<div class="row">
							<div class="col-md-12">
								<label>Please enter OTP below to verify:-</label>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group row">
									<div class="col-md-8">
										<input class="form-control" name="userotp" id="userotp" minlength="6" maxlength="6" required="required" placeholder="Enter the OTP *" aria-required="true" type="number">
									</div>
									<div class="col-md-4">
										<input id="verify" class="btn btn-info btnVerify" value="Verify" type="button" onClick="verifyOTP();">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									If you have not recieved the OTP yet, please click on Resend OTP below to recieve the OTP again: 
									<input id="resend" class="btn btn-link btnVerify" value="Resend OTP" type="button" onClick="resendOTP();">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!--enter otp form end-->
	
	<!-- THANK YOU MODAL START -->
	<div class="modal fade" id="thankModal">
	  <div class="modal-dialog">
		<div class="modal-content">

		  <!-- Modal Header -->
		  <div class="modal-header">
			<div class="row register-form">
				<h4 class="modal-title"></h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
		  </div>

		  <!-- Modal body -->
		  <div class="modal-body">
			<div class="row register-form">
				<div class="col-md-12 text-center"></div>
			</div>
		  </div>

		  <!-- Modal footer -->
		  <div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		  </div>

		</div>
	  </div>
	</div>
	<!-- THANK YOU MODAL END -->
	