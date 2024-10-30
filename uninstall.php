<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function vBulletinBb_delete_plugin() {
	delete_option('cms2cms-vBulletinBb-login');
	delete_option('cms2cms-vBulletinBb-key');
	delete_option('cms2cms-vBulletinBb-depth');
	removeBridge();
}

function removeBridge()
{
	global $wp_filesystem;
	$bridgeFolder = ABSPATH . 'cms2cms';
	if ($wp_filesystem->is_dir($bridgeFolder)) {
		$wp_filesystem->delete($bridgeFolder, true);
	} else {
		return new WP_Error('writing_error', 'Cannot Remove bridge folder');
	}
}

vBulletinBb_delete_plugin();