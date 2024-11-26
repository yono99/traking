<?php

namespace App\Http\Controllers;

use App\Models\Teams;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TeamsController extends Controller
{
    public function index()
    {
        $teams = Teams::with('users')->get();
        //memanggil user yang tidak ada dalam team
        $users = User::whereNotIn('id', function ($query) use ($teams) {
            $query->select('user_id')->from('team_users')->whereIn('team_id', $teams->pluck('id'));
        })->get();
        return Inertia::render('Teams/Index', ['teams' => $teams, 'users' => $users]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate(['name' => 'required|string|max:255']);
        $team = Teams::create($request->only('name'));
        // dd($team);
        $team->save();

        return response()->json(['message' => 'Team created successfully.']);
    }

    public function addMember(Request $request, Teams $team)
    {
        // dd($request, $team);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $team->users()->attach($request->user_id);

        return response()->json(['message' => 'Member added successfully.']);
    }

    public function removeMember(Team $team, User $user)
    {
        $team->users()->detach($user);

        return redirect()->back()->with('success', 'Member removed successfully.');
    }

    public function moveMember(Request $request, User $user)
    {
        $request->validate(['new_team_id' => 'required|exists:teams,id']);

        // Detach from current team and attach to new team
        $user->teams()->syncWithoutDetaching([$request->new_team_id]);

        return redirect()->back()->with('success', 'Member moved successfully.');
    }

    public function destroy(Teams $team)
    {
        // dd($team);
        if ($team->users()->exists()) {
            return response()->json(['message' => 'Cannot delete a team with members.']);
        }

        $team->delete();
        return response()->json(['message' => 'Team deleted successfully.']);
    }
}
