<?php
// app/Http/Controllers/RewardController.php
namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
use App\Http\Resources\RewardResource;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::where('user_id', auth()->id())
            ->with('mission')
            ->paginate(15);
        return RewardResource::collection($rewards);
    }

    public function redeem(Request $request)
    {
        $validated = $request->validate([
            'reward_id' => 'required|exists:rewards,id'
        ]);

        $reward = Reward::where('user_id', auth()->id())
            ->where('id', $validated['reward_id'])
            ->where('redeemed', false)
            ->firstOrFail();

        $reward->update(['redeemed' => true]);
        auth()->user()->increment('points', $reward->points_earned);

        return new RewardResource($reward);
    }
}
