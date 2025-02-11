<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/admin
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Learndash_Discord_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Instance of Learndash_Discord_Public class
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Learndash_Discord_Public
	 */
	private $learndash_discord_public_instance;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $learndash_discord_public_instance ) {

		$this->plugin_name                       = $plugin_name;
		$this->version                           = $version;
		$this->learndash_discord_public_instance = $learndash_discord_public_instance;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$min_css = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		wp_register_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . 'discord_tabs_css', plugin_dir_url( __FILE__ ) . 'css/skeletabs.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/learndash-discord-admin' . $min_css . '.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), $this->version, false );

		wp_register_script( $this->plugin_name . '-tabs-js', plugin_dir_url( __FILE__ ) . 'js/skeletabs.js', array( 'jquery' ), $this->version, false );
		$min_js = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/learndash-discord-admin' . $min_js . '.js', array( 'jquery' ), $this->version, false );
		$script_params = array(
			'admin_ajax'                  => admin_url( 'admin-ajax.php' ),
			'permissions_const'           => LEARNDASH_DISCORD_BOT_PERMISSIONS,
			'is_admin'                    => is_admin(),
			'ets_learndash_discord_nonce' => wp_create_nonce( 'ets-learndash-discord-ajax-nonce' ),
		);
		wp_localize_script( $this->plugin_name, 'etsLearnDashParams', $script_params );

	}

	/**
	 * Method to add discord setting sub-menu under top level menu of learndash-lms.
	 *
	 * @since    1.0.0
	 */
	public function ets_learndash_Discord_add_settings_menu() {
		add_submenu_page( 'learndash-lms', __( 'Discord Settings', 'connect-learndash-and-discord' ), __( 'Discord Settings', 'connect-learndash-and-discord' ), 'manage_options', 'connect-learndash-discord-addon', array( $this, 'ets_learndash_discord_setting_page' ) );
	}

	/**
	 * Callback to Display settings page
	 *
	 * @since    1.0.0
	 */
	public function ets_learndash_discord_setting_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		wp_enqueue_style( $this->plugin_name . '-select2' );
		wp_enqueue_style( $this->plugin_name . 'discord_tabs_css' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name . '-select2' );
		wp_enqueue_script( $this->plugin_name . '-tabs-js' );
		wp_enqueue_script( $this->plugin_name );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/learndash-discord-admin-display.php';
	}

	/**
	 * Callback to Connect to bot
	 *
	 * @since    1.0.0
	 */
	public function ets_learndash_discord_connect_to_bot() {

		// if ( current_user_can( 'administrator' ) && isset( $_GET['action'] ) && $_GET['action'] == 'learndash-discord-connect-to-bot' ) {
		// $params                    = array(
		// 'client_id'   => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) ),
		// 'permissions' => LEARNDASH_DISCORD_BOT_PERMISSIONS,
		// 'scope'       => 'bot',
		// 'guild_id'    => sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) ),
		// );
		// $discord_authorise_api_url = LEARNDASH_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );
		//
		// wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
		// exit;
		// }
	}

	/**
	 * Save application details.
	 */
	public function ets_learndash_discord_application_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_learndash_discord_client_id = isset( $_POST['ets_learndash_discord_client_id'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_client_id'] ) ) : '';

		$ets_learndash_discord_client_secret = isset( $_POST['ets_learndash_discord_client_secret'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_client_secret'] ) ) : '';

		$ets_learndash_discord_bot_token = isset( $_POST['ets_learndash_discord_bot_token'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_bot_token'] ) ) : '';

		$ets_learndash_discord_redirect_url = isset( $_POST['ets_learndash_discord_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_redirect_url'] ) ) : '';

		$ets_learndash_discord_redirect_page_id = isset( $_POST['ets_learndash_discord_redirect_page_id'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_redirect_page_id'] ) ) : '';

		$ets_learndash_discord_server_id = isset( $_POST['ets_learndash_discord_server_id'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_server_id'] ) ) : '';

		$ets_current_url = sanitize_text_field( trim( $_POST['current_url'] ) );

		if ( isset( $_POST['submit'] ) ) {
			if ( isset( $_POST['ets_learndash_discord_save_settings'] ) && wp_verify_nonce( $_POST['ets_learndash_discord_save_settings'], 'save_learndash_discord_general_settings' ) ) {
				if ( $ets_learndash_discord_client_id ) {
					update_option( 'ets_learndash_discord_client_id', $ets_learndash_discord_client_id );
				}

				if ( $ets_learndash_discord_client_secret ) {
					update_option( 'ets_learndash_discord_client_secret', $ets_learndash_discord_client_secret );
				}

				if ( $ets_learndash_discord_bot_token ) {
					update_option( 'ets_learndash_discord_bot_token', $ets_learndash_discord_bot_token );
				}

				if ( $ets_learndash_discord_redirect_url ) {
					update_option( 'ets_learndash_discord_redirect_page_id', $ets_learndash_discord_redirect_url );
					$ets_learndash_discord_redirect_url = ets_get_learndash_discord_formated_discord_redirect_url( $ets_learndash_discord_redirect_url );
					update_option( 'ets_learndash_discord_redirect_url', $ets_learndash_discord_redirect_url );

				}

				if ( $ets_learndash_discord_server_id ) {
					update_option( 'ets_learndash_discord_server_id', $ets_learndash_discord_server_id );
				}
				/**
				 * * Call function to save bot name option.
				 */
				ets_learndash_discord_update_bot_name_option();

				$message = 'Your settings are saved successfully.';

				$pre_location = $ets_current_url . '&save_settings_msg=' . $message . '#ets_learndash_application_details';
				wp_safe_redirect( $pre_location );
			}
		}
	}

	/**
	 * Load discord roles from server
	 *
	 * @return OBJECT REST API response
	 */
	public function ets_learndash_discord_load_discord_roles() {

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_learndash_discord_nonce'], 'ets-learndash-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$user_id = get_current_user_id();

		$guild_id          = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );
		$discord_bot_token = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
		$client_id         = sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) );
		if ( $guild_id && $discord_bot_token ) {
			$discod_server_roles_api = LEARNDASH_DISCORD_API_URL . 'guilds/' . $guild_id . '/roles';
			$guild_args              = array(
				'method'  => 'GET',
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bot ' . $discord_bot_token,
				),
			);
			$guild_response          = wp_remote_post( $discod_server_roles_api, $guild_args );

			ets_learndash_discord_log_api_response( $user_id, $discod_server_roles_api, $guild_args, $guild_response );

			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );

			if ( is_array( $response_arr ) && ! empty( $response_arr ) ) {
				if ( array_key_exists( 'code', $response_arr ) || array_key_exists( 'error', $response_arr ) ) {
					Learndash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				} else {
					$response_arr['previous_mapping'] = get_option( 'ets_learndash_discord_role_mapping' );

					$discord_roles = array();
					foreach ( $response_arr as $key => $value ) {
						$isbot = false;
						if ( is_array( $value ) ) {
							if ( array_key_exists( 'tags', $value ) ) {
								if ( array_key_exists( 'bot_id', $value['tags'] ) ) {
									$isbot = true;
									if ( $value['tags']['bot_id'] === $client_id ) {
										$response_arr['bot_connected'] = 'yes';
									}
								}
							}
						}
						if ( $key != 'previous_mapping' && $isbot == false && isset( $value['name'] ) && $value['name'] != '@everyone' ) {
							$discord_roles[ $value['id'] ]       = $value['name'];
							$discord_roles_color[ $value['id'] ] = $value['color'];
						}
					}
					update_option( 'ets_learndash_discord_all_roles', serialize( $discord_roles ) );
					update_option( 'ets_learndash_discord_roles_color', serialize( $discord_roles_color ) );
				}
			}
				return wp_send_json( $response_arr );
		}

				exit();

	}

	/**
	 * Save Role mapping settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_learndash_discord_save_role_mapping() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_discord_roles = isset( $_POST['ets_learndash_discord_role_mapping'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_role_mapping'] ) ) : '';

		$ets_learndash_discord_default_role_id = isset( $_POST['learndash_defaultRole'] ) ? sanitize_textarea_field( trim( $_POST['learndash_defaultRole'] ) ) : '';
		$allow_none_student                    = isset( $_POST['allow_none_student'] ) ? sanitize_textarea_field( trim( $_POST['allow_none_student'] ) ) : '';
		$ets_discord_roles                     = stripslashes( $ets_discord_roles );
		$save_mapping_status                   = update_option( 'ets_learndash_discord_role_mapping', $ets_discord_roles );
		$ets_current_url                       = sanitize_text_field( trim( $_POST['current_url'] ) );
		if ( isset( $_POST['ets_learndash_discord_role_mappings_nonce'] ) && wp_verify_nonce( $_POST['ets_learndash_discord_role_mappings_nonce'], 'learndash_discord_role_mappings_nonce' ) ) {
			if ( ( $save_mapping_status || isset( $_POST['ets_learndash_discord_role_mapping'] ) ) && ! isset( $_POST['flush'] ) ) {
				if ( $ets_learndash_discord_default_role_id ) {
					update_option( 'ets_learndash_discord_default_role_id', $ets_learndash_discord_default_role_id );
				}
				if ( $allow_none_student ) {
					update_option( 'ets_learndash_discord_allow_none_student', $allow_none_student );
				}

				$message = 'Your mappings are saved successfully.';
			}
			if ( isset( $_POST['flush'] ) ) {
				delete_option( 'ets_learndash_discord_role_mapping' );
				delete_option( 'ets_learndash_discord_default_role_id' );

				$message = 'Your settings flushed successfully.';
			}
			$pre_location = $ets_current_url . '&save_settings_msg=' . $message . '#ets_learndash_discord_role_mapping';
			wp_safe_redirect( $pre_location );
		}
	}

	/**
	 * Save advanced settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_learndash_discord_save_advance_settings() {

		if ( ! current_user_can( 'administrator' ) || ! wp_verify_nonce( $_POST['ets_learndash_discord_advance_settings_nonce'], 'learndash_discord_advance_settings_nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

			$ets_learndash_discord_send_welcome_dm             = isset( $_POST['ets_learndash_discord_send_welcome_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_send_welcome_dm'] ) ) : '';
			$ets_learndash_discord_welcome_message             = isset( $_POST['ets_learndash_discord_welcome_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_welcome_message'] ) ) : '';
			$ets_learndash_discord_send_course_complete_dm     = isset( $_POST['ets_learndash_discord_send_course_complete_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_send_course_complete_dm'] ) ) : '';
			$ets_learndash_discord_course_complete_message     = isset( $_POST['ets_learndash_discord_course_complete_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_course_complete_message'] ) ) : '';
			$ets_learndash_discord_send_lesson_complete_dm     = isset( $_POST['ets_learndash_discord_send_lesson_complete_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_send_lesson_complete_dm'] ) ) : '';
			$ets_learndash_discord_lesson_complete_message     = isset( $_POST['ets_learndash_discord_lesson_complete_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_lesson_complete_message'] ) ) : '';
			$ets_learndash_discord_send_topic_complete_dm      = isset( $_POST['ets_learndash_discord_send_topic_complete_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_send_topic_complete_dm'] ) ) : '';
			$ets_learndash_discord_topic_complete_message      = isset( $_POST['ets_learndash_discord_topic_complete_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_topic_complete_message'] ) ) : '';
			$ets_learndash_discord_send_quiz_complete_dm       = isset( $_POST['ets_learndash_discord_send_quiz_complete_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_send_quiz_complete_dm'] ) ) : '';
			$ets_learndash_discord_quiz_complete_message       = isset( $_POST['ets_learndash_discord_quiz_complete_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_quiz_complete_message'] ) ) : '';
			$ets_learndash_discord_send_assignment_approved_dm = isset( $_POST['ets_learndash_discord_send_assignment_approved_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_send_assignment_approved_dm'] ) ) : '';
			$ets_learndash_discord_assignment_approved_message = isset( $_POST['ets_learndash_discord_assignment_approved_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_assignment_approved_message'] ) ) : '';
			$retry_failed_api                                  = isset( $_POST['retry_failed_api'] ) ? sanitize_textarea_field( trim( $_POST['retry_failed_api'] ) ) : '';
			$kick_upon_disconnect                              = isset( $_POST['kick_upon_disconnect'] ) ? sanitize_textarea_field( trim( $_POST['kick_upon_disconnect'] ) ) : '';
			$retry_api_count                                   = isset( $_POST['ets_learndash_retry_api_count'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_retry_api_count'] ) ) : '';
			$set_job_cnrc                                      = isset( $_POST['set_job_cnrc'] ) ? sanitize_textarea_field( trim( $_POST['set_job_cnrc'] ) ) : '';
			$set_job_q_batch_size                              = isset( $_POST['set_job_q_batch_size'] ) ? sanitize_textarea_field( trim( $_POST['set_job_q_batch_size'] ) ) : '';
			$log_api_res                                       = isset( $_POST['log_api_res'] ) ? sanitize_textarea_field( trim( $_POST['log_api_res'] ) ) : '';
			$embed_messaging_feature                           = isset( $_POST['embed_messaging_feature'] ) ? sanitize_textarea_field( trim( $_POST['embed_messaging_feature'] ) ) : '';
			$remove_role_course_expired                        = isset( $_POST['remove_role_course_expired'] ) ? sanitize_textarea_field( trim( $_POST['remove_role_course_expired'] ) ) : '';
			$ets_current_url                                   = sanitize_text_field( trim( $_POST['current_url'] ) );

		if ( isset( $_POST['ets_learndash_discord_advance_settings_nonce'] ) && wp_verify_nonce( $_POST['ets_learndash_discord_advance_settings_nonce'], 'learndash_discord_advance_settings_nonce' ) ) {
			if ( isset( $_POST['adv_submit'] ) ) {

				if ( isset( $_POST['ets_learndash_discord_send_welcome_dm'] ) ) {
					update_option( 'ets_learndash_discord_send_welcome_dm', true );
				} else {
					update_option( 'ets_learndash_discord_send_welcome_dm', false );
				}
				if ( isset( $_POST['ets_learndash_discord_welcome_message'] ) && $_POST['ets_learndash_discord_welcome_message'] != '' ) {
					update_option( 'ets_learndash_discord_welcome_message', $ets_learndash_discord_welcome_message );
				} else {
					update_option( 'ets_learndash_discord_welcome_message', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_send_course_complete_dm'] ) ) {
					update_option( 'ets_learndash_discord_send_course_complete_dm', true );
				} else {
					update_option( 'ets_learndash_discord_send_course_complete_dm', false );
				}
				if ( isset( $_POST['ets_learndash_discord_course_complete_message'] ) && $_POST['ets_learndash_discord_course_complete_message'] != '' ) {
					update_option( 'ets_learndash_discord_course_complete_message', $ets_learndash_discord_course_complete_message );
				} else {
					update_option( 'ets_learndash_discord_course_complete_message', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_send_lesson_complete_dm'] ) ) {
					update_option( 'ets_learndash_discord_send_lesson_complete_dm', true );
				} else {
					update_option( 'ets_learndash_discord_send_lesson_complete_dm', false );
				}
				if ( isset( $_POST['ets_learndash_discord_lesson_complete_message'] ) && $_POST['ets_learndash_discord_lesson_complete_message'] != '' ) {
					update_option( 'ets_learndash_discord_lesson_complete_message', $ets_learndash_discord_lesson_complete_message );
				} else {
					update_option( 'ets_learndash_discord_lesson_complete_message', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_send_topic_complete_dm'] ) ) {
					update_option( 'ets_learndash_discord_send_topic_complete_dm', true );
				} else {
					update_option( 'ets_learndash_discord_send_topic_complete_dm', false );
				}
				if ( isset( $_POST['ets_learndash_discord_topic_complete_message'] ) && $_POST['ets_learndash_discord_topic_complete_message'] != '' ) {
					update_option( 'ets_learndash_discord_topic_complete_message', $ets_learndash_discord_topic_complete_message );
				} else {
					update_option( 'ets_learndash_discord_topic_complete_message', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_send_quiz_complete_dm'] ) ) {
					update_option( 'ets_learndash_discord_send_quiz_complete_dm', true );
				} else {
					update_option( 'ets_learndash_discord_send_quiz_complete_dm', false );
				}
				if ( isset( $_POST['ets_learndash_discord_quiz_complete_message'] ) && $_POST['ets_learndash_discord_quiz_complete_message'] != '' ) {
					update_option( 'ets_learndash_discord_quiz_complete_message', $ets_learndash_discord_quiz_complete_message );
				} else {
					update_option( 'ets_learndash_discord_quiz_complete_message', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_send_assignment_approved_dm'] ) ) {
					update_option( 'ets_learndash_discord_send_assignment_approved_dm', true );
				} else {
					update_option( 'ets_learndash_discord_send_assignment_approved_dm', false );
				}
				if ( isset( $_POST['ets_learndash_discord_assignment_approved_message'] ) && $_POST['ets_learndash_discord_assignment_approved_message'] != '' ) {
					update_option( 'ets_learndash_discord_assignment_approved_message', $ets_learndash_discord_assignment_approved_message );
				} else {
					update_option( 'ets_learndash_discord_assignment_approved_message', '' );
				}
				if ( isset( $_POST['retry_failed_api'] ) ) {
					update_option( 'ets_learndash_discord_retry_failed_api', true );
				} else {
					update_option( 'ets_learndash_discord_retry_failed_api', false );
				}
				if ( isset( $_POST['kick_upon_disconnect'] ) ) {
					update_option( 'ets_learndash_discord_kick_upon_disconnect', true );
				} else {
					update_option( 'ets_learndash_discord_kick_upon_disconnect', false );
				}
				if ( isset( $_POST['ets_learndash_retry_api_count'] ) ) {
					if ( $retry_api_count < 1 ) {
						update_option( 'ets_learndash_discord_retry_api_count', 1 );
					} else {
						update_option( 'ets_learndash_discord_retry_api_count', $retry_api_count );
					}
				}
				if ( isset( $_POST['set_job_cnrc'] ) ) {
					if ( $set_job_cnrc < 1 ) {
						update_option( 'ets_learndash_discord_job_queue_concurrency', 1 );
					} else {
						update_option( 'ets_learndash_discord_job_queue_concurrency', $set_job_cnrc );
					}
				}
				if ( isset( $_POST['set_job_q_batch_size'] ) ) {
					if ( $set_job_q_batch_size < 1 ) {
						update_option( 'ets_learndash_discord_job_queue_batch_size', 1 );
					} else {
						update_option( 'ets_learndash_discord_job_queue_batch_size', $set_job_q_batch_size );
					}
				}
				if ( isset( $_POST['log_api_res'] ) ) {
					update_option( 'ets_learndash_discord_log_api_response', true );
				} else {
					update_option( 'ets_learndash_discord_log_api_response', false );
				}
				if ( isset( $_POST['embed_messaging_feature'] ) ) {
					update_option( 'ets_learndash_discord_embed_messaging_feature', true );
				} else {
					update_option( 'ets_learndash_discord_embed_messaging_feature', false );
				}
				if ( isset( $_POST['remove_role_course_expired'] ) ) {
					update_option( 'ets_learndash_discord_remove_role_course_expired', true );
				} else {
					update_option( 'ets_learndash_discord_remove_role_course_expired', false );
				}
				if ( isset( $_POST['ets_learndash_discord_data_erases'] ) ) {
					update_option( 'ets_learndash_discord_data_erases', true );
				} else {
					update_option( 'ets_learndash_discord_data_erases', false );
				}
				if ( isset( $_POST['ets_learndash_discord_login_with_discord'] ) ) {
					update_option( 'ets_learndash_discord_login_with_discord', true );
				} else {
					update_option( 'ets_learndash_discord_login_with_discord', false );
				}

				$message      = 'Your settings are saved successfully.';
				$pre_location = $ets_current_url . '&save_settings_msg=' . $message . '#ets_learndash_discord_advanced';
				wp_safe_redirect( $pre_location );

			}
		}

	}
	/**
	 * Save appearance settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_learndash_discord_save_appearance_settings() {

		if ( ! current_user_can( 'administrator' ) || ! wp_verify_nonce( $_POST['ets_learndash_discord_appearance_settings_nonce'], 'learndash_discord_appearance_settings_nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_learndash_discord_connect_button_bg_color    = isset( $_POST['ets_learndash_discord_connect_button_bg_color'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_connect_button_bg_color'] ) ) : '';
		$ets_learndash_discord_disconnect_button_bg_color = isset( $_POST['ets_learndash_discord_disconnect_button_bg_color'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_disconnect_button_bg_color'] ) ) : '';
		$ets_learndash_discord_loggedin_button_text       = isset( $_POST['ets_learndash_discord_loggedin_button_text'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_loggedin_button_text'] ) ) : '';
		$ets_learndash_discord_non_login_button_text      = isset( $_POST['ets_learndash_discord_non_login_button_text'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_non_login_button_text'] ) ) : '';
		$ets_learndash_discord_disconnect_button_text     = isset( $_POST['ets_learndash_discord_disconnect_button_text'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_disconnect_button_text'] ) ) : '';
		$ets_current_url                                  = sanitize_text_field( trim( $_POST['current_url'] ) );

		$ets_learndash_discord_send_welcome_dm = isset( $_POST['ets_learndash_discord_send_welcome_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_learndash_discord_send_welcome_dm'] ) ) : '';
		if ( isset( $_POST['ets_learndash_discord_appearance_settings_nonce'] ) && wp_verify_nonce( $_POST['ets_learndash_discord_appearance_settings_nonce'], 'learndash_discord_appearance_settings_nonce' ) ) {
			if ( isset( $_POST['appearance_submit'] ) ) {

				if ( isset( $_POST['ets_learndash_discord_connect_button_bg_color'] ) ) {
					update_option( 'ets_learndash_discord_connect_button_bg_color', $ets_learndash_discord_connect_button_bg_color );
				} else {
					update_option( 'ets_learndash_discord_connect_button_bg_color', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_disconnect_button_bg_color'] ) ) {
					update_option( 'ets_learndash_discord_disconnect_button_bg_color', $ets_learndash_discord_disconnect_button_bg_color );
				} else {
					update_option( 'ets_learndash_discord_disconnect_button_bg_color', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_loggedin_button_text'] ) ) {
					update_option( 'ets_learndash_discord_loggedin_button_text', $ets_learndash_discord_loggedin_button_text );
				} else {
					update_option( 'ets_learndash_discord_loggedin_button_text', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_non_login_button_text'] ) ) {
					update_option( 'ets_learndash_discord_non_login_button_text', $ets_learndash_discord_non_login_button_text );
				} else {
					update_option( 'ets_learndash_discord_non_login_button_text', '' );
				}
				if ( isset( $_POST['ets_learndash_discord_disconnect_button_text'] ) ) {
					update_option( 'ets_learndash_discord_disconnect_button_text', $ets_learndash_discord_disconnect_button_text );
				} else {
					update_option( 'ets_learndash_discord_disconnect_button_text', '' );
				}

				$message = 'Your settings are saved successfully.';

				$pre_location = $ets_current_url . '&save_settings_msg=' . $message . '#ets_learndash_discord_appearance';
				wp_safe_redirect( $pre_location );

			}
		}

	}
	/**
	 *
	 * @param type $user_id
	 * @param type $course_id
	 * @param type $course_access_list
	 * @param type $remove
	 */
	public function ets_learndash_discord_admin_update_course_access( $user_id, $course_id, $course_access_list, $remove ) {

		$this->learndash_discord_public_instance->ets_learndash_discord_update_course_access( $user_id, $course_id, $course_access_list, $remove );
	}

	/**
	 * Send DM about assignment approval
	 *
	 * @param int $assignment_id Assignment ID.
	 * @return NONE
	 */
	public function ets_learndash_discord_admin_assignment_approved( $assignment_id ) {

		$this->learndash_discord_public_instance->ets_learndash_discord_assignment_approved( $assignment_id );
	}

	/**
	 * Add LearnDash Discord column to WP Users listing
	 *
	 * @param array $columns
	 * @return NONE
	 */
	public function ets_learndash_discord_add_learndash_discord_column( $columns ) {

		$columns['ets_learndash_discord_api'] = esc_html__( 'LearnDash Discord', 'connect-learndash-and-discord' );
		return $columns;
	}

	/**
	 * Display Run API button
	 *
	 * @param array $columns
	 * @return NONE
	 */
	public function ets_learndash_discord_run_learndash_discord_api( $value, $column_name, $user_id ) {

		if ( $column_name === 'ets_learndash_discord_api' ) {
			wp_enqueue_script( $this->plugin_name );
			$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
			if ( $access_token ) {
				return '<a href="#" data-user-id="' . esc_attr( $user_id ) . '" class="ets-learndash-discord-run-api" >' . esc_html__( 'RUN API', 'connect-learndash-and-discord' ) . '</a><span class=" run-api spinner" ></span><div class="run-api-success"></div>';
			}
			return esc_html__( 'Not Connected', 'connect-learndash-and-discord' );
		}
		return $value;
	}
	/**
	 * Run API
	 *
	 * @return NONE
	 */
	public function ets_learndash_discord_run_api() {

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_learndash_discord_nonce'], 'ets-learndash-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$user_id                            = sanitize_text_field( $_POST['ets_learndash_discord_user_id'] );
		$access_token                       = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
		$refresh_token                      = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_refresh_token', true ) ) );
		$ets_learndash_discord_role_mapping = json_decode( get_option( 'ets_learndash_discord_role_mapping' ), true );
		$default_role                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );
		$last_default_role                  = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_last_default_role', true ) ) );
		$student_courses                    = ets_learndash_discord_get_student_courses_id( $user_id );

		if ( $access_token && $refresh_token && is_array( $ets_learndash_discord_role_mapping ) && is_array( $student_courses ) ) {
			foreach ( $student_courses as $course_id ) {
				/**
				 * 1 - The course has a role and the student already has this role: Nothing to do.
				 * 2 - The role of the course is changed: Delete the old and assign the new.
				 * 3 - The course has a role and the student does not have this role: Assign this role.
				 * 4 - The student has this role, but the role is removed from the map: Remove the role.
				 */
				$student_role_for_course = get_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, true );

				if ( $student_role_for_course && array_key_exists( 'learndash_course_id_' . $course_id, $ets_learndash_discord_role_mapping ) ) {

					// Nothing to do.

				}
				if ( $student_role_for_course && array_key_exists( 'learndash_course_id_' . $course_id, $ets_learndash_discord_role_mapping ) && $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $course_id ] != $student_role_for_course ) {

					// Remove $student_role_for_course
					$old_role = $student_role_for_course;
					delete_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, $old_role );
					$this->learndash_discord_public_instance->delete_discord_role( $user_id, $old_role );

					// Assign $ets_learndash_discord_role_mapping['learndash_course_id_' . $course_id]
					$new_role = $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $course_id ];
					update_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, $new_role );
					$this->learndash_discord_public_instance->put_discord_role_api( $user_id, $new_role );
				}

				if ( ! $student_role_for_course && array_key_exists( 'learndash_course_id_' . $course_id, $ets_learndash_discord_role_mapping ) ) {

					$new_role = $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $course_id ];
					update_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, $new_role );
					$this->learndash_discord_public_instance->put_discord_role_api( $user_id, $new_role );
				}

				if ( $student_role_for_course && ! array_key_exists( 'learndash_course_id_' . $course_id, $ets_learndash_discord_role_mapping ) ) {
					$old_role = $student_role_for_course;
					delete_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, $old_role );
					$this->learndash_discord_public_instance->delete_discord_role( $user_id, $old_role );
				}
			}
		}
		if ( $access_token && $refresh_token ) {
			// Default role
			/*
			* 1 - The default role is defined and it's the same as the student has: Nothing to do.
			* 2 - The default role is changed: Delete the old and assign the new.
			* 3 - The default role is not defined: Delete the default role.
			*/

			if ( $default_role && $default_role != 'none' && $default_role === $last_default_role ) {

			} elseif ( $default_role && $default_role != 'none' && $default_role != $last_default_role ) {

				update_user_meta( $user_id, '_ets_learndash_discord_last_default_role', $default_role );
				$this->learndash_discord_public_instance->delete_discord_role( $user_id, $last_default_role );
				$this->learndash_discord_public_instance->put_discord_role_api( $user_id, $default_role );
			} else {

				delete_user_meta( $user_id, '_ets_learndash_discord_last_default_role' );
				$this->learndash_discord_public_instance->delete_discord_role( $user_id, $last_default_role );
			}
		}
		exit();

	}
	/**
	 * Add LearnDash Discord Connection column to WP Users listing
	 *
	 * @param array $columns
	 */
	public function ets_learndash_discord_add_learndash_disconnect_discord_column( $columns ) {

		$columns['ets_learndash_disconnect_discord_connection'] = esc_html__( 'LD Discord Connection', 'learndash-discord-addon' );
		return $columns;
	}

	/**
	 * Display Discord Disconnect button
	 *
	 * @param array $columns
	 */
	public function ets_learndash_discord_disconnect_discord_button( $value, $column_name, $user_id ) {

		if ( $column_name === 'ets_learndash_disconnect_discord_connection' ) {

			$access_token                    = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
			$_ets_learndash_discord_username = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_username', true ) ) );
			if ( $access_token ) {
				return '<button  data-user-id="' . esc_attr( $user_id ) . '" class="learndash-disconnect-discord-user" >' . esc_html__( 'Disconnect from discord ', 'connect-learndash-and-discord' ) . ' <i class="fab fa-discord"></i> <span class="spinner"></span> </button><p>' . esc_html__( sprintf( 'Connected account: %s', $_ets_learndash_discord_username ), 'connect-learndash-and-discord' ) . '</p>';
			}
			return esc_html__( 'Not Connected', 'connect-learndash-and-discord' );
		}
		return $value;
	}
	/**
	 * Display Disconnect Discord button Profile User Page.
	 */
	public function ets_learndash_discord_disconnect_user_button() {

		if ( current_user_can( 'administrator' ) ) {
			wp_enqueue_script( $this->plugin_name );
			$user_id                         = ( isset( $_GET['user_id'] ) ) ? sanitize_text_field( $_GET['user_id'] ) : get_current_user_id();
			$access_token                    = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
			$refresh_token                   = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_refresh_token', true ) ) );
			$_ets_learndash_discord_username = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_username', true ) ) );
			if ( $access_token && $refresh_token ) {
				$DisConnect  = '<h3>' . esc_html__( 'LearnDash Discrod Add-On', 'connect-learndash-and-discord' ) . '</h3>';
				$DisConnect .= '<button data-user-id=' . esc_attr( $user_id ) . ' type="button" class="button learndash-disconnect-discord-user" id="disconnect-discord-user">' . esc_html__( 'Disconnect from Discord', 'connect-learndash-and-discord' ) . ' <i class="fab fa-discord"></i> <span class="spinner"></span> </button>';
				$DisConnect .= '<p>' . esc_html__( sprintf( 'Connected account %s', $_ets_learndash_discord_username ) ) . '</p>';
				_e( wp_kses( $DisConnect, ets_learndash_discord_allowed_html() ) );
			}
		}
	}
	/**
	 * Run disconnect discord
	 */
	public function ets_learndash_disconnect_user() {

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_learndash_discord_nonce'], 'ets-learndash-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$user_id              = sanitize_text_field( trim( $_POST['ets_learndash_discord_user_id'] ) );
		$kick_upon_disconnect = sanitize_text_field( trim( get_option( 'ets_learndash_discord_kick_upon_disconnect' ) ) );
		$access_token         = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
		$refresh_token        = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_refresh_token', true ) ) );
		if ( $user_id && $access_token && $refresh_token ) {
			delete_user_meta( $user_id, '_ets_learndash_discord_access_token' );
			delete_user_meta( $user_id, '_ets_learndash_discord_refresh_token' );
			$user_roles = ets_learndash_discord_get_user_roles( $user_id );
			if ( $kick_upon_disconnect ) {

				if ( is_array( $user_roles ) ) {
					foreach ( $user_roles as $user_role ) {
						$this->learndash_discord_public_instance->delete_discord_role( $user_id, $user_role );
					}
				}
			} else {
				$this->learndash_discord_public_instance->delete_member_from_guild( $user_id, false );
			}

			$event_res = array(
				'status'  => 1,
				'message' => 'Successfully disconnected',
			);
			wp_send_json( $event_res );
			exit();
		}
		exit();
	}

	/**
	 * Update redirect url in DB.
	 */
	public function ets_learndash_discord_update_redirect_url() {

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security.
		if ( ! wp_verify_nonce( $_POST['ets_learndash_discord_nonce'], 'ets-learndash-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$page_id = sanitize_text_field( $_POST['ets_learndash_page_id'] );
		if ( isset( $page_id ) ) {
			$formated_discord_redirect_url = ets_get_learndash_discord_formated_discord_redirect_url( $page_id );
			update_option( 'ets_learndash_discord_redirect_page_id', $page_id );
			update_option( 'ets_learndash_discord_redirect_url', $formated_discord_redirect_url );
			$res = array(
				'formated_discord_redirect_url' => $formated_discord_redirect_url,
			);
			wp_send_json( $res );
		}
		exit();

	}

	/**
	 *
	 */
	public function ets_learndash_discord_send_support_mail() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		if ( isset( $_POST['ets_learndash_send_support_mail'] ) ) {

			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['ets_learndas_discord_send_support_mail'], 'send_support_mail' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
			}
			$etsUserName  = isset( $_POST['ets_user_name'] ) ? sanitize_text_field( trim( $_POST['ets_user_name'] ) ) : '';
			$etsUserEmail = isset( $_POST['ets_user_email'] ) ? sanitize_text_field( trim( $_POST['ets_user_email'] ) ) : '';
			$message      = isset( $_POST['ets_support_msg'] ) ? sanitize_text_field( trim( $_POST['ets_support_msg'] ) ) : '';
			$sub          = isset( $_POST['ets_support_subject'] ) ? sanitize_text_field( trim( $_POST['ets_support_subject'] ) ) : '';

			if ( $etsUserName && $etsUserEmail && $message && $sub ) {

				$subject   = $sub;
				$to        = array(
					'contact@expresstechsoftwares.com',
					'vinod.tiwari@expresstechsoftwares.com',
				);
				$content   = 'Name: ' . $etsUserName . '<br>';
				$content  .= 'Contact Email: ' . $etsUserEmail . '<br>';
				$content  .= 'LearnDash Support Message: ' . $message;
				$headers   = array();
				$blogemail = get_bloginfo( 'admin_email' );
				$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $blogemail . '>' . "\r\n";
				$mail      = wp_mail( $to, $subject, $content, $headers );

				if ( $mail ) {
					$message = esc_html__( 'Your request have been successfully submitted!', 'connect-learndash-and-discord' );
				} else {
					$message = esc_html__( 'failure to send email!', 'connect-learndash-and-discord' );
				}
				if ( isset( $_POST['current_url'] ) ) {
					$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#ets_learndash_discord_support';
					wp_safe_redirect( $pre_location );
				}
			}
		}
	}

	/**
	 *
	 * Update user meta notification
	 */
	public function ets_learndash_discord_notice_dismiss() {

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}

		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_learndash_discord_nonce'], 'ets-learndash-discord-ajax-nonce' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
		}

		update_user_meta( get_current_user_id(), '_ets_learndash_discord_dismissed_notification', true );
		$event_res = array(
			'status'  => 1,
			'message' => __( 'success', 'connect-learndash-and-discord' ),
		);
		return wp_send_json( $event_res );

		exit();
	}
}
