<?php

/**
 * Class CmsPluginFunctionsvBulletinBb
 */
class CmsPluginFunctionsvBulletinBb
{

    private $jsConfig = '{
        "host"            : "https://app.cms2cms.com",
        "youtube"         : "https://www.youtube.com/watch?feature=player_detailpage&v=DQK01NbrCdw#t=25s",
        "feedback"        : "https://cms2cms.polldaddy.com/s/survey",
        "support"         : "https://app.cms2cms.com?chat=fullscreen",
        "wizard"          : "https://app.cms2cms.com/wizard",
        "facebook"        : "//www.facebook.com/CMS2CMS/",
        "twitter"         : "//twitter.com/Cms2Cms",
        "wp_feedback"     : "//wordpress.org/support/plugin/cms2cms-automated-vbulletin-to-bbpress-migrator/reviews",
        "public_host"     : "//www.cms2cms.com",
        "bridge"          : "https://app.cms2cms.com/bridge/download",
        "vBulletinExtension" : "https://cms2cms.com/files/vBulletin/cms2cmsconnector/com_cms2cmsconnector.zip",
        "ticket"          : "//support.magneticone.com/index.php?/Tickets/Submit/RenderForm/56",
        "logout"          : "https://app.cms2cms.com/auth/logout",
        "auth_check"      : "https://app.cms2cms.com/api/auth-check"
    }';

    private $config = array (
        'host'            => 'https://app.cms2cms.com',
        'youtube'         => 'https://www.youtube.com/watch?feature=player_detailpage&v=DQK01NbrCdw#t=25s',
        'feedback'        => 'https://cms2cms.polldaddy.com/s/survey',
        'support'         => 'https://app.cms2cms.com?chat=fullscreen',
        'wizard'          => 'https://app.cms2cms.com/wizard',
        'facebook'        => '//www.facebook.com/CMS2CMS/',
        'twitter'         => '//twitter.com/Cms2Cms',
        'wp_feedback'     => '//wordpress.org/support/plugin/cms2cms-automated-vbulletin-to-bbpress-migrator/reviews',
        'public_host'     => '//www.cms2cms.com',
        'bridge'          => 'https://app.cms2cms.com/bridge/download',
        'vBulletinExtension' => 'https://cms2cms.com/files/vBulletin/cms2cmsconnector/com_cms2cmsconnector.zip',
        'ticket'          => '//support.magneticone.com/index.php?/Tickets/Submit/RenderForm/56',
        'logout'          => 'https://app.cms2cms.com/auth/logout',
        'auth_check'      => 'https://app.cms2cms.com/api/auth-check'
    );

    /**
     * User data
     * @return object
     */
    public function getUser()
    {
        $user_ID = get_current_user_id();
        $user_info = get_userdata($user_ID);

        return $user_info;
    }

    /**
     * User name
     * @return string
     */
    public function getUserName()
    {
        return $this->getUser()->display_name;
    }

    /**
     * User email
     * @return string
     */
    public function getUserEmail()
    {
        return $this->getUser()->user_email;
    }

    /**
     * Get front Url
     * @return string
     */
    public function getFrontUrl()
    {
        return str_replace(array('http:', 'https:'), '', plugin_dir_url( __FILE__ ));
    }

    /**
     * @return array
     */
    public function getAuthData()
    {
        $cms2cms_vBulletinBb_access_login = get_option('cms2cms-vBulletinBb-login');
        $cms2cms_vBulletinBb_access_key   = get_option('cms2cms-vBulletinBb-key');

        return array(
            'email'     => $cms2cms_vBulletinBb_access_login,
            'accessKey' => $cms2cms_vBulletinBb_access_key
        );
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        $cms2cms_vBulletinBb_access_key = get_option('cms2cms-vBulletinBb-key');

        return ($cms2cms_vBulletinBb_access_key != false);
    }

    /**
     * Get Options
     * @return array
     */
    public function getOptions()
    {
        $key   = get_option('cms2cms-vBulletinBb-key');
        $login = get_option('cms2cms-vBulletinBb-login');

        $response = 0;

        if ( $key && $login ) {
            $response = array(
                'email' => $login,
                'accessKey' => $key,
            );
        }

        return $response;
    }

    /**
     * Save options
     * @return array
     */
    public function saveOptions()
    {
        $key          = substr( $_POST['accessKey'], 0, 64 );
        $login        = sanitize_email( $_POST['login'] );
        $bridge_depth = str_replace(get_site_url(), '', $this->getFrontUrl());
        $bridge_depth = trim($bridge_depth, DIRECTORY_SEPARATOR);
        $bridge_depth = explode(DIRECTORY_SEPARATOR, $bridge_depth);
        $bridge_depth = count( $bridge_depth );
        $response     = array('errors' => _('Provided credentials are not correct: ' . $key . ' = ' . $login ));

        if ( $key && $login ) {
            delete_option('cms2cms-vBulletinBb-key');
            add_option('cms2cms-vBulletinBb-key', $key);

            delete_option('cms2cms-vBulletinBb-login');
            add_option('cms2cms-vBulletinBb-login', $login);

            delete_option('cms2cms-vBulletinBb-depth');
            add_option('cms2cms-vBulletinBb-depth', $bridge_depth);

            $response = array('success' => true);
        }

        return $response;
    }

    /**
     * Clear options
     */
    public function clearOptions()
    {
        delete_option('cms2cms-vBulletinBb-login');
        delete_option('cms2cms-vBulletinBb-key');
        delete_option('cms2cms-vBulletinBb-depth');
    }

    /**
     * @param $message
     * @param $domain
     * @inheritdoc
     */
    public function _e($message, $domain)
    {
        return _e($message, $domain);
    }

    /**
     * @param $message
     * @param $domain
     * @return string|void
     */
    public function __($message, $domain)
    {
        return __($message, $domain);
    }

    /**
     * @param $name
     * @return string
     */
    public function getFormTempKey($name)
    {
        return wp_create_nonce($name);
    }

    /**
     * @param $value
     * @param $name
     * @return false|int
     */
    public function verifyFormTempKey($value, $name)
    {
        return wp_verify_nonce($value, $name);
    }

    /**
     * Get app url
     * @param bool $json Json return
     * @return string
     */
    public function getConfig($json = false)
    {
        return  $json ? $this->jsConfig : $this->config;
    }

    /**
     * Get app url
     * @return string
     */
    public function getAppUrl()
    {
        $config = $this->getConfig();
        return $config['host'];
    }

    /**
     * @return string|void
     */
    public function getPluginSourceName()
    {
        return $this->__('vBulletin', 'cms2cms-vBulletinBb-migration');
    }

    /**
     * @return string
     */
    public function getPluginSourceType()
    {
        return 'vBulletin';
    }

    /**
     * @return string|void
     */
    public function getPluginTargetName()
    {
        return $this->__('bbPress', 'cms2cms-vBulletinBb-migration');
    }

    /**
     * @return string
     */
    public function getPluginTargetType()
    {
        return 'bbPress';
    }

    /**
     * @return string
     */
    public function getPluginNameLong()
    {
        return sprintf(
            $this->__('CMS2CMS: Automated %s to %s Migration ', 'cms2cms-vBulletinBb-migration'),
            $this->getPluginSourceName(),
            $this->getPluginTargetName()
        );
    }

    /**
     * @return string
     */
    public function getPluginNameShort()
    {
        return sprintf(
            $this->__('%s to %s', 'cms2cms-vBulletinBb-migration'),
            $this->getPluginSourceName(),
            $this->getPluginTargetName()
        );
    }

    /**
     * @return string
     */
    public function getPluginReferrerId()
    {
        return sprintf(
            'Plugin | %s | %s to %s',
            $this->getPluginTargetType(),
            $this->getPluginSourceType(),
            $this->getPluginTargetType()
        );
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->getAppUrl() . '/auth/sign-up';
    }

    /**
     * Get bridge Url
     * @return string
     */
    public function getBridgeUrl()
    {
        $config = $this->getConfig();

        return $config['bridge'];
    }

    /**
     * Get bridge Url
     * @return string
     */
    public function getExtensionUrl()
    {
        $config = $this->getConfig();
        return $config['vBulletinBbExtension'];
    }

    /**
     * @return string
     */
    public function getBridgeFaqUrl()
    {
        return 'https://www.cms2cms.com/faqs/what-is-the-connection-bridge-and-how-to-install-it';
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->getAppUrl() . '/auth/sign-in';
    }

    /**
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->getAppUrl() . '/auth#forgot-password';
    }

    /**
     * @return string
     */
    public function getLogOutUrl()
    {
        return $this->getAppUrl() . '/auth/logout';
    }

    public function logOut()
    {
        if (isset($_REQUEST['_wpnonce'])) {
            $nonce = $_REQUEST['_wpnonce'];
            if ($this->verifyFormTempKey($nonce, 'cms2cms_vBulletinBb_logout')
                && $_POST['cms2cms_vBulletinBb_logout'] == 1
            ) {
                $this->clearOptions();
            }
        }
    }

    /**
     * @return string
     */
    public function getWizardUrl()
    {
        return $this->getAppUrl() . '/wizard';
    }

    /**
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->getAppUrl() . '/dashboard';
    }

}