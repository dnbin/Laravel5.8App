<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Search;
use App\Rules\ChildrenAgeString;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SearchesController extends Controller
{
    //
	public function list(){
		try {
			/** @var User $user */
			$user   = Auth::user();
			/** @var Search $search */
			if($user->hasRole(['admin'])){
				$searches=Search::with(['user','city.neighborhoods','neighborhoods'])->withCount('entries')->withCount('snapshots')->get();
			}
			else{
				$searches = $user->searches()->with(['user','city.neighborhoods','neighborhoods'])->withCount('entries')->withCount('snapshots')->get();
			}
			return response()->json($searches);
		}
		catch(\Exception $e){
			return response()->json($e->getMessage(),400);
		}
	}

	/**
	 * @param int $id
	 *
	 * @throws \Exception
	 */
	public function delete(int $id){
		try {
			/** @var User $user */
			$user   = Auth::user();
			if($user->hasRole(['admin'])){
				/** @var Search $search */
				$search=Search::findOrFail($id);
			}
			else{
				$search = $user->searches()->findOrFail( $id );
			}
			$search->entries()->detach();
			$search->delete();
			return response()->json('Search has been deleted');
		}
		catch(\Exception $e){
			return response()->json($e->getMessage(),400);
		}
	}

    /**
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function view(int $id){
			/** @var User $user */

			if(Auth::check()) {
                $user = Auth::user();
                if($user->hasRole(['admin'])){
                    /** @var Search $search */
                    $search=Search::with(['user','city','neighborhoods','entries'=>function($q){
                        $q->wherePivot('is_latest',1)->orderBy('price');
                    },'snapshots'])->findOrFail($id);
                }
                else{
                    $search = $user->searches()->with(['user','city','neighborhoods','entries'=>function($q){
                        //$q->orderBy('price');
                        $q->wherePivot('is_latest',1)->orderBy('price');
                    },'snapshots'])->findOrFail( $id );
                }
            }
			else{
			    // no auth.. signed route
                $search=Search::with(['user','city','neighborhoods','entries'=>function($q){
                    $q->orderBy('price');
                },'snapshots'])->findOrFail($id);
            }

			return view('searches.view')->with('search',$search);
	}


	public function add(Request $request) {
		try {
			if(!Auth::check()){
				throw new \Exception('Only registered users is allowed to create new searches. Please register <a href="'.route('register').'" target="_blank">here</a>');
			}
			$data = $request->validate( [
				//'city'=>'required|string',
                'city_id'=>'required|integer|exists:cities,id',
                'neighborhood_ids'=>'sometimes|nullable|array|max:3',
                'neighborhood_ids.*'=>'required_with:neighborhood_ids|integer|exists:neighborhoods,id',
				'check_in_date'=>'required|date|after_or_equal:today',
				'nights'=>'required|numeric|min:1|max:365',
				'hotel_class'=>'sometimes|nullable|numeric|min:1|max:5',
				'rating'=>'sometimes|nullable|numeric|min:1|max:10',
				'max_budget'=>'sometimes|nullable|numeric',
				'max_budget_currency'=>'sometimes|nullable|in:USD,EUR,GBP,CAD',
                //'max_budget_discount'=>'sometimes|nullable|integer|min:30|max:90',
				'number_of_adults'=>'required|integer|min:1|max:99',
                'children'=>['sometimes','nullable',new ChildrenAgeString],
                'frequency'=>'required|string|in:default,daily'
			] );

			$search=new Search();
			$search->user_id=Auth::user()->id;
            $search->city_id = $data['city_id'];

            $search->check_in_date=$data['check_in_date'];
			$search->nights=$data['nights'];

            if(!empty($data['hotel_class'])) {
                $search->hotel_class = $data['hotel_class'];
            }
            if(!empty($data['rating'])) {
                $search->rating = $data['rating'];
            }

            if(!empty($data['max_budget'])) {
                $search->max_budget = $data['max_budget'];
                $search->max_budget_currency = $data['max_budget_currency'];
            }
/*
            if(!empty($data['max_budget_discount'])){
                $search->max_budget_discount=$data['max_budget_discount'];
            }
*/

			$search->number_of_adults=$data['number_of_adults'];
            if(isset($data['children'])){
                $search->children=explode(',',$data['children']);
            }
			$search->frequency=$data['frequency'];
			$search->ip=$_SERVER['REMOTE_ADDR'] ?? null;
			$search->referrer=$_SERVER['HTTP_REFERER'] ?? null;
			$search->save();
            if(!empty($data['neighborhood_ids'])) {
                //dump($data['neighborhood_ids']);
                $search->neighborhoods()->sync($data['neighborhood_ids']);
            }
            return response()->json('Your search has been saved. We will send you the list of matching hotel offers to your email address shortly.');
		}catch(ValidationException $e){
			return response()->json([ 'errors' => $e->validator->errors() ],400);
		} catch ( \Exception $e ) {
			return response()->json( $e->getMessage(), 400 );
		}
	}

    /**
     * @param int $id
     *
     * @throws \Exception
     */
    public function update(int $id,Request $request){
        try {
            $data = $request->validate( [
                //'city'=>'required|string',
                'city_id'=>'required|integer|exists:cities,id',
                'neighborhood_ids'=>'sometimes|nullable|array',
                'neighborhood_ids.*'=>'required_with:neighborhood_ids|integer|exists:neighborhoods,id',
                //'neighborhood_id'=>'sometimes|nullable|integer|exists:neighborhoods,id',
                'check_in_date'=>'required|date|after_or_equal:today',
                'nights'=>'required|numeric|min:1|max:365',
                'hotel_class'=>'sometimes|nullable|numeric|min:1|max:5',
                'rating'=>'sometimes|nullable|numeric|min:1|max:10',
                'max_budget'=>'sometimes|nullable|numeric',
                'max_budget_currency'=>'sometimes|nullable|in:USD,EUR,GBP,CAD',
                //'max_budget_discount'=>'sometimes|nullable|integer|min:30|max:90',
                'number_of_adults'=>'required|integer|min:1|max:99',
                'children'=>['sometimes','nullable',new ChildrenAgeString],
                'frequency'=>'required|string|in:default,daily'
            ] );
            /** @var User $user */
            $user   = Auth::user();
            if($user->hasRole(['admin'])){
                /** @var Search $search */
                $search=Search::with(['city','neighborhoods'])->findOrFail($id);
            }
            else{
                $search = $user->searches()->with(['city','neighborhoods'])->findOrFail( $id );
            }

            //$search->city=$data['city'];

            if(!empty($data['city_id'])) {
                $search->city_id = $data['city_id'];
            }
            else{
                $search->city_id=null;
            }
            if(!empty($data['neighborhood_ids'])) {
                $search->neighborhoods()->sync($data['neighborhood_ids']);
            }
            else{
                $search->neighborhoods()->detach();
            }


            $search->check_in_date=$data['check_in_date'];
            $search->nights=$data['nights'];
            if(!empty($data['hotel_class'])) {
                $search->hotel_class = $data['hotel_class'];
            }
            else{
                $search->hotel_class=null;
            }

            if(!empty($data['rating'])) {
                $search->rating = $data['rating'];
            }
            else{
                $search->rating=null;
            }

            if(!empty($data['max_budget'])) {
                $search->max_budget = $data['max_budget'];
                $search->max_budget_currency = $data['max_budget_currency'];
            }
            else{
                $search->max_budget=null;
                $search->max_budget_currency=null;
            }
/*
            if(!empty($data['max_budget_discount'])){
                $search->max_budget_discount=$data['max_budget_discount'];
            }
            else{
                $search->max_budget_discount=null;
            }
*/

            $search->number_of_adults=$data['number_of_adults'];
            $search->frequency=$data['frequency'];
            $search->ip=$_SERVER['REMOTE_ADDR'] ?? null;
            $search->hotel_offers_sent_at=null; // clear hotel offers sent at on update. will send email immediately.
            if(isset($data['children'])){
                $search->children=explode(',',$data['children']);
            }
            else{
                $search->children=null;
            }

            //$search->referrer=$_SERVER['HTTP_REFERER'] ?? null;
            $search->save();
            $search->load('user')->loadCount('entries')->loadCount('snapshots');
            return response()->json($search);
        }
        catch(ValidationException $e){
            return response()->json(['errors'=>$e->validator->errors()],400);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),400);
        }
    }

    // set search status to false. It's signed url. No more validation
    public function unsubscribe(int $id){
        /** @var Search $search */
        $search=Search::findOrFail($id);
        $search->status=0;
        $search->save();
        return view('searches.unsubscribe')->with(['search'=>$search]);
    }


    public function getAvgCityPrice(string $city,Request $request){
        //SELECT AVG(price) FROM entries WHERE feed_id=3 AND city LIKE 'San Francisco' AND star_rating=3 AND review_score=7
        try{
            $feed_id=1;//expedia
            $avgprice=DB::table('entries')->selectRaw('AVG(price) as amount,currency')
                            ->where('feed_id',$feed_id)
                            ->where('city','like',rawurldecode($city));
            if($request->query('hotel_class')){
                $hotel_class=(int)$request->query('hotel_class');
                if($hotel_class>0 && $hotel_class<=5){
                    $avgprice=$avgprice->where('star_rating','>=',$request->query('hotel_class'));
                }
            }

            /*
             * // review score is not available from expedia feed
            if($request->query('review_score')){
                $review_score=(int)$request->query('review_score');
                if($review_score>0 && $review_score<=10){
                    $avgprice=$avgprice->where('review_score','>=',$review_score);
                }
            }
            */

            $avgprice=$avgprice->groupBy('currency')
                            ->get();// get avg price based on expedia feed
            if(empty($avgprice)){
                throw new \Exception('No avg price found');
            }
            return response()->json($avgprice->first());
        }
        catch (\Exception $e){
            return response()->json($e->getMessage(),400);
        }
    }

    //  search city by user input
    public function citySearch(Request $request){
        try{
            if(empty($request->query('city'))){
                throw new \Exception('City parameter is required');
            }
            $cities=City::with(['country','neighborhoods'])->where('name','like',$request->query('city'))->get();
            if($cities->isEmpty()){
                throw new \Exception('City is not found');
            }
            return response()->json($cities);
        }
        catch (\Exception $e){
            return response()->json($e->getMessage(),400);
        }
    }

    /**
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cityNeighborhoods(int $id,Request $request){
        try{
            /** @var City $city */
            $city=City::findOrFail($id);
            return response()->json($city->neighborhoods);
        }
        catch (\Exception $e){
            return response()->json($e->getMessage(),400);
        }

    }

    public function city(int $id,Request $request){
        try{
            /** @var City $city */
            $city=City::with(['neighborhoods','country'])->findOrFail($id);
            return response()->json($city);
        }
        catch (\Exception $e){
            return response()->json($e->getMessage(),400);
        }

    }

}
