<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Farm;
use App\Models\BeehiveInspection; // Assuming you have this model
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeehiveInspectionController extends Controller
{
    /**
     * Store the inspection data from Flutter form
     */
    public function storeInspection(Request $request)
    {
        $validated = $request->validate([
            'beekeeper_name' => 'required|string|max:255',
            'date' => 'required|date',
            'apiary_location' => 'required|string|max:255',
            'weather_conditions' => 'nullable|string|max:255',
    
            'hive_id' => 'required|string|max:50',
            'hive_type' => 'nullable|string|max:50',
            'hive_condition' => 'nullable|string|max:255',
    
            'presence_of_queen' => 'nullable|boolean',
            'queen_cells_present' => 'nullable|boolean',
            'brood_pattern' => 'nullable|string|max:255',
            'eggs_and_larvae_present' => 'nullable|boolean',
    
            'honey_stores' => 'nullable|string|in:Low,Medium,Full',
            'pollen_stores' => 'nullable|string|in:Low,Medium,High,Abundant',
    
            'bee_population' => 'nullable|string|in:Strong,Moderate,Weak',
            'aggressiveness_of_bees' => 'nullable|string|max:255',
    
            'diseases_or_pests_observed' => 'nullable|boolean',
            'disease_details' => 'nullable|string|max:255',
    
            'presence_of_beetles' => 'nullable|boolean',
            'presence_of_other_pests' => 'nullable|boolean',
            'other_pests_details' => 'nullable|string|max:255',
    
            'frames_checked' => 'nullable|integer',
            'any_frames_replaced' => 'nullable|boolean',
            'hive_cleaned' => 'nullable|boolean',
            'supers_added_or_removed' => 'nullable|string|max:255',
            'any_other_actions_taken' => 'nullable|string|max:255',
    
            'comments_and_recommendations' => 'nullable|string|max:255',
            'inspected_by' => 'nullable|string|max:255',
            'signature' => 'nullable|string|max:255',
        ]);

        if ($request->has('date') && $request->date) {
            $validated['date'] = Carbon::parse($request->date)->toIso8601String();
        }
    
        $inspection = BeehiveInspection::create($validated);

        return response()->json([
            'message' => 'Inspection data saved successfully',
            'data' => $inspection,
            'success' => true
        ], 201);
        
    }
    
}
