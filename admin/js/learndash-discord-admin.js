(function($){
//	'use strict';
	if (etsLearnDashParams.is_admin) {
		if(jQuery().select2) {
			$('#ets_learndash_discord_redirect_url').select2({ width: 'resolve' });                
                        $('#ets_learndash_discord_redirect_url').on('change', function(){
				$.ajax({
					url: etsLearnDashParams.admin_ajax,
					type: "POST",
					context: this,
					data: { 'action': 'ets_learndash_discord_update_redirect_url', 'ets_learndash_page_id': $(this).val() , 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
					beforeSend: function () {
						$('p.redirect-url').find('b').html("");
                                                $('p.ets-discord-update-message').css('display','none');                                               
						$(this).siblings('p.description').find('span.spinner').addClass("ets-is-active").show();
					},
					success: function (data) { 
						$('p.redirect-url').find('b').html(data.formated_discord_redirect_url);
						$('p.ets-discord-update-message').css('display','block');                                               
					},
					error: function (response, textStatus, errorThrown ) {
						console.log( textStatus + " :  " + response.status + " : " + errorThrown );
					},
					complete: function () {
						$(this).siblings('p.description').find('span.spinner').removeClass("ets-is-active").hide();
					}
				});

			});                        
		}


		/*Load all roles from discord server*/
		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: etsLearnDashParams.admin_ajax,
			data: { 'action': 'ets_learndash_discord_load_discord_roles', 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
			beforeSend: function () {
				$(".learndash-discord-roles .spinner").addClass("is-active");
				$(".initialtab.spinner").addClass("is-active");
			},
			success: function (response) {
				if (response != null && response.hasOwnProperty('code') && response.code == 50001 && response.message == 'Missing Access') {
					$(".learndash-btn-connect-to-bot").show();
				} else if ( response.code === 10004 && response.message == 'Unknown Guild' ) {
					$(".learndash-btn-connect-to-bot").show().after('<p><b>The server ID is wrong or you did not connect the Bot.</b></p>');
				}else if( response.code === 0 && response.message == '401: Unauthorized' ) {
					$(".learndash-btn-connect-to-bot").show().html("Error: Unauthorized - The Bot Token is wrong").addClass('error-bk');					
				} else if (response == null || response.message == '401: Unauthorized' || response.hasOwnProperty('code') || response == 0) {
					$("#learndash-connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
				} else {
					if ($('.ets-tabs button[data-identity="level-mapping"]').length) {
						$('.ets-tabs button[data-identity="level-mapping"]').show();
					}
					if (response.bot_connected === 'yes') {
						$("#learndash-connect-discord-bot").show().html("Bot Connected <i class='fab fa-discord'></i>").addClass('not-active');                                            
					}else{
						$("#learndash-connect-discord-bot").show().html("Error: Please check the Client ID is correct").addClass('error-bk');
                                        }                                        
					

					var activeTab = localStorage.getItem('activeTab');
					if ($('.ets-tabs button[data-identity="level-mapping"]').length == 0 && activeTab == 'level-mapping') {
						$('.ets-tabs button[data-identity="settings"]').trigger('click');
					}
					$.each(response, function (key, val) {
						var isbot = false;
						if (val.hasOwnProperty('tags')) {
							if (val.tags.hasOwnProperty('bot_id')) {
								isbot = true;
							}
						}

						if (key != 'bot_connected' && key != 'previous_mapping' && isbot == false && val.name != '@everyone') {
							$('.learndash-discord-roles').append('<div class="makeMeDraggable" style="background-color:#'+val.color.toString(16)+'" data-learndash_role_id="' + val.id + '" >' + val.name + '</div>');
							$('#learndash-defaultRole').append('<option value="' + val.id + '" >' + val.name + '</option>');
							makeDrag($('.makeMeDraggable'));
						}
					});
					var defaultRole = $('#selected_default_role').val();
					if (defaultRole) {
						$('#learndash-defaultRole option[value=' + defaultRole + ']').prop('selected', true);
					}

					if (response.previous_mapping) {
						var mapjson = response.previous_mapping;
					} else {
						var mapjson = localStorage.getItem('learndash_mappingjson');
					}

					$("#ets_learndash_mapping_json_val").html(mapjson);
					$.each(JSON.parse(mapjson), function (key, val) {
						var arrayofkey = key.split('id_');
						var preclone = $('*[data-learndash_role_id="' + val + '"]').clone();
						if(preclone.length>1){
							preclone.slice(1).hide();
						}
						
						if (jQuery('*[data-learndash_course_id="' + arrayofkey[1] + '"]').find('*[data-learndash_role_id="' + val + '"]').length == 0) {
							$('*[data-learndash_course_id="' + arrayofkey[1] + '"]').append(preclone).attr('data-drop-learndash_role_id', val).find('span').css({ 'order': '2' });
						}
						if ($('*[data-learndash_course_id="' + arrayofkey[1] + '"]').find('.makeMeDraggable').length >= 1) {
							$('*[data-learndash_course_id="' + arrayofkey[1] + '"]').droppable("destroy");
						}

						preclone.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'order': '1' }).attr('data-learndash_course_id', arrayofkey[1]);
						makeDrag(preclone);
					});
				}

			},
			error: function (response) {
				$("#learndash-connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
				console.error(response);
			},
			complete: function () {
				$(".learndash-discord-roles .spinner").removeClass("is-active").css({ "float": "right" });
				$("#skeletabsTab1 .spinner").removeClass("is-active").css({ "float": "right", "display": "none" });
			}
		});
                var discordWindow;
		$('#learndash-connect-discord-bot').click(function (e) {
			e.preventDefault();
			discordWindow = window.open($(this).attr('href'), "", "height=650,width=500,directories=no,titlebar=no,toolbar=no,location=no,resizable=yes");
 
		});    
                 var queryString = window.location.search;
                 var urlParams = new URLSearchParams(queryString);
                 var via = urlParams.get('via');
                 if( via == 'learndash-discord-bot'){
                     window.opener.location.reload();
                     window.close();
                 }
		/*Clear log log call-back*/
		$('#ets-learndash-clrbtn').click(function (e) {
			e.preventDefault();
			$.ajax({
				url: etsLearnDashParams.admin_ajax,
				type: "POST",
				data: { 'action': 'ets_learndash_discord_clear_logs', 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
				beforeSend: function () {
					$(".clr-log.spinner").addClass("is-active").show();
				},
				success: function (data) {
         
					if (data.error) {
						// handle the error
						alert(data.error.msg);
					} else {
                                            
						$('.error-log').html("Clear logs Sucesssfully !");
					}
				},
				error: function (response, textStatus, errorThrown ) {
					console.log( textStatus + " :  " + response.status + " : " + errorThrown );
				},
				complete: function () {
					$(".clr-log.spinner").removeClass("is-active").hide();
				}
			});
		});                
		/*RUN API */
		$('.ets-learndash-discord-run-api').click(function (e) {
			e.preventDefault();
			$.ajax({
				url: etsLearnDashParams.admin_ajax,
				type: "POST",
				context: this,
				data: { 'action': 'ets_learndash_discord_run_api', 'ets_learndash_discord_user_id': $(this).data('user-id') , 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
				beforeSend: function () {
					$(this).siblings("div.run-api-success").html("");
					$(this).siblings('span.spinner').addClass("is-active").show();
				},
				success: function (data) {         
					if (data.error) {
						// handle the error
						alert(data.error.msg);
					} else {
                                            
						$(this).siblings("div.run-api-success").html("Update Discord Roles Sucesssfully !");
					}
				},
				error: function (response, textStatus, errorThrown ) {
					console.log( textStatus + " :  " + response.status + " : " + errorThrown );
				},
				complete: function () {
					$(this).siblings('span.spinner').removeClass("is-active").hide();
				}
			});
		});                
		$('.learndash-disconnect-discord-user').click(function (e) {
                    e.preventDefault();
			$.ajax({
				url: etsLearnDashParams.admin_ajax,
				type: "POST",
				context: this,
				data: { 'action': 'ets_learndash_discord_disconnect_user', 'ets_learndash_discord_user_id': $(this).data('user-id') , 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
				beforeSend: function () {
                                    $(this).find('span').addClass("is-active").show();
				},
				success: function (data) {         
					if (data.error) {
						// handle the error
						alert(data.error.msg);
					} else {
						$(this).prop('disabled', true);
						console.log(data);
					}
				},
				error: function (response, textStatus, errorThrown ) {
					console.log( textStatus + " :  " + response.status + " : " + errorThrown );
				},
				complete: function () {
					$(this).find('span').removeClass("is-active").hide();
				}
			});
		});                
		/*Flush settings from local storage*/
		$("#revertMapping").on('click', function () {
			localStorage.removeItem('learndash_mapArray');
			localStorage.removeItem('learndash_mappingjson');
			window.location.href = window.location.href;
		});        
   
		/*Create droppable element*/
		function init() {
                    if($('.makeMeDroppable').length){
			$('.makeMeDroppable').droppable({
				drop: handleDropEvent,
				hoverClass: 'hoverActive',
			});
                    }
                    if($('.learndash-discord-roles-col').length){
			$('.learndash-discord-roles-col').droppable({
				drop: handlePreviousDropEvent,
				hoverClass: 'hoverActive',
			});
                    }
		}

		$(init);

		/*Create draggable element*/
		function makeDrag(el) {
			// Pass me an object, and I will make it draggable
			el.draggable({
				revert: "invalid",
				helper: 'clone',
				start: function(e, ui) {
				ui.helper.css({"width":"45%"});
				}
			});
		}

		/*Handel droppable event for saved mapping*/
		function handlePreviousDropEvent(event, ui) {
			var draggable = ui.draggable;
			if(draggable.data('learndash_course_id')){
				$(ui.draggable).remove().hide();
			}
			$(this).append(draggable);
			$('*[data-drop-learndash_role_id="' + draggable.data('learndash_role_id') + '"]').droppable({
				drop: handleDropEvent,
				hoverClass: 'hoverActive',
			});
			$('*[data-drop-learndash_role_id="' + draggable.data('learndash_role_id') + '"]').attr('data-drop-learndash_role_id', '');

			var oldItems = JSON.parse(localStorage.getItem('learndash_mapArray')) || [];
			$.each(oldItems, function (key, val) {
				if (val) {
					var arrayofval = val.split(',');
					if (arrayofval[0] == 'learndash_course_id_' + draggable.data('learndash_course_id') && arrayofval[1] == draggable.data('learndash_role_id')) {
						delete oldItems[key];
					}
				}
			});
			var jsonStart = "{";
			$.each(oldItems, function (key, val) {
				if (val) {
					var arrayofval = val.split(',');
					if (arrayofval[0] != 'learndash_course_id_' + draggable.data('learndash_course_id') || arrayofval[1] != draggable.data('learndash_role_id')) {
						jsonStart = jsonStart + '"' + arrayofval[0] + '":' + '"' + arrayofval[1] + '",';
					}
				}
			});
			localStorage.setItem('learndash_mapArray', JSON.stringify(oldItems));
			var lastChar = jsonStart.slice(-1);
			if (lastChar == ',') {
				jsonStart = jsonStart.slice(0, -1);
			}

			var learndash_mappingjson = jsonStart + '}';
			$("#ets_learndash_mapping_json_val").html(learndash_mappingjson);
			localStorage.setItem('learndash_mappingjson', learndash_mappingjson);
			draggable.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '10px' });
		}

		/*Handel droppable area for current mapping*/
		function handleDropEvent(event, ui) {
			var draggable = ui.draggable;
			var newItem = [];
			var newClone = $(ui.helper).clone();
			if($(this).find(".makeMeDraggable").length >= 1){
				return false;
			}
			$('*[data-drop-learndash_role_id="' + newClone.data('learndash_role_id') + '"]').droppable({
				drop: handleDropEvent,
				hoverClass: 'hoverActive',
			});
			$('*[data-drop-learndash_role_id="' + newClone.data('learndash_role_id') + '"]').attr('data-drop-learndash_role_id', '');
			if ($(this).data('drop-learndash_role_id') != newClone.data('learndash_role_id')) {
				var oldItems = JSON.parse(localStorage.getItem('learndash_mapArray')) || [];
				$(this).attr('data-drop-learndash_role_id', newClone.data('learndash_role_id'));
				newClone.attr('data-learndash_course_id', $(this).data('learndash_course_id'));

				$.each(oldItems, function (key, val) {
					if (val) {
						var arrayofval = val.split(',');
						if (arrayofval[0] == 'learndash_course_id_' + $(this).data('learndash_course_id')) {
							delete oldItems[key];
						}
					}
				});

				var newkey = 'learndash_course_id_' + $(this).data('learndash_course_id');
				oldItems.push(newkey + ',' + newClone.data('learndash_role_id'));
				var jsonStart = "{";
				$.each(oldItems, function (key, val) {
					if (val) {
						var arrayofval = val.split(',');
						if (arrayofval[0] == 'learndash_course_id_' + $(this).data('learndash_course_id') || arrayofval[1] != newClone.data('learndash_role_id') && arrayofval[0] != 'learndash_course_id_' + $(this).data('learndash_course_id') || arrayofval[1] == newClone.data('learndash_role_id')) {
							jsonStart = jsonStart + '"' + arrayofval[0] + '":' + '"' + arrayofval[1] + '",';
						}
					}
				});

				localStorage.setItem('learndash_mapArray', JSON.stringify(oldItems));
				var lastChar = jsonStart.slice(-1);
				if (lastChar == ',') {
					jsonStart = jsonStart.slice(0, -1);
				}

				var learndash_mappingjson = jsonStart + '}';
				localStorage.setItem('learndash_mappingjson', learndash_mappingjson);
				$("#ets_learndash_mapping_json_val").html(learndash_mappingjson);
			}

			$(this).append(newClone);
			$(this).find('span').css({ 'order': '2' });
			if (jQuery(this).find('.makeMeDraggable').length >= 1) {
				$(this).droppable("destroy");
			}
			makeDrag($('.makeMeDraggable'));
			newClone.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'position':'unset', 'order': '1' });
		}
		$(document.body).on('change', '#ets_learndash_discord_redirect_url', function(e){
			var page_url = $(this).find(':selected').data('page-url');
                        $('p.redirect-url').html('<b>'+page_url+'</b>');
		});
		$('#ets_learndash_discord_connect_button_bg_color').wpColorPicker();
		$('#ets_learndash_discord_disconnect_button_bg_color').wpColorPicker(); 
		
		$(document).ready(function(){
			$(' .ets-learndash-discord-review-notice > button.notice-dismiss' ).on('click', function() {

				$.ajax({
					type: "POST",
					dataType: "JSON",
					url: etsLearnDashParams.admin_ajax,
					data: { 
						'action': 'ets_learndash_discord_notice_dismiss', 
						'ets_learndash_discord_nonce' : etsLearnDashParams.ets_learndash_discord_nonce 
					},
					beforeSend: function () {
						console.log('sending...');
					},
					success: function (response) {
						console.log(response);
					},
					error: function (response) {
						console.error(response);
					},
					complete: function () {
						// 
					}
				});
			});			
		});

	}
        

	/*Tab options*/

		$.skeletabs.setDefaults({
			keyboard: false,
		});            
	
    
})(jQuery);
