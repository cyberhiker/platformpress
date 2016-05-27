<?php

function ap_user_fields($args = '', $group = false) {
	if ( ap_get_displayed_user_id() != get_current_user_id() ) {
		return; }

	if ( ! $group ) {
		$group = ! isset( $_GET['group'] ) ? 'basic' : sanitize_text_field( $_GET['group'] ); }

	echo ap_user_get_fields( $args, $group );
}

/**
 * Return fields of users
 * @param  string|array $args
 * @return object
 */
function ap_user_get_fields($args = '', $group = 'basic') {

	$defaults = array(
		'user_id' => get_current_user_id(),
		'form'  => array(),
	);

	$args = wp_parse_args( $args, $defaults );

	$args['form'] = wp_parse_args($args['form'], array(
		'is_ajaxified'      => true,
		'name'              => 'ap_user_profile_form',
		'user_id'           => $args['user_id'],
		'nonce_name'        => 'nonce_user_profile_'.$args['user_id'].'_'.$group,
		'fields'            => ap_get_user_fields( $group ),
	));

	$args['form']['fields'][] = array(
		'name'          => 'group',
		'type'          => 'hidden',
		'value'         => $group,
	);

	platformpress()->form = new PlatformPress_Form( $args['form'] );

	return platformpress()->form->get_form();
}

function ap_get_user_fields($group = 'basic', $user_id = false) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id(); }

	$fields_value = ap_user_get_the_meta( false, $user_id );

	$form_fields = array();

	$form_fields['basic'] = array(
	array(
		'name' => 'hide_profile',
		'label' => __( 'Hide my profile', 'platformpress' ),
		'type'  => 'checkbox',
		'desc'  => __( 'Hide your profile from public', 'platformpress' ),
		'value' => ( !empty( $fields_value['hide_profile'] ) ? $fields_value['hide_profile'] : '' ),
		'order' => 5,
		'autocomplete' => false,
		'sanitize' => array( 'strip_tags', 'sanitize_text_field' ),
		'show_desc_tip' => false,
	),
	array(
		'name' => 'first_name',
		'label' => __( 'First name', 'platformpress' ),
		'type'  => 'text',
		'placeholder'  => __( 'Your first name', 'platformpress' ),
		'value' => ( !empty( $fields_value['first_name'] ) ? $fields_value['first_name'] : ''),
		'order' => 5,
		'autocomplete' => false,
		'sanitize' => array( 'strip_tags', 'sanitize_text_field' ),
	),
	array(
		'name' => 'last_name',
		'label' => __( 'Last name', 'platformpress' ),
		'type'  => 'text',
		'placeholder'  => __( 'Your surname', 'platformpress' ),
		'value' => ( !empty( $fields_value['last_name'] ) ? $fields_value['last_name'] : '' ),
		'order' => 5,
		'autocomplete' => false,
		'sanitize' => array( 'strip_tags', 'sanitize_text_field' ),
	),
	array(
		'name' => 'nickname',
		'label' => __( 'Nickname', 'platformpress' ),
		'type'  => 'text',
		'placeholder'  => __( 'Your nickname', 'platformpress' ),
		'value' => ( !empty( $fields_value['nickname'] ) ? $fields_value['nickname'] : '' ),
		'order' => 5,
		'autocomplete' => false,
		'sanitize' => array( 'strip_tags', 'sanitize_text_field' ),
	),
	array(
		'name' => 'display_name',
		'label' => __( 'Display name', 'platformpress' ),
		'type'  => 'select',
		'options'  => ap_user_get_display_name_option( $user_id ),
		'value' => ( !empty( $fields_value['display_name'] ) ? $fields_value['display_name'] : '' ),
		'order' => 5,
		'autocomplete' => false,
		'sanitize' => array( 'strip_tags', 'sanitize_text_field' ),
	),
	array(
		'name' => 'description',
		'label' => __( 'Description', 'platformpress' ),
		'type'  => 'textarea',
		'value' => ( !empty( $fields_value['description'] ) ? $fields_value['description'] : '' ),
		'placeholder'  => __( 'Write something about yourself', 'platformpress' ),
		'rows' => 5,
		'order' => 5,
		'sanitize' => array( 'strip_tags', 'sanitize_text_field' ),
	),
	array(
		'name' => 'signature',
		'label' => __( 'Signature', 'platformpress' ),
		'type'  => 'textarea',
		'value' => ( !empty( $fields_value['signature'] ) ? $fields_value['signature'] : '' ),
		'placeholder'  => __( 'A short signature for showing in hover card', 'platformpress' ),
		'rows' => 5,
		'order' => 5,
		'sanitize' => array( 'strip_tags', 'sanitize_text_field' ),
	),
	);

	$form_fields['account'] = array(
	array(
		'name' => 'user_login',
		'label' => __( 'Username', 'platformpress' ),
		'type'  => 'text',
		'placeholder'  => __( 'Your username', 'platformpress' ),
		'desc'  => __( 'This cannot be changed.', 'platformpress' ),
		'value' => ( !empty( $fields_value['user_login'] ) ? $fields_value['user_login'] : '' ),
		'order' => 5,
		'attr' => 'disabled="disabled"',
		'autocomplete' => false,
		'sanitize' => array( 'sanitize_text_field' ),
		'visibility' => 'me',
	),
	array(
		'name' => 'user_email',
		'label' => __( 'Email', 'platformpress' ),
		'type'  => 'text',
		'placeholder'  => __( 'Your contact email', 'platformpress' ),
		'desc'  => __( 'NOTICE: If you update email then you need to re-verify your email and account.', 'platformpress' ),
		'value' => ( !empty( $fields_value['user_email'] ) ? $fields_value['user_email'] : '' ),
		'order' => 5,
		'autocomplete' => false,
		'edit_disabled' => true,
		'sanitize' => array( 'is_email' ),
		'validate' => array( 'is_email' ),
		'visibility' => 'me',
		'show_desc_tip' => false,
	),
	array(
		'name' => 'password',
		'label' => __( 'Password', 'platformpress' ),
		'type'  => 'password',
		'placeholder'  => __( 'Update your password', 'platformpress' ),
		'value' => '',
		'visibility' => 'me',
		'order' => 5,
		'autocomplete' => false,
	),
	);

	$form_fields = apply_filters( 'ap_user_fields', $form_fields );

	if ( isset( $form_fields[ $group ] ) ) {
		return $form_fields[ $group ];
	}

	return false;
}
