<?php
  class Bobachat_Api {
    protected $headers = array('Content-Type' => 'application/json');
    protected $requestType = 'get';
    protected $boba_prefix;
    protected $referral = 'bobachat-wordpress-plugin';

    function __construct() {
      $this->boba_prefix = 'bobachat_';
    }


    function getSetting($settingName) {
      return get_option($this->getPrefix(). $settingName);
    }

    function setSetting($settingName, $value=null) {
      return update_option($this->getPrefix(). $settingName, $value);
    }

    function getUserToken() {
      return get_option($this->getPrefix(). 'user_token');
    }

    function setUserToken($userToken) {
      return update_option($this->getPrefix(). 'user_token', $userToken );
    }

    function getPrefix() {
      return $this->boba_prefix;
    }
  
    function getSubscriptionForms() {
      $this->requestType = 'get';
      $request = $this->ping('/wordpress/plugin/sf/get/1/2');

      if( is_wp_error( $request ) ) {
        return new WP_Error( 'broke', "Unable to get sites. Please try again later." );
      }
      $sites = $request['body'];
      $result = json_decode($sites);
      return $result;
    }

    function getSites() {
      $this->requestType = 'get';
      $request = $this->ping('/sites');

      if( is_wp_error( $request ) ) {
        return new WP_Error( 'broke', "Unable to get sites. Please try again later." );
      }

      $sites = $request['body'];
      $result = json_decode($sites);
      return $result;
    }

    function getSite($siteId=null) {
      if (empty($siteId)) $siteId = $this->getSiteId();
      if (empty($siteId)) return false;

      $this->requestType = 'get';
      $request = $this->ping('/sites/'. $siteId);

      if( is_wp_error( $request ) ) {
        return false;
      }

      $site = $request['body'];
      $result = json_decode($site);
      return $result;
    }

    function getLists($siteId=null) {
      if (empty($siteId)) { $siteId = $this->getSiteId(); }
      $this->requestType = 'get';
      $request = $this->ping('/sites/'. $siteId. '/lists');
      if( is_wp_error( $request ) ) {
        return new WP_Error( 'broke', "Unable to get lists. Please try again later." );
      }

      return json_decode($request['body']);
    }
  
    function createIntegration($status) {
      if (!empty($siteId)) {
        $this->requestType = 'post';
        $response = $this->ping('/integrationSubscriptionForm', array(
          'status' => $status
        ));
        $integration = json_decode($response['body']);
        return $integration;
      }
      return false;
    }

    function getSiteId() {
      return get_option($this->getPrefix(). 'site_id');
    }

    function setSiteId($siteId) {
      return update_option($this->getPrefix(). 'site_id', $siteId);
    }

    function getListId() {
      return get_option($this->getPrefix(). 'list_id');
    }

    function setListId($listId) {
      return update_option($this->getPrefix(). 'list_id', $listId);
    }

    function deleteWidget($widgetId) {
      $this->requestType = 'post';
      $request = $this->ping('/sites/'.$this->getSiteId().'/widgets/'.$widgetId.'/delete');
      if ( is_wp_error( $request ) ) {
        return array('success' => false);
      }
      return array('success' => true);
    }
    
    function changeEmailStatus($emailId, $emailStatus) {
      $this->requestType = 'put';
      $request = $this->ping('/sites/'.$this->getSiteId().'/emails/'.$emailId, array(
        'email' => array(
          'status' => $emailStatus  
        )
      ));
      if ( is_wp_error( $request ) ) {
        return array('success' => false);
      }
      return array('success' => true);
    }
    
    function deleteEmail($emailId) {
      $this->requestType = 'delete';
      $request = $this->ping('/sites/'.$this->getSiteId().'/emails/'.$emailId);
      if ( is_wp_error( $request ) ) {
        return array('success' => false);
      }
      return array('success' => true);
    }

    function signInUser($email, $password) {
      $this->requestType = 'post';
      $request = $this->ping('/wordpress/sign_in.json', array(
          'user' => array(
            'email' => $email,
            'password' => $password
          ),
          'site_id' => $this->getSiteId()
        )
      );

      if( is_wp_error( $request ) ) {
        return false;
      }

      $newUser = json_decode($request['body']);
      if (intval($newUser->site_id) && $newUser->site_id != $this->getSiteId()) {
        $this->setSiteId($newUser->site_id);
      }
      return $newUser;
    }

    function signUpUser($email, $password, $siteName, $siteDomain) {
      $this->requestType = 'post';
      $request = $this->ping('/wordpress/sign_up.json', array(
          'user' => array(
            'email' => $email,
            'password' => $password,
            'guest_user' => false
          ),
          'site' => array(
            'id' => $this->getSiteId(),
            'name' => $siteName,
            'domain' => $siteDomain
          )
        )
      );

      if( is_wp_error( $request ) ) {
        return false;
      }

      $newUser = json_decode($request['body']);
      if (intval($newUser->site_id) && $newUser->site_id != $this->getSiteId()) {
        $this->setSiteId($newUser->site_id);
      }
      return $newUser;
    }

    function signOutUser() {
      delete_option($this->getPrefix(). 'user_token');
      delete_option($this->getPrefix(). 'site_id');
      delete_option($this->getPrefix(). 'bobachat_access_token');
      delete_option($this->getPrefix(). 'bobachat_list_id');
    }
    function getIntegration() {
      $bobachat_uniq_key = get_option('bobachat_uniq_key');
      $siteUrl = get_bloginfo('url');

      if (!empty($bobachat_uniq_key)) {
        $args = array(
          'headers' => $this->headers,
          'timeout' => 120,
        );
        $body = wp_json_encode( array(
          'domain' => $siteUrl,
          'key' => $bobachat_uniq_key,
        ));
        $url = getenv('BOBACHAT_URL').'/sfi/wordpress/get';
        $response =  wp_remote_post( $url, [
            'headers'   =>  $this->headers,
            'body'       => $body,
        ]);
        $integration = json_decode($response['body']);
        return $integration;
    }
    return false;
    }
    
    function ping($path='', $options=array(), $useTokenAuth=true) {
      $type = $this->requestType;
      $url = getenv('BOBACHAT_URL'). $path;

      // $parsedUrl = parse_url($url);
      // $parseUrlQuery = isset($parsedUrl['query']) ? $parsedUrl['query'] : null;

      // if ($useTokenAuth) { $url .= '&token='. $this->getUserToken(); }

      $args = array(
        'headers' => $this->headers,
        'timeout' => 120,
      );

      if ($type == 'post') {
        $args = array_merge($args, array('method' => 'POST', 'body' => $options));
        $request = wp_remote_post($url, $args);
      }
      else if ($type == 'put') {
        $args = array_merge($args, array('method' => 'PUT', 'body' => $options));
        $request = wp_remote_request($url, $args);
      }
      else if ($type == 'delete') {
        $args = array_merge($args, array('method' => 'DELETE', 'body' => $options));
        $request = wp_remote_request($url, $args);
      }
      else {
        $request = wp_remote_get($url, $args);
      }

      if ( !is_wp_error( $request ) && ( $request['response']['code'] == 500 || $request['response']['code'] == 503 ) ) {
        return new WP_Error( 'broke', "Internal Server Error" );
      }

      if ($useTokenAuth) {
        if (!is_wp_error( $request ) && isset($request['response']['code']) && $request['response']['code'] == 401) {
          $this->signOutUser();
          return new WP_Error( 'broke', 'Unauthorized. Please try again.');
        }
      }

      return $request;
    }
  }
?>
