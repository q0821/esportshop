<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Google
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JFBCOAuth1Client', JPATH_SITE . '/components/com_jfbconnect/libraries/joomla/oauth1/client.php');

/**
 * Google OAuth authentication class
 *
 * @package     Joomla.Platform
 * @subpackage  Google
 * @since       12.3
 */
class JFBConnectProviderYahooOauth1 extends JFBCOAuth1Client
{
    /**
     * Constructor.
     *
     * @param   JRegistry $options  JGoogleAuth options object.
     * @param   JOAuth2Client $client   OAuth client for Google authentication.
     *
     * @since   12.3
     */
    public function __construct(JRegistry $options = null, JHttp $http = null, JInput $input = null, JApplicationWeb $application = null, $version = null)
    {
        // We want to use cURL, so need to build this all up. This also helps poke into each of the things when debugging
        $httpOptions = new JRegistry();
        $httpOptions->set('follow_location', false);
        try
        {
            $transport = new JFBConnectJoomlaHttpTransportCurl($httpOptions);
        }
        catch (Exception $e)
        {
            JFBCFactory::log('JFBConnect requires the CURL PHP extension to be installed and callable.', 'error');
            $transport = null;
        }
        $http = new JHttp($httpOptions, $transport);

        $options = isset($options) ? $options : new JRegistry;
        $options->set('state', JSession::getFormToken()); // This is sent back to us to identify the provider
        if (!$options->exists('sendheaders'))
            $options->set('sendheaders', true);

        parent::__construct($options, $http, $input, $application, $version);
    }

    /**
     * Method to retrieve data from Yahoo
     *
     * @param   string $url      The URL for the request.
     * @param   mixed $data     The data to include in the request.
     * @param   array $headers  The headers to send with the request.
     * @param   string $method   The type of http request to send.
     *
     * @return  mixed  Data from Yahoo.
     *
     * @since   12.3
     */
    public function query($url, $data = array(), $method = 'GET')
    {
        $token = $this->getToken();
        $params = array(
            'oauth_token' => $token['key']
        );
        return $this->oauthRequest($url, $method, $params, $data);
    }

    public function isAuthenticated()
    {
        $creds = $this->verifyCredentials();
        if (is_object($creds) && isset($creds->code) && $creds->code == 200)
            return true;
        else
            return false;
    }

    /**
     * Method to verify if the access token is valid by making a request to an API endpoint.
     *
     * @return  boolean  Returns true if the access token is valid and false otherwise.
     *
     * @since   13.1
     */
    private static $creds;
    public function verifyCredentials()
    {
        $token = $this->getToken();

        if (!$token)
        {
            $response = new stdClass();
            $response->code = null;
            return $response;
        }

        if (self::$creds)
            return self::$creds;

        // Set parameters.
        $parameters = array(
            'oauth_token' => $token['key']
        );

        // Set the API url.
        // $path = 'https://social.yahooapis.com/v1/me/guid';
        $path = 'https://social.yahooapis.com/v1/user/me/profile';

        // Send the request.
        $response = $this->oauthRequest($path, 'GET', $parameters);

        // Verify response
        if (is_object($response) && isset($response->code) && $response->code == 200)
        {
            $xml = JFactory::getXML($response->body, false);
            $arr = json_decode(json_encode($xml),TRUE);
            $response->body = JArrayHelper::toObject($arr);

            // Store this response for this page load to be used again
            // This prevents rate limiting
            // Probably should store in session, but want more security checks to ensure it doesn't validate the wrong person
            self::$creds = $response;
            return $response;
        }
        else
        {
            return $response;
        }
    }

    public function getCreds() {
        return self::$creds;
    }
    /**
     * Method to validate a response.
     *
     * @param   string $url       The request URL.
     * @param   JHttpResponse $response  The response to validate.
     *
     * @return  void
     *
     * @since  13.1
     * @throws DomainException
     */
    public function validateResponse($url, $response)
    {
        if (!$code = $this->getOption('success_code'))
        {
            $code = 200;
        }

        if (strpos($url, '::(~)') === false && $response->code != $code)
        {
            if ($error = json_decode($response->body))
            {
                if (isset($error->errors))
                {
                    $error = $error->errors;
                    if (is_array($error))
                        $error = $error[0];
                }
                if (JFBCFactory::config()->get('facebook_display_errors'))
                    JFBCFactory::log('Yahoo API Error: Code ' . $response->code . ' received with message: ' . $error->detail . '.', 'error');
            }
            else
            {
                JFBCFactory::log('Yahoo API Error: ' . $response->body, 'error');
            }
            return false;
        }
        return true;
    }

    /**
     * Method used to authorise the application.
     *
     * @return  void
     *
     * @since   13.1
     */
    private function _authorise()
    {
        $url = $this->getOption('authenticateURL') . '?oauth_token=' . $this->token['key'];

        if ($this->getOption('scope'))
        {
            $scope = is_array($this->getOption('scope')) ? implode(' ', $this->getOption('scope')) : $this->getOption('scope');
            $url .= '&scope=' . urlencode($scope);
        }

        if ($this->getOption('sendheaders'))
        {
            $this->application->redirect($url);
        }
    }
}
