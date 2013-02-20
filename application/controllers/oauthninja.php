<?php

class Oauthninja extends CI_Controller {

    protected $providers = array(
        'blooie' => 'OAuth2',
        'dropbox' => 'OAuth',
        'facebook' => 'OAuth2',
        'foursquare' => 'OAuth2',
        'flickr' => 'OAuth',
        'google' => 'OAuth2',
        'github' => 'OAuth2',
        'linkedin' => 'OAuth',
        'openid' => 'OpenId',
        'paypal' => 'OAuth2',
        'soundcloud' => 'OAuth2',
        'twitter' => 'OAuth',
        'windowslive' => 'OAuth2',
    );

    public function authorize($provider) {
        switch ($this->providers[$provider]) {
            case 'OAuth':
                $this->oauth1($provider);
                break;
            case 'OAuth2':
                $this->oauth2($provider);
                break;
            default:
                throw new Exception('Unsupported provider');
        }
    }

    public function oauth1($provider) {
        $this->load->helper('url');

        $this->load->spark('oauth/0.3.1');

        $item = $this->config->item($provider);

        if (!$item['key'] || !$item['secret']) {
            throw new Exception('Invalid "key" or "secret"');
        }

        // Create an consumer from the config
        $consumer = $this->oauth->consumer(array(
            'key' => $item['key'],
            'secret' => $item['secret'],
                )
        );

        // Load the provider
        $provider = $this->oauth->provider($provider);

        // Create the URL to return the user to
        $callback = site_url('auth/oauth/' . $provider->name);

        if (!$this->input->get_post('oauth_token')) {
            // Add the callback URL to the consumer
            $consumer->callback($callback);

            // Get a request token for the consumer
            $token = $provider->request_token($consumer);

            // Store the token
            $this->session->set_userdata('oauth_token', base64_encode(serialize($token)));

            // Get the URL to the twitter login page
            $url = $provider->authorize($token, array(
                'oauth_callback' => $callback,
                    ));

            // Send the user off to login
            redirect($url);
        } else {
            if ($this->session->userdata('oauth_token')) {
                // Get the token from storage
                $token = unserialize(base64_decode($this->session->userdata('oauth_token')));
            }

            if (!empty($token) AND $token->access_token !== $this->input->get_post('oauth_token')) {
                // Delete the token, it is not valid
                $this->session->unset_userdata('oauth_token');

                // Send the user back to the beginning
                exit('invalid token after coming back to site');
            }

            // Get the verifier
            $verifier = $this->input->get_post('oauth_verifier');

            // Store the verifier in the token
            $token->verifier($verifier);

            // Exchange the request token for an access token
            $token = $provider->access_token($consumer, $token);

            // We got the token, let's get some user data
            $user = $provider->get_user_info($consumer, $token);

            // Here you should use this information to A) look for a user B) help a new user sign up with existing data.
            // If you store it all in a cookie and redirect to a registration page this is crazy-simple.
            echo "<pre>Tokens: ";
            var_dump($token) . PHP_EOL . PHP_EOL;

            echo "User Info: ";
            var_dump($user);
        }
    }

    public function oauth2($provider) {
        $this->load->helper('url_helper');

        $this->load->spark('oauth2/0.4.0');
        $item = $this->config->item($provider);
        $provider = $this->oauth2->provider($provider, array(
            'id' => $item['key'],
            'secret' => $item['secret'],
                ));
        if (!$this->input->get('code')) {
            // By sending no options it'll come back here
            $url = $provider->authorize();
            redirect($url);
        } else {
            // Howzit?
            try {
                $token = $provider->access($_GET['code']);

                $user = $provider->get_user_info($token);

                // Here you should use this information to A) look for a user B) help a new user sign up with existing data.
                // If you store it all in a cookie and redirect to a registration page this is crazy-simple.
                echo "<pre>Tokens: ";
                var_dump($token);

                echo "\n\nUser Info: ";
                var_dump($user);
            } catch (OAuth2_Exception $e) {
                show_error('That didnt work: ' . $e);
            }
        }
    }

}
