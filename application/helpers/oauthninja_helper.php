<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('oauthninja')) {

    function oauthninja($provader) {
        $provader = strtolower($provader);

        $providers = array(
            'blooie' 		=> 'OAuth2',
            'dropbox' 		=> 'OAuth',
            'facebook' 		=> 'OAuth2',
            'foursquare' 	=> 'OAuth2',
            'flickr' 		=> 'OAuth',
            'google' 		=> 'OAuth2',
            'github' 		=> 'OAuth2',
            'linkedin' 		=> 'OAuth',
            'paypal' 		=> 'OAuth2',
            'soundcloud' 	=> 'OAuth2',
            'twitter' 		=> 'OAuth',
            'windowslive' 	=> 'OAuth2'
        );

        if (!isset($providers[$provader])) {
            throw new Exception(sprintf('There is no strategy for provider "%s"', $provader));
        }

        return '/' . strtolower($providers[$provader]) . '/' . strtolower($providers[$provader]) . '/' . $provader;
    }

}