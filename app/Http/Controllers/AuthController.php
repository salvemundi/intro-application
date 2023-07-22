<?php

namespace App\Http\Controllers;

use App\Enums\AuditCategory;
use App\Http\Middleware\AzureAuth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function signOut(): Redirector|Application|RedirectResponse
    {
        $tokenCache = new TokenCache();
        $tokenCache->clearTokens();
        Session::forget('id');
        Session::forget('groups');
        return redirect('/');
    }

    public function signIn(Request $request): RedirectResponse
    {
      // Initialize the OAuth client
      $oauthClient = new GenericProvider([
        'clientId'                => env('OAUTH_APP_ID'),
        'clientSecret'            => env('OAUTH_APP_PASSWORD'),
        'redirectUri'             => env('OAUTH_REDIRECT_URI'),
        'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
        'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
        'urlResourceOwnerDetails' => '',
        'scopes'                  => env('OAUTH_SCOPES')
      ]);

      $authUrl = $oauthClient->getAuthorizationUrl();

      // Save client state so we can validate in callback
      session(['oauthState' => $oauthClient->getState()]);
      // Redirect to AAD sign-in page
      return redirect()->away($authUrl);
    }

    public function callback(Request $request): Redirector|RedirectResponse|Application
    {
      // Validate state
      $expectedState = session('oauthState');
      $request->session()->forget('oauthState');
      $providedState = $request->query('state');

      if (!isset($expectedState)) {
        // If there is no expected state in the session,
        // do nothing and redirect to the home page.
        return redirect('/');
      }

      if (!isset($providedState) || $expectedState != $providedState) {
        return redirect('/')
          ->with('error', 'Invalid auth state')
          ->with('errorDetail', 'The provided auth state did not match the expected value');
      }

      // Authorization code should be in the "code" query param
      $authCode = $request->query('code');
      if (isset($authCode)) {
        // Initialize the OAuth client
        $oauthClient = new GenericProvider([
          'clientId'                => env('OAUTH_APP_ID'),
          'clientSecret'            => env('OAUTH_APP_PASSWORD'),
          'redirectUri'             => env('OAUTH_REDIRECT_URI'),
          'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
          'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
          'urlResourceOwnerDetails' => '',
          'scopes'                  => env('OAUTH_SCOPES')
        ]);

        try
        {
          // Make the token request
          $accessToken = $oauthClient->getAccessToken('authorization_code', [
            'code' => $authCode
          ]);

          $graph = new Graph();
          $graph->setAccessToken($accessToken->getToken());

          $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName,id')
            ->setReturnType(Model\User::class)
            ->execute();

          $groups = $this->getGroupsByUserID($user->getId());

          session(['id' => $user->getId(), 'groups' => $groups,'userName' => $user->getDisplayName()]);

          AuditLogController::Log(AuditCategory::Other(), "Ingelogd");
          $tokenCache = new TokenCache();
          $tokenCache->storeTokens($accessToken, $user);
          if(session('intendedUrl') !== null) {
              return redirect(session('intendedUrl'));
          }
          return redirect('/');
        }
        catch (IdentityProviderException $e)
        {
          return redirect('/')
            ->with('error', 'Error requesting access token')
            ->with('errorDetail', $e->getMessage());
        }
      }

      return redirect('/')
        ->with('error', $request->query('error'))
        ->with('errorDetail', $request->query('error_description'));
    }

    public function connectToAzure(): Graph
    {
        $guzzle = new Client();
        $url = 'https://login.microsoftonline.com/salvemundi.onmicrosoft.com/oauth2/token';
        $token = json_decode($guzzle->post($url, [
            'form_params' => array(
                'client_id' => env("OAUTH_APP_ID"),
                'client_secret' => env("OAUTH_APP_PASSWORD"),
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'client_credentials',
            ),
        ])->getBody()->getContents());

        $accessToken = $token->access_token;

        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        return $graph;
    }

    public function getGroupsByUserID($userID) {
      $graph = $this->connectToAzure();
      try {
          $graphRequest = $graph->createRequest("GET", '/users/'.$userID.'/memberOf')
              ->setReturnType(Model\Group::class)
              ->execute();

          return $graphRequest;
      }
      catch (GraphException $e) {
          return false;
      }
  }
}
