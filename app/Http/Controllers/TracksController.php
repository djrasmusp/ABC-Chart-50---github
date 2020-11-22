<?php

namespace App\Http\Controllers;

use App\Track;
use http\Env\Response;
use Illuminate\Http\Request;
use SpotifyWebAPI;

class TracksController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->spotify = new SpotifyWebAPI\SpotifyWebAPI();
        $this->session = new SpotifyWebAPI\Session( env('SPOTIFY_CLIENT_ID'), env('SPOTIFY_CLIENT_SECRET'));

        $this->session->requestCredentialsToken();
        $accessToken = $this->session->getAccessToken();

        $this->spotify->setAccessToken($accessToken);
    }

    public function get_tracks(){
        return response()->json([
            'tracks' => Track::all()
        ]);
    }

    public function get_track($id){
        $track = Track::where('id','=', $id)->first();

        return response()->json([
            'track' => $track
        ]);
    }

    public function get_spotify($id){
        $search_resultat = $this->spotify->search(urldecode($id), 'track', array('market' => 'DK', 'limit' => 2));

        if(empty($search_resultat->tracks->items)){
            $album_data[] = array('id' => null, 'name' => null, 'image' => null);
            return $album_data;

        }else{
            foreach ($search_resultat->tracks->items as $album){
            $track_id = $album->id;
            $track_name = $album->name;
            $track_images = $album->album->images;
            $track_image = $track_images['2']->url;

            $album_data[] = array('id' => $track_id, 'name' => $track_name, 'image' => $track_image);
            }

            return $album_data;
        }
    }
}
