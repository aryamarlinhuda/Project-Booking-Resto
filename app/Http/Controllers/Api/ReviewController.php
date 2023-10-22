<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function list(Request $request) {
        $user = auth()->id();

        $data = Review::where('resto_id',$request->input('resto_id'))->orderBy('created_by',$user)->get();

        $average_rating = Review::where('resto_id',$request->input('resto_id'))->average('rating');
        $getRating = substr($average_rating, 0, 3);
        $formattedRating = str_replace('.', ',', $getRating);
        $data->rating = $formattedRating;

        foreach($data as $x => $item) {
            $data[$x]->user = $item->user;

            $updated_at = $item->updated_at;

            if(now()->diffInSeconds($updated_at) === 0) {
                $data[$x]->last_made = "now";
            } else if(now()->diffInSeconds($updated_at) < 60) {
                $data[$x]->last_made = now()->diffInSeconds($updated_at) . " seconds ago";
            } else if(now()->diffInSeconds($updated_at) < 3600) {
                $data[$x]->last_made = now()->diffInMinutes($updated_at) . " minutes ago";
            } else if(now()->diffInSeconds($updated_at) < 86400) {
                $data[$x]->last_made = now()->diffInHours($updated_at) . " hours ago";
            } else if(now()->diffInSeconds($updated_at) < 172800) {
                $data[$x]->last_made = "yesterday";
            } else if(now()->diffInSeconds($updated_at) >= 172800) {
                $dateTime = new DateTime($updated_at);
                $formated_date = $dateTime->format('H:i d-M-Y');
                $date = Carbon::createFromFormat('H:i d-M-Y', $formated_date);
                $data[$x]->last_made = $date->format('H.i l, d F Y');
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $data],
            200
        );

    }
    public function add(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "rating" => "required | numeric | between:1,5",
                "review" => "required",
                "resto_id" => "required"
            ],[
                "rating.required" => "Rating is required!",
                "rating.numeric" => "Rating must be a number!",
                "rating.between" => "Rating must be between the numbers 1 to 5!",
                "review.required" => "Review is required!",
                "resto_id.required" => "Resto ID is required!",
            ]);

            $rated = Review::where('created_by',$user)->first();
            if($rated) {
                return response()->json([
                    "status" => 400,
                    "message" => "You have already rated this Resto",
                ], 400);
            }

            Review::create([
                "rating" => $request->input('rating'),
                "review" => $request->input('review'),
                "resto_id" => $request->input('resto_id'),
                "created_by" => $user
            ]);

            return response()->json([
                "status" => 200,
                "message" => "Review has been sent successfully",
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function edit(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "review_id" => "required | numeric",
                "rating" => "required | numeric | between:1,5",
                "review" => "required"
            ],[
                "rating.required" => "Rating is required!",
                "rating.numeric" => "Rating must be a number!",
                "rating.between" => "Rating must be between the numbers 1 to 5!",
                "review_id.required" => "Review ID is required!",
                "review_id.numeric" => "Review ID must be a number!"
            ]);

            $data = Review::where('id',$request->input('review_id'))->where('created_by',$user)->first();
            if(!$data) {
                return response()->json([
                    "status" => 404,
                    "message" => "Data not found",
                ], 404);
            } else {
                Review::where('id',$request->input('review_id'))->update([
                    "rating" => $request->input('rating'),
                    "description" => $request->input('review'),
                    "created_by" => $user
                ]);
            }

            return response()->json([
                "status" => 200,
                "message" => "Review has been sent successfully",
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function delete(Request $request) {
        $user = auth()->id();

        try {
            $request->validate([
                "review_id" => "required"
            ],[
                "review.required" => "Review ID is required!"
            ]);

            $data = Review::where('id',$request->input('review_id'))->where('created_by',$user)->first();
            if(!$data) {
                return response()->json([
                    "status" => 404,
                    "message" => "Data not found",
                ], 404);
            } else {
                Review::where('id',$request->input('review_id'))->where('created_by',$user)->delete();

                return response()->json([
                    "status" => 200,
                    "message" => "Review has been deleted successfully",
                ], 200);
            }
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }
}
