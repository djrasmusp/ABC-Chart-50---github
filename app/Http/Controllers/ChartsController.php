<?php

namespace App\Http\Controllers;

use App\Track;
use Carbon\Carbon;
use App\Chart;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChartsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function current_chart(){
        $get_week_number = Chart::where('show', '=', 1)->orderByDesc('week', 'DESC')->firstOrFail();
        $chart = DB::select("SELECT (51 - ct.position) AS position,
(51 - (SELECT position FROM chart_track WHERE `chart_id` = (SELECT id FROM charts WHERE `show` = 1 ORDER BY week DESC LIMIT 1, 1) AND track_id = t.id)) AS last_week,
(SELECT count(*) FROM chart_track WHERE track_id = t.id AND chart_id IN (SELECT id from charts WHERE `show` = 1)) AS number_of_weeks,
 t.artist, t.title, t.social, t.image
FROM charts c 
INNER JOIN chart_track ct ON c.id = ct.chart_id
INNER JOIN tracks t ON ct.track_id = t.id
WHERE c.id = (SELECT id FROM charts WHERE `show` = 1 ORDER BY week DESC LIMIT 1)
ORDER BY ct.position DESC");

        $date = Carbon::createFromFormat('Y-m-d H:i:s',$get_week_number->week);

        $finale_array = array(
            'week' => $date->weekOfYear,
            'year' => $date->year,
            'chart' => $chart);

        return response()->json($finale_array);
    }

    public function selected_chart($id){
        $get_week_number = Chart::where('id', $id)->firstOrFail();

        $chart = DB::select("SELECT (51 - ct.position) AS position,
(51 - (SELECT position FROM chart_track WHERE `chart_id` = (SELECT id FROM charts WHERE DATE_FORMAT(week, '%Y-%m-%d') = DATE_FORMAT(DATE_SUB('".$get_week_number->week."', INTERVAL 7 DAY), '%Y-%m-%d') LIMIT 1) AND track_id = t.id)) AS last_week,
(SELECT count(*) FROM chart_track WHERE track_id = t.id AND chart_id IN (SELECT id from charts WHERE `show` = 1)) AS number_of_weeks,
 t.artist, t.title
FROM charts c 
INNER JOIN chart_track ct ON c.id = ct.chart_id
INNER JOIN tracks t ON ct.track_id = t.id
WHERE c.id = ".$id."
ORDER BY ct.position DESC");

        $date = Carbon::createFromFormat('Y-m-d H:i:s',$get_week_number->week);

        $finale_array = array(
            'week' => $date->weekOfYear,
            'year' => $date->year,
            'chart' => $chart);

        return response()->json($finale_array);
    }

    private function getSpotify($track){
        $track_url = urlencode($track);
        $data = json_decode(file_get_contents('https://api.7357.dk/api/spotify/'. $track_url), true);
        return $data['0'];
    }

    public function post_chart(){
        $json = json_decode(file_get_contents('php://input'));

        $chart_date = $json->date .' 12:00:00';

        $make_chart = Chart::firstOrCreate(
            ['week' => $chart_date],
            ['show' => 0]
        );
        $make_chart->save();

        if($make_chart->wasRecentlyCreated) {

            $chart_id = $make_chart->id;

            $top50 = $json->top50;

            foreach ($top50 as $track) {
                $track_exists = Track::where('id', '=', $track->trackID)->first();
                if ($track_exists === null) {
                    $add_track = new Track;
                    $add_track->id = $track->trackID;
                    $add_track->artist = $track->artist;
                    $add_track->title = $track->title;
                    if ($track->social != null) {
                        $add_track->social = $track->social;
                    }
                    $add_track->image = $this->getSpotify($track->artist . ' ' . $track->title)['image'];
                    $add_track->spotify = $this->getSpotify($track->artist . ' ' . $track->title)['id'];
                    $add_track->save();
                }

                $make_chart = Chart::find($chart_id);
                $make_chart->tracks()->attach(
                    $track->trackID,
                    ['position' => (51 - $track->position)]
                );
            };
        };

        return 'oprettet';
    }
}
