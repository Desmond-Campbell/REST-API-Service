<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $R) {

        return view('index');

    }

    public function request(Request $R) {

        $requestRaw = $R->input( 'request' );

        // return $requestRaw;

        $request = [];
        $request['url'] = $requestRaw['url'];

        $request['method'] = $requestRaw['method'] ?? 'GET';
        $request['options'] = $requestRaw['options'] ?? [];
        $request['auth_type'] = $requestRaw['auth_type'] ?? '';
        $request['auth_token'] = $requestRaw['auth_token'] ?? '';
        $request['auth_username'] = $requestRaw['auth_username'] ?? '';
        $request['auth_password'] = $requestRaw['auth_password'] ?? '';
        $request['body_type'] = $requestRaw['body_type'] ?? '';
        $request['body'] = $requestRaw['body'] ?? '{}';
        $headersRaw = array_filter( explode( "\n", $requestRaw['headers'] ?? '' ) );

        $headers = [];

        if ( $headersRaw ) {
            
            foreach ( $headersRaw as $header ) {

                $parts = explode( ':', $header );

                if ( count( $parts ) > 1 ) {

                    $key = $parts[0];
                    $value = trim( substr( $header, strlen( $key ) + 1, 1000 ) );
                    $key = trim( $key );

                    $headers[$key] = $value;

                }

            }

        }

        $auth_type = $request['auth_type'] ?? null;
        $url = $request['url'];
        $verb = $request['method'];
        $body_type = $request['body_type'] ?? null;

        $payload = $request['body'] ?? '{}';

        if ( $body_type == 'json' ) {
            
            $payload = json_decode( $payload );

        } elseif( $body_type == 'form' ) {
            
            $payload = $payload;

        } else {

            $payload = '{}';
            
        }

        $isShowHttpErrors = $request['options']['http_errors'] ?? false;

        $client = new Client( [ 'base_uri' => $url, 'http_errors' => $isShowHttpErrors, 'stream' => true ] );

        if ( $verb == 'POST' ) {
            
            $args = [ 'form_params' => $payload ];

        } else {

            if ( is_a( $payload, 'Array' ) ) {
            
                $url = $this->addParameters( $url, $payload );

            }

            $args = [];

        }

        $args['headers'] = $headers;
        // $args['headers']['Accept'] = 'application/json';

        if ( $auth_type == 'basic' ) {

        } elseif ( $auth_type == 'bearer' ) {

            $token = $request['auth_token'] ?? '';

            if ( $token ) {

                $args['headers']['Authorization'] = "Bearer {$token}";

            }

        }

        $response = $client->request( $verb, $url, $args );

        $result = $response->getBody();

        while (!$result->eof()) {
            echo $result->read(1024);
        }

        if ( json_decode( $result ) ?? null ) {

            $result = json_decode( $result );

        }

        return $result;

    }

    public function addParameters( $url, $values ) {

        $new_values = array_walk( $values, function ( &$value, $key ) { $value = "$key=" . urlencode( $value ); } );

        $query_string = implode( '&', array_values( $values ) );

        $url .= stristr( $url, '?' ) ? '&' : '?';
        $url .= $query_string;

        return $url;

    }

}
